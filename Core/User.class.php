<?php
class User {
	public function __construct(int $id) {
		$this->id = $id;
	}
	
	public function checkPassword(string $password) : bool {
		global $db;
		
		$query = $db->prepare("SELECT password FROM users WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		return password_verify($password, trim($data["password"]));
	}
	
	public static function emailExists(string $email) : bool {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM users WHERE email = :email AND deleted = 0");
		$query->bindValue(":email", $email, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] == 1;
	}
	
	public static function create(string $email, string $password, string $firstname, string $lastname, string $country, string $address, string $postalcode, string $city, string $phone) : int {
		global $db;
		
		$query = $db->prepare("INSERT INTO users(email, password, firstname, lastname, country, address, postalcode, city, phone) VALUES(:email, :password, :firstname, :lastname, :country, :address, :postalcode, :city, :phone)");
		$query->bindValue(":email", $email, PDO::PARAM_STR);
		$query->bindValue(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
		$query->bindValue(":firstname", $firstname, PDO::PARAM_STR);
		$query->bindValue(":lastname", $lastname, PDO::PARAM_STR);
		$query->bindValue(":country", $country, PDO::PARAM_STR);
		$query->bindValue(":address", $address, PDO::PARAM_STR);
		$query->bindValue(":postalcode", $postalcode, PDO::PARAM_STR);
		$query->bindValue(":city", $city, PDO::PARAM_STR);
		$query->bindValue(":phone", $phone, PDO::PARAM_STR);
		$query->execute();
		
		return $db->lastInsertId();
	}
	
	public static function emailToId(string $email) : int {
		global $db;
		
		$query = $db->prepare("SELECT id FROM users WHERE email = :email");
		$query->bindValue(":email", $email, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return $data["id"];
	}
}