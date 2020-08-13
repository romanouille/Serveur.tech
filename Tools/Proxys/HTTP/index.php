<?php
header("Content-Type: text/plain;chaset=utf-8");

$dev = PHP_OS == "WINNT";

$db = new PDO("pgsql:host=127.0.0.1;dbname=reseauio", "postgres", !$dev ? "_#\GC[6N9nsV8+Pq" : "azerty", [PDO::ATTR_PERSISTENT => true]);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$serverIp = "90.63.25.246";
$token = "2afa6130500e10ce3b9ce7eeed4794083ae5d4ac";
$debug = false;


function stop(string $reason) {
	global $debug;
	
	if ($debug) {
		file_put_contents("debug", $reason);
	}
	
	exit($reason);
}

if (isset($_GET["purge-opx234"])) {
	$db->exec("DELETE FROM proxys WHERE ".time()."-timestamp >= 86400");
	
	exit("Purged");
}

unset($_SERVER["SERVER_NAME"], $_SERVER["HTTP_HOST"]);

http_response_code(400);

if (!isset($_COOKIE["tk"]) || !is_string($_COOKIE["tk"]) || empty($_COOKIE["tk"]) || $_COOKIE["tk"] != $token) {
	stop("n 0");
}

if (!isset($_GET["a"]) || !is_string($_GET["a"]) || empty($_GET["a"]) || !@ip2long($_GET["a"])) {
	stop("n 1");
}

if (!isset($_GET["b"]) || !is_string($_GET["b"])|| empty($_GET["b"]) || !is_numeric($_GET["b"])) {
	stop("n 2");
}

if (!isset($_GET["c"]) || !is_string($_GET["c"])) {
	stop("n 3");
}

if (!isset($_GET["d"]) || !is_string($_GET["d"])) {
	stop("n 4");
}

if (!isset($_GET["e"]) || !is_string($_GET["e"])) {
	stop("n 5");
}

foreach ($_SERVER as $key=>$value) {
	if (strstr($key, $serverIp) || strstr($value, $serverIp)) {
		stop("n 6");
	}
}

$ip = $_GET["a"];
$port = $_GET["b"]/1337;
$type = $_GET["c"];
$mode = $_GET["d"];
$filename = "$ip-$port";

if ($ip != $_SERVER["REMOTE_ADDR"]) {
	stop("n 7");
}


http_response_code(200);

$query = $db->prepare("SELECT COUNT(*) AS nb FROM proxys WHERE ip = :ip AND port = :port");
$query->bindValue(":ip", $ip, PDO::PARAM_STR);
$query->bindValue(":port", $port, PDO::PARAM_INT);
$query->execute();
$data = $query->fetch();

if ($data["nb"] == 1) {
	$query = $db->prepare("UPDATE proxys SET timestamp = ".time()." WHERE ip = :ip AND port = :port");
	$query->bindValue(":ip", $ip, PDO::PARAM_STR);
	$query->bindValue(":port", $port, PDO::PARAM_INT);
	$query->execute();
} else {
	$query = $db->prepare("INSERT INTO proxys(type, ip, port, score, timestamp) VALUES(1, :ip, :port, 1, ".time().")");
	$query->bindValue(":ip", $ip, PDO::PARAM_STR);
	$query->bindValue(":port", $port, PDO::PARAM_INT);
	$query->execute();
}