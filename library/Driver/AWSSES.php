<?php namespace PHPBook\Email\Driver;

class AWSSES extends Adapter  {
    	
    private $key;

    private $secret;

    private $region;

    private $email;

    private $name;

    public function getKey(): String {
    	return $this->key;
    }

    public function setKey(String $key): AWSSES {
    	$this->key = $key;
    	return $this;
    }

    public function getSecret(): String {
    	return $this->secret;
    }

    public function setSecret(String $secret): AWSSES {
    	$this->secret = $secret;
    	return $this;
    }

    public function getRegion(): String {
    	return $this->region;
    }

    public function setRegion(String $region): AWSSES {
    	$this->region = $region;
    	return $this;
    }

	public function getEmail(): String {
		return $this->email;
	}

	public function setEmail(String $email): AWSSES {
		$this->email = $email;
		return $this;
	}

	public function getName(): String {
		return $this->name;
	}

	public function setName(String $name): AWSSES {
		$this->name = $name;
		return $this;
	}

    public function dispatch(\PHPBook\Email\Message $message): Bool {

		$key = $this->getKey();
		$secret = $this->getSecret();
		$host = 'email.' . $this->getRegion() . '.amazonaws.com';
		$from = $message->getFromEmail() ? $message->getFromEmail() : $this->getEmail();
		
		$to = $message->getTo();	
		$bcc = $message->getCco();
		$cc = $message->getCc();
		$subject = $message->getSubject();
		$content = $message->getContent();
		$attachs = [];

		foreach($message->getAttach() as $attach) {
			$attachs[] = [
				'name' => $attach->getFileAlias(),
				'content' => base64_encode($attach->getFileBuffer() ? $attach->getFileBuffer() : file_get_contents($attach->getFilePath()))
			];
		};
		
		$boundaries = rand(5000000, 9000000);
		
		$raw = '';
		
		$raw .= 'To:' . implode(',', $to) . PHP_EOL;
		
		$raw .= 'Bcc:' . implode(',', $bcc) . PHP_EOL;
		
		$raw .= 'Cc:' . implode(',', $cc) . PHP_EOL;
		
		$raw .= 'From:' . $from . PHP_EOL;
		
		$raw .= 'Subject:=?UTF-8?B?' . base64_encode(($subject)) . '=?=' . PHP_EOL;
		
		$raw .= 'MIME-Version: 1.0' . PHP_EOL;
		
		$raw .= 'Content-type: Multipart/Mixed; boundary="'.$boundaries.'" ' . PHP_EOL . PHP_EOL;
		
		$raw .= '--'.$boundaries . PHP_EOL;
		
		$raw .= 'Content-type: Multipart/Alternative; boundary="alt-'.$boundaries.'"' . PHP_EOL . PHP_EOL;
		
		$raw .= '--alt-'.$boundaries. PHP_EOL;
		
		$raw .= 'Content-Type: text/html; charset="UTF-8"' . PHP_EOL . PHP_EOL;
		
		$raw .= $content . PHP_EOL;
		
		$raw .= '--alt-'.$boundaries.'--' . PHP_EOL;
		
		$finfo = new \finfo(FILEINFO_MIME);
		
		foreach($attachs as $attach) {
			
			$mime = $finfo->buffer(base64_decode($attach['content']));
			
			$raw .= PHP_EOL . '--'.$boundaries . PHP_EOL;
			$raw .= 'Content-Type: '.$mime.'; name="'.$attach['name'].'"' . PHP_EOL;
			$raw .= 'Content-Disposition: attachment' . PHP_EOL;
			$raw .= 'Content-Transfer-Encoding: base64' . PHP_EOL . PHP_EOL;
			$raw .=  $attach['content'] . PHP_EOL . PHP_EOL;
			
		};
		
		$raw .= '--'.$boundaries.'--';
		
		$msg = $raw;

		$date = gmdate('D, d M Y H:i:s e');

		$auth = 'AWS3-HTTPS AWSAccessKeyId='.$key.',Algorithm=HmacSHA256,Signature='.base64_encode(hash_hmac('sha256', $date, $secret, true));
		
		$url = 'https://'.$host.'/';
		
		$parametros_ = ['Action' => 'SendRawEmail',
			'RawMessage.Data' => base64_encode($msg), 
			'Message.Subject.Data' => 'raw',
			'Message.Subject.Charset' => 'utf-8'
		];		

		$parametros = [];

		foreach($parametros_ as $key => $par) {
			$parametros[$key] = $key . '=' . str_replace('%7E', '~', rawurlencode($par));
		};
		
		$headers = array();
		$headers[] = 'Date: ' . $date;
		$headers[] = 'Host: ' . $host;
		$headers[] = 'X-Amzn-Authorization: ' . $auth;
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		
		$curl_handler = curl_init();
		
		curl_setopt($curl_handler, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl_handler, CURLOPT_POSTFIELDS, implode('&', $parametros));
		curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl_handler, CURLOPT_HEADER, false);
		curl_setopt($curl_handler, CURLOPT_URL, $url);
		curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handler, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl_handler, CURLOPT_HEADER, false);

		$response = curl_exec($curl_handler);

		$httpcode = curl_getinfo($curl_handler, CURLINFO_HTTP_CODE);

		curl_close($curl_handler);

		if (($httpcode >= 200) and ($httpcode <= 299)) {

			return true;

		};

		throw new \Exception($response);
	
    }

}