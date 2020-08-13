<?php
require "Core/ReseauIo.class.php";

if ($match[1] < 1) {
	http_response_code(400);
	require "Handlers/Error.php";
}

$data = ReseauIo::getAsData($match[1]);

$pageTitle = "AS{$match[1]}";
$pageDescription = "Description";

require "Pages/As.php";