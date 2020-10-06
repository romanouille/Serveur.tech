<?php
require "vendor/autoload.php";
require "inc/Captcha.class.php";
require "inc/Functions.php";
require "inc/User.class.php";

$dev = PHP_OS == "WINNT";

session_name("session");
session_set_cookie_params(31536000, "/", $_SERVER["HTTP_HOST"], $_SERVER["SERVER_PORT"] == 443, true);
session_start();

ob_start();
register_shutdown_function("renderPage");

$config = parse_ini_file(".env", true);

$captcha = new Captcha($config["recaptcha"]["public_key"], $config["recaptcha"]["private_key"]);

$db = new PDO("pgsql:host={$config["db"]["server"]};dbname={$config["db"]["name"]}", $config["db"]["username"], $config["db"]["password"], [PDO::ATTR_PERSISTENT => true]);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$countries = [
	"fr" => "France"
];

$offers = [
	1 => [
		"ram" => 4,
		"cpu" => 4,
		"ssd" => 20,
		"price" => 4.99
	]
];

if (!empty($_SESSION)) {
	$user = new User($_SESSION["phone"]);
}