<?php
class User {
	public function __construct(string $phone) {
		$this->phone = $phone;
	}
	
	public function exists() {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM users WHERE phone = :phone");
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] > 0;
	}
	
	public static function create(string $phone, string $password, string $firstName, string $lastName, string $companyName, string $address1, string $address2, string $city, string $postalCode, string $country) {
		global $db;
		
		$query = $db->prepare("INSERT INTO users(phone, password, first_name, last_name, company_name, address1, address2, city, postal_code, country) VALUES(:phone, :password, :first_name, :last_name, :company_name, :address1, :address2, :city, :postal_code, :country)");
		$query->bindValue(":phone", $phone, PDO::PARAM_STR);
		$query->bindValue(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
		$query->bindValue(":first_name", $firstName, PDO::PARAM_STR);
		$query->bindValue(":last_name", $lastName, PDO::PARAM_STR);
		$query->bindValue(":company_name", $companyName, PDO::PARAM_STR);
		$query->bindValue(":address1", $address1, PDO::PARAM_STR);
		$query->bindValue(":address2", $address2, PDO::PARAM_STR);
		$query->bindValue(":city", $city, PDO::PARAM_STR);
		$query->bindValue(":postal_code", $postalCode, PDO::PARAM_STR);
		$query->bindValue(":country", $country, PDO::PARAM_STR);
		$query->execute();
		
		return $db->lastInsertId();
	}
	
	public function getProfile() : array {
		global $db;
		
		$query = $db->prepare("SELECT phone, password, first_name, last_name, company_name, address1, address2, city, postal_code, country FROM users WHERE phone = :phone");
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return array_map("trim", $data);
	}
	
	public function sendSmsCode() : bool {
		global $config, $db;
		
		$code = random_int(1000000000, 9999999999);
		
		$query = $db->prepare("UPDATE users SET validation_code = :validation_code WHERE phone = :phone");
		$query->bindValue(":validation_code", $code, PDO::PARAM_INT);
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		$query->execute();
		
		$sms = new SMS($config["bulksms"]["token_id"], $config["bulksms"]["token_secret"]);
		return $sms->send("+33".substr($this->phone, 1), "Votre code de validation Serveur.tech est : $code");
	}
	
	public function getValidationCode() : int {
		global $db;
		
		$query = $db->prepare("SELECT validation_code FROM users WHERE phone = :phone");
		$query->bindValue(":phone", $this->phone, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		return $data["validation_code"];
	}
	
	public function verifyPassword(string $password) : bool {
		global $db;
		
		$query = $db->prepare("SELECT password FROM users WHERE phone = :phone");
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return password_verify($password, trim($data["password"]));
	}
	
	public function changePassword(string $password) : bool {
		global $db;
		
		$query = $db->prepare("UPDATE users SET password = :password WHERE phone = :phone");
		$query->bindValue(":password", password_hash($password, PASSWORD_DEFAULT));
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		
		return $query->execute();
	}
	
	public function createPayment(string $paymentId, int $offerType, int $serverId = 0) : bool {
		global $db;
		
		$query = $db->prepare("INSERT INTO users_payments(payment_id, offer_type, server_id) VALUES(:payment_id, :offer_type, :server_id)");
		$query->bindValue(":payment_id", $paymentId, PDO::PARAM_STR);
		$query->bindValue(":offer_type", $offerType, PDO::PARAM_INT);
		$query->bindValue(":server_id", $serverId, PDO::PARAM_INT);
		
		return $query->execute();
	}
	
	public function getPaymentData(string $paymentId) : array {
		global $db;
		
		$query = $db->prepare("SELECT offer_type, server_id FROM users_payments WHERE payment_id = :payment_id");
		$query->bindValue(":payment_id", $paymentId, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		if (empty($data)) {
			return [];
		}
		
		$result = [
			"offer_type" => (int)$data["offer_type"],
			"server_id" => (int)$data["server_id"]
		];
		
		$query = $db->prepare("DELETE FROM users_payments WHERE payment_id = :payment_id");
		$query->bindValue(":payment_id", $paymentId, PDO::PARAM_STR);
		$query->execute();
		
		return $result;
	}
	
	public function getId() : int {
		global $db;
		
		$query = $db->prepare("SELECT id FROM users WHERE phone = :phone");
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return $data["id"];
	}
	
	public function createInvoice(int $type, float $price) : int {
		global $db;
		
		$query = $db->prepare("INSERT INTO users_invoices(owner, type, price, microtime) VALUES(:owner, :type, :price, ".str_replace(".", "", microtime(1)).")");
		$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		$query->bindValue(":type", $type, PDO::PARAM_INT);
		$query->bindValue(":price", $price, PDO::PARAM_STR);
		$query->execute();
		
		return $db->lastInsertId();
	}
	
	public function getInvoice(int $id) : array {
		global $db;
		
		$query = $db->prepare("SELECT type, price, microtime FROM users_invoices WHERE id = :id AND owner = :owner");
		$query->bindValue(":id", $id, PDO::PARAM_INT);
		$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		if (empty($data)) {
			return [];
		}
		
		$data = array_map("trim", $data);
		
		return $data;
	}
	
	public function getServersList() : array {
		global $db;
		
		$query = $db->prepare("SELECT id, ip, type, expiration FROM servers WHERE owner = :owner");
		$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetchAll();
		if (empty($data)) {
			return [];
		}
		
		$result = [];
		
		foreach ($data as $value) {
			$result[] = [
				"id" => (int)$value["id"],
				"ip" => trim($value["ip"]),
				"type" => (int)$value["type"],
				"expiration" => (int)$value["expiration"]
			];
		}
		
		return $result;
	}
	
	public function getInvoicesList() : array {
		global $db;
		
		$query = $db->prepare("SELECT id, type, price, microtime FROM users_invoices WHERE owner = :owner");
		$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetchAll();
		if (empty($data)) {
			return [];
		}
		
		$result = [];
		
		foreach ($data as $value) {
			$result[] = [
				"id" => (int)$value["id"],
				"type" => (int)$value["type"],
				"price" => (float)$value["price"],
				"timestamp" => (int)substr($value["microtime"], 0, -4)
			];
		}
		
		return $result;
	}
	
	public function hasServer(int $serverId) : bool {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE id = :id AND owner = :owner");
		$query->bindValue(":id", $serverId, PDO::PARAM_INT);
		$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] == 1;
	}
}