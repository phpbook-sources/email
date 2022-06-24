<?php namespace PHPBook\Email;

class Message {

    private $fromName;

    private $fromEmail;

    private $to;

    private $cc;

    private $cco;

    private $subject;

    private $content;

    private $attach = [];

    public function setFromName(String $fromName): Message {
        $this->fromName = $fromName;
        return $this;
    }

    public function getFromName(): ?String {
        return $this->fromName;
    }

    public function setFromEmail(String $fromEmail): Message {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    public function getFromEmail(): ?String {
        return $this->fromEmail;
    }

    public function setTo(Array $to): Message {
    	$this->to = $to;
    	return $this;
    }

    public function getTo(): ?Array {
    	return $this->to;
    }

    public function setCc(Array $cc): Message {
    	$this->cc = $cc;
    	return $this;
    }

    public function getCc(): ?Array {
    	return $this->cc;
    }

    public function setCco(Array $cco): Message {
    	$this->cco = $cco;
    	return $this;
    }

    public function getCco(): ?Array {
    	return $this->cco;
    }

    public function setSubject(String $subject): Message {
    	$this->subject = $subject;
    	return $this;
    }

    public function getSubject(): String {
    	return $this->subject;
    }

    public function setContent(String $content): Message {
    	$this->content = $content;
    	return $this;
    }

    public function getContent(): String {
    	return $this->content;
    }

    public function setAttach(Array $attach): Message {
    	$this->attach = $attach;
    	return $this;
    }

    public function getAttach(): ?Array {
    	return $this->attach;
    }
  
}
