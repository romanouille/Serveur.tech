<?php
class MariaDB {
	/**
	 * Constructeur
	 *
	 * @param string $server Serveur cible
	 * @param string $username Nom d'utilisateur
	 * @param string $password Mot de passe
	 */
	public function __construct(string $server, string $username, string $password) {
		$this->server = $server;
		$this->db = new mysqli($server, $username, $password);
	}
	
	/**
	 * Crée un utilisateur
	 *
	 * @param string $username Nom d'utilisateur
	 * @param string $password Mot de passe
	 */
	public function createUser(string $username, string $password) {
		$this->db->query("CREATE USER '$username'@'%' IDENTIFIED BY '$password';");
		$this->db->query("GRANT USAGE ON *.* TO '$username'@'%' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;");
		$this->db->query("CREATE DATABASE IF NOT EXISTS `$username`;");
		$this->db->query("GRANT ALL PRIVILEGES ON `$username`.* TO '$username'@'%';");
	}
	
	/**
	 * Supprime un utilisateur
	 *
	 * @param string $username Nom d'utilisateur
	 */
	public function deleteUser(string $username) {
		$this->db->query("DROP USER '$username'@'%';");
		$this->db->query("DROP DATABASE IF EXISTS `$username`;");
	}
	
	/**
	 * Modifie le mot de passe d'un utilisateur
	 *
	 * @param string $username Nom d'utilisateur
	 * @param string $password Nouveau mot de passe
	 */
	public function changePassword(string $username, string $password) {
		$this->db->query("ALTER USER '$username'@'%' IDENTIFIED BY '$password'");
	}
}