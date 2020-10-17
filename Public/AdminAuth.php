<?php
set_include_path("../");
chdir("../");

require "inc/Admin.class.php";
require "inc/Init.php";

if (!isset($user) || !$session["has2fa"]) {
	header("Location: /Auth.php");
	exit;
}

if (!$user->isAdmin()) {
	http_response_code(403);
	require "inc/Pages/Panel_error.php";
	exit;
}

if (count($_POST) > 0) {
	$messages = [];
	
	if (!isset($_POST["token"]) || !is_string($_POST["token"]) || $_POST["token"] != $token) {
		$messages[] = "Le formulaire est invalide.";
	}
	
	if (!isset($_POST["password"]) || !is_string($_POST["password"]) || empty($_POST["password"])) {
		$messages[] = "Vous devez spécifier le mot de passe d'administration.";
	}
	
	if (!$captcha->check()) {
		$messages[] = "Vous devez prouver que vous n'êtes pas un robot.";
	}
	
	if (empty($messages)) {
		if ($_POST["password"] == $config["admin"]["password"]) {
			$user->setSessionAsAdmin();
			$session["admin"] = true;
		} else {
			$messages[] = "Le mot de passe spécifié est incorrect.";
		}
	}
}

$breadcrumb = "Authentification administrateur";

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

if (!$session["admin"]) {
?>
			<form method="post" class="form">
				<input type="hidden" name="token" value="<?=$token?>">
				<div class="form-group row">
					<label class="col-xl-3 col-lg-3 col-form-label text-right">Mot de passe d'administration</label>
					<div class="col-lg-9 col-xl-6">
						<input class="form-control form-control-lg form-control-solid" type="password" name="password" required>
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
<?php
} else {
?>
			<div class="text-center">
				Vous êtes connecté en tant qu'administrateur.
			</div>
<?php
}
?>
		</div>
	</div>
</div>
<?php
require "inc/Layout/Panel/End.php";