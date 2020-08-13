<?php
class Cache {
	public static function exists(string $name) : bool {
		return !empty($this->read($name));
	}
	
	public static function read(string $name) : string {
		global $db;
		
		$query = $db->prepare("SELECT value FROM cache WHERE name = :name");
		$query->bindValue(":name", $name, PDO::PARAM_STR);
		$query->execute();
		
		return trim($query->fetch()["value"]);
	}
	
	public static function write(string $name, string $value, int $expiration = 86400) : bool {
		global $db;
		
		if (self::exists($name)) {
			$query = $db->prepare("UPDATE cache SET value = :value WHERE name = :name");
			$query->bindValue(":value", $value, PDO::PARAM_STR);
			$query->bindValue(":name", $name, PDO::PARAM_STR);
			return $query->execute();
		} else {
			$query = $db->prepare("INSERT INTO cache(name, value, expiration) VALUES(:name, :value, ".(time()+$expiration).")");
			$query->bindValue(":name", $name, PDO::PARAM_STR);
			$query->bindValue(":value", $value, PDO::PARAM_STR);
			return $query->execute();
		}
	}
}