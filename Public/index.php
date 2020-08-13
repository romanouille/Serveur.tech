<?php
$dev = PHP_OS == "WINNT";

if ($dev) {
	$whitelist = ["127.0.0.1"];

	if (!in_array($_SERVER["REMOTE_ADDR"], $whitelist)) {
		header("Content-Type: text/plain;charset=utf-8");
		http_response_code(403);
		exit("Votre adresse IP n'est pas dans la whitelist.");
	}
	
	ini_set("display_errors", true);
	error_reporting(-1);
} else {
	ini_set("display_errors", false);
	error_reporting(0);
}

set_include_path("../");
chdir("../");

require "Core/Routes.php";
require "Core/Functions.php";
require "Core/Cache.class.php";
require "Core/Recaptcha.class.php";
require "Core/Session.class.php";
require "Core/User.class.php";

ob_start();
register_shutdown_function("renderPage");

foreach ($routes as $route=>$routeData) {
	if (preg_match($route, $_SERVER["REQUEST_URI"], $match)) {
		$handler = $routeData["handler"];
		require "Core/Init.php";
		
		require "Handlers/$handler";
		exit;
	}
}

$handler = "Error.php";

http_response_code(404);
require "Core/Init.php";
require "Handlers/$handler";