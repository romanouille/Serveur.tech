<?php
set_include_path("../");
chdir("../");
require "inc/Init.php";
require "inc/Server.class.php";

$query = $db->prepare("SELECT id FROM servers");
$query->execute();
$data = $query->fetchAll();

foreach ($data as $value) {
	echo "-> {$value["id"]}\n";
	
	$server = new Server($value["id"], true);
	if ($server->isStarted()) {
		$server->forcedStop();
	}
}