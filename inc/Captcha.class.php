<?php
class Captcha {
	public function __construct(string $publicKey, string $privateKey) {
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
	}
	
	/**
	 * Crée un captcha
	 */
	public function create() {
		global $config;
		
		echo "<div class=\"g-recaptcha\" data-sitekey=\"{$this->publicKey}\"></div>\n";
	}
	
	/**
	 * Effectue une vérification du résultat envoyé à l'utilisateur
	 */
	public function check() : bool {
		global $config;
		
		if (!isset($_POST["g-recaptcha-response"]) || !is_string($_POST["g-recaptcha-response"]) || empty($_POST["g-recaptcha-response"])) {
			return false;
		}
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(["secret" => $this->privateKey, "response" => $_POST["g-recaptcha-response"], "remoteip" => $_SERVER["REMOTE_ADDR"]]));
		curl_setopt($curl, CURLOPT_TIMEOUT, 3);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$page = @json_decode(curl_exec($curl));
		curl_close($curl);
		
		return isset($page->success) && $page->success ? true : false;
	}
}