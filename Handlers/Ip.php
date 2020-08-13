<?php
require "Core/ReseauIo.class.php";

$ip = explode("/", $_SERVER["REQUEST_URI"])[2];
$reducedIp = ReseauIo::reduceIp($ip);

if ($ip != $reducedIp) {
	header("Location: /ip/$reducedIp");
	exit;
}

$data = ReseauIo::getIpData($ip);

$pageTitle = "Adresse IP $ip";
$pageDescription = "Description";

require "Pages/Ip.php";