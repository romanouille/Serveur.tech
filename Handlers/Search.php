<?php
require "Core/ReseauIo.class.php";

if (isset($match[1])) {
	if (is_string($match[1])) {
		$match[1] = str_replace("%", "", urldecode($match[1]));
		
		if (!empty($match[1])) {
			if (ReseauIo::validateIp($match[1])) {
				header("Location: /ip/{$match[1]}");
				exit;
			}
			
			$data = ReseauIo::search($match[1]);
		}
	}
}

$pageTitle = "Rechercher";
$pageDescription = "Description";

require "Pages/Search.php";