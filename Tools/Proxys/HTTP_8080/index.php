<?php
header("Content-Type: application/javascript");
chdir("../");

function stop() {
	global $result, $json;
	
	if (!$json) {
		exit;
	}
	
	echo json_encode($result);
}

register_shutdown_function("stop");

$result = [
	"message" => "Ok",
	"proxies" => []
];

$tokens = [
	"5ffe499cf3143c006282e4f1952dbc4a",
	"c1230962951d1287bf1a21a1a6e65075"
];

$json = true;


if (!isset($_GET["token"]) || !is_string($_GET["token"])) {
	http_response_code(401);
	$result["message"] = "Vous devez spécifier votre token.";
	exit;
}

if (!in_array($_GET["token"], $tokens)) {
	http_response_code(401);
	$result["message"] = "Le token spécifié est invalide.";
	exit;
}


if (!isset($_GET["type"]) || !is_string($_GET["type"])) {
	http_response_code(400);
	$result["message"] = "Vous devez spécifier le type de liste de proxys à charger.";
	exit;
}

if (!in_array($_GET["type"], ["http", "socks"])) {
	http_response_code(404);
	$result["message"] = "Le type de proxys spécifié est inconnu.";
	exit;
}

$db = $_GET["type"] == "http" ? 0 : 1;

$files = glob("db/$db/*");
usort($files, create_function('$a,$b', 'return filemtime($a) - filemtime($b);'));

$files = array_reverse($files);

foreach ($files as $file) {
	$filename = $file;
	$data = file_get_contents($file);
	$file = explode("/", $file);
	$file = end($file);
	
	$proxy = explode("-", $file);
	
	if ($db == 0 && $data < 2) {
		continue;
	}
	
	$entry = [
		"ip" => (string)$proxy[0],
		"port" => (int)$proxy[1],
		"timestamp" => (int)filemtime($filename)
	];
	
	if ($db == 0) {
		$entry["full_secured"] = (int)($db == 0 ? ($data == 2 ? 1 : 0) : 1);
	}
	
	$result["proxies"][] = $entry;
}

if (isset($_GET["plain"]) && is_string($_GET["plain"]) && $_GET["plain"] == 1) {
	$json = false;
	
	foreach ($result["proxies"] as $proxy) {
		echo "{$proxy["ip"]}:{$proxy["port"]}\n";
	}
}