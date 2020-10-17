<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";
require "inc/SMS.class.php";

if (isset($user)) {
	if (!$_SESSION["2fa"]) {
		header("Location: /2FA.php");
		exit;
	}
	
	header("Location: /");
	exit;
}

if (!isFrenchIp()) {
	http_response_code(403);
	$errorMessage = "Votre adresse IP doit être située en France afin d'accéder à cette section du site.";
	require "inc/Pages/Error.php";
	exit;
}

if (count($_POST) > 0 && isset($_POST["mode"]) && is_string($_POST["mode"]) && in_array($_POST["mode"], ["login", "register"])) {
	$messages = [];
	
	if ($_POST["mode"] == "login") {
		if (!isset($_POST["phonenumber"]) || !is_string($_POST["phonenumber"]) || empty(trim($_POST["phonenumber"]))) {
			$messages[] = "Vous devez spécifier votre numéro de téléphone.";
		} elseif (strlen($_POST["phonenumber"]) != 10) {
			$messages[] = "Votre numéro de téléphone doit se composer de 10 caractères.";
		}
		
		if (!isset($_POST["password"]) || !is_string($_POST["password"]) || empty(trim($_POST["password"]))) {
			$messages[] = "Vous devez spécifier votre mot de passe.";
		} elseif (strlen($_POST["password"]) < 8 || strlen($_POST["password"]) > 72) {
			$messages[] = "Votre mot de passe doit se composer d'au minimum 8 caractères et d'au maximum 72 caractères.";
		}
		
		if (!$captcha->check()) {
			$messages[] = "Vous devez prouver que vous n'êtes pas un robot.";
		}
		
		if (empty($messages)) {
			$user = new User($_POST["phonenumber"]);
			if ($user->exists()) {
				if ($user->verifyPassword($_POST["password"])) {
					$userProfile = $user->getProfile();
					$user->createSession();
					
					if ($user->sendSmsCode()) {
						header("Location: /2FA.php");
						exit;
					} else {
						header("Location: /ClientArea.php");
						exit;
					}
				} else {
					$messages[] = "Les identifiants spécifiés sont incorrects.";
				}
			} else {
				$messages[] = "Le numéro de téléphone mobile spécifié n'est pas inscrit.";
			}
		}
	} elseif ($_POST["mode"] == "register") {
		if (!isset($_POST["firstname"]) || !is_string($_POST["firstname"]) || empty(trim($_POST["firstname"]))) {
			$messages[] = "Vous devez spécifier votre prénom.";
		} elseif (strlen($_POST["firstname"]) > 255) {
			$messages[] = "Votre prénom doit se composer d'au maximum 255 caractères.";
		}
		
		if (!isset($_POST["lastname"]) || !is_string($_POST["lastname"]) || empty(trim($_POST["lastname"]))) {
			$messages[] = "Vous devez spécifier votre nom.";
		} elseif (strlen($_POST["lastname"]) > 255) {
			$messages[] = "Votre nom doit se composer d'au maximum 255 caractères.";
		}
		
		if (!isset($_POST["phonenumber"]) || !is_string($_POST["phonenumber"]) || empty(trim($_POST["phonenumber"]))) {
			$messages[] = "Vous devez spécifier votre numéro de téléphone mobile.";
		} elseif (strlen($_POST["phonenumber"]) != 10) {
			$messages[] = "Votre numéro de téléphone mobile doit être composé de 10 caractères.";
		} elseif (!is_numeric($_POST["phonenumber"])) {
			$messages[] = "Le numéro de téléphone spécifié est incorrect.";
		} else {
			$user = new User($_POST["phonenumber"]);
			if ($user->exists()) {
				$messages[] = "Il existe déjà un compte associé à ce numéro de téléphone. <a href=\"ForgotPassword.php\" title=\"Mot de passe oublié\">Cliquez ici pour réinitialiser votre mot de passe.</a>";
			}
		}
		
		if (isset($_POST["companyname"]) && !is_string($_POST["companyname"])) {
			$messages[] = "Le nom de votre entreprise est incorrect.";
		} elseif (isset($_POST["companyname"]) && strlen($_POST["companyname"]) > 255) {
			$messages[] = "Le nom de votre entreprise doit se composer d'au maximum 255 caractères.";
		}
		
		if (!isset($_POST["address1"]) || !is_string($_POST["address1"]) || empty(trim($_POST["address1"]))) {
			$messages[] = "Vous devez spécifier votre adresse.";
		} elseif (strlen($_POST["address1"]) > 255) {
			$messages[] = "Votre adresse doit se composer d'au maximum 255 caractères.";
		}
		
		if (isset($_POST["address2"]) && !is_string($_POST["address2"])) {
			$messages[] = "Votre deuxième adresse est incorrecte.";
		} elseif (isset($_POST["address2"]) && strlen($_POST["address2"]) > 255) {
			$messages[] = "Votre deuxième adresse doit se composer d'au maximum 255 caractères.";
		}
		
		if (!isset($_POST["city"]) || !is_string($_POST["city"]) || empty(trim($_POST["city"]))) {
			$messages[] = "Vous devez spécifier votre ville.";
		} elseif (strlen($_POST["city"]) > 255) {
			$messages[] = "Votre ville doit se composer d'au maximum 255 caractères.";
		}
		
		if (!isset($_POST["postcode"]) || !is_string($_POST["postcode"]) || empty(trim($_POST["postcode"]))) {
			$messages[] = "Vous devez spécifier votre code postal.";
		} elseif (strlen($_POST["postcode"]) > 5) {
			$messages[] = "Votre code postal doit se composer d'au maximum 5 caractères.";
		}
		
		if (!isset($_POST["country"]) || !is_string($_POST["country"]) && !is_string($_POST["country"])) {
			$messages[] = "Vous devez spécifier votre pays.";
		} elseif (!isset($countries[$_POST["country"]])) {
			$messages[] = "Le pays spécifié n'existe pas.";
		}
		
		if (!isset($_POST["password"]) || !is_string($_POST["password"]) || empty(trim($_POST["password"]))) {
			$messages[] = "Vous devez spécifier votre mot de passe.";
		} elseif (strlen($_POST["password"]) < 8 || strlen($_POST["password"]) > 72) {
			$messages[] = "Votre mot de passe doit se composer d'au minimum 8 caractères et d'au maximum 72 caractères.";
		} else {	
			if (!isset($_POST["password2"]) || !is_string($_POST["password2"]) || empty(trim($_POST["password2"]))) {
				$messages[] = "Vous devez confirmer votre mot de passe.";
			} elseif ($_POST["password"] != $_POST["password2"]) {
				$messages[] = "Les mots de passe ne correspondent pas.";
			}
		}
		
		if (!$captcha->check()) {
			$messages[] = "Vous devez prouver que vous n'êtes pas un robot.";
		}
		
		if (empty($messages)) {
			$userId = User::create($_POST["phonenumber"], $_POST["password"], $_POST["firstname"], $_POST["lastname"], $_POST["companyname"], $_POST["address1"], $_POST["address2"], $_POST["city"], $_POST["postcode"], $_POST["country"]);			
			$user = new User($_POST["phonenumber"]);
			$user->createSession();
			
			if ($user->sendSmsCode()) {
				header("Location: /2FA.php");
			} else {
				$user->validate2fa();
				header("Location: /ClientArea.php");
			}
			
			exit;
		}
	}
}
	
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Connexion</title>
		<meta name="description" content="">
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'UA-180603057-1');
		</script>
		<link href="/assets/media/logos/logo.png" rel="shortcut icon">
		<!-- Fonts -->
		<link href="fonts/fontawesome/css/all.css" rel="stylesheet" media="none" onload="if(media!='all')media='all'">
		<link href="fonts/cloudicon/cloudicon.css" rel="stylesheet" media="none" onload="if(media!='all')media='all'">
		<link href="fonts/opensans/opensans.css" rel="stylesheet" media="none" onload="if(media!='all')media='all'">
		<!-- CSS styles -->
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/filter.css" rel="stylesheet">
		<link href="css/style.min.css" rel="stylesheet">
		<!-- Custom color styles -->
		<link href="css/colors/pink.css" rel="stylesheet" title="pink" media="none" onload="if(media!='all')media='all'"/>
		<link href="css/colors/blue.css" rel="stylesheet" title="blue" media="none" onload="if(media!='all')media='all'"/>
		<link href="css/colors/green.css" rel="stylesheet" title="green" media="none" onload="if(media!='all')media='all'"/>
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	</head>
	<body>
		<!-- ***** LOADING PAGE ****** -->
		<div id="spinner-area">
			<div class="spinner">
				<div class="double-bounce1"></div>
				<div class="double-bounce2"></div>
				<div class="spinner-txt"></div>
			</div>
		</div>
		<p id="nav-toggle"></p>
		<!-- ***** FULL COUNTDOWN PAGE ***** -->
		<div class="fullrock config sec-bg2 motpath">
			<a onclick="window.history.go(-1); return false;" class="closebtn">
			<img class="svg closer bg-transparent" src="fonts/svg/close.svg" alt="">
			</a>
			<section class="fullrock-content">
				<div class="container">
					<div class="sec-main sec-bg1 tabs mb-100">
						<div class="randomline">
							<div class="bigline"></div>
							<div class="smallline"></div>
						</div>
						<h3>Connexion / Créer un compte</h3>
						<p class="mb-5">Si vous êtes client, connectez-vous. Sinon, créez un compte.</p>
						<div class="tabs-header btn-select-customer">
							<ul class="btn-group btn-group-toggle" data-toggle="buttons">
								<li class="btn btn-secondary active mb-2">
									<input type="radio" name="options" id="option1" checked> Déjà client ?
								</li>
								<li class="btn btn-secondary">
									<input type="radio" name="options" id="option2"> Créer un compte
								</li>
							</ul>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="table tabs-item active">
									<div class="cd-filter-block mb-0">
										<h4 class="m-0">Connexion</h4>
										<div class="cd-filter-content">
											<form  method="post" class="comments-form">
												<input type="hidden" name="mode" value="login">
												<div><small>
<?php
if (isset($messages)) {
	foreach ($messages as $nb=>$message) {
		if ($nb > 0) {
			echo "<br>";
		}
		
		echo $message;
	}
}
?>
												</small></div>
												<div class="row">
													<div class="col-md-6">
														<label for="inputPhone"><i class="fas fa-phone"></i></label>
														<input type="tel" name="phonenumber" id="inputPhone" placeholder="Numéro de téléphone mobile" value="<?=isset($_POST["phonenumber"]) && is_string($_POST["phonenumber"]) ? htmlspecialchars($_POST["phonenumber"]) : ""?>" required>
													</div>
													<div class="col-md-6">
														<label><i class="fas fa-lock"></i></label>
														<input type="password" name="password" placeholder="Mot de passe" value="<?=isset($_POST["password"]) && is_string($_POST["password"]) ? htmlspecialchars($_POST["password"]) : ""?>" required>
													</div>
													<div style="margin-top:20px">
														<?=$captcha->create()?>
													</div>
													<div class="col-md-12 mt-5">
														<button type="submit" value="login" id="login" class="btn btn-default-yellow-fill mt-0 mb-3 mr-3">Valider <i class="fas fa-lock"></i>
														</button>
														<a href="ForgotPassword.php" titie="Mot de passe oublié" class="golink mr-3">Mot de passe oublié ?</a>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
								<div class="table tabs-item">
									<div class="cd-filter-block mb-0">
										<h4>Créer un compte</h4>
										<div class="cd-filter-content">
											<form method="post" name="orderfrm" class="comments-form">
												<input type="hidden" name="mode" value="register">
												<div><small>
<?php
if (isset($messages)) {
	foreach ($messages as $nb=>$message) {
		if ($nb > 0) {
			echo "<br>";
		}
		
		echo $message;
	}
}
?>
												</small></div>
												<div class="row mb-5">
													<div class="col-md-6">
														<label for="firstname"><i class="fas fa-user-tie"></i></label>
														<input type="text" name="firstname" id="firstname" placeholder="Prénom" value="<?=isset($_POST["firstname"]) && is_string($_POST["firstname"]) ? htmlspecialchars($_POST["firstname"]) : ""?>" required>
													</div>
													<div class="col-md-6">
														<label for="inputLastName"><i class="fas fa-user-tie"></i></label>
														<input type="text" name="lastname" id="inputLastName" placeholder="Nom" value="<?=isset($_POST["lastname"]) && is_string($_POST["lastname"]) ? htmlspecialchars($_POST["lastname"]) : ""?>" required>
													</div>
													<div class="col-md-6">
														<label for="inputPhone"><i class="fas fa-phone"></i></label>
														<input type="tel" name="phonenumber" id="inputPhone" placeholder="Numéro de téléphone mobile" value="<?=isset($_POST["phonenumber"]) && is_string($_POST["phonenumber"]) ? htmlspecialchars($_POST["phonenumber"]) : ""?>" required>
													</div>
												</div>
												<div class="row mb-5">
													<div class="col-md-6">
														<label for="inputCompanyName"><i class="fas fa-building"></i></label>
														<input type="text" name="companyname" id="inputCompanyName" placeholder="Nom de l'entreprise (facultatif)" value="<?=isset($_POST["companyname"]) && is_string($_POST["companyname"]) ? htmlspecialchars($_POST["companyname"]) : ""?>">
													</div>
													<div class="col-md-6">
														<label for="inputAddress1"><i class="far fa-building"></i></label>
														<input type="text" name="address1" id="inputAddress1" placeholder="Adresse" value="<?=isset($_POST["address1"]) && is_string($_POST["address1"]) ? htmlspecialchars($_POST["address1"]) : ""?>" required>
													</div>
													<div class="col-md-6 ">
														<label for="inputAddress2"><i class="fas fa-map-marker-alt"></i></label>
														<input type="text" name="address2" id="inputAddress2" placeholder="Adresse 2 (facultatif)" value="<?=isset($_POST["address2"]) && is_string($_POST["address2"]) ? htmlspecialchars($_POST["address2"]) : ""?>">
													</div>
													<div class="col-md-6 ">
														<label for="inputCity"><i class="far fa-building"></i></label>
														<input type="text" name="city" id="inputCity" placeholder="Ville" value="<?=isset($_POST["city"]) && is_string($_POST["city"]) ? htmlspecialchars($_POST["city"]) : ""?>" required>
													</div>
													<div class="col-md-4">
														<label for="inputPostcode"><i class="fas fa-certificate"></i></label>
														<input type="text" name="postcode" id="inputPostcode" placeholder="Code postal" value="<?=isset($_POST["postcode"]) && is_string($_POST["postcode"]) ? htmlspecialchars($_POST["postcode"]) : ""?>" required>
													</div>
													<div class="col-md-4">
														<div class="cd-select mt-4">
															<label for="inputCountry" id="inputCountryIcon" class="db"></label>
															<select name="country" id="inputCountry" class="select-filter">
																<option value="fr">France</option>
															</select>
														</div>
													</div>
												</div>
												<div class="row mb-5">
													<div class="col-md-6">
														<label for="inputNewPassword1"><i class="fas fa-lock"></i></label>
														<input type="password" name="password" id="inputNewPassword1" placeholder="Mot de passe" value="<?=isset($_POST["password"]) && is_string($_POST["password"]) ? htmlspecialchars($_POST["password"]) : ""?>">
													</div>
													<div class="col-md-6">
														<label for="inputNewPassword2"><i class="fas fa-lock"></i></label>
														<input type="password" name="password2" id="inputNewPassword2" placeholder="Confirmez le mot de passe" value="<?=isset($_POST["password"]) && is_string($_POST["password2"]) ? htmlspecialchars($_POST["password2"]) : ""?>">
													</div>
												</div>
												<div style="margin-top:20px;margin-bottom:20px">
													<?=$captcha->create()?>
												</div>
												
												<button type="submit" value="Submit" class="btn btn-default-yellow-fill mb-1 disable-on-click spinner-on-click ">Valider</button>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<!-- ***** COLORS ***** -->
		<section>
			<ul class="color-scheme">
				<li class="pink"><a href="#" data-rel="pink" class="styleswitch"></a></li>
				<li class="blue"><a href="#" data-rel="blue" class="styleswitch"></a></li>
				<li class="green"><a href="#" data-rel="green" class="styleswitch"></a></li>
			</ul>
		</section>
		<!-- Javascript -->
		<script src="js/jquery.min.js"></script>
		<script defer src="js/bootstrap.min.js"></script>
		<script defer src="js/jquery.countdown.js"></script>
		<script defer src="js/jquery.magnific-popup.min.js"></script>
		<script defer src="js/slick.min.js"></script>
		<script defer src="js/owl.carousel.min.js"></script>
		<script defer src="js/isotope.min.js"></script>
		<script defer src="js/swiper.min.js"></script>
		<script defer src="js/filter.js"></script>
		<script defer src="js/scripts.min.js"></script>
	</body>
</html>