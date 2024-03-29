<?php namespace PHPBook\Email\Driver;

class SMTP extends Adapter {
    	
    private $host;

    private $port;

    private $user;

    private $password;

    private $secure;

    private $email;

    private $name;

    private $encode;

    public function getHost(): String {
    	return $this->host;
    }

    public function setHost(String $host): SMTP {
    	$this->host = $host;
    	return $this;
    }

	public function getPort(): Int {
		return $this->port;
	}

	public function setPort(Int $port): SMTP {
		$this->port = $port;
		return $this;
	}

	public function getUser(): String {
		return $this->user;
	}

	public function setUser(String $user): SMTP {
		$this->user = $user;
		return $this;
	}

	public function getPassword(): String {
		return $this->password;
	}

	public function setPassword(String $password): SMTP {
		$this->password = $password;
		return $this;
	}

	public function getSecure(): Bool {
		return $this->secure;
	}

	public function setSecure(Bool $secure): SMTP {
		$this->secure = $secure;
		return $this;
	}

	public function getEmail(): String {
		return $this->email;
	}

	public function setEmail(String $email): SMTP {
		$this->email = $email;
		return $this;
	}

	public function getName(): String {
		return $this->name;
	}

	public function setName(String $name): SMTP {
		$this->name = $name;
		return $this;
	}

	public function getEncode(): String {
		return $this->encode;
	}

	public function setEncode(String $encode): SMTP {
		$this->encode = $encode;
		return $this;
	}

    public function dispatch(\PHPBook\Email\Message $message): Bool {
				
		$mail = new \PHPBook\Email\Driver\Third\PHPMailer\PHPMailer(true);

		try {

			$mail->SMTPDebug = 0;
			$mail->isSMTP();
			$mail->Host =  $this->getHost(); 
			$mail->SMTPAuth = true;
			$mail->Username = $this->getUser();
			$mail->Password = $this->getPassword();
			$mail->SMTPSecure = $this->getSecure();
			$mail->Port = $this->getPort();
			$mail->setFrom($message->getFromEmail() ? $message->getFromEmail() : $this->getEmail(), $message->getFromName() ? $message->getFromName() : $this->getName());
			
			if ($message->getTo()) {
				foreach($message->getTo() as $to) {
					$mail->addAddress($to);
				};
			};

			if ($message->getCc()) {
				foreach($message->getCc() as $cc) {
					$mail->addCC($cc);
				};
			};

			if ($message->getCco()) {
				foreach($message->getCco() as $cco) {
					$mail->addBCC($cco);
				};
			};

			$finfo = new \finfo(FILEINFO_MIME);

			foreach($message->getAttach() as $attach) {

				$fileAlias = strtolower($this->getEncode()) == 'utf8' ? utf8_decode($attach->getFileAlias()) : $attach->getFileAlias();

				if ($attach->getFilePath()) {
	
					$mail->addAttachment($attach->getFilePath(), $fileAlias);
	
				} else {
					
					$mail->AddStringAttachment($attach->getFileBuffer(), $fileAlias, 'binary', $finfo->buffer($attach->getFileBuffer()));
	
				};
	
			};

			$mail->isHTML(true);
			$mail->Subject = strtolower($this->getEncode()) == 'utf8' ? utf8_decode($message->getSubject()) : $message->getSubject();
			$mail->Body    = strtolower($this->getEncode()) == 'utf8' ? utf8_decode($message->getContent()) : $message->getContent();

			$mail->send();

			return true;

		} catch (\PHPMailer\PHPMailer\Exception $e) {

			throw new \Exception($mail->ErrorInfo);

		};

    }

}