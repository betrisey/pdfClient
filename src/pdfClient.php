<?php namespace Axianet\pdfConverter;

class pdfClient {
	private $converterUrl;
	private $tokenUrl;
	private $access_token;
	
	public function __construct($params) {
		if(!empty($params['url']))
			$this->converterUrl = $params['url'];
		else
			$this->converterUrl = 'http://pdfconverter.axianet.ch/api/';
		
		if(!empty($params['authUrl']))
			$this->tokenUrl = $params['authUrl'];
		else
			$this->tokenUrl = $this->converterUrl . '/auth/token.php';
		
		if(!empty($params['clientId']) && !empty($params['clientSecret']))
			$this->getToken($params['clientId'], $params['clientSecret']);
		else
			throw new exception("Veuillez passer en paramètre le client ID et client secret");
	}
	
	private function getToken($clientId, $clientSecret) {
		$ch = curl_init($this->tokenUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$clientSecret");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('grant_type' => 'client_credentials'));
		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($result, true);

		if(!empty($result['error'])) {
			throw new Exception("Erreur authentification OAuth " . $result['error'] . ' : ' . $result['error_description']);
		} else {
			// Pas d'erreur, le token a été récupéré
			$this->access_token = $result['access_token'];
		}
	}
	
	public function fromString($html, $to = null, $subject = null, $content = null) {
		$ch = curl_init($this->converterUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$this->access_token));
		curl_setopt($ch, CURLOPT_POST, 1);
		
		$postFields = [
			'file' => $html
		];
		
		if(!empty($to)) {
			$postFields['to'] = is_array($to) ? join(',', $to) : $to;
			$postFields['subject'] = $subject;
			$postFields['content'] = $content;
		}
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	public function fromFile($fileName, $to = null, $subject = null, $content = null) {
		return $this->fromString(file_get_contents($fileName), $to, $subject, $content);
	}
	
	public function fromUrl($url, $to = null, $subject = null, $content = null) {
		$ch = curl_init($this->converterUrl.'?url='.$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$this->access_token));
		
		
		if(!empty($to)) {
			$postFields = [];
			$postFields['to'] = is_array($to) ? join(',', $to) : $to;
			$postFields['subject'] = $subject;
			$postFields['content'] = $content;
			
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		}
		
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}