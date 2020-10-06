<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";
require "inc/Paypal.class.php";
require "inc/Server.class.php";

if (!isset($_SESSION["2fa"]) || !$_SESSION["2fa"]) {
	$_SESSION = [];
	
	header("Location: /Auth.php");
	exit;
}

if (!isset($_GET["type"]) || !is_string($_GET["type"]) || !isset($offers[$_GET["type"]])) {
	http_response_code(400);
	require "inc/Pages/Error.php";
	exit;
}

if (!Server::isAvailable($_GET["type"])) {
	http_response_code(503);
	require "inc/Pages/Error.php";
	exit;
}

if (count($_POST) > 0) {
	$paypal = new Paypal($config["paypal"]["client_id"], $config["paypal"]["secret"]);
	$payment = $paypal->createPayment($offers[$_GET["type"]]["price"], "http".($_SERVER["SERVER_PORT"] == 443 ? "s" : "")."://{$_SERVER["HTTP_HOST"]}/ValidatePayment.php");
	$user->createPayment($payment["id"], $_GET["type"]);
	header("Location: {$payment["links"][1]["href"]}");
	exit;
}

require "inc/Layout/Start.php";
?>
<!-- ***** BANNER ***** -->
<div class="top-header item7 overlay scrollme">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-12">
				<div class="wrapper animateme" data-when="span" data-from="0" data-to="0.75" data-opacity="1" data-translatey="-50">
					<h1 class="heading">Panier</h1>
					<h3 class="subheading">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</h3>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- ***** KNOWLEDGEBASE ***** -->
<section class="config cd-main-content pb-80 blog sec-bg2 motpath">
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-lg-8 pt-80">
				<div class="wrap-blog">
					<div class="row">
						<div class="col-md-12 col-lg-12">
							<div class="wrapper targetDiv sec-grad-white-to-green">
								<h3>Votre panier</h3>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
								<div class="row">
									<div class="col-md-12 pt-4">
										<div class="table-responsive-lg">
											<table class="table compare">
												<thead>
													<tr>
														<td>Produit</td>
														<td>Prix</td>
														<td>RAM</td>
														<td>Coeurs CPU</td>
														<td>Stockage</td>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Serveur Minecraft #<?=$_GET["type"]?></td>
														<td><span class=""><?=$offers[$_GET["type"]]["price"]?>€</span></td>
														<td><?=$offers[$_GET["type"]]["ram"]?> Mo</td>
														<td><?=$offers[$_GET["type"]]["cpu"]?></td>
														<td><?=$offers[$_GET["type"]]["ssd"]?> Go</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- sidebar -->
			<div class="col-md-12 col-lg-4">
				<aside id="sidebar" class="mt-120 sidebar sec-bg1">
					<div class="ordersummary mt-0">
						<h4>Récapitulatif de la commande</h4>
						<div class="table-responsive-lg">
							<table class="table">
								<tbody>
									<tr>
										<td>
											<div class="title-table">Montant total</div>
										</td>
										<td>
											<h6><b><?=$offers[$_GET["type"]]["price"]?>€</b></h6>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<form method="post">
						<input type="hidden" name="mode" value="2">
						<button type="submit" class="btn btn-default-yellow-fill mb-3">Commander avec Paypal <i class="fas fa-arrow-alt-circle-right"></i></button>
					</form>
				</aside>
			</div>
		</div>
	</div>
</section>
<!-- ***** HELP ***** -->
<section class="services help pt-4 pb-80">
	<div class="container">
		<div class="service-wrap">
			<div class="row">
				<div class="col-sm-12 col-md-6 col-lg-4">
					<div class="help-container">
						<a href="javascript:void(Tawk_API.toggle())" class="help-item">
							<div class="img">
								<img class="svg ico" src="fonts/svg/livechat.svg" height="65" alt="">
							</div>
							<div class="inform">
								<div class="title">Live Chat</div>
								<div class="description">Lorem Ipsum is simply dummy text printing.</div>
							</div>
						</a>
					</div>
				</div>
				<div class="col-sm-12 col-md-6 col-lg-4">
					<div class="help-container">
						<a href="contact.html" class="help-item">
							<div class="img">
								<img class="svg ico" src="fonts/svg/emailopen.svg" height="65" alt="">
							</div>
							<div class="inform">
								<div class="title">Send Ticket</div>
								<div class="description">Lorem Ipsum is simply dummy text printing.</div>
							</div>
						</a>
					</div>
				</div>
				<div class="col-sm-12 col-md-6 col-lg-4">
					<div class="help-container">
						<a href="knowledgebase.html" class="help-item">
							<div class="img">
								<img class="svg ico" src="fonts/svg/book.svg" height="65" alt="">
							</div>
							<div class="inform">
								<div class="title">Knowledge base</div>
								<div class="description">Lorem Ipsum is simply dummy text printing.</div>
							</div>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ***** SMALL MODAL ***** -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><b>Remove Item!</b></h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to remove this item from your cart?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default-fill " data-dismiss="modal">No</button>
				<button type="button" class="btn btn-default-purple-fill" data-dismiss="modal">Yes</button>
			</div>
		</div>
	</div>
</div>
<?php
require "inc/Layout/End.php";