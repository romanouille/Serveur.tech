<?php
class Session {
	public function __construct(string $id) {
		$this->id = $id;
	}
	
	public function exists() : bool {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM users_sessions WHERE name = :name AND expiration > ".time());
		$query->bindValue(":name", $this->id, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] == 1;
	}
	
	public function update() : bool {
		global $db;
		
		$query = $db->prepare("UPDATE users_sessions SET ip = :ip, user_agent = :user_agent");
		$query->bindValue(":ip", $_SERVER["REMOTE_ADDR"], PDO::PARAM_STR);
		$query->bindValue(":user_agent", $_SERVER["HTTP_USER_AGENT"], PDO::PARAM_STR);
		$query->execute();
		
		$query = $db->prepare("SELECT id FROM users_sessions WHERE name = :name");
		$query->bindValue(":name", $this->id, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		$query = $db->prepare("INSERT INTO logs(session, ip, port, user_agent) VALUES(:session, :ip, :port, :user_agent)");
		$query->bindValue(":session", trim($data["id"]), PDO::PARAM_STR);
		$query->bindValue(":ip", $_SERVER["REMOTE_ADDR"], PDO::PARAM_STR);
		$query->bindValue(":port", $_SERVER["REMOTE_PORT"], PDO::PARAM_INT);
		$query->bindValue(":user_agent", $_SERVER["HTTP_USER_AGENT"], PDO::PARAM_STR);
		$query->execute();
		
		return true;
	}
	
	public static function create(int $userId) : bool {
		global $db, $dev;
		
		$hash = hash("sha512", uniqid().microtime(1).$userId.random_bytes(100).random_int(1000000000, 9999999999));
		setcookie("session", $hash, time()+31536000, "/", $_SERVER["HTTP_HOST"], !$dev, true);
		
		$query = $db->prepare("INSERT INTO users_sessions(name, user_id, ip, user_agent, created, expiration) VALUES(:name, :user_id, :ip, :user_agent, ".time().", ".(time()+31536000).")");
		$query->bindValue(":name", $hash, PDO::PARAM_STR);
		$query->bindValue(":user_id", $userId, PDO::PARAM_INT);
		$query->bindValue(":ip", $_SERVER["REMOTE_ADDR"]);
		$query->bindValue(":user_agent", substr($_SERVER["HTTP_USER_AGENT"], 0, 255), PDO::PARAM_STR);
		return $query->execute();
	}
}