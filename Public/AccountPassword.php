<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";

if (!isset($user) || !$_SESSION["2fa"]) {
	$_SESSION = [];
	
	header("Location: /Auth.php");
	exit;
}

if (count($_POST) > 0) {
	$messages = [];
	
	if (!isset($_POST["token"]) || !is_string($_POST["token"]) || $_POST["token"] != $token) {
		$messages[] = "Le formulaire est invalide.";
	}
	
	if (!isset($_POST["current_password"]) || !is_string($_POST["current_password"]) || empty($_POST["current_password"])) {
		$messages[] = "Vous devez spécifier votre mot de passe actuel.";
	}
	
	if (!isset($_POST["new_password"]) || !is_string($_POST["new_password"]) || empty($_POST["new_password"])) {
		$messages[] = "Vous devez spécifier votre nouveau mot de passe.";
	} elseif (strlen($_POST["new_password"]) < 8) {
		$messages[] = "Votre mot de passe doit se composer d'au minimum 8 caractères.";
	} else {	
		if (!isset($_POST["new_password_confirmation"]) || !is_string($_POST["new_password_confirmation"]) || empty($_POST["new_password_confirmation"])) {
			$messages[] = "Vous devez confirmer votre nouveau mot de passe.";
		} elseif ($_POST["new_password"] != $_POST["new_password_confirmation"]) {
			$messages[] = "Les nouveaux mots de passe ne correspondent pas.";
		}
	}
	
	if (empty($messages)) {
		if ($user->verifyPassword($_POST["current_password"])) {
			$user->changePassword($_POST["new_password"]);
			$messages[] = "Votre mot de passe a été modifié.";
		} else {
			$messages[] = "Le mot de passe actuel que vous avez saisi est incorrect.";
		}
	}
}
	
$breadcrumb = "Modifier mon mot de passe";

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
					<label class="col-xl-3 col-lg-3 col-form-label text-right">Mot de passe actuel</label>
					<div class="col-lg-9 col-xl-6">
						<input class="form-control form-control-lg form-control-solid" type="password" name="current_password" required>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-xl-3 col-lg-3 col-form-label text-right">Nouveau mot de passe</label>
					<div class="col-lg-9 col-xl-6">
						<input class="form-control form-control-lg form-control-solid" type="password" name="new_password" required>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-xl-3 col-lg-3 col-form-label text-right">Confirmez le nouveau mot de passe</label>
					<div class="col-lg-9 col-xl-6">
						<input class="form-control form-control-lg form-control-solid" type="password" name="new_password_confirmation" required>
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