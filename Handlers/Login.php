<?php
$pageTitle = "Connexion";
$pageDescription = "Connectez-vous à votre compte Serveur.tech.";

if (count($_POST) > 0) {
	$messages = [];
	
	if (!isset($_POST["email"]) || !is_string($_POST["email"]) || empty($_POST["email"])) {
		$messages[] = "Vous devez spécifier votre adresse e-mail.";
	} elseif (strlen($_POST["email"]) > 255) {
		$messages[] = "Votre adresse e-mail doit contenir au maximum 255 caractères.";
	} elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
		$messages[] = "L'adresse e-mail spécifiée est invalide.";
	} elseif (!User::emailExists($_POST["email"])) {
		$messages[] = "Il n'existe pas de compte associé à cette adresse e-mail.";
	}
	
	if (!isset($_POST["password"]) || !is_string($_POST["password"]) || empty($_POST["password"])) {
		$messages[] = "Vous devez spécifier votre mot de passe.";
	} elseif (strlen($_POST["password"]) > 72) {
		$messages[] = "Votre mot de passe doit contenir au maximum 72 caractères.";
	}
	
	if (!$recaptcha->check()) {
		$messages[] = "Vous devez prouver que vous n'êtes pas un robot.";
	}
	
	if (empty($messages)) {
		$user = new User(User::emailToId($_POST["email"]));
		if ($user->checkPassword($_POST["password"])) {
			Session::create($user->id);
			header("Location: /");
			exit;
		} else {
			$messages[] = "Le mot de passe spécifié est incorrect.";
		}
	}
}

require "Pages/Login.php";