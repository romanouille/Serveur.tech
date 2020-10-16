<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";

if (!isset($user) || !$session["has2fa"]) {	
	header("Location: /Auth.php");
	exit;
}

if (count($_POST) > 0) {
	$messages = [];
	
	if (!isset($_POST["token"]) || !is_string($_POST["token"]) || $_POST["token"] != $token) {
		$messages[] = "Le formulaire est invalide.";
	}
	
	if (!isset($_POST["first_name"]) || !is_string($_POST["first_name"]) || empty(trim($_POST["first_name"]))) {
		$messages[] = "Vous devez spécifier votre prénom.";
	} elseif (strlen(trim($_POST["first_name"])) > 255) {
		$messages[] = "Votre prénom doit se composer d'au maximum 255 caractères.";
	} else {
		$_POST["first_name"] = trim($_POST["first_name"]);
	}
	
	if (!isset($_POST["last_name"]) || !is_string($_POST["last_name"]) || empty(trim($_POST["last_name"]))) {
		$messages[] = "Vous devez spécifier votre nom.";
	} elseif (strlen(trim($_POST["last_name"])) > 255) {
		$messages[] = "Votre nom doit se composer d'au maximum 255 caractères.";
	} else {
		$_POST["last_name"] = trim($_POST["last_name"]);
	}
	
	if (isset($_POST["company_name"]) && is_string($_POST["company_name"]) && !empty(trim($_POST["company_name"]))) {
		if (strlen(trim($_POST["company_name"])) > 255) {
			$messages[] = "Le nom de votre entreprise doit se composer d'au maximum 255 caractères.";
		} else {
			$_POST["company_name"] = trim($_POST["company_name"]);
		}
	}
	
	if (!isset($_POST["address1"]) || !is_string($_POST["address1"]) || empty(trim($_POST["address1"]))) {
		$messages[] = "Vous devez spécifier votre adresse.";
	} elseif (strlen($_POST["address1"]) > 255) {
		$messages[] = "Votre adresse doit se composer d'au maximum 255 caractères.";
	} else {
		$_POST["address1"] = trim($_POST["address1"]);
	}
	
	if (isset($_POST["address2"]) && is_string($_POST["address2"]) && !empty(trim($_POST["address2"]))) {
		if (strlen(trim($_POST["address2"])) > 255) {
			$messages[] = "Votre deuxième adresse doit se composer d'au maximum 255 caractères.";
		} else {
			$_POST["address2"] = trim($_POST["address2"]);
		}
	}
	
	if (!isset($_POST["city"]) || !is_string($_POST["city"]) || empty(trim($_POST["city"]))) {
		$messages[] = "Vous devez spécifier votre ville.";
	} elseif (strlen(trim($_POST["city"])) > 255) {
		$messages[] = "Votre ville doit se composer d'au maximum 255 caractères.";
	} else {
		$_POST["city"] = trim($_POST["city"]);
	}
	
	if (!isset($_POST["postal_code"]) || !is_string($_POST["postal_code"]) || empty(trim($_POST["postal_code"]))) {
		$messages[] = "Vous devez spécifier votre code postal.";
	} elseif (strlen(trim($_POST["postal_code"])) > 5) {
		$messages[] = "Votre code postal doit se composer d'au maximum 5 caractères.";
	} else {
		$_POST["postal_code"] = trim($_POST["postal_code"]);
	}
	
	if (!$captcha->check()) {
		$messages[] = "Vous devez prouver que vous n'êtes pas un robot.";
	}
	
	if (empty($messages)) {
		$user->updateProfile($_POST["first_name"], $_POST["last_name"], $_POST["company_name"], $_POST["address1"], $_POST["address2"], $_POST["city"], $_POST["postal_code"]);
		$messages[] = "Votre profil a été mis à jour.";
	}
}
	
$breadcrumb = "Mon compte";
$profile = $user->getProfile();

require "inc/Layout/Panel/Start.php";

?>
<div class="container">
	<div class="card card-custom gutter-b">
		<div class="card-body px-0">

<?php
if (isset($messages)) {
?>
			<div class="text-center">
<?php
	foreach ($messages as $nb=>$message) {
		if ($nb > 0) {
			echo "<br>";
		}
		
		echo $message;
	}
		?>
			</div>
			<br><br>
<?php
}
?>
			<form method="post" class="form">
				<input type="hidden" name="token" value="<?=$token?>">
				<div class="form-group row">
					<label class="col-xl-3 col-lg-3 col-form-label text-right">Prénom</label>
					<div class="col-lg-9 col-xl-6">
						<input class="form-control form-control-lg form-control-solid" type="text" name="first_name" value="<?=htmlspecialchars($profile["first_name"])?>" required>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-xl-3 col-lg-3 col-form-label text-right">Nom</label>
					<div class="col-lg-9 col-xl-6">
						<input class="form-control form-control-lg form-control-solid" type="text" name="last_name" value="<?=htmlspecialchars($profile["last_name"])?>" required>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-xl-3 col-lg-3 col-form-label text-right">Entreprise</label>
					<div class="col-lg-9 col-xl-6">
						<input class="form-control form-control-lg form-control-solid" type="text" name="company_name" value="<?=htmlspecialchars($profile["company_name"])?>" required>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-xl-3 col-lg-3 col-form-label text-right">Adresse 1</label>
					<div class="col-lg-9 col-xl-6">
						<input class="form-control form-control-lg form-control-solid" type="text" name="address1" value="<?=htmlspecialchars($profile["address1"])?>" required>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-xl-3 col-lg-3 col-form-label text-right">Adresse 2</label>
					<div class="col-lg-9 col-xl-6">
						<input class="form-control form-control-lg form-control-solid" type="text" name="address2" value="<?=htmlspecialchars($profile["address2"])?>" required>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-xl-3 col-lg-3 col-form-label text-right">Ville</label>
					<div class="col-lg-9 col-xl-6">
						<input class="form-control form-control-lg form-control-solid" type="text" name="city" value="<?=htmlspecialchars($profile["city"])?>" required>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-xl-3 col-lg-3 col-form-label text-right">Code postal</label>
					<div class="col-lg-9 col-xl-6">
						<input class="form-control form-control-lg form-control-solid" type="text" name="postal_code" value="<?=htmlspecialchars($profile["postal_code"])?>" required>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-xl-3 col-lg-3 col-form-label text-right"></label>
					<div class="col-lg-9 col-xl-6">
						<?=$captcha->create()?>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-xl-3 col-lg-3 col-form-label text-right"></label>
					<div class="col-lg-9 col-xl-6">
						<input type="submit" class="btn btn-light-primary font-weight-bold btn-sm" onclick="this.disabled=true;document.getElementsByTagName('form')[0].submit()">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php

require "inc/Layout/Panel/End.php";