<?php
class Client {
	private $resource;
	public $source, $selfId, $destinationIp, $destinationPort;
	
	/**
	 * Constructeur
	 *
	 * @param Resource $resource Ressource associée à un client connecté
	 */
	public function __construct($resource) {
		$this->resource = $resource;
		stream_set_blocking($this->resource, false);
	}
	
	/**
	 * Ajoute une entrée dans les logs
	 *
	 * @param string $text Chaîne à ajouter
	 */
	private function logs(string $text) {
		echo date("[H:i:s] ")."({$this->source}) $text\n";
	}
	
	/**
	 * Kill le client
	 */
	private function kill() {
		global $server;
		
		@fclose($this->resource);
		unset($server->clients[$this->selfId]);
		
		return true;
	}
	
	/**
	 * Vérifie si des données ont été envoyées par le client, puis les traîte si c'est le cas
	 */
	public function update() {
		global $db;

		$metadata = stream_get_meta_data($this->resource);
		if ($metadata["eof"]) {
			$this->logs("Client is gone (connection reset by peer)");
			$this->kill();
			return false;
		}
		
		$this->data = fread($this->resource, 1024);
		
		if (!empty($this->data)) {
			$this->logs("New message : {$this->data}");
			$this->kill();

			$source = explode(":", $this->source);

			$query = $db->prepare("INSERT INTO honeypot_reports(source_ip, source_port, destination_port, data, timestamp) VALUES(:source_ip, :source_port, :destination_port, :data, :timestamp)");
			$query->bindValue(":source_ip", $source[0], PDO::PARAM_STR);
			$query->bindValue(":source_port", $source[1], PDO::PARAM_INT);
			$query->bindValue(":destination_port", $this->destinationPort, PDO::PARAM_INT);
			$query->bindValue(":data", $this->data, PDO::PARAM_STR);
			$query->bindValue(":timestamp", time(), PDO::PARAM_INT);
			$query->execute();
		}
	}
}