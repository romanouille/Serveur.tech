<?php
error_reporting(-1);
ini_set("display_errors", true);
ini_set("memory_limit", -1);

$db = new PDO("pgsql:host=127.0.0.1;dbname=ip_reports", "postgres", PHP_OS == "WINNT" ? "SnRkmGT73XS3QKgdscYS9daxGXeAMqg9" : "azerty", [PDO::ATTR_PERSISTENT => true]);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)/

require "inc/Client.class.php";
require "inc/Server.class.php";

$exceptions = [135, 445, 3389, 5985];
$servers = [];

for ($i = 1; $i <= 1024; $i++) {
	if (in_array($i, $exceptions)) {
		continue;
	}
	
	$servers[$i] = new Server("0.0.0.0", $i);
	$servers[$i]->start();
}

while(1) {
	foreach ($servers as $server) {
		
		$newClient = $server->checkForNewClient();

		if (empty($server->clients)) {
			continue;
		}
		
		foreach ($server->clients as $client) {
			$client->update();
		}
	}
}