<?php
require "inc/Init.php";
require "vendor/autoload.php";
require "inc/MariaDB.class.php";
require "inc/Server.class.php";
require "inc/SMS.class.php";

echo "Loading uninitialized servers list...\n";
$uninitializedServers = Server::getUninitializedServersList();
foreach ($uninitializedServers as $serverId) {
	echo "-> $serverId\n";
	$server = new Server($serverId, true);
	$server->reset();
}

echo "\nLoading expired servers...\n";
$expiredServers = Server::getExpiredServers();
foreach ($expiredServers as $serverId) {
	echo "-> $serverId\n";
	$server = new Server($serverId, true);
	$server->reset();
}

echo "\nLoading servers near expiration...\n";
$serversNearExpiration = Server::getServersNearExpiration();
foreach ($serversNearExpiration as $serverId) {
	echo "-> $serverId\n";
	$server = new Server($serverId);
	$serverConfig = $server->getConfig();
	
	if (!$serverConfig["expiration_warning"]) {
		$sms = new SMS($config["bulksms"]["token_id"], $config["bulksms"]["token_secret"]);
		$sms->send("+33".substr($serverConfig["owner"], 1), "Serveur.tech : votre serveur #$serverId expire le ".date("d/m/Y à H:i:s", $serverConfig["expiration"]).". Pensez à le renouveler si nécessaire.");
		$server->setExpirationWarning();
	}
}