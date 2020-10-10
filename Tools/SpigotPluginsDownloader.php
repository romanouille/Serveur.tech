<?php
set_include_path("../");
chdir("../");
require "inc/Init.php";

$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_ENCODING, "gzip");
curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_COOKIE, "cf_clearance=021de835d8df80cee336f4e91a98dfd90f904da6-1602343760-0-1z3242204dz1fba760bza4101264-150");

for ($i = 1; $i <= 2285; $i++) {
	curl_setopt($curl, CURLOPT_URL, "https://www.spigotmc.org/resources/categories/spigot.4/?page=$i");
	$page = curl_exec($curl);
	
	preg_match_all("`<div class=\"listBlockInner\">\n<a href=\"(.+)\" class=\"resourceIcon\">(.+)</a>`isU", $page, $urls);
	$urls = $urls[1];
	
	preg_match_all("`<div class=\"tagLine\">\n(.+)</div>`isU", $page, $descriptions);
	$descriptions = array_map(function($a) { return html_entity_decode($a, ENT_HTML5); }, array_map("trim", $descriptions[1]));
	
	foreach ($urls as $id=>$url) {
		$name = urldecode(explode("/", $url)[1]);
		
		echo "-> $name (Page $i)\n"; 
		
		curl_setopt($curl, CURLOPT_REFERER, "https://www.spigotmc.org/resources/categories/spigot.4/?page=$i");
		curl_setopt($curl, CURLOPT_URL, "https://www.spigotmc.org/$url");
		$page = curl_exec($curl);
		
		preg_match("`<ul class=\"plainList\">(.+)</ul>`isU", $page, $versions);
		$versions = explode(",", str_replace("<li>", "", str_replace("</li>", ",", $versions[1])));
		unset($versions[count($versions)-1]);
		
		preg_match("`<label class=\"downloadButton \">\n<a href=\"(.+)\" class=\"inner\">`isU", $page, $downloadLink);
		$downloadLink = $downloadLink[1];
		
		curl_setopt($curl, CURLOPT_URL, "https://www.spigotmc.org/$downloadLink");
		curl_setopt($curl, CURLOPT_REFERER, "https://www.spigotmc.org/$url");
		
		$page = curl_exec($curl);
		if (strstr($page, "Checking your browser before accessing")) {
			exit("Cloudflared\n");
		}
		
		file_put_contents("Tools/plugins/$name.jar", $page);
		
		$query = $db->prepare("INSERT INTO plugins(name, description, versions) VALUES(:name, :description, :versions)");
		$query->bindValue(":name", $name, PDO::PARAM_STR);
		$query->bindValue(":description", $descriptions[$id], PDO::PARAM_STR);
		$query->bindValue(":versions", implode(", ", $versions), PDO::PARAM_STR);
		$query->execute();
	}
}