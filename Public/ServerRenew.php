<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";
require "inc/Paypal.class.php";
require "inc/Server.class.php";

if (!isset($user) || !$session["has2fa"]) {
	header("Location: /Auth.php");
	exit;
}

if (!isset($_GET["id"]) || !is_string($_GET["id"]) || !is_numeric($_GET["id"])) {
	http_response_code(400);
	require "inc/Pages/Panel_error.php";
	exit;
}

$server = new Server($_GET["id"]);
if (!$server->exists()) {
	http_response_code(404);
	require "inc/Pages/Panel_error.php";
	exit;
}

if (!$user->hasServer($_GET["id"])) {
	http_response_code(403);
	require "inc/Pages/Panel_error.php";
	exit;
}

$serverConfig = $server->getConfig();

if ($offers[$serverConfig["type"]]["price"] > 0) {
	$paypal = new Paypal($config["paypal"]["client_id"], $config["paypal"]["secret"]);
	$payment = $paypal->createPayment($offers[$serverConfig["type"]]["price"], "http".($_SERVER["SERVER_PORT"] == 443 ? "s" : "")."://{$_SERVER["HTTP_HOST"]}/ValidatePayment.php");
	$user->createPayment($payment["id"], $serverConfig["type"], $_GET["id"]);
	header("Location: {$payment["links"][1]["href"]}");
} else {
	if ($serverConfig["expiration"]-time() > 259200) {
		http_response_code(403);
		$errorMessage = "Vous pourrez renouveler ce serveur à partir du ".date("d/m/Y à H:i:s", $serverConfig["expiration"]-259200);
		require "inc/Pages/Panel_error.php";
		exit;
	}
	
	$paymentId = random(32).microtime(1);
	$user->createPayment($paymentId, $serverConfig["type"], $_GET["id"]);
	header("Location: /ValidatePayment.php?paymentId=$paymentId&PayerID=x");
}