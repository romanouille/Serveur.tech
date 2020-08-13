<?php
class Server {
	public $ip, $port, $certLocation, $server, $clients = [];
	
	/**
	 * Constructeur
	 *
	 * @param string $ip Adresse d'écoute
	 * @param int $port Port TCP d'écoute
	 */
	public function __construct(string $ip, int $port) {
		$this->ip = $ip;
		$this->port = $port;
	}
	
	/**
	 * Lance le serveur
	 */
	public function start() {
		$this->server = @stream_socket_server("{$this->ip}:{$this->port}", $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN);
		if (!empty($this->server)) {
			//$this->logs("Server started on {$this->ip}:{$this->port}");
			return true;
		} else {
			$this->logs("Unable to bind to {$this->ip}:{$this->port}");
			return false;
		}
	}
	
	/**
	 * Ajoute une entrée dans les logs
	 *
	 * @param string $text Chaîne à ajouter
	 */
	private function logs(string $text) {
		echo date("[H:i:s] ")."$text\n";
	}
	
	/**
	 * Vérifie si un nouveau client essaye de se connecter
	 */
	public function checkForNewClient() {
		global $db;

		$client = @stream_socket_accept($this->server, 0);
		
		if (!empty($client)) {
			$this->clients[] = new Client($client);
			$clientId = array_key_last($this->clients);
			
			$this->clients[$clientId]->source = stream_socket_get_name($client, true);
			$this->clients[$clientId]->selfId = $clientId;
			$this->clients[$clientId]->destinationIp = $this->ip;
			$this->clients[$clientId]->destinationPort = $this->port;
			
			$this->logs("New client : {$this->clients[$clientId]->source}");
			$source = explode(":", $this->clients[$clientId]->source);

			$query = $db->prepare("INSERT INTO honeypot_connections(source_ip, source_port, destination_port, timestamp) VALUES(:source_ip, :source_port, :destination_port, :timestamp)");
			$query->bindValue(":source_ip", $source[0], PDO::PARAM_STR);
			$query->bindValue(":source_port", $source[1], PDO::PARAM_INT);
			$query->bindValue(":destination_port", $this->port, PDO::PARAM_INT);
			$query->bindValue(":timestamp", time(), PDO::PARAM_INT);
			$query->execute();
			
			return $this->clients[$clientId]->source;
		}

		return "";
	}
}