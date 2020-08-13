<?php
$config = parse_ini_file(".env", true);

$db = new PDO("pgsql:host={$config["db"]["server"]};dbname={$config["db"]["name"]}", $config["db"]["username"], $config["db"]["password"], [PDO::ATTR_PERSISTENT => true]);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$staticServer = !$dev ? "" : "http://127.0.0.2";
$table = Cache::read("dump_id");
$rirList = [
	"AFRINIC",
	"APNIC",
	"ARIN",
	"LACNIC",
	"RIPENCC"
];

if (php_sapi_name() != "cli") {
	$recaptcha = new Recaptcha($config["recaptcha"]["public_key"], $config["recaptcha"]["private_key"]);
}

if (isset($_COOKIE["session"])) {
	if (!is_string($_COOKIE["session"]) || strlen($_COOKIE["session"]) != 128 || !(new Session($_COOKIE["session"]))->exists()) {
		setcookie("session", "", time()-3600, "/", $_SERVER["HTTP_HOST"], !$dev, true);
		header("Location: {$_SERVER["REQUEST_URI"]}");
		exit;
	}
	
	$logged = true;
	
	$session = new Session($_COOKIE["session"]);
	$session->update();
} else {
	$logged = false;
}