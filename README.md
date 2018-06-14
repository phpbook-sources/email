    
+ [About Email](#about-email)
+ [Composer Install](#composer-install)
+ [Declare Configurations](#declare-configurations)
+ [Sending Emails](#sending-emails)

### About Email

- A lightweight e-mail PHP library available for SMTP, AWSSES, MAILGUN AND SPARKPOST
- Requires PHP Extension FINFO.

### Composer Install

	composer require phpbook/email

### Declare Configurations

```php

/********************************************
 * 
 *  Declare Configurations
 * 
 * ******************************************/

//Driver connection SMTP

\PHPBook\Email\Configuration\Email::setConnection('main',
	(new \PHPBook\Email\Configuration\Connection)
		->setName('Main')
		->setExceptionCatcher(function(String $message) {
			//the PHPBook Email does not throw exceptions, but you can take it here
			//you can store $message in database or something else
		})
		->setDriver((new \PHPBook\Email\Driver\SMTP)
			->setHost('host')
			->setPort(100)
			->setUser('user')
			->setPassword('password')
			->setSecure('tls')
			->setEmail('email@email.com')
			->setName('Jhon'))
);

//Driver connection AWSSES

\PHPBook\Email\Configuration\Email::setConnection('other',
	(new \PHPBook\Email\Configuration\Connection)
		->setName('Other')
		->setExceptionCatcher(function(String $message) {
			//the PHPBook Email does not throw exceptions, but you can take it here
			//you can store $message in database or something else
		})
		->setDriver((new \PHPBook\Email\Driver\AWSSES)
			->setKey('key')
			->setSecret('secret')
			->setRegion('region')
			->setEmail('email@email.com')
			->setName('Jhon'))
);


//Driver connection MAILGUN

\PHPBook\Email\Configuration\Email::setConnection('important', 
	(new \PHPBook\Email\Configuration\Connection)
		->setName('Important')
		->setExceptionCatcher(function(String $message) {
			//the PHPBook Email does not throw exceptions, but you can take it here
			//you can store $message in database or something else
		})
		->setDriver((new \PHPBook\Email\Driver\MAILGUN)
			->setKey('key')
			->setDomain('domain')
			->setEmail('email@email.com')
			->setName('Jhon'))
);

//Driver connection SPARKPOST

\PHPBook\Email\Configuration\Email::setConnection('backups', 
	(new \PHPBook\Email\Configuration\Connection)
		->setName('Backups')
		->setExceptionCatcher(function(String $message) {
			//the PHPBook Email does not throw exceptions, but you can take it here
			//you can store $message in database or something else
		})
		->setDriver((new \PHPBook\Email\Driver\SPARKPOST)
			->setKey('key')
			->setEmail('email@email.com')
			->setName('Jhon'))
);


//Set default connection by connection alias

\PHPBook\Email\Configuration\Email::setDefault('main');

//Getting connections

$connections = \PHPBook\Email\Configuration\Email::getConnections();

foreach($connections as $code => $connection) {

	$connection->getName(); 

	$connection->getDriver();

};

?>
```

### Sending Emails


```php
		

	//Connection code is not required if you set default connection

	$boolean = (new \PHPBook\Email\Email)
		->setConnectionCode('other')
		->setMessage(
			(new \PHPBook\Email\Message)
				->setTo(['jhon@email.com'])
				->setCc(['paul@email.com'])
				->setCco(['ana@email.com'])
				->setSubject('email subject')
				->setContent('my html body')
				->setAttach([
					(new \PHPBook\Email\Attach)->setFileAlias('myfile')->setFileBuffer('my-file-buffer'),
					(new \PHPBook\Email\Attach)->setFileAlias('myfile')->setFilePath('my/file/path')
				])
		)->dispatch();

	if ($boolean) {
		//sent
	};

		
```