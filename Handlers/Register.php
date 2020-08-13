<?php
$pageTitle = "Créer un compte";
$pageDescription = "Créez votre compte sur Serveur.tech.";

if (count($_POST) > 0) {
	$success = false;
	$messages = [];
	
	if (!isset($_POST["email"]) || !is_string($_POST["email"]) || empty($_POST["email"])) {
		$messages[] = "Vous devez spécifier votre adresse e-mail.";
	} elseif (strlen($_POST["email"]) > 255) {
		$messages[] = "Votre adresse e-mail doit contenir au maximum 255 caractères.";
	} elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
		$messages[] = "L'adresse e-mail spécifiée est invalide.";
	} elseif (User::emailExists($_POST["email"])) {
		$messages[] = "Il existe déjà un compte avec cette adresse e-mail.";
	}
	
	if (!isset($_POST["password"]) || !is_string($_POST["password"]) || empty($_POST["password"])) {
		$messages[] = "Vous devez spécifier votre mot de passe.";
	} elseif (strlen($_POST["password"]) < 8 || strlen($_POST["password"]) > 72) {
		$messages[] = "Votre mot de passe doit contenir au minimum 8 caractères et au maximum 72 caractères.";
	}
	
	if (!isset($_POST["password2"]) || !is_string($_POST["password2"]) || empty($_POST["password2"])) {
		$messages[] = "Vous devez confirmer votre mot de passe.";
	} elseif ($_POST["password"] != $_POST["password2"]) {
		$messages[] = "Les deux mots de passe ne correspondent pas.";
	}
	
	if (!isset($_POST["firstname"]) || !is_string($_POST["firstname"]) || empty($_POST["firstname"])) {
		$messages[] = "Vous devez spécifier votre prénom.";
	} elseif (strlen($_POST["firstname"]) > 255) {
		$messages[] = "Votre prénom doit contenir au maximum 255 caractères.";
	}
	
	if (!isset($_POST["lastname"]) || !is_string($_POST["lastname"]) || empty($_POST["lastname"])) {
		$messages[] = "Vous devez spécifier votre nom.";
	} elseif (strlen($_POST["lastname"]) > 255) {
		$messages[] = "Votre nom doit contenir au maximum 255 caractères.";
	}
	
	if (!isset($_POST["country"]) || !is_string($_POST["country"]) || empty($_POST["country"])) {
		$messages[] = "Vous devez spécifier votre pays.";
	} elseif (strlen($_POST["country"]) > 255) {
		$messages[] = "Votre pays doit contenir au maximum 255 caractères.";
	}
	
	if (!isset($_POST["address"]) || !is_string($_POST["address"]) || empty($_POST["address"])) {
		$messages[] = "Vous devez spécifier votre adresse.";
	} elseif (strlen($_POST["address"]) > 255) {
		$messages[] = "Votre adresse doit contenir au maximum 255 caractères.";
	}
	
	if (!isset($_POST["postalcode"]) || !is_string($_POST["postalcode"]) || empty($_POST["postalcode"])) {
		$messages[] = "Vous devez spécifier votre code postal.";
	} elseif (strlen($_POST["postalcode"]) > 255) {
		$messages[] = "Votre code postal doit contenir au maximum 255 caractères.";
	}
	
	if (!isset($_POST["city"]) || !is_string($_POST["city"]) || empty($_POST["city"])) {
		$messages[] = "Vous devez spécifier votre ville.";
	} elseif (strlen($_POST["city"]) > 255) {
		$messages[] = "Votre ville doit contenir au maximum 255 caractères.";
	}
	
	if (!isset($_POST["phone"]) || !is_string($_POST["phone"]) || empty($_POST["phone"])) {
		$messages[] = "Vous devez spécifier votre numéro de téléphone.";
	} elseif (strlen($_POST["phone"]) > 255) {
		$messages[] = "Votre numéro de téléphone doit contenir au maximum 255 caractères.";
	}
	
	if (!$recaptcha->check()) {
		$messages[] = "Vous devez prouver que vous n'êtes pas un robot.";
	}
	
	if (empty($messages)) {
		User::create($_POST["email"], $_POST["password"], $_POST["firstname"], $_POST["lastname"], $_POST["country"], $_POST["address"], $_POST["postalcode"], $_POST["city"], $_POST["phone"]);
		$success = true;
		
		$messages[] = "Votre compte a été créé, vous pouvez désormais <a href=\"/account/login\" title=\"Connexion\">vous connecter</a>.";
	}
}

require "Pages/Register.php";