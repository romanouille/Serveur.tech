<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";

if (!isset($user) || !$session["has2fa"]) {
	header("Location: /");
	exit;
}

if (isset($_GET["token"]) && is_string($_GET["token"]) && $_GET["token"] == $token) {
	$user->deleteSession();
}

header("Location: /");