<?php
class Server {
	public static function create(int $type, string $owner) : int {
		global $db;
		
		$query = $db->prepare("SELECT id FROM servers WHERE type = :type AND expiration = 0");
		$query->bindValue(":type", $type, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		if (empty($data)) {
			return 0;
		}
		
		$query = $db->prepare("UPDATE servers SET owner = :owner, expiration = :expiration WHERE id = :id");
		$query->bindValue(":owner", $owner, PDO::PARAM_STR);
		$query->bindValue(":expiration", strtotime("+1 month"), PDO::PARAM_INT);
		$query->bindValue(":id", $data["id"], PDO::PARAM_INT);
		$query->execute();
		
		return $data["id"];
	}
		
	public static function isAvailable(int $type) : bool {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE type = :type AND owner = ''");
		$query->bindValue(":type", $type, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] >= 1;
	}
}