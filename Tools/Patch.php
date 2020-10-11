<?php
set_include_path("../");
chdir("../");
require "inc/Init.php";

$query = $db->prepare("SELECT id, jar_name, name, description FROM plugins");
$query->execute();
$data = $query->fetchAll();

foreach ($data as $value) {
	$value = array_map("trim", $value);
	
	$query = $db->prepare("UPDATE plugins SET name = :name, description = :description WHERE id = :id");
	$query->bindValue(":name", html_entity_decode($value["name"], ENT_QUOTES), PDO::PARAM_STR);
	$query->bindValue(":description", html_entity_decode($value["description"], ENT_QUOTES), PDO::PARAM_STR);
	$query->bindValue(":id", $value["id"], PDO::PARAM_INT);
	$query->execute();
}

foreach ($data as $value) {
	$value = array_map("trim", $value);
	
	if (strstr(file_get_contents("Tools/plugins/{$value["jar_name"]}.jar"), "<!DOCTYPE html>")) {
		$query = $db->prepare("DELETE FROM plugins WHERE id = :id");
		$query->bindValue(":id", $value["id"], PDO::PARAM_INT);
		$query->execute();
		unlink("Tools/plugins/{$value["jar_name"]}.jar");
	}
}

foreach ($data as $value) {	
	if (!file_exists("Tools/plugins/{$value["jar_name"]}.jar")) {
		echo "-> {$value["jar_name"]}\n";
		
		$query = $db->prepare("DELETE FROM plugins WHERE id = :id");
		$query->bindValue(":id", $value["id"], PDO::PARAM_INT);
		$query->execute();
	}
}