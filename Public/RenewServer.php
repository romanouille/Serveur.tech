<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";
require "inc/Paypal.class.php";
require "inc/Server.class.php";

if (!isset($user) || !$_SESSION["2fa"]) {
	$_SESSION = [];
	
	header("Location: /Auth.php");
	exit;
}

if (!isset($_GET["id"]) || !is_string($_GET["id"]) || !is_numeric($_GET["id"])) {
	http_response_code(400);
	require "inc/Pages/Error.php";
	exit;
}

$server = new Server($_GET["id"]);
if (!$server->exists()) {
	http_response_code(404);
	require "inc/Pages/Error.php";
	exit;
}

if (!$user->hasServer($_GET["id"])) {
	http_response_code(403);
	require "inc/Pages/Error.php";
	exit;
}

$serverConfig = $server->getConfig();

$paypal = new Paypal($config["paypal"]["client_id"], $config["paypal"]["secret"]);
$payment = $paypal->createPayment($offers[$serverConfig["type"]]["price"], "http".($_SERVER["SERVER_PORT"] == 443 ? "s" : "")."://{$_SERVER["HTTP_HOST"]}/ValidatePayment.php");
$user->createPayment($payment["id"], $serverConfig["type"], $_GET["id"]);
header("Location: {$payment["links"][1]["href"]}");