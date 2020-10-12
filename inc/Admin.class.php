<?php
class Admin {
	public static function getTicketsList() : array {
		global $db;
		
		$query = $db->prepare("SELECT DISTINCT owner, seen FROM tickets_messages WHERE recipient = '0' ORDER BY seen ASC");
		$query->execute();
		$data = $query->fetchAll();
		$result = [];
		
		foreach ($data as $value) {
			$user = new User($value["owner"]);
			$profile = $user->getProfile();
			
			$result[] = [
				"first_name" => $profile["first_name"],
				"last_name" => $profile["last_name"],
				"phone" => trim($value["owner"]),
				"seen" => (int)$value["seen"]
			];
		}
		
		return $result;
	}
		
	public static function loadTicket(string $userPhone) : array {
		global $db;
		
		$query = $db->prepare("SELECT owner, content, timestamp FROM tickets_messages WHERE (owner = '0' AND recipient = :owner) OR (owner = :owner AND recipient = '0') ORDER BY timestamp DESC LIMIT 20");
		$query->bindValue(":owner", $userPhone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetchAll();
		$result = [];
		
		foreach ($data as $value) {
			$result[] = [
				"owner" => (string)trim($value["owner"]),
				"content" => (string)trim($value["content"]),
				"timestamp" => (int)$value["timestamp"]
			];
		}
		
		$query = $db->prepare("UPDATE tickets_messages SET seen = 1 WHERE (owner = '0' AND recipient = :owner) OR (owner = :owner AND recipient = '0')");
		$query->bindValue(":owner", $userPhone, PDO::PARAM_STR);
		$query->execute();
		
		return $result;
	}
	
	public static function replyToTicket(string $recipient, string $message) : bool {
		global $db;
		
		$query = $db->prepare("INSERT INTO tickets_messages(owner, recipient, content, timestamp) VALUES('0', :recipient, :content, ".time().")");
		$query->bindValue(":recipient", $recipient, PDO::PARAM_STR);
		$query->bindValue(":content", $message, PDO::PARAM_STR);
		
		return $query->execute();
	}
}