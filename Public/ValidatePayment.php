<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";
require "inc/MariaDB.class.php";
require "inc/Paypal.class.php";
require "inc/Server.class.php";

if (!isset($user) || !$_SESSION["2fa"]) {
	header("Location: /Auth.php");
	exit;
}

if (!isset($_GET["paymentId"]) || !is_string($_GET["paymentId"]) || empty(trim($_GET["paymentId"]))) {
	http_response_code(400);
	require "inc/Pages/Error.php";
	exit;
}

if (!isset($_GET["PayerID"]) || !is_string($_GET["PayerID"]) || empty(trim($_GET["PayerID"]))) {
	http_response_code(400);
	require "inc/Pages/Error.php";
	exit;
}

$paymentData = $user->getPaymentData($_GET["paymentId"]);
if (empty($paymentData)) {
	http_response_code(400);
	require "inc/Pages/Error.php";
	exit;
}

$isRenew = $paymentData["server_id"] > 0;

if ($offers[$paymentData["offer_type"]]["price"] > 0) {
	$paypal = new Paypal($config["paypal"]["client_id"], $config["paypal"]["secret"]);
	$result = $paypal->validatePayment($_GET["paymentId"], $_GET["PayerID"]);
} else {
	if (!$isRenew && $user->hasFreeServer()) {
		http_response_code(403);
		$errorMessage = "Vous possédez déjà un serveur gratuit.";
		require "inc/Pages/Error.php";
		exit;
	}
	
	$result = true;
}

if ($result) {
	if (!$isRenew) {
		if (Server::isAvailable($paymentData["offer_type"]) && Server::create($paymentData["offer_type"], $_SESSION["phone"])) {
			if ($offers[$paymentData["offer_type"]]["price"] > 0) {
				$user->createInvoice($paymentData["offer_type"], $offers[$paymentData["offer_type"]]["price"]);
			}
		} else {
			http_response_code(500);
			require "inc/Pages/Error.php";
			exit;
		}
	} else {
		$server = new Server($paymentData["server_id"]);
		$serverConfig = $server->getConfig();
		$server->renew();
		
		if ($offers[$serverConfig["type"]]["price"] > 0) {
			$user->createInvoice($serverConfig["type"], $offers[$serverConfig["type"]]["price"]);
		}
		
		$isRenew = true;
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
					<div class="heading"><?=$result ? ($isRenew ? "Renouvellement réussi" : "Création réussie") : ($isRenew ? "Échec du renouvellement" : "Échec de la création")?></div>
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
			
<?php
if ($result) {
	if (!$isRenew) {
?>
				Votre serveur a été créé. <a href="/ClientArea.php" title="Espace client">Cliquez ici pour accéder à l'espace client.</a>
<?php
	} else {
?>
				Votre serveur a été renouvelé. <a href="/ClientArea.php" title="Espace client">Cliquez ici pour accéder à l'espace client.</a>
<?php
	}
} else {
?>
				Le paiement a échoué. <a href="/" title="Accueil">Cliquez ici pour retourner à la page d'accueil.</a>
<?php
}
?>
		</div>
	</div>
</section>
<?php
require "inc/Layout/End.php";