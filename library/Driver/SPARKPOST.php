<?php namespace PHPBook\Email\Driver;

class SPARKPOST extends Adapter {
    	
    private $key;

    private $email;

    private $name;

    public function getKey(): String {
    	return $this->key;
    }

    public function setKey(String $key): SPARKPOST {
    	$this->key = $key;
    	return $this;
    }

	public function getEmail(): String {
		return $this->email;
	}

	public function setEmail(String $email): SPARKPOST {
		$this->email = $email;
		return $this;
	}

	public function getName(): String {
		return $this->name;
	}

	public function setName(String $name): SPARKPOST {
		$this->name = $name;
		return $this;
	}

    public function dispatch(\PHPBook\Email\Message $message): Bool {

		$url = 'https://api.sparkpost.com/api/v1/transmissions/';
		
		$headers = [
			'Content-Type: application/json"',
			'Authorization: ' . $this->getKey()
		];

		$recipients = [];
		
		$recipe_to = [];

		$recipe_cc = [];

		$recipe_cco = [];

		if ($message->getTo()) {
			foreach($message->getTo() as $to) {

				$recipe_to[] = $to;
	
				$recipients[] = [
					'address' => [
						'email' => $to,
						'tags' => new \Stdclass,
						'substitution_data' => new \Stdclass
					]
				];
	
			};
		};

		if ($message->getCc()) {

			foreach($message->getCc() as $cc) {

				$recipe_cc[] = $cc;
	
				$recipients[] = [
					'address' => [
						'email' => $cc,
						'header_to' => implode(',', $recipe_to),
						'tags' => new \Stdclass,
						'substitution_data' => new \Stdclass
					]
				];
	
			};

		};

		if ($message->getCco()) {

			foreach($message->getCco() as $cco) {

				$recipe_cco[] = $cco;
	
				$recipients[] = [
					'address' => [
						'email' => $cco,
						'header_to' => implode(',', $recipe_to),
						'tags' => new \Stdclass,
						'substitution_data' => new \Stdclass
					]
				];
	
			};

		};		

		$attachments = [];

		$finfo = new \finfo(FILEINFO_MIME);

		foreach($message->getAttach() as $attach) {

			if ($attach->getFilePath()) {

				$type = mime_content_type($attach->getFilePath());

				$data = base64_encode(file_get_contents($attach->getFilePath()));

			} else {

				$type = $finfo->buffer($attach->getFileBuffer());
				
				$data = base64_encode($attach->getFileBuffer());

			};

			$attachments[] = [
				'type' => $type,
				'name' => $attach->getFileAlias(),
				'data' => $data
			];

		};
		
		$body = [
			'options' => [
				'open_tracking' => true,
				'click_tracking' => true,
			],
			'cname_verify' => false,
			'metadata' => new \Stdclass,
			'substitution_data' => new \Stdclass,
			'recipients' => $recipients,
			'content' => [
				'from' => [
					'name' => $this->getName(),
					'email' => $this->getEmail()
				],
				'headers' => [
					'CC' => implode(',', $recipe_cc)
				],
				'subject' => $message->getSubject(),
				'reply_to' => null,
				'text' => null,
				'html' => $message->getContent(),
				'attachments' => $attachments,
			]
			
		];		
		
		$curl_handler = curl_init();
		
		curl_setopt($curl_handler, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl_handler, CURLOPT_POSTFIELDS, json_encode($body));
		curl_setopt($curl_handler, CURLOPT_HEADER, false);
		curl_setopt($curl_handler, CURLOPT_URL, $url);
		curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handler, CURLOPT_FOLLOWLOCATION, true);

		$data = curl_exec($curl_handler);

		$httpcode = curl_getinfo($curl_handler, CURLINFO_HTTP_CODE);

		curl_close($curl_handler);

		if (($httpcode >= 200) and ($httpcode <= 299)) {

			return true;

		};

		throw new \Exception($data);

    }

}