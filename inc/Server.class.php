<?php
class Server {
	public function __construct(int $id) {
		$this->id = $id;
	}
	
	public function exists() : bool {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE id = :id");
		$query->bindValue(":id", $this->id, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] == 1;
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
			"password" => (string)$data["password"],
			"owner" => (int)$data["owner"],
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
			"version" => (string)$data["version"]
		];
		
		return $result;
	}
	
	public function updateServerProperties(string $motd, int $maxPlayers, string $difficulty, string $levelName, string $levelSeed, string $levelType, int $gamemode, int $whiteList, int $onlineMode, int $generateStructures, int $enableCommandBlock, int $allowNether, int $pvp, int $spawnNpcs, int $spawnMonsters, $spawnAnimals, int $hardcore) {
		global $db;
		
		$query = $db->prepare("UPDATE servers SET motd = :motd, \"max-players\" = :maxPlayers, difficulty = :difficulty, \"level-name\" = :levelName, \"level-seed\" = :levelSeed, \"level-type\" = :levelType, gamemode = :gamemode, \"white-list\" = :whiteList, \"online-mode\" = :onlineMode, \"generate-structures\" = :generateStructures, \"enable-command-block\" = :enableCommandBlock, \"allow-nether\" = :allowNether, pvp = :pvp, \"spawn-npcs\" = :spawnNpcs, \"spawn-monsters\" = :spawnMonsters, \"spawn-animals\" = :spawnAnimals, hardcore = :hardcore WHERE id = :id");
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
		
		$config = $this->getConfig();
		
		$server = new MinecraftServer($config["ip"], "", true);
		$server->updateServerProperties(false, 25575, $levelSeed, $gamemode, $enableCommandBlock, false, "", $levelName, $motd, 25565, $pvp, $generateStructures, $difficulty, 256, 60000, true, $maxPlayers, $onlineMode, true, false, true, 10, 256, "", $allowNether, 25565, true, true, 4, false, "", 100, "RCON12345", 0, false, 0, $hardcore, $whiteList, true, $spawnNpcs, $spawnAnimals, true, 2, $levelType, $spawnMonsters, false, "", 16, 29999984);
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
}