<?php
class SMS {
	private $authorization, $curl;
	protected $apiUrl = "https://api.bulksms.com/v1";
	
	/**
	 * Constructeur
	 *
	 * @param string $tokenId tokenId
	 * @param string $tokenSecret tokenSecret
	 */
	public function __construct(string $tokenId, string $tokenSecret) {
		$this->authorization = base64_encode("$tokenId:$tokenSecret");
	}
	
	/**
	 * Destructeur
	 */
	public function __destruct() {
		if (!empty($this->curl)) {
			curl_close($this->curl);
		}
	}
	
	/**
	 * Envoie un SMS
	 *
	 * @param string $number Numéro de téléphone
	 * @param string $content Contenu du SMS
	 * @param bool $premium Utiliser le réseau premium ou non
	 *
	 * @return bool Résultat
	 */
	public function send(string $number, string $content, bool $premium = false) : bool {
		$post = [
			"to" => $number,
			"body" => $content,
			"routingGroup" => $premium ? "PREMIUM" : "STANDARD"
		];
		
		$data = $this->query("messages", $post);
		
		return isset($data[0]["type"]) && $data[0]["type"] == "SENT";
	}
	
	/**
	 * Effectue une requête vers l'API
	 *
	 * @param string $uri URI
	 * @param array $post Données POST
	 *
	 * @return array Résultat
	 */
	private function query(string $uri, array $post = []) : array {
		if (empty($this->curl)) {
			$this->curl = curl_init();
			curl_setopt($this->curl, CURLOPT_ENCODING, "gzip");
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, ["Authorization: Basic {$this->authorization}"]);
			curl_setopt($this->curl, CURLOPT_TIMEOUT, 3);
			curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		}
		
		curl_setopt($this->curl, CURLOPT_URL, "{$this->apiUrl}/$uri");
		if (!empty($post)) {
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($post));
		} else {
			curl_setopt($this->curl, CURLOPT_POST, false);
		}
		
		$result = @json_decode(curl_exec($this->curl), true);
		
		return !empty($result) ? $result : [];
	}
}