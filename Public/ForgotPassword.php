<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";
require "inc/SMS.class.php";

$showResetContent = false;

if (isset($user)) {
	header("Location: /");
	exit;
}

if (count($_POST) > 0) {
	$messages = [];
	
	if (isset($_POST["mode"]) && is_string($_POST["mode"]) && in_array($_POST["mode"], [1, 2])) {
		if ($_POST["mode"] == 1) {
			if (!isset($_POST["phone"]) || !is_string($_POST["phone"]) || empty(trim($_POST["phone"]))) {
				$messages[] = "Vous devez spécifier votre numéro de téléphone mobile.";
			} elseif (strlen($_POST["phone"]) != 10) {
				$messages[] = "Le numéro de téléphone spécifié est incorrect.";
			}
			
			if (empty($messages)) {
				$user = new User($_POST["phone"]);
				if ($user->exists()) {
					if ($user->sendSmsCode()) {
						$showResetContent = true;
					} else {
						$messages[] = "Un problème temporaire est survenu, veuillez réessayer plus tard.";
					}
				} else {
					$messages[] = "Le numéro de téléphone spécifié n'est pas inscrit.";
				}
			}
		} elseif ($_POST["mode"] == 2) {
			if (!isset($_POST["phone"]) || !is_string($_POST["phone"]) || empty(trim($_POST["phone"]))) {
				$messages[] = "Vous devez spécifier votre numéro de téléphone mobile.";
			} elseif (strlen($_POST["phone"]) != 10) {
				$messages[] = "Le numéro de téléphone spécifié est incorrect.";
			}
			
			if (!isset($_POST["code"]) || !is_string($_POST["code"]) || empty(trim($_POST["code"]))) {
				$messages[] = "Vous devez spécifier le code de confirmation reçu par SMS.";
			} elseif (strlen($_POST["code"]) != 10) {
				$messages[] = "Le code de confirmation spécifié est incorrect.";
			}
			
			if (!isset($_POST["password"]) || !is_string($_POST["password"]) || empty(trim($_POST["password"]))) {
				$messages[] = "Vous devez spécifier le nouveau mot de passe du compte.";
			} elseif (strlen($_POST["password"]) < 8 || strlen($_POST["password"]) > 72) {
				$messages[] = "Le mot de passe doit se composer d'au minimum 8 caractères et d'au maximum 72 caractères.";
			} else {			
				if (!isset($_POST["password2"]) || !is_string($_POST["password2"]) || empty(trim($_POST["password2"]))) {
					$messages[] = "Vous devez confirmer le nouveau mot de passe.";
				} elseif ($_POST["password"] != $_POST["password2"]) {
					$messages[] = "Les deux mots de passe ne correspondent pas.";
				}
			}
			
			if (empty($messages)) {
				$user = new User($_POST["phone"]);
				if ($user->exists()) {
					if ($user->getValidationCode() == $_POST["code"]) {
						$user->changePassword($_POST["password"]);
						$userProfile = $user->getProfile();
						$user->createSession($userProfile["has2fa"], $userProfile["admin"]);
						
						header("Location: /");
						exit;
					} else {
						$messages[] = "Le code spécifié est incorrect.";
					}
				} else {
					$messages[] = "Le numéro de téléphone spécifié n'est pas inscrit.";
				}
			}
		}
	}
}

require "inc/Layout/Start.php";
?>
<!-- ***** BANNER ***** -->
<div class="top-header exapath-w">
	<div class="total-grad-inverse"></div>
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="wrapper">
					<div class="heading">Mot de passe perdu</div>
					<div class="subheding">Veuillez entrer votre numéro de téléphone mobile.</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- ***** YOUR CONTENT ***** -->
<section class="balancing sec-normal bg-white pb-80">
	<div class="h-services">
		<div class="container">
			<div class="randomline">
				<div class="bigline"></div>
				<div class="smallline"></div>
			</div>
			<div class="row">
				<div class="col-md-12 text-left">
<?php
if (isset($messages) && !empty($messages)) {
	foreach ($messages as $nb=>$message) {
		if ($nb > 0) {
			echo "<br>";
		}
		
		echo $message;
	}
}
?>
					<br><br>
					<div class="cd-filter-block">
						<form method="post">
							<div class="col-md-6">
								<input type="text" name="phone" placeholder="Numéro de téléphone" value="<?=isset($_POST["phone"]) && is_string($_POST["phone"]) ? htmlspecialchars($_POST["phone"]) : ""?>"><br><br>
							</div>
							
<?php
if ($showResetContent) {
?>
							<input type="hidden" name="mode" value="2">
							<div class="col-md-6">
								<input type="text" name="code" placeholder="Code de validation" value="<?=isset($_POST["code"]) && is_string($_POST["code"]) ? htmlspecialchars($_POST["code"]) : ""?>"><br><br>
							</div>
							<div class="col-md-6">
								<input type="password" name="password" placeholder="Nouveau mot de passe" value="<?=isset($_POST["password"]) && is_string($_POST["password"]) ? htmlspecialchars($_POST["password"]) : ""?>"><br><br>
							</div>
							<div class="col-md-6">
								<input type="password" name="password2" placeholder="Confirmez le nouveau mot de passe" value="<?=isset($_POST["password2"]) && is_string($_POST["password2"]) ? htmlspecialchars($_POST["password2"]) : ""?>"><br><br>
							</div>
<?php
} else {
?>
							<input type="hidden" name="mode" value="1">
<?php
}
?>
							<div class="col-md-6">
								<button type="submit" class="btn btn-default-yellow-fill mb-1 disable-on-click spinner-on-click ">Valider</button>
							</div>
						</form>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</section>
<?php
require "inc/Layout/End.php";