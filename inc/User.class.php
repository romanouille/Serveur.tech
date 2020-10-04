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
}