<?php
/**
 * Types :
 * - ZO : Le NRA-ZO (zone d'ombre), créé par France Télécom en 2007 et financé par les collectivités locales. Sous forme d'armoire, il ne contient pas d'équipements dédiés à la téléphonie classique, mais seulement des DSLAM. Il se trouve proche du SR.
 * - MED : NRA de Montée En Débit
*/


chdir("../");
require "inc/Init.php";

function normalizeCity(string $name) : string {
	$name = str_split($name);
	$result = "";
	
	foreach ($name as $key=>$value) {		
		$ancientChar = $key > 0 ? $name[$key-1] : " ";
		
		if (in_array($ancientChar, [" ", "-"])) {
			$result .= strtoupper($value);
		} else {
			$result .= strtolower($value);
		}
	}
	
	return $result;
}

$types = [];
		
	
$lines = explode("\n", file_get_contents("Tools/podi.csv"));


$db->exec("TRUNCATE podi_departments");
$db->exec("ALTER SEQUENCE podi_departments_id_seq RESTART WITH 1");

$db->exec("TRUNCATE podi_regions");
$db->exec("ALTER SEQUENCE podi_regions_id_seq RESTART WITH 1");

$db->exec("TRUNCATE podi_nra");
$db->exec("ALTER SEQUENCE podi_nra_id_seq RESTART WITH 1");


$departments = [];
$regions = [];

foreach ($lines as $key=>$value) {
	if ($key == 0) {
		continue;
	}
	
	$value = explode(",", $value);
	
	$result = [
		"id" => $value[0],
		"department_id" => $value[1],
		"department_name" => $value[2],
		"region" => $value[4],
		"city" => normalizeCity($value[6]),
		"lines" => explode(" à ", $value[7]),
		"type" => trim($value[10])
	];
	
	$result["lines"] = [
		"start" => $result["lines"][0],
		"end" => $result["lines"][1]
	];
	
	
	
	$query = $db->prepare("SELECT COUNT(*) AS nb FROM podi_departments WHERE public_id = :public_id");
	$query->bindValue(":public_id", $result["department_id"], PDO::PARAM_INT);
	$query->execute();
	$data = $query->fetch();
	
	if ($data["nb"] == 0) {
		$query = $db->prepare("INSERT INTO podi_departments(public_id, name) VALUES(:public_id, :name)");
		$query->bindValue(":public_id", $result["department_id"], PDO::PARAM_INT);
		$query->bindValue(":name", $result["department_name"], PDO::PARAM_STR);
		$query->execute();
		
		$departments[$result["department_id"]] = $db->lastInsertId();
	}
	
	
	$query = $db->prepare("SELECT COUNT(*) AS nb FROM podi_regions WHERE name = :name");
	$query->bindValue(":name", $result["region"], PDO::PARAM_STR);
	$query->execute();
	$data = $query->fetch();
	
	if ($data["nb"] == 0) {
		$query = $db->prepare("INSERT INTO podi_regions(name) VALUES(:name)");
		$query->bindValue(":name", $result["region"], PDO::PARAM_STR);
		$query->execute();
		
		$regions[$result["region"]] = $db->lastInsertId();
	}
	
	if (empty($result["type"])) {
		$type = 0;
	} elseif ($result["type"] == "ZO") {
		$type = 1;
	} elseif ($result["type"] == "MED") {
		$type = 2;
	} else {
		exit("Type inconnu : {$result["type"]}\n");
	}
	
	$query = $db->prepare("INSERT INTO podi_nra(name, department, region, city, lines_start, lines_end, type) VALUES(:name, :department, :region, :city, :lines_start, :lines_end, :type)");
	$query->bindValue(":name", $result["id"], PDO::PARAM_STR);
	$query->bindValue(":department", $departments[$result["department_id"]], PDO::PARAM_INT);
	$query->bindValue(":region", $regions[$result["region"]], PDO::PARAM_INT);
	$query->bindValue(":city", utf8_decode($result["city"]), PDO::PARAM_STR);
	$query->bindValue(":lines_start", $result["lines"]["start"], PDO::PARAM_INT);
	$query->bindValue(":lines_end", $result["lines"]["end"], PDO::PARAM_INT);
	$query->bindValue(":type", $type, PDO::PARAM_INT);
	$query->execute();
}