<?php
class MariaDB {
	public function __construct(string $server, string $username, string $password) {
		$this->server = $server;
		$this->db = new mysqli($server, $username, $password);
	}
	
	public function createUser(string $username, string $password) {
		$this->db->query("CREATE USER '$username'@'%' IDENTIFIED BY '$password';");
		$this->db->query("GRANT USAGE ON *.* TO '$username'@'%' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;");
		$this->db->query("CREATE DATABASE IF NOT EXISTS `$username`;");
		$this->db->query("GRANT ALL PRIVILEGES ON `$username`.* TO '$username'@'%';");
	}
	
	public function deleteUser(string $username) {
		$this->db->query("DROP USER '$username'@'%';");
		$this->db->query("DROP DATABASE IF EXISTS `$username`;");
	}
	
	public function changePassword(string $username, string $password) {
		$this->db->query("ALTER USER '$username'@'%' IDENTIFIED BY '$password'");
	}
}