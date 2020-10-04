<?php
require "vendor/autoload.php";

use phpseclib\Net\SSH2;
use phpseclib\Crypt\RSA;
use Thedudeguy\Rcon;

require "inc/Functions.php";
require "inc/User.class.php";

session_name("session");
session_set_cookie_params(31536000, "/", $_SERVER["HTTP_HOST"], $_SERVER["SERVER_PORT"] == 443, true);
session_start();

ob_start();
register_shutdown_function("renderPage");

$config = parse_ini_file(".env", true);

$db = new PDO("pgsql:host={$config["db"]["server"]};dbname={$config["db"]["name"]}", $config["db"]["username"], $config["db"]["password"], [PDO::ATTR_PERSISTENT => true]);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$countries = [
	"fr" => "France"
];