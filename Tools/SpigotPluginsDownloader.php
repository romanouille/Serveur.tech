<?php
set_include_path("../");
chdir("../");
require "inc/Init.php";

$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_ENCODING, "gzip");
curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_COOKIE, "cf_clearance=f6e7f84387e88d21dd821ec111fa6b781e7d8f20-1602968958-0-1z3242204dz1fba760bza4101264-150");

for ($i = 927; $i <= 2293; $i++) {
	curl_setopt($curl, CURLOPT_URL, "https://www.spigotmc.org/resources/categories/spigot.4/?page=$i");
	$page = curl_exec($curl);
	
	preg_match_all("`<div class=\"listBlockInner\">\n<a href=\"(.+)\" class=\"resourceIcon\">(.+)</a>`isU", $page, $urls);
	$urls = $urls[1];
	
	preg_match_all("`<div class=\"tagLine\">\n(.+)</div>`isU", $page, $descriptions);
	$descriptions = array_map(function($a) { return html_entity_decode(html_entity_decode($a, ENT_HTML5), ENT_QUOTES); }, array_map("trim", $descriptions[1]));
	
	foreach ($urls as $id=>$url) {
		$filename = urldecode(explode("/", $url)[1]);
		$pluginId = @end(explode(".", $filename));
		
		echo "-> $filename (Page $i)\n"; 
		
		curl_setopt($curl, CURLOPT_REFERER, "https://www.spigotmc.org/resources/categories/spigot.4/?page=$i");
		curl_setopt($curl, CURLOPT_URL, "https://www.spigotmc.org/$url");
		$page = curl_exec($curl);
		
		preg_match("`<h1>(.+) <span class=\"muted\">`isU", $page, $name);
		$name = html_entity_decode(html_entity_decode(urldecode($name[1]), ENT_HTML5), ENT_QUOTES);
		
		preg_match("`<ul class=\"plainList\">(.+)</ul>`isU", $page, $versions);
		$versions = explode(",", str_replace("<li>", "", str_replace("</li>", ",", isset($versions[1]) ? $versions[1] : "")));
		unset($versions[count($versions)-1]);
		
		preg_match("`<label class=\"downloadButton \">\n<a href=\"(.+)\" class=\"inner\">`isU", $page, $downloadLink);
		if (!isset($downloadLink[1])) {
			continue;
		}
		$downloadLink = $downloadLink[1];
		
		preg_match("`<small class=\"minorText\">(.+)</small>`isU", $page, $fileType);
		$fileType = explode(" ", $fileType[1])[2];
		if ($fileType == "site") {
			echo "Bypass\n";
			continue;
		}
		
		curl_setopt($curl, CURLOPT_URL, "https://www.spigotmc.org/$downloadLink");
		curl_setopt($curl, CURLOPT_REFERER, "https://www.spigotmc.org/$url");
		
		$page = curl_exec($curl);
		if (strstr($page, "Checking your browser before accessing")) {
			exit("Cloudflared\n");
		}
		
		if (!file_put_contents("Tools/Plugins/".$filename.$fileType, $page)) {
			echo "Write failed\n";
			unlink("Tools/Plugins/$filename.jar");
			continue;
		}
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM plugins WHERE id = :id");
		$query->bindValue(":id", $pluginId, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		if ($data["nb"] > 0) {
			$query = $db->prepare("UPDATE plugins SET filename = :filename, name = :name, description = :description, versions = :versions, zip = :zip WHERE id = :id");
			$query->bindValue(":filename", $filename.$fileType, PDO::PARAM_STR);
			$query->bindValue(":name", $name, PDO::PARAM_STR);
			$query->bindValue(":description", $descriptions[$id], PDO::PARAM_STR);
			$query->bindValue(":versions", implode(", ", $versions), PDO::PARAM_STR);
			$query->bindValue(":zip", strstr($fileType, ".zip") ? 1 : 0, PDO::PARAM_INT);
			$query->bindValue(":id", $pluginId, PDO::PARAM_INT);
			$query->execute();
		} else {		
			$query = $db->prepare("INSERT INTO plugins(id, filename, name, description, versions, zip) VALUES(:id, :filename, :name, :description, :versions, :zip)");
			$query->bindValue(":id", $pluginId, PDO::PARAM_INT);
			$query->bindValue(":filename", $filename.$fileType, PDO::PARAM_STR);
			$query->bindValue(":name", $name, PDO::PARAM_STR);
			$query->bindValue(":description", $descriptions[$id], PDO::PARAM_STR);
			$query->bindValue(":versions", implode(", ", $versions), PDO::PARAM_STR);
			$query->bindValue(":zip", strstr($fileType, ".zip") ? 1 : 0, PDO::PARAM_INT);
			$query->execute();
		}
	}
}