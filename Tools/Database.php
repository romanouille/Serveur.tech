<?php
ini_set("memory_limit", "128M");
$dev = false;

set_include_path("../");
chdir("../");

if (!file_exists("Core/table")) {
	file_put_contents("Core/table", 1);
}

$tempDir = sys_get_temp_dir();

require "Core/Functions.php";
require "Core/Init.php";
require "Core/ReseauIo.class.php";
require "Core/Cache.class.php";

$oldTable = (int)Cache::read("dump_id");
$table = $oldTable == 0 ? 1 : 0;

logs("Old table : $oldTable");
logs("New table : $table");

$rirList = [
	"afrinic",
	"apnic",
	"arin",
	"lacnic",
	"ripencc"
];
$states = [
	"allocated",
	"assigned",
	/*"reserved",
	"available"*/
];


$db->exec("ALTER SEQUENCE dump_as_{$table}_id_seq RESTART WITH 1");
$db->exec("ALTER SEQUENCE dump_blocks_{$table}_id_seq RESTART WITH 1");
$db->exec("ALTER SEQUENCE dump_lir_{$table}_id_seq RESTART WITH 1");
$db->exec("ALTER SEQUENCE dump_ripe_allocations_{$table}_id_seq RESTART WITH 1");
$db->exec("ALTER SEQUENCE dump_ripe_as_{$table}_id_seq RESTART WITH 1");
$db->exec("ALTER SEQUENCE dump_ripe_as_peers_{$table}_id_seq RESTART WITH 1");
$db->exec("ALTER SEQUENCE dump_ripe_organisations_{$table}_id_seq RESTART WITH 1");
$db->exec("ALTER SEQUENCE dump_ripe_routes_{$table}_id_seq RESTART WITH 1");

/**********
 AS
 **********/

logs("-> AS");
download("https://ftp.ripe.net/ripe/asnames/asn.txt", "$tempDir/reseauio-as");

logs("Parsing");
$fp = fopen("$tempDir/reseauio-as", "r");

//$as = [];

while (($line = fgets($fp))) {
	$line = explode(" ", $line);
	if (!isset($line[0])) {
		continue;
	}
	
	$asId = (int)$line[0];
	$asCountry = trim(strtolower(end($line)));
	unset($line[0], $line[count($line)]);
	
	$name = trim(substr(implode(" ", $line), 0, -1));
	if (strstr($name, " #")) {
		$name = explode(" #", $name)[0];
	}
	
	if ($asCountry == "zz" || strlen($asCountry) != 2) {
		continue;
	}
	
	$query = $db->prepare("INSERT INTO dump_as_$table(id, name, country) VALUES(:id, :name, :country)");
	$query->bindValue(":id", $asId, PDO::PARAM_INT);
	$query->bindValue(":name", utf8_encode($name), PDO::PARAM_STR);
	$query->bindValue(":country", $asCountry, PDO::PARAM_STR);
	$query->execute();
}

fclose($fp);

unlink("$tempDir/reseauio-as");
unset($fp, $line, $asId, $asCountry, $name, $query);
logs("--------------------");


/**********
 AS RIPE
 **********/

logs("-> AS RIPE");
download("https://ftp.ripe.net/ripe/dbase/split/ripe.db.aut-num.gz", "$tempDir/reseauio-ripe-as.gz");

logs("Uncompressing");
shell_exec("gzip -f -d $tempDir/reseauio-ripe-as.gz");

logs("Parsing");
$lastAS = "";

$fp = fopen("$tempDir/reseauio-ripe-as", "r");

while ($line = fgets($fp)) {
	$data = explode(": ", trim($line), 2);
	$data = array_map("trim", $data);

	if (empty($lastAS) && $data[0] == "aut-num") {
		$lastAS = (int)str_replace("AS", "", $data[1]);
		$result = ["org" => "", "sponsoring-org" => "", "peers" => [], "descr" => "", "remarks" => "", "created" => 0, "modified" => 0];
	} elseif (!empty($lastAS)) {
		if ($data[0] == "org") {
			$result["org"] = strtolower($data[1]);
		} elseif ($data[0] == "sponsoring-org") {
			$result["sponsoring-org"] = strtolower($data[1]);
		} elseif ($data[0] == "import") {
			$line = explode(" ", $data[1]);
			if (!isset($line[1]) || !strstr(strtolower($line[1]), "as")) {
				continue;
			}
			
			$result["peers"][] = (int)str_replace("AS", "", $line[1]);
		} elseif ($data[0] == "descr") {
			if (!empty($result["descr"])) {
				$result["descr"] .= "\n";
			} else {
				$result["descr"] = "";
			}
			
			$result["descr"] .= $data[1];
		} elseif ($data[0] == "remarks") {
			if (strstr($data[1], "****************************")) {
				// INSERTION
				
				$query = $db->prepare("INSERT INTO dump_ripe_as_$table(id, org, sponsoring_org, description, remarks, created, modified) VALUES(:id, :org, :sponsoring_org, :description, :remarks, :created, :modified)");
				$query->bindValue(":id", $lastAS, PDO::PARAM_INT);
				$query->bindValue(":org", $result["org"], PDO::PARAM_STR);
				$query->bindValue(":sponsoring_org", $result["sponsoring-org"], PDO::PARAM_STR);
				$query->bindValue(":description", utf8_encode($result["descr"]), PDO::PARAM_STR);
				$query->bindValue(":remarks", utf8_encode($result["remarks"]), PDO::PARAM_STR);
				$query->bindValue(":created", $result["created"], PDO::PARAM_INT);
				$query->bindValue(":modified", $result["modified"], PDO::PARAM_INT);
				$query->execute();
				
				if (!empty($result["peers"])) {
					foreach ($result["peers"] as $peer) {
						$query = $db->prepare("INSERT INTO dump_ripe_as_peers_$table(asn, peer) VALUES(:asn, :peer)");
						$query->bindValue(":asn", $lastAS, PDO::PARAM_INT);
						$query->bindValue(":peer", $peer, PDO::PARAM_INT);
						$query->execute();
					}
				}
				
				// FIN INSERTION
				
				$lastAS = "";
				
				continue;
			}
			
			if (!empty($result["remarks"])) {
				$result["remarks"] .= "\n";
			} else {
				$result["remarks"] = "";
			}
			
			$result["remarks"] .= $data[1];
		} elseif ($data[0] == "created") {
			$result["created"] = strtotime($data[1]);
		} elseif ($data[0] == "last-modified") {
			$result["modified"] = strtotime($data[1]);
		}
	}
}

fclose($fp);

unlink("$tempDir/reseauio-ripe-as");
unset($lastAS, $fp, $line, $data, $result, $query);
logs("--------------------");

/**********
 BLOCS LIR
 **********/

$lirId = [];
$rirHash = [];

foreach ($rirList as $rirId=>$rir) {
	logs("-> RIR $rir");
	download("https://ftp.ripe.net/pub/stats/$rir/delegated-$rir-extended-latest", "$tempDir/reseauio-$rir");
	
	logs("Parsing");
	
	for ($i = 1; $i <= 2; $i++) {
		$fp = fopen("$tempDir/reseauio-$rir", "r");
		while (($line = fgets($fp))) {
			$line = explode("|", $line);
			if (!isset($line[0])) {
				continue;
			}
			
			// Recherche des AS/identifiants LIR
			if ($i == 1) {
				if (isset($line[6]) && $line[2] == "asn" && in_array($line[6], $states)) {
					if (!isset($line[7]) || !isset($line[5]) || empty($line[5])) {
						continue;
					}
					
					$lirId[$line[7]] = [
						"as" => (int)$line[3],
						"allocated" => strtotime($line[5]),
						"rir" => $rirId
					];
				}
			} elseif ($i == 2) {
				// Recherche des blocs IP
				if (!isset($line[2])) {
					continue;
				}
				
				if ($line[2] == "ipv4" || $line[2] == "ipv6") {
					if ($line[3] == "*" || !isset($line[6]) || !in_array($line[6], $states)) {
						continue;
					}
					
					if ($line[2] == "ipv4") {
						$blockStart = $line[3];
						$blockEnd = long2ip(ip2long($blockStart)+$line[4]-1);
					} else {
						$blockStart = $line[3];
						$blockEnd = ReseauIo::long2ipv2(gmp_strval(gmp_sub(gmp_strval(gmp_add(ReseauIo::ip2longv2($blockStart), ReseauIo::cidrToHostsV6($line[4]))), 1)));
					}
					
					if (!isset($line[7])) {
						continue;
					}
					
					if (isset($lirId[$line[7]])) {
						$asn = $lirId[$line[7]]["as"];
					} else {
						$asn = 0;
					}
					
					$cidr = $line[2] == "ipv4" ? ReseauIo::ipRangeToCidr($blockStart, $blockEnd) : $line[4];
					
					
					$query = $db->prepare("INSERT INTO dump_blocks_$table(version, block, block_start, block_end, country, lir, created, rir) VALUES(:version, :block, :block_start, :block_end, :country, :lir, :created, :rir);");
					$query->bindValue(":version", (int)str_replace("ipv", "", $line[2]), PDO::PARAM_INT);
					$query->bindValue(":block", "$blockStart/$cidr", PDO::PARAM_STR);
					$query->bindvalue(":block_start", $blockStart, PDO::PARAM_STR);
					$query->bindValue(":block_end", $blockEnd, PDO::PARAM_STR);
					$query->bindValue(":country", strtolower($line[1]), PDO::PARAM_STR);
					$query->bindValue(":lir", $asn, PDO::PARAM_INT);
					$query->bindValue(":created", strtotime($line[5]), PDO::PARAM_INT);
					$query->bindValue(":rir", $rirId, PDO::PARAM_INT);
					$query->execute();
				}
			}
		}
	}
	
	fclose($fp);
	unlink("$tempDir/reseauio-$rir");
}

unset($fp, $rirId, $rir, $line, $blockStart, $blockEnd, $cidr, $id, $query, $rirHash);
logs("--------------------");


/**********
 LIR
 **********/
 
logs("Inserting LIR");

foreach ($lirId as $lir) {
	/*if (!isset($as[$lir["as"]]["name"])) {
		continue;
		
		$asn = 0;
		//$lirName = "";
		$country = "ZZ";
	} else {
		$asn = $lir["as"];
		//$lirName = trim(utf8_encode($as[$lir["as"]]["name"]));
		$country = trim($as[$lir["as"]]["country"]);
	}
	
	if ($country == "zz") {
		continue;
	}*/
	
	$query = $db->prepare("INSERT INTO dump_lir_$table(id, rir, created) VALUES(:id, :rir, :created)");
	$query->bindValue(":id", $lir["as"], PDO::PARAM_INT);
	/*$query->bindValue(":name", $lirName, PDO::PARAM_STR);
	$query->bindValue(":country", strtolower($country), PDO::PARAM_STR);*/
	$query->bindValue(":rir", $lir["rir"], PDO::PARAM_INT);
	$query->bindValue(":created", $lir["allocated"], PDO::PARAM_INT);
	$query->execute();
}

unset($lirId, $as, $lir, $as, $asn, $lirName, $country, $query);
logs("--------------------");



/**********
 ROUTES RIPE
 **********/

for ($i = 1; $i <= 2; $i++) {
	$ipVersion = $i == 1 ? 4 : 6;
	
	logs("-> RIPE's IPv$ipVersion routes");
	download("https://ftp.ripe.net/ripe/dbase/split/ripe.db.route".($ipVersion == 6 ? 6 : "").".gz", "$tempDir/reseauio-routes$ipVersion.gz");

	logs("Uncompressing");
	shell_exec("gzip -f -d $tempDir/reseauio-routes$ipVersion.gz");
	
	logs("Parsing");
	
	$lastIp = "";
	
	$fp = fopen("$tempDir/reseauio-routes$ipVersion", "r");
	while (($line = fgets($fp))) {
		$data = explode(": ", trim($line), 2);
		$data = array_map("trim", $data);
		
		if (empty($lastIp) && $data[0] == "route".($ipVersion == 6 ? 6 : "")) {
			$lastIp = $data[1];
			$result = ["descr" => "", "origin" => 0, "created" => 0, "modified" => 0];
		} elseif (!empty($lastIp)) {
			if ($data[0] == "descr") {
				$result["descr"] = (string)$data[1];
			} elseif ($data[0] == "origin") {
				$result["origin"] = (int)str_replace("AS", "", $data[1]);
			} elseif ($data[0] == "created") {
				$result["created"] = strtotime($data[1]);
			} elseif ($data[0] == "last-modified") {
				$result["modified"] = strtotime($data[1]);
			} elseif ($data[0] == "remarks" && $data[1] == "****************************") {				
				// INSERTION
				
				if ($ipVersion == 4) {
					$blockData = ReseauIo::cidrToRange($lastIp);
					$blockStart = $blockData[0];
					$blockEnd = $blockData[1];
				} else {
					$block = explode("/", $lastIp);
					$blockStart = $block[0];
					if ($block[1] != 128) {
						$blockEnd = ReseauIo::long2ipv2(gmp_strval(gmp_sub(gmp_strval(gmp_add(ReseauIo::ip2longv2($blockStart), ReseauIo::cidrToHostsV6($block[1]))), 1)));
					} else {
						$blockEnd = $blockStart;
					}
				}
				
				$query = $db->prepare("INSERT INTO dump_ripe_routes_$table(version, block, block_start, block_end, description, origin, created, modified) VALUES(:version, :block, :block_start, :block_end, :description, :origin, :created, :modified)");
				$query->bindValue(":version", $ipVersion, PDO::PARAM_INT);
				$query->bindValue(":block", $lastIp, PDO::PARAM_STR);
				$query->bindValue(":block_start", $blockStart, PDO::PARAM_STR);
				$query->bindValue(":block_end", $blockEnd, PDO::PARAM_STR);
				$query->bindValue(":description", utf8_encode($result["descr"]), PDO::PARAM_STR);
				$query->bindValue(":origin", $result["origin"], PDO::PARAM_INT);
				$query->bindValue(":created", $result["created"], PDO::PARAM_INT);
				$query->bindValue(":modified", $result["modified"], PDO::PARAM_INT);
				$query->execute();
				
				
				
				// FIN INSERTION
				
				$lastIp = "";
			}
		}
	}
	
	fclose($fp);
	
	unlink("$tempDir/reseauio-routes$ipVersion");
}

unset($i, $ipVersion, $lastIp, $fp, $line, $data, $result, $blockData, $blockStart, $blockEnd, $block, $query);
logs("--------------------");



/**********
 ORGANISATIONS RIPE
 **********/
logs("-> RIPE organisations");
download("https://ftp.ripe.net/ripe/dbase/split/ripe.db.organisation.gz", "$tempDir/reseauio-org.gz");

logs("Uncompressing");
shell_exec("gzip -f -d $tempDir/reseauio-org.gz");

logs("Parsing");

$orgStarted = false;
$lastOrg = "";

$fp = fopen("$tempDir/reseauio-org", "r");
while (($line = fgets($fp))) {
	$data = explode(": ", trim($line), 2);
	$data = array_map("trim", $data);
	
	if ($data[0] == "organisation") {
		$orgStarted = true;
		$data[1] = trim(strtolower($data[1]));
		$lastOrg = $data[1];
		
		$org[$data[1]] = ["name" => "", "is_lir" => false, "description" => "", "remarks" => "", "created" => 0, "modified" => 0];
	} else {
		if ($orgStarted) {
			if ($data[0] == "org-name") {
				$result["name"] = $data[1];
			} elseif ($data[0] == "org-type") {
				$result["is_lir"] = $data[1] == "LIR";
			} elseif ($data[0] == "descr") {
				$result["description"] .= !empty($result["description"]) ? "\n{$data[1]}" : $data[1];
			} elseif ($data[0] == "remarks") {
				$result["remarks"] .= !empty($result["remarks"]) ? "\n{$data[1]}" : $data[1];
			} elseif ($data[0] == "created") {
				$result["created"] = strtotime($data[1]);
			} elseif ($data[0] == "last-modified") {
				$result["modified"] = strtotime($data[1]);
			} elseif ($data[0] == "source") {				
				$orgStarted = false;
				
				// INSERTION
				
				
				$query = $db->prepare("INSERT INTO dump_ripe_organisations_$table(org, name, is_lir, created, modified) VALUES(:org, :name, :is_lir, :created, :modified)");
				$query->bindValue(":org", $lastOrg, PDO::PARAM_STR);
				$query->bindValue(":name", utf8_encode($result["name"]), PDO::PARAM_STR);
				$query->bindValue(":is_lir", (int)$result["is_lir"], PDO::PARAM_INT);
				$query->bindValue(":created", $result["created"], PDO::PARAM_INT);
				$query->bindValue(":modified", $result["modified"], PDO::PARAM_INT);
				$query->execute();
				
				
				// FIN INSERTION
				
				$lastOrg = "";
			}
		}
	}
}
fclose($fp);
unlink("$tempDir/reseauio-org");
unset($orgStarted, $lastOrg, $fp, $line, $data, $org, $result, $query);
logs("--------------------");


/**********
 ALLOCATIONS RIPE
 **********/
$blacklist = ["::/0", "128.0.0.0/1", "0.0.0.0/0"];
for ($i = 1; $i <= 2; $i++) {
	$filename = $i == 1 ? "inetnum" : "inet6num";
	
	logs("-> Downloading $filename");
	
	download("https://ftp.ripe.net/ripe/dbase/split/ripe.db.$filename.gz", "$tempDir/reseauio-$filename.gz");
	
	logs("Uncompressing");
	shell_exec("gzip -f -d $tempDir/reseauio-$filename.gz");
	
	logs("Parsing");

	$blockStarted = false;
	$lastBlock = "";

	$fp = fopen("$tempDir/reseauio-$filename", "r");
	while (($line = fgets($fp))) {
		$data = explode(": ", trim($line), 2);
		$data = array_map("trim", $data);
		
		if ($data[0] == $filename) {
			if (!strstr($data[1], ":")) {
				// IPv4
				$data2 = explode(" - ", $data[1]);
				$block = "{$data2[0]}/".ReseauIo::ipRangeToCidr($data2[0], $data2[1]);
				$blockStart = $data2[0];
				$blockEnd = long2ip(ip2long($blockStart)+(ip2long($data2[1])-ip2long($blockStart)));
			} else {
				// IPv6
				$data2 = explode("/", $data[1]);
				$block = $data[1];
				$blockStart = $data2[0];
				if ($data2[1] != 128) {
					$blockEnd = ReseauIo::long2ipv2(gmp_strval(gmp_sub(gmp_strval(gmp_add(ReseauIo::ip2longv2($blockStart), ReseauIo::cidrToHostsV6($data2[1]))), 1)));
				} else {
					$blockEnd = $blockStart;
				}
			}
			
			$result = ["block_start" => $blockStart, "block_end" => $blockEnd, "org" => "", "country" => "", "netname" => "", "description" => "", "remarks" => "", "status" => "", "created" => 0, "modified" => 0];
			
			$blockStarted = true;
			$lastBlock = $block;
		} else {
			if ($blockStarted) {
				if ($data[0] == "org") {
					$result["org"] = strtolower($data[1]);
				} elseif ($data[0] == "country") {
					$result["country"] = substr(strtolower($data[1]), 0, 2);
				} elseif ($data[0] == "netname") {
					$result["netname"] = $data[1];
				} elseif ($data[0] == "descr") {
					$result["description"] .= !empty($result["description"]) ? "\n{$data[1]}" : $data[1];
				} elseif ($data[0] == "remarks") {
					$result["remarks"] .= !empty($result["remarks"]) ? "\n{$data[1]}" : $data[1];
				} elseif ($data[0] == "status") {
					$result["status"] = $data[1];
				} elseif ($data[0] == "created") {
					$result["created"] = strtotime($data[1]);
				} elseif ($data[0] == "last-modified") {
					$result["modified"] = strtotime($data[1]);
				} elseif ($data[0] == "source") {
					if (empty($result["netname"]) || $result["country"] == "zz" || in_array($lastBlock, $blacklist)) {
						continue;
					}
	
					$blockVersion = strstr($lastBlock, ":") ? 6 : 4;
					
					$cidr = explode("/", $block);
					$cidr = end($cidr);
					
					$query = $db->prepare("INSERT INTO dump_ripe_allocations_$table(version, block, block_start, block_end, org, country, netname, description, remarks, status, created, modified) VALUES(:version, :block, :block_start, :block_end, :org, :country, :netname, :description, :remarks, :status, :created, :modified)");
					$query->bindValue(":version", $blockVersion, PDO::PARAM_INT);
					$query->bindValue(":block", $lastBlock, PDO::PARAM_STR);
					$query->bindValue(":block_start", $result["block_start"], PDO::PARAM_STR);
					$query->bindValue(":block_end", $result["block_end"], PDO::PARAM_STR);
					$query->bindValue(":org", $result["org"], PDO::PARAM_STR);
					$query->bindValue(":country", $result["country"], PDO::PARAM_STR);
					$query->bindValue(":netname", utf8_encode($result["netname"]), PDO::PARAM_STR);
					$query->bindValue(":description", utf8_encode($result["description"]), PDO::PARAM_STR);
					$query->bindValue(":remarks", utf8_encode($result["remarks"]), PDO::PARAM_STR);
					$query->bindValue(":status", $result["status"], PDO::PARAM_STR);
					$query->bindValue(":created", $result["created"], PDO::PARAM_INT);
					$query->bindValue(":modified", $result["modified"], PDO::PARAM_INT);
					$query->execute();
					
					
					$blockStarted = false;
					$lastBlock = "";
				}
			}
		}
	}
	fclose($fp);
}

unlink("$tempDir/reseauio-inetnum");
unlink("$tempDir/reseauio-inet6num");




logs("-> Optimization of the database");

$query = $db->prepare("VACUUM \"dump_ripe_allocations_$table\"");
$query->execute();

$query = $db->prepare("VACUUM \"dump_as_$table\"");
$query->execute();

$query = $db->prepare("VACUUM \"dump_blocks_$table\"");
$query->execute();

$query = $db->prepare("VACUUM \"dump_lir_$table\"");
$query->execute();

$query = $db->prepare("VACUUM \"dump_ripe_organisations_$table\"");
$query->execute();

$query = $db->prepare("VACUUM \"proxys\"");
$query->execute();

$query = $db->prepare("VACUUM \"dump_ripe_routes_$table\"");
$query->execute();

$query = $db->prepare("ANALYZE");
$query->execute();


Cache::write("dump_id", $table);

logs("sleep(60)");

sleep(60);

logs("Cleaning the $oldTable table");

$query = $db->prepare("TRUNCATE dump_as_$oldTable");
$query->execute();

$query = $db->prepare("TRUNCATE dump_ripe_as_$oldTable");
$query->execute();

$query = $db->prepare("TRUNCATE dump_ripe_as_peers_$oldTable");
$query->execute();

$query = $db->prepare("TRUNCATE dump_ripe_allocations_$oldTable");
$query->execute();

$query = $db->prepare("TRUNCATE dump_blocks_$oldTable");
$query->execute();

$query = $db->prepare("TRUNCATE dump_lir_$oldTable");
$query->execute();

$query = $db->prepare("TRUNCATE dump_ripe_organisations_$oldTable");
$query->execute();

$query = $db->prepare("TRUNCATE dump_ripe_routes_$oldTable");
$query->execute();


logs("The end");