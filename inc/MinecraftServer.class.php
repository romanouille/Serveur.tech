<?php
class MinecraftServer {
	public function __construct(string $ip, string $rconPassword = "", bool $sshAuth = false, $storageServer = "") {
		$this->ip = $ip;
		
		if (!empty($rconPassword)) {
			$this->rcon = new Thedudeguy\Rcon($ip, 25575, $rconPassword, 10);
			$this->rcon->connect();
		}
		
		if ($sshAuth) {
			$this->sshAuth();
		}
	}
	
	private function sshAuth() {
		$this->ssh = new phpseclib\Net\SSH2($this->ip);
		$key = new phpseclib\Crypt\RSA();
		$key->setPassword(file_get_contents("Auth/Password"));
		$key->loadKey(file_get_contents("Auth/Private.ppk"));
		if (!$this->ssh->login("user", $key)) {
			return false;
		}
		
		return true;
	}
	
	public function changeVersion(string $type, string $version) {
		$this->ssh->exec("wget -O server.jar http://{$this->storageServer}/Minecraft/$type/$version.jar");
		$this->ssh->exec("echo eula=true >> eula.txt");
		
		$this->start();
	}
	
	public function start() {
		$this->ssh->exec("screen -dmS minecraft java -Xms512M -Xmx4096M -jar server.jar");
	}
	
	public function stop() {
		return $this->rcon->sendCommand("stop");
	}
	
	public function updateServerProperties(
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
}