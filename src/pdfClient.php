<?php namespace Axianet\pdfClient;

class pdfClient {
	private $converterUrl;
	private $tokenUrl;
	private $access_token;
	
	public function __construct($params) {
		if(!empty($params['url']))
			$this->converterUrl = $params['url'];
		
		if(!empty($params['authUrl']))
			$this->tokenUrl = $params['authUrl'];
		else
			$this->tokenUrl = $this->converterUrl . '/auth/token.php';
		
		if(!empty($params['clientId']) && !empty($params['clientSecret']))
			$this->getToken($params['clientId'], $params['clientSecret']);
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
	
	public function fromString($html) {
		$ch = curl_init($this->converterUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$this->access_token));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('file' => $html));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	public function fromFile($fileName) {
		return $this->fromString(file_get_contents($fileName));
	}
	
	public function fromUrl($url) {
		$ch = curl_init($this->converterUrl.'?url='.$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$this->access_token));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}