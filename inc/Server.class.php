<?php
class Server {
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
	
	public function sshAuth() {
		$this->ssh = new phpseclib\Net\SSH2($this->ip);
		$key = new phpseclib\Crypt\RSA();
		$key->setPassword(file_get_contents("Auth/Password"));
		$key->loadKey(file_get_contents("Auth/Private.ppk"));
		if (!$this->ssh->login("user", $key)) {
			return false;
		}
		
		return true;
	}
	
	public function rconAuth() {
		$this->rcon = new Thedudeguy\Rcon($this->ip, 25575, $this->getRconPassword(), 3);
		$this->rcon->connect();
	}
	
	public function changeVersion(string $type, string $version, bool $zip = false) {
		global $config, $offers, $db;
		
		if ($this->isStarted()) {
			$this->stop();
		}
		
		$storageServer = $offers[$this->getServerType()]["price"] > 0 ? $config["storage"]["paying_servers"] : $config["storage"]["free_servers"];
		
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
	
	public function start() {
		global $offers;
		
		$this->ssh->exec("screen -dmS minecraft java -Xms512M -Xmx".(1024*$offers[$this->getServerType()]["ram"])."M -jar server.jar");
	}
	
	public function stop() {
		return $this->rcon->sendCommand("stop");
	}
	
	public function isStarted() : bool {
		return strstr($this->ssh->exec("ps ax | grep java"), "java -Xms");
	}
	
	public function saveServerProperties(
		bool $enableJmxMonitoring = false,
		int $rconPort = 25575,
		string $levelSeed = "",
		string $gamemode = "survival",
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
		$this->ssh->exec("echo gamemode=".normalizeString($gamemode)." >> server.properties");
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
	
	public function reset() {
		global $db;
		
		if ($this->isStarted()) {
			$this->stop();
		}
		
		$this->ssh->exec("rm -R ~/*");
		$config = $this->getConfig();
		$newSshPassword = $this->resetSshPassword();
		
		$query = $db->prepare("DELETE FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		
		$newRconPassword = random(32);
		
		$query = $db->prepare("INSERT INTO servers(ip, ssh_password, rcon_password, owner, type, expiration) VALUES(:ip, :ssh_password, :rcon_password, '', :type, 0)");
		$query->bindValue(":ip", $config["ip"], PDO::PARAM_STR);
		$query->bindValue(":ssh_password", $newSshPassword, PDO::PARAM_STR);
		$query->bindValue(":rcon_password", $newRconPassword, PDO::PARAM_STR);
		$query->bindValue(":type", $config["type"], PDO::PARAM_INT);
		$query->execute();
		$this->id = $db->lastInsertId();
		
		$this->updateServerProperties($newRconPassword);
		$this->changeVersion("Spigot", "1.16.3");
	}
	
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
	
	public function loadConsole() : string {
		return $this->ssh->exec("tail -100 ~/logs/latest.log");
	}
	
	public function getServerType() : int {
		global $db;
		
		$query = $db->prepare("SELECT type FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		
		return (int)$query->fetch()["type"];
	}
	
	public function getRconPassword() : string {
		global $db;
		
		$query = $db->prepare("SELECT rcon_password FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		
		return (string)$query->fetch()["rcon_password"];
	}
	
	public function exists() : bool {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] == 1;
	}
	
	public function getIp() : string {
		global $db;
		
		$query = $db->prepare("SELECT ip FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		return trim($data["ip"]);
	}
	
	public function getSshPassword() : string {
		global $db;
		
		$query = $db->prepare("SELECT ssh_password FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		return (string)trim($data["ssh_password"]);
	}
	
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
			"owner" => (string)$data["owner"],
			"type" => (int)$data["type"],
			"expiration" => (int)$data["expiration"],
			"motd" => (string)$data["motd"],
			"max-players" => (int)$data["max-players"],
			"difficulty" => (string)$data["difficulty"],
			"level-name" => (string)$data["level-name"],
			"level-seed" => (string)$data["level-seed"],
			"level-type" => (string)$data["level-type"],
			"gamemode" => (bool)$data["gamemode"],
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
	
	public static function getServersNearExpiration() : array {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE expiration-".time()." < 86400 AND expiration != 0");
		$query->execute();
		$data = $query->fetch();
		if ($data["nb"] == 0) {
			return [];
		}
		
		$query = $db->prepare("SELECT id FROM servers WHERE expiration-".time()." < 86400 AND expiration != 0");
		$query->execute();
		$data = $query->fetchAll();
		$result = [];
		
		foreach ($data as $value) {
			$result[] = (int)$value["id"];
		}
		
		return $result;
	}
	
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
	
	public function setExpirationWarning() : bool {
		global $db;
		
		$query = $db->prepare("UPDATE servers SET expiration_warning = 1 WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		
		return $query->execute();
	}
	
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
	
	public static function getUninitializedServersList() {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE expiration = -1");
		$query->execute();
		$data = $query->fetch();
		if ($data["nb"] == 0) {
			return [];
		}
		
		$query = $db->prepare("SELECT id FROM servers WHERE expiration = -1");
		$query->execute();
		$data = $query->fetchAll();
		$result = [];
		
		foreach ($data as $value) {
			$result[] = (int)$value["id"];
		}
		
		return $result;
	}
	
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
			$result[] = [
				"id" => (int)$value["id"],
				"jar_name" => (string)$value["jar_name"],
				"name" => (string)$value["name"],
				"description" => (string)$value["description"],
				"versions" => (string)$value["versions"]
			];
		}
		
		return $result;
	}
}