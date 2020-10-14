<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";

if (!isset($user) || !$_SESSION["2fa"]) {
	$_SESSION = [];
	
	header("Location: /Auth.php");
	exit;
}

if (isset($_GET["token"]) && is_string($_GET["token"]) && $_GET["token"] == $token) {
	session_destroy();
}

header("Location: /");