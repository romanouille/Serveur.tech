<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";

if (!isset($_SESSION["2fa"]) || $_SESSION["2fa"]) {
	header("Location: /");
	exit;
}

if (count($_POST) > 0) {
	$messages = [];
	
	if (!isset($_POST["code"]) || !is_string($_POST["code"])) {
		$messages[] = "Vous devez spécifier le code de validation.";
	} elseif (!is_numeric($_POST["code"]) || strlen($_POST["code"]) != 10) {
		$messages[] = "Le code de validation spécifié est incorrect.";
	}
	
	if (empty($messages)) {
		$user = new User($_SESSION["phone"]);
		if ($user->getValidationCode() == $_POST["code"]) {
			$_SESSION["2fa"] = true;
			header("Location: /ClientArea.php");
			exit;
		} else {
			$messages[] = "Le code de validation spécifié est incorrect.";
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
					<div class="heading">Authentification SMS</div>
					<div class="subheding">Un code de validation a été envoyé au <?=$_SESSION["phone"]?>.</div>
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
								<input type="text" name="code" placeholder="Code de validation" value="<?=isset($_POST["code"]) && is_string($_POST["code"]) ? htmlspecialchars($_POST["code"]) : ""?>"><br><br>
							</div>
							
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