<?php
class Paypal {
	private $curl;
	
	/**
	 * Constructeur
	 *
	 * @param string $client clientID
	 * @param string $secret Secret
	 */
	public function __construct(string $clientId, string $secret) {
		global $dev;
		$this->dev = $dev;

		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_USERAGENT, "");
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
		curl_setopt($this->curl, CURLOPT_USERPWD, "$clientId:$secret");
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query(["grant_type" => "client_credentials"]));
		curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
	}
	
	/**
	 * Crée un formulaire de paiement
	 *
	 * @param float $price Prix
	 * @param string $url URL de retour
	 *
	 * @return array Formulaire de paiement
	 */
	public function createPayment(float $price, string $url) : array {
		$post = '{"intent":"sale","payer":{"payment_method":"paypal"},"transactions":[{"amount":{"total":'.$price.',"currency":"EUR"}}],"redirect_urls":{"return_url":"'.$url.'","cancel_url":"'.$url.'"}}';

		curl_setopt($this->curl, CURLOPT_URL, "https://api.".($this->dev ? "sandbox." : "")."paypal.com/v1/payments/payment");
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
		$page = json_decode(curl_exec($this->curl), true);
		if (empty($page) || !isset($page["links"][1]["href"])) {
			trigger_error("Paypal : Un probleme est survenu pendant la creation du paiement.", E_USER_ERROR);
		}

		return $page;
	}
	
	/**
	 * Valide un paiement
	 *
	 * @param string $paymentId paymentId
	 * @param string $payerId payerId
	 *
	 * @return bool Paiement approuvé ou non
	 */
	public function validatePayment(string $paymentId, string $payerId) : bool {
		$post = '{"payer_id":"'.$payerId.'"}';
		curl_setopt($this->curl, CURLOPT_URL, "https://api.".($this->dev ? "sandbox." : "")."paypal.com/v1/payments/payment/$paymentId/execute");
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
		$page = @json_decode(curl_exec($this->curl), true);
		curl_close($this->curl);
	
		return isset($page["state"]) && $page["state"] == "approved";
	}
}