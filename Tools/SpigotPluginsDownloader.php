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
curl_setopt($curl, CURLOPT_COOKIE, "cf_clearance=b6ec352128a0929a153a036cdf27a47fa78069fb-1602345187-0-1z3242204dz1fba760bza4101264-150");

for ($i = 1; $i <= 2285; $i++) {
	curl_setopt($curl, CURLOPT_URL, "https://www.spigotmc.org/resources/categories/spigot.4/?page=$i");
	$page = curl_exec($curl);
	
	preg_match_all("`<div class=\"listBlockInner\">\n<a href=\"(.+)\" class=\"resourceIcon\">(.+)</a>`isU", $page, $urls);
	$urls = $urls[1];
	
	preg_match_all("`<div class=\"tagLine\">\n(.+)</div>`isU", $page, $descriptions);
	$descriptions = array_map(function($a) { return html_entity_decode($a, ENT_HTML5); }, array_map("trim", $descriptions[1]));
	
	foreach ($urls as $id=>$url) {
		$jarName = urldecode(explode("/", $url)[1]);
		
		echo "-> $jarName (Page $i)\n"; 
		
		curl_setopt($curl, CURLOPT_REFERER, "https://www.spigotmc.org/resources/categories/spigot.4/?page=$i");
		curl_setopt($curl, CURLOPT_URL, "https://www.spigotmc.org/$url");
		$page = curl_exec($curl);
		
		preg_match("`<h1>(.+) <span class=\"muted\">`isU", $page, $name);
		$name = html_entity_decode(urldecode($name[1]), ENT_HTML5);
		
		preg_match("`<ul class=\"plainList\">(.+)</ul>`isU", $page, $versions);
		$versions = explode(",", str_replace("<li>", "", str_replace("</li>", ",", isset($versions[1]) ? $versions[1] : "")));
		unset($versions[count($versions)-1]);
		
		preg_match("`<label class=\"downloadButton \">\n<a href=\"(.+)\" class=\"inner\">`isU", $page, $downloadLink);
		$downloadLink = $downloadLink[1];
		
		curl_setopt($curl, CURLOPT_URL, "https://www.spigotmc.org/$downloadLink");
		curl_setopt($curl, CURLOPT_REFERER, "https://www.spigotmc.org/$url");
		
		$page = curl_exec($curl);
		if (strstr($page, "Checking your browser before accessing")) {
			exit("Cloudflared\n");
		}
		
		if (!file_put_contents("Tools/plugins/$jarName.jar", $page)) {
			echo "Write failed\n";
			unlink("Tools/plugins/$jarName.jar");
			continue;
		}
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM plugins WHERE name = :name");
		$query->bindValue(":name", $name, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		if ($data["nb"] > 0) {
			continue;
		}
		
		$query = $db->prepare("INSERT INTO plugins(jar_name, name, description, versions) VALUES(:jar_name, :name, :description, :versions)");
		$query->bindValue(":jar_name", $jarName, PDO::PARAM_STR);
		$query->bindValue(":name", $name, PDO::PARAM_STR);
		$query->bindValue(":description", $descriptions[$id], PDO::PARAM_STR);
		$query->bindValue(":versions", implode(", ", $versions), PDO::PARAM_STR);
		$query->execute();
	}
}