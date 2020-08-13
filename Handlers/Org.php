<?php
require "Core/ReseauIo.class.php";

$data = ReseauIo::getOrgData($match[1]);
if (empty($data["org"])) {
	http_response_code(404);
	require "Handlers/Error.php";
}

$pageTitle = $match[1];
$pageDescription = "Description";

require "Pages/Org.php";