<?php
class Server {
	/**
	 * Constructeur
	 *
	 * @param int $id ID du serveur
	 * @param bool $sshAuth Se connecter ou non au serveur SSH
	 * @param bool $rconAuth Se connecter ou non au serveur RCON
	 */
	public function __construct(int $id, bool $sshAuth = false, bool $rconAuth = false) {
		$this->id = $id;
		if (!$this->exists()) {
			return false;
		}
		$this->ip = $this->getIp();
		
		if ($sshAuth) {
			$this->sshAuth();
		}
		
		if ($rconAuth) {
			$this->rconAuth();
		}
	}
	
	/**
	 * Se connecte au serveur SSH
	 */
	public function sshAuth() {
		$this->ssh = new phpseclib\Net\SSH2($this->ip);
		$key = new phpseclib\Crypt\RSA();
		$key->setPassword(file_get_contents("Auth/Password"));
		$key->loadKey(file_get_contents("Auth/Private.ppk"));
		if (!$this->ssh->login("user", $key)) {
			trigger_error("Erreur auth SSH");
			return false;
		}
		
		return true;
	}
	
	/**
	 * Se connecte au serveur RCON
	 */
	public function rconAuth() {
		$this->rcon = new Thedudeguy\Rcon($this->ip, 25575, $this->getRconPassword(), 3);
		$this->rcon->connect();
	}
	
	/**
	 * Change la version du serveur
	 *
	 * @param string $type Type de serveur
	 * @param string $version Version du serveur
	 * @param bool $zip Spécifie si la version est dans une archive zip ou non
	 */
	public function changeVersion(string $type, string $version, bool $zip = false) {
		global $config, $offers, $db;
		
		if ($this->isStarted()) {
			$this->stop();
		}
		
		$storageServer = $offers[$this->getServerType()]["price"] > 0 ? $config["storage"]["paying_server"] : $config["storage"]["free_server"];
		
		if (!$zip) {
			$this->ssh->exec("wget -O server.jar http://$storageServer/Minecraft/Versions/$type/$version.jar");
		} else {
			$this->ssh->exec("wget http://$storageServer/Minecraft/Versions/$type/$version.zip");
			$this->ssh->exec("unzip $version.zip");
			$this->ssh->exec("rm $version.zip");
		}
		
		$this->ssh->exec("echo eula=true >> eula.txt");
		
		$this->start();
		
		$query = $db->prepare("UPDATE servers SET version = :version WHERE id = :id");
		$query->bindValue(":version", $type."_".$version, PDO::PARAM_STR);
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
	}
	
	/**
	 * Démarre le serveur
	 */
	public function start() {
		global $offers;
		
		return $this->ssh->exec("screen -dmS minecraft java -Xms512M -Xmx".(1024*$offers[$this->getServerType()]["ram"])."M -jar server.jar");
	}
	
	/**
	 * Stoppe le serveur
	 */
	public function stop() {
		return $this->rcon->sendCommand("stop");
	}
	
	/**
	 * Kill le serveur
	 */
	public function forcedStop() {
		return $this->ssh->exec("pkill -9 java");
	}
	
	/**
	 * Vérifie si le serveur est lancé ou non
	 *
	 * @return bool Résultat
	 */
	public function isStarted() : bool {
		return strstr($this->ssh->exec("ps ax | grep java"), "java -Xms");
	}
	
	/**
	 * Génère un server.properties
	 *
	 * [...]
	 */
	public function saveServerProperties(
		bool $enableJmxMonitoring = false,
		int $rconPort = 25575,
		string $levelSeed = "",
		int $gamemode = 0,
		bool $enableCommandBlock = false,
		bool $enableQuery = false,
		string $generatorSettings = "",
		string $levelName = "world",
		string $motd = "A Minecraft Server",
		int $queryPort = 25565,
		bool $pvp = true,
		bool $generateStructures = true,
		string $difficulty = "easy",
		int $networkCompressionThreshold = 256,
		int $maxTickTime = 60000,
		bool $useNativeTransport = true,
		int $maxPlayers = 20,
		bool $onlineMode = true,
		bool $enableStatus = true,
		bool $allowFlight = false,
		bool $broadcastRconToOps = true,
		int $viewDistance = 10,
		int $maxBuildHeight = 256,
		string $serverIp = "",
		bool $allowNether = true,
		int $serverPort = 25565,
		bool $enableRcon = true,
		bool $syncChunkWrites = true,
		int $opPermissionLevel = 4,
		bool $preventProxyConnections = false,
		string $resourcePack = "",
		int $entityBroadcastRangePercentage = 100,
		string $rconPassword = "",
		int $playerIdleTimeout = 0,
		bool $forceGamemode = false,
		int $rateLimit = 0,
		bool $hardcore = false,
		bool $whiteList = false,
		bool $broadcastConsoleToOps = true,
		bool $spawnNpcs = true,
		bool $spawnAnimals = true,
		bool $snooperEnabled = true,
		int $functionPermissionLevel = 2,
		string $levelType = "default",
		bool $spawnMonsters = true,
		bool $enforceWhiteList = false,
		string $resourcePackSha1 = "",
		int $spawnProtection = 16,
		int $maxWorldSize = 29999984
	) {
		$this->ssh->exec("echo enable-jmx-monitoring=".($enableJmxMonitoring ? "true" : "false")." > server.properties");
		$this->ssh->exec("echo rcon.port=$rconPort >> server.properties");
		$this->ssh->exec("echo level-seed=".normalizeString($levelSeed, "abcdefghijklmnopqrstuvwxyz0123456789")." >> server.properties");
		$this->ssh->exec("echo gamemode=$gamemode >> server.properties");
		$this->ssh->exec("echo enable-command-block=".($enableCommandBlock ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo enable-query=".($enableQuery ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo generator-settings=".normalizeString($generatorSettings, "abcdefghijlmnopqrstuvwxyz0123456789:_()")." >> server.properties");
		$this->ssh->exec("echo level-name=".normalizeString($levelName, "abcdefghijklmnopqrstuvwxyz0123456789")." >> server.properties");
		$this->ssh->exec("echo motd=".normalizeString($motd, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789àâäéèêëïîôöùûüÿçÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ§&é\"'(-è_çà)^$*ù!:;,\/°+£¨µ%§.? ")." >> server.properties");
		$this->ssh->exec("echo query.port=$queryPort >> server.properties");
		$this->ssh->exec("echo pvp=".($pvp ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo generate-structures=".($generateStructures ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo difficulty=".normalizeString($difficulty)." >> server.properties");
		$this->ssh->exec("echo network-compression-threshold=$networkCompressionThreshold >> server.properties");
		$this->ssh->exec("echo max-tick-time=$maxTickTime >> server.properties");
		$this->ssh->exec("echo use-native-transport=".($useNativeTransport ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo max-players=$maxPlayers >> server.properties");
		$this->ssh->exec("echo online-mode=".($onlineMode ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo enable-status=".($enableStatus ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo allow-flight=".($allowFlight ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo broadcast-rcon-to-ops=".($broadcastRconToOps ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo view-distance=$viewDistance >> server.properties");
		$this->ssh->exec("echo max-build-height=$maxBuildHeight >> server.properties");
		$this->ssh->exec("echo server-ip=".normalizeString($serverIp, "0123456789.:")." >> server.properties");
		$this->ssh->exec("echo allow-nether=".($allowNether ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo server-port=$serverPort >> server.properties");
		$this->ssh->exec("echo enable-rcon=".($enableRcon ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo sync-chunk-writes=".($syncChunkWrites ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo op-permission-level=$opPermissionLevel >> server.properties");
		$this->ssh->exec("echo prevent-proxy-connections=".($preventProxyConnections ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo resource-pack=".normalizeString($resourcePack, "abcdefghijklmnopqrstuvwxyz:/.-_")." >> server.properties");
		$this->ssh->exec("echo entity-broadcast-range-percentage=$entityBroadcastRangePercentage >> server.properties");
		$this->ssh->exec("echo rcon.password=".normalizeString($rconPassword, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789")." >> server.properties");
		$this->ssh->exec("echo player-idle-timeout=$playerIdleTimeout >> server.properties");
		$this->ssh->exec("echo force-gamemode=".($forceGamemode ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo rate-limit=$rateLimit >> server.properties");
		$this->ssh->exec("echo hardcore=".($hardcore ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo white-list=".($whiteList ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo broadcast-console-to-ops=".($broadcastConsoleToOps ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo spawn-npcs=".($spawnNpcs ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo spawn-animals=".($spawnAnimals ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo snooper-enabled=".($snooperEnabled ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo function-permission-level=$functionPermissionLevel >> server.properties");
		$this->ssh->exec("echo level-type=".normalizeString($levelType)." >> server.properties");
		$this->ssh->exec("echo spawn-monsters=".($spawnMonsters ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo enforce-whitelist=".($enforceWhiteList ? "true" : "false")." >> server.properties");
		$this->ssh->exec("echo resource-pack-sha1=".normalizeString($resourcePackSha1, "abcdefghijklmnopqrstuvwxyz0123456789")." >> server.properties");
		$this->ssh->exec("echo spawn-protection=$spawnProtection >> server.properties");
		$this->ssh->exec("echo max-world-size=$maxWorldSize >> server.properties");
		
		return true;
	}
	
	/**
	 * Effectue un reset du serveur
	 */
	public function reset() {
		global $db, $config, $offers;
		
		$this->forcedStop();
		
		$this->ssh->exec("rm -R ~/*");
		$serverConfig = $this->getConfig();
		$newSshPassword = $this->resetSshPassword();
		
		$query = $db->prepare("DELETE FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		
		$newRconPassword = random(32);
		
		$isFree = $offers[$serverConfig["type"]]["price"] == 0;
		
		$mariadbHost = $isFree ? $config["mariadb"]["free_server"] : $config["mariadb"]["paying_server"];
		$mariadbPassword = random(32);
		$mariadb = new MariaDB($mariadbHost, "root", $isFree ? $config["mariadb"]["free_server_password"] : $config["mariadb"]["paying_server_password"]);
		$mariadb->deleteUser($serverConfig["ip"]);
		$mariadb->createUser($serverConfig["ip"], $mariadbPassword);
		
		$query = $db->prepare("INSERT INTO servers(ip, ssh_password, rcon_password, mysql_password, owner, type, expiration) VALUES(:ip, :ssh_password, :rcon_password, :mysql_password, '', :type, 0)");
		$query->bindValue(":ip", $serverConfig["ip"], PDO::PARAM_STR);
		$query->bindValue(":ssh_password", $newSshPassword, PDO::PARAM_STR);
		$query->bindValue(":rcon_password", $newRconPassword, PDO::PARAM_STR);
		$query->bindValue(":mysql_password", $mariadbPassword, PDO::PARAM_STR);
		$query->bindValue(":type", $serverConfig["type"], PDO::PARAM_INT);
		$query->execute();
		$this->id = $db->lastInsertId();
		
		$this->updateServerProperties($newRconPassword);
		$this->changeVersion($config["servers"]["default_type"], $config["servers"]["default_version"]);
	}
	
	/**
	 * Reset le password SSH du serveur
	 *
	 * @return string Nouveau mot de passe SSH
	 */
	public function resetSshPassword() {
		global $db;
		
		$currentPassword = $this->getSshPassword();
		$newSshPassword = random(32);
		$this->ssh->exec("echo -e \"$currentPassword\n$newSshPassword\n$newSshPassword\" | passwd user");
		
		$query = $db->prepare("UPDATE servers SET ssh_password = :ssh_password WHERE id = :id");
		$query->bindValue(":ssh_password", $newSshPassword, PDO::PARAM_STR);
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		
		return $newSshPassword;
	}
	
	/**
	 * Charge la console du serveur
	 *
	 * @return string Console
	 */
	public function loadConsole() : string {
		return $this->ssh->exec("tail -100 ~/logs/latest.log");
	}
	
	/**
	 * Récupère le type du serveur
	 *
	 * @return int Type du serveur
	 */
	public function getServerType() : int {
		global $db;
		
		$query = $db->prepare("SELECT type FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		
		return (int)$query->fetch()["type"];
	}
	
	/**
	 * Récupère le mot de passe RCON du serveur
	 *
	 * @return string Mot de passe RCON
	 */
	public function getRconPassword() : string {
		global $db;
		
		$query = $db->prepare("SELECT rcon_password FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		
		return (string)trim($query->fetch()["rcon_password"]);
	}
	
	/**
	 * Vérifie si le serveur existe
	 *
	 * @return bool Résultat
	 */
	public function exists() : bool {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] == 1;
	}
	
	/**
	 * Récupère l'adresse IP du serveur
	 *
	 * @return string Adresse IP du serveur
	 */
	public function getIp() : string {
		global $db;
		
		$query = $db->prepare("SELECT ip FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		return trim($data["ip"]);
	}
	
	/**
	 * Récupère le mot de passe SSH du serveur
	 */
	public function getSshPassword() : string {
		global $db;
		
		$query = $db->prepare("SELECT ssh_password FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		return (string)trim($data["ssh_password"]);
	}
	
	/**
	 * Charge la configuration du serveur
	 *
	 * @return array Résultat
	 */
	public function getConfig() : array {
		global $db;
		
		$query = $db->prepare("SELECT * FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		if (empty($data)) {
			return [];
		}
		
		$data = array_map("trim", $data);
		
		$result = [
			"ip" => (string)$data["ip"],
			"ssh_password" => (string)$data["ssh_password"],
			"rcon_password" => (string)$data["rcon_password"],
			"mysql_password" => (string)$data["mysql_password"],
			"owner" => (string)$data["owner"],
			"type" => (int)$data["type"],
			"expiration" => (int)$data["expiration"],
			"motd" => (string)$data["motd"],
			"max-players" => (int)$data["max-players"],
			"difficulty" => (string)$data["difficulty"],
			"level-name" => (string)$data["level-name"],
			"level-seed" => (string)$data["level-seed"],
			"level-type" => (string)$data["level-type"],
			"gamemode" => (int)$data["gamemode"],
			"white-list" => (bool)$data["white-list"],
			"online-mode" => (bool)$data["online-mode"],
			"generate-structures" => (bool)$data["generate-structures"],
			"enable-command-block" => (bool)$data["enable-command-block"],
			"allow-nether" => (bool)$data["allow-nether"],
			"pvp" => (bool)$data["pvp"],
			"spawn-npcs" => (bool)$data["spawn-npcs"],
			"spawn-monsters" => (bool)$data["spawn-monsters"],
			"spawn-animals" => (bool)$data["spawn-animals"],
			"hardcore" => (bool)$data["hardcore"],
			"version" => (string)$data["version"],
			"expiration_warning" => (int)$data["expiration_warning"]
		];
		
		return $result;
	}
	
	/**
	 * Met à jour le server.properties en base de données
	 *
	 * [...]
	 */
	public function updateServerProperties(string $rconPassword, string $motd = "A Minecraft Server", int $maxPlayers = 20, string $difficulty = "normal", string $levelName = "world", string $levelSeed = "", string $levelType = "default", int $gamemode = 0, int $whiteList = 0, int $onlineMode = 1, int $generateStructures = 1, int $enableCommandBlock = 0, int $allowNether = 1, int $pvp = 1, int $spawnNpcs = 1, int $spawnMonsters = 1, $spawnAnimals = 1, int $hardcore = 0) {
		global $db;
		
		$query = $db->prepare("UPDATE servers SET rcon_password = :rconPassword, motd = :motd, \"max-players\" = :maxPlayers, difficulty = :difficulty, \"level-name\" = :levelName, \"level-seed\" = :levelSeed, \"level-type\" = :levelType, gamemode = :gamemode, \"white-list\" = :whiteList, \"online-mode\" = :onlineMode, \"generate-structures\" = :generateStructures, \"enable-command-block\" = :enableCommandBlock, \"allow-nether\" = :allowNether, pvp = :pvp, \"spawn-npcs\" = :spawnNpcs, \"spawn-monsters\" = :spawnMonsters, \"spawn-animals\" = :spawnAnimals, hardcore = :hardcore WHERE id = :id");
		$query->bindValue(":rconPassword", $rconPassword, PDO::PARAM_STR);
		$query->bindValue(":motd", $motd, PDO::PARAM_STR);
		$query->bindValue(":maxPlayers", $maxPlayers, PDO::PARAM_INT);
		$query->bindValue(":difficulty", $difficulty, PDO::PARAM_STR);
		$query->bindValue(":levelName", $levelName, PDO::PARAM_STR);
		$query->bindValue(":levelSeed", $levelSeed, PDO::PARAM_STR);
		$query->bindValue(":levelType", $levelType, PDO::PARAM_STR);
		$query->bindValue(":gamemode", $gamemode, PDO::PARAM_INT);
		$query->bindValue(":whiteList", $whiteList, PDO::PARAM_INT);
		$query->bindValue(":onlineMode", $onlineMode, PDO::PARAM_INT);
		$query->bindValue(":generateStructures", $generateStructures, PDO::PARAM_INT);
		$query->bindValue(":enableCommandBlock", $enableCommandBlock, PDO::PARAM_INT);
		$query->bindValue(":allowNether", $allowNether, PDO::PARAM_INT);
		$query->bindValue(":pvp", $pvp, PDO::PARAM_INT);
		$query->bindValue(":spawnNpcs", $spawnNpcs, PDO::PARAM_INT);
		$query->bindValue(":spawnMonsters", $spawnMonsters, PDO::PARAM_INT);
		$query->bindValue(":spawnAnimals", $spawnAnimals, PDO::PARAM_INT);
		$query->bindValue(":hardcore", $hardcore, PDO::PARAM_INT);
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		
		$this->saveServerProperties(false, 25575, $levelSeed, $gamemode, $enableCommandBlock, false, "", $levelName, $motd, 25565, $pvp, $generateStructures, $difficulty, 256, 60000, true, $maxPlayers, $onlineMode, true, false, true, 10, 256, "", $allowNether, 25565, true, true, 4, false, "", 100, $rconPassword, 0, false, 0, $hardcore, $whiteList, true, $spawnNpcs, $spawnAnimals, true, 2, $levelType, $spawnMonsters, false, "", 16, 29999984);
	}
	
	/**
	 * Crée un serveur
	 *
	 * @param int $type Type de serveur
	 * @param string $owner Numéro de téléphone du détenteur
	 *
	 * @return int ID du serveur
	 */
	public static function create(int $type, string $owner) : int {
		global $db;
		
		$query = $db->prepare("SELECT id, ip FROM servers WHERE type = :type AND expiration = 0 ORDER BY id ASC");
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
	
	/**
	 * Vérifie si il reste des serveurs disponibles selon un type spécifique
	 *
	 * @param int $type Type de serveur
	 *
	 * @return bool Résultat
	 */
	public static function isAvailable(int $type) : bool {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE type = :type AND expiration = 0");
		$query->bindValue(":type", $type, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] >= 1;
	}
	
	/**
	 * Récupère la liste des serveurs proches de l'expiration
	 *
	 * @return array Résultat
	 */
	public static function getServersNearExpiration() : array {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE expiration-".time()." < 259200 AND expiration != 0");
		$query->execute();
		$data = $query->fetch();
		if ($data["nb"] == 0) {
			return [];
		}
		
		$query = $db->prepare("SELECT id FROM servers WHERE expiration-".time()." < 259200 AND expiration != 0");
		$query->execute();
		$data = $query->fetchAll();
		$result = [];
		
		foreach ($data as $value) {
			$result[] = (int)$value["id"];
		}
		
		return $result;
	}
	
	/**
	 * Récupère la liste des serveurs expirés
	 *
	 * @return array Résultat
	 */
	public static function getExpiredServers() : array {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE expiration-".time()." < 0 AND expiration != 0");
		$query->execute();
		$data = $query->fetch();
		if ($data["nb"] == 0) {
			return [];
		}
		
		$query = $db->prepare("SELECT id FROM servers WHERE expiration-".time()." < 0 AND expiration != 0");
		$query->execute();
		$data = $query->fetchAll();
		$result = [];
		
		foreach ($data as $value) {
			$result[] = (int)$value["id"];
		}
		
		return $result;
	}
	
	/**
	 * Définit le serveur comme ayant été signalé comme proche de l'expiration
	 *
	 * @return bool Résultat
	 */
	public function setExpirationWarning() : bool {
		global $db;
		
		$query = $db->prepare("UPDATE servers SET expiration_warning = 1 WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		
		return $query->execute();
	}
	
	/**
	 * Renouvelle le serveur
	 *
	 * @return bool Résultat
	 */
	public function renew() : bool {
		global $db;
		
		$query = $db->prepare("SELECT expiration FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		$query = $db->prepare("UPDATE servers SET expiration = :expiration WHERE id = :id");
		$query->bindValue(":expiration", strtotime("+1 month", $data["expiration"]), PDO::PARAM_INT);
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		
		return $query->execute();
	}
	
	/**
	 * Récupère la liste des serveurs non initialisés
	 *
	 * @return array Résultat
	 */
	public static function getUninitializedServersList() : array {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE expiration = -1");
		$query->execute();
		$data = $query->fetch();
		if ($data["nb"] == 0) {
			return [];
		}
		
		$query = $db->prepare("SELECT id FROM servers WHERE expiration = -1 ORDER BY id ASC");
		$query->execute();
		$data = $query->fetchAll();
		$result = [];
		
		foreach ($data as $value) {
			$result[] = (int)$value["id"];
		}
		
		return $result;
	}
	
	/**
	 * Recherche des plugins selon un texte défini
	 *
	 * @param string $text Texte à rechercher
	 *
	 * @return array Résultat
	 */
	public function searchPlugins(string $text) : array {
		global $db;
		
		$text = str_replace("%", "", $text);
		if (empty($text)) {
			return [];
		}
		
		$query = $db->prepare("SELECT id, jar_name, name, description, versions FROM plugins WHERE name ILIKE :text ORDER BY id ASC LIMIT 100");
		$query->bindValue(":text", "%$text%", PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetchAll();
		$result = [];
		
		foreach ($data as $value) {
			$value = array_map("trim", $value);
			
			$result[] = [
				"id" => (int)$value["id"],
				"name" => (string)$value["name"],
				"description" => (string)$value["description"],
				"versions" => (string)$value["versions"]
			];
		}
		
		return $result;
	}
	
	/**
	 * Modifie le mot de passe MySQL associé au serveur
	 *
	 * @return bool Résultat
	 */
	public function changeMysqlPassword() : bool {
		global $db, $config, $offers;
		
		$serverConfig = $this->getConfig();
		
		$isFree = $offers[$serverConfig["type"]]["price"] == 0;
		$mariadbHost = $isFree ? $config["mariadb"]["free_server"] : $config["mariadb"]["paying_server"];
		$mariadbPassword = random(32);
		$mariadb = new MariaDB($mariadbHost, "root", $isFree ? $config["mariadb"]["free_server_password"] : $config["mariadb"]["paying_server_password"]);
		$mariadb->changePassword($serverConfig["ip"], $mariadbPassword);
		
		$query = $db->prepare("UPDATE servers SET mysql_password = :mysql_password WHERE id = :id");
		$query->bindValue(":mysql_password", $mariadbPassword, PDO::PARAM_STR);
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		
		return $query->execute();
	}
	
	/**
	 * Vérifie si un plugin existe
	 *
	 * @param int $id ID du plugin
	 *
	 * @return bool Résultat
	 */
	public function pluginExists(int $id) : bool {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM plugins WHERE id = :id");
		$query->bindValue(":id", $id, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] == 1;
	}
	
	/**
	 * Charge les données à propos d'un plugin
	 *
	 * @param int $id ID du plugin
	 *
	 * @return array Résultat
	 */
	public function getPluginData(int $id) : array {
		global $db;
		
		$query = $db->prepare("SELECT name, description, versions FROM plugins WHERE id = :id");
		$query->bindValue(":id", $id, PDO::PARAM_INT);
		$query->execute();
		$data = array_map("trim", $query->fetch());
		
		$result = [
			"name" => (string)$data["name"],
			"description" => (string)$data["description"],
			"versions" => (string)$data["versions"]
		];
		
		return $result;
	}
	
	/**
	 * Installe un plugin
	 *
	 * @param int $id ID du plugin
	 *
	 * @return bool Résultat
	 */
	public function installPlugin(int $id) : bool {
		global $db, $offers, $config;
		
		$storageServer = $offers[$this->getServerType()]["price"] > 0 ? $config["storage"]["paying_server"] : $config["storage"]["free_server"];
		
		$query = $db->prepare("SELECT jar_name, zip FROM plugins WHERE id = :id");
		$query->bindValue(":id", $id, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		$jarName = trim($data["jar_name"]);
		
		if (!$data["zip"]) {
			$this->ssh->exec("wget -O plugins/$jarName.jar http://$storageServer/Plugins/$jarName.jar");
		} else {
			$this->ssh->exec("wget -O plugins/$jarName.zip http://$storageServer/Plugins/$jarName.zip");
			$this->ssh->exec("unzip plugins/$jarName.zip -d plugins/");
			$this->ssh->exec("rm plugins/$jarName.zip");
		}
		
		return true;
	}
	
	/**
	 * Vérifie si le système d'installation de plugins est disponible
	 *
	 * @return bool Résultat
	 */
	 public static function isPluginsAutoInstallAvailable() : bool {
		 global $db;
		 
		 $query = $db->prepare("SELECT COUNT(*) AS nb FROM plugins");
		 $query->execute();
		 $data = $query->fetch();
		 
		 return $data["nb"] > 0;
	 }
}