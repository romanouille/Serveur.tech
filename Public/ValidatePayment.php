<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";
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

$type = $user->getPaymentOfferType($_GET["paymentId"]);
if ($type == 0) {
	http_response_code(400);
	require "inc/Pages/Error.php";
	exit;
}

$paypal = new Paypal($config["paypal"]["client_id"], $config["paypal"]["secret"]);
$result = $paypal->validatePayment($_GET["paymentId"], $_GET["PayerID"]);

if ($result) {
	if (Server::isAvailable($type) && Server::create($type, $_SESSION["phone"])) {
		$user->createInvoice($type, $offers[$type]["price"]);
	} else {
		http_response_code(500);
		require "inc/Pages/Error.php";
		exit;
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
					<div class="heading"><?=$result ? "Paiement réussi" : "Échec du paiement"?></div>
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
?>
				Votre serveur a été créé. <a href="/ClientArea.php" title="Espace client">Cliquez ici pour accéder à l'espace client.</a>
<?php
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