<?php
set_include_path("../");
chdir("../");
require "inc/Init.php";
require "vendor/autoload.php";

$query = $db->prepare("SELECT ip, ssh_password, rcon_password, mysql_password, type FROM servers ORDER BY id ASC");
$query->execute();
$data = $query->fetchAll();

foreach ($data as $value) {
	$value = array_map("trim", $value);
	$isFree = $offers[$value["type"]]["price"] == 0;
	$mariadbHost = $isFree ? $config["mariadb"]["free_server"] : $config["mariadb"]["paying_server"];
	
	echo "-> {$value["ip"]}\n";
	
	// FTP
	$ftp = ftp_connect($value["ip"]);
	$login = ftp_login($ftp, "user", $value["ssh_password"]);
	
	if (!$ftp || !$login) {
		echo "FTP auth failed\n";
	} else {
		ftp_close($ftp);
	}
	
	
	// RCON
	$rcon = new Thedudeguy\Rcon($value["ip"], 25575, $value["rcon_password"], 3);
	if (!$rcon->connect()) {
		echo "RCON auth failed\n";
	}
	
	
	// MySQL
	if (!new mysqli($mariadbHost, $value["ip"], $value["mysql_password"])) {
		echo "MySQL auth failed\n";
	}
}