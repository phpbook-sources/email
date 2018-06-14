<?php namespace PHPBook\Email\Driver;

class MAILGUN extends Adapter  {
    	
    private $key;

    private $domain;

    private $email;

    private $name;

    public function getKey(): String {
    	return $this->key;
    }

    public function setKey(String $key): MAILGUN {
    	$this->key = $key;
    	return $this;
    }

    public function getDomain(): String {
    	return $this->domain;
    }

    public function setDomain(String $domain): MAILGUN {
    	$this->domain = $domain;
    	return $this;
    }

	public function getEmail(): String {
		return $this->email;
	}

	public function setEmail(String $email): MAILGUN {
		$this->email = $email;
		return $this;
	}

	public function getName(): String {
		return $this->name;
	}

	public function setName(String $name): MAILGUN {
		$this->name = $name;
		return $this;
	}

    public function dispatch(\PHPBook\Email\Message $message): Bool {
	
		$url = 'https://api.mailgun.net/v3/' . $this->getDomain() . '/messages';

		$files = [];

		$finfo = new \finfo(FILEINFO_MIME);

		foreach($message->getAttach() as $attach) {

			if ($attach->getFilePath()) {

				$files[] = new \CurlFile($attach->getFilePath(), $finfo->file($attach->getFilePath()), $attach->getFileAlias());

			} else {

				$contents_buffer = $attach->getFileBuffer();
				
				$contents_file = tmpfile();
				
				fwrite($contents_file, $contents_buffer);
				
				fseek($contents_file, 0);
				
				$contents_meta = stream_get_meta_data($contents_file);
				
				$files[] = new \CurlFile($contents_meta['uri'], $finfo->buffer($attach->getFileBuffer()), $attach->getFileAlias());

			};

		};
		
		$post_data = [
			'from' => $this->getName() .'<'.$this->getEmail().'>',
			'to' => implode(',', $message->getTo() ? $message->getTo() : []),
			'cc' => implode(',', $message->getCc() ? $message->getCc() : []),
			'bcc' => implode(',', $message->getCco() ? $message->getCco() : []),
			'subject' => $message->getSubject(),
			'html' => $message->getContent(),
			'text' => '',
			'o:tracking' => 'yes',
			'o:tracking-clicks' => 'yes',
			'o:tracking-opens' => 'yes',
			'h:Content-Type' => 'multipart/form-data',
		];
		
		foreach($files as $key => $file) {
			$post_data['attachment['.$key.']'] = $file;
		};

		$headers = [];

		$headers[] = "Content-Type: multipart/form-data";
		
		$session = curl_init($url);
		curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($session, CURLOPT_USERPWD, 'api:'.$this->getKey());
		curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($session, CURLOPT_POST, true);
		curl_setopt($session, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
		
		$response = curl_exec($session);

		$httpcode = curl_getinfo($session, CURLINFO_HTTP_CODE);

		curl_close($session);

		if (($httpcode >= 200) and ($httpcode <= 299)) {

			return true;

		};

		throw new \Exception($response);

    }

}