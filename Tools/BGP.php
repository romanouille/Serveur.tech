<?php
ini_set("memory_limit", -1);
$server = "45.63.115.203";
$lastVersion = file_get_contents("http://$server/last_version.txt");
$tempDir = sys_get_temp_dir();

require "inc/Init.php";

$routes = [];
$blacklist = ["0.0.0.0/0", "::/0"];

for ($i = 1; $i <= 2; $i++) {
	$ipVersion = $i == 1 ? 4 : 6;
	
	download("http://$server/routes$ipVersion-$lastVersion.zip", "$tempDir/routes$ipVersion.zip");
	
	$zip = new ZipArchive;
	$zip->open("$tempDir/routes$ipVersion.zip");
	$zip->extractTo("$tempDir/");
	$zip->close();
	unlink("$tempDir/routes$ipVersion.zip");
	
	$file = explode("\n", file_get_contents("$tempDir/-"));
	unlink("$tempDir/-");
	
	logs("Parsage du fichier");
	
	foreach ($file as $line) {
		for ($y = 1; $y <= 5; $y++) {
			$line = str_replace("  ", " ", $line);
		}
		
		$line = explode(" ", $line);
		
		if (count($line) != 9 || in_array($line[0], $blacklist)) {
			continue;
		}
		
		$as = str_replace("AS", "", str_replace("[", "", str_replace("]", "", end($line))));
		if (!is_numeric($as[strlen($as)-1])) {
			$as = str_split($as);
			unset($as[count($as)-1]);
			$as = implode("", $as);
		}
		
		$as = (int)$as;
		
		$ipVersion = strstr($line[0], ":") ? 6 : 4;
		
		if ($ipVersion == 4) {
			$blockData = ReseauIo::cidrToRange($line[0]);
			$blockStart = $blockData[0];
			$blockEnd = $blockData[1];
		} else {
			$block = explode("/", $line[0]);
			$blockStart = $block[0];
			if ($block[1] != 128) {
				$blockEnd = ReseauIo::long2ipv2(gmp_strval(gmp_sub(gmp_strval(gmp_add(ReseauIo::ip2longv2($blockStart), ReseauIo::cidrToHostsV6($block[1]))), 1)));
			} else {
				$blockEnd = $blockStart;
			}
		}
		
		
		$routes[$line[0]] = [
			"blockStart" => $blockStart,
			"blockEnd" => $blockEnd,
			"as" => $as,
			"timestamp" => strtotime($line[3])
		];
	}
	
	unset($file);
	logs("----------");
}


$currentRoutes = [];

logs("Récupération des routes actuelles");

$query = $db->prepare("SELECT block, origin FROM bgp_routes");
$query->execute();
$data = $query->fetchAll();

foreach ($data as $value) {
	$currentRoutes[$value["block"]] = (int)$value["origin"];
}

$timestamp = time();

if (empty($currentRoutes)) {
	logs("La table BGP est vide");
	logs("Ajout de toutes les routes BGP");
	
	/*$db->exec("ALTER SEQUENCE bgp_events_id_seq RESTART WITH 1");*/
	$db->exec("ALTER SEQUENCE bgp_routes_id_seq RESTART WITH 1");
	
	foreach ($routes as $block=>$data) {
		$query = $db->prepare("INSERT INTO bgp_routes(version, block, block_start, block_end, origin, timestamp) VALUES(:version, :block, :block_start, :block_end, :origin, $timestamp)");
		$query->bindValue(":version", strstr($block, ":") ? 6 : 4, PDO::PARAM_INT);
		$query->bindValue(":block", $block, PDO::PARAM_STR);
		$query->bindValue(":block_start", $data["blockStart"], PDO::PARAM_STR);
		$query->bindValue(":block_end", $data["blockEnd"], PDO::PARAM_STR);
		$query->bindValue(":origin", $data["as"], PDO::PARAM_INT);
		$query->execute();
	}
	
	exit;
}



$newRoutes = [];

foreach ($routes as $block=>$data) {
	if (!isset($currentRoutes[$block])) {
		logs("Nouvelle route : $block -> {$data["as"]}");
		
		$query = $db->prepare("INSERT INTO bgp_routes(version, block, block_start, block_end, origin, timestamp) VALUES(:version, :block, :block_start, :block_end, :origin, :timestamp)");
		$query->bindValue(":version", strstr($block, ":") ? 6 : 4, PDO::PARAM_INT);
		$query->bindValue(":block", $block, PDO::PARAM_STR);
		$query->bindValue(":block_start", $data["blockStart"], PDO::PARAM_STR);
		$query->bindValue(":block_end", $data["blockEnd"], PDO::PARAM_STR);
		$query->bindValue(":origin", $data["as"], PDO::PARAM_INT);
		$query->bindValue(":timestamp", $data["timestamp"], PDO::PARAM_INT);
		$query->execute();
		
		$query = $db->prepare("INSERT INTO bgp_events(type, version, block, block_start, block_end, timestamp) VALUES(0, :version, :block, :block_start, :block_end, :timestamp)");
		$query->bindValue(":version", strstr($block, ":") ? 6 : 4, PDO::PARAM_INT);
		$query->bindValue(":block", $block, PDO::PARAM_STR);
		$query->bindValue(":block_start", $data["blockStart"], PDO::PARAM_STR);
		$query->bindValue(":block_end", $data["blockEnd"], PDO::PARAM_STR);
		$query->bindValue(":timestamp", $data["timestamp"], PDO::PARAM_INT);
		$query->execute();
	} elseif ($currentRoutes[$block] != $data["as"]) {
		logs("Nouvelle origine pour $block : {$data["as"]}");
		
		$query = $db->prepare("UPDATE bgp_routes SET origin = :origin, timestamp = $timestamp WHERE block = :block");
		$query->bindValue(":origin", $data["as"], PDO::PARAM_INT);
		$query->bindValue(":block", $block, PDO::PARAM_STR);
		$query->execute();
		
		$query = $db->prepare("INSERT INTO bgp_events(type, version, block, block_start, block_end, before, after, timestamp) VALUES(1, :version, :block, :block_start, :block_end, :before, :after, :timestamp)");
		$query->bindValue(":version", strstr($block, ":") ? 6 : 4, PDO::PARAM_INT);
		$query->bindValue(":block", $block, PDO::PARAM_STR);
		$query->bindValue(":block_start", $data["blockStart"], PDO::PARAM_STR);
		$query->bindValue(":block_end", $data["blockEnd"], PDO::PARAM_STR);
		$query->bindValue(":before", $currentRoutes[$block], PDO::PARAM_INT);
		$query->bindValue(":after", $data["as"], PDO::PARAM_INT);
		$query->bindValue(":timestamp", $data["timestamp"], PDO::PARAM_INT);
		$query->execute();
	}
}


foreach ($currentRoutes as $block=>$as) {
	if (!isset($routes[$block])) {
		logs("Route perdue : $block");
		
		$query = $db->prepare("SELECT block_start, block_end FROM bgp_routes WHERE block = :block");
		$query->bindValue(":block", $block, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		$query = $db->prepare("DELETE FROM bgp_routes WHERE block = :block");
		$query->bindValue(":block", $block, PDO::PARAM_STR);
		$query->execute();
		
		$query = $db->prepare("INSERT INTO bgp_events(type, version, block, block_start, block_end, timestamp) VALUES(2, :version, :block, :block_start, :block_end, $timestamp)");
		$query->bindValue(":version", strstr($block, ":") ? 6 : 4, PDO::PARAM_INT);
		$query->bindValue(":block", $block, PDO::PARAM_STR);
		$query->bindValue(":block_start", $data["block_start"], PDO::PARAM_STR);
		$query->bindValue(":block_end", $data["block_end"], PDO::PARAM_STR);
		$query->execute();
	}
}