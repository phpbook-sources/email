<?php namespace PHPBook\Email;

class Email {
    
    private $connectionCode;

    private $message;

    public function setConnectionCode(String $connectionCode): Email {
    	$this->connectionCode = $connectionCode;
    	return $this;
    }

    public function getConnectionCode(): ?String {
    	return $this->connectionCode;
    }

    public function setMessage(Message $message): Email {
    	$this->message = $message;
    	return $this;
    }

    public function getMessage(): ?Message {
    	return $this->message;
    }

    public function dispatch(): Bool {

    	$connection = \PHPBook\Email\Configuration\Email::getConnection($this->getConnectionCode());

    	if (($connection) and ($connection->getDriver())) {

            try {
                
                return $connection->getDriver()->dispatch($this->getMessage());
                
            } catch(\Exception $e) {

                if ($connection->getExceptionCatcher()) {

                    $connection->getExceptionCatcher()($e->getMessage());
                    
                };

                return false;

            };

    	};

    	return false;

    }
  
}
