<?php
class Recaptcha {
	public function __construct(string $publicKey, string $privateKey) {
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
	}
	
	public function generate(bool $center = false) {
		echo "<div class=\"g-recaptcha".($center ? " captcha-center" : "")."\" data-sitekey=\"{$this->publicKey}\"></div>\n";
	}

	public function check() : bool {		
		if (!isset($_POST["g-recaptcha-response"]) || empty($_POST["g-recaptcha-response"])) {
			return false;
		}

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($curl, CURLOPT_ENCODING, "gzip");
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_USERAGENT, "");
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(["secret" => $this->privateKey, "response" => $_POST["g-recaptcha-response"], "remoteip" => $_SERVER["REMOTE_ADDR"]]));
		curl_setopt($curl, CURLOPT_HTTPHEADER, ["X-Forwarded-For: {$_SERVER["REMOTE_ADDR"]}"]);
		curl_setopt($curl, CURLOPT_TIMEOUT, 3);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$page = curl_exec($curl);
		curl_close($curl);

		$page = @json_decode($page, true);
		
		if (empty($page) || !isset($page["success"]) || !is_bool($page["success"])) {
			return true;
		}

		return $page["success"];
	}
}