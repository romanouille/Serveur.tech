<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";

if (!isset($user) || !$session["has2fa"]) {
	header("Location: /Auth.php");
	exit;
}

$servers = $user->getServersList();

$breadcrumb = "Liste des serveurs";

require "inc/Layout/Panel/Start.php";
?>
<!--begin::Container-->
<div class=" container ">
	<div class="alert alert-info">
		Voici les identifiants PayPal de test :<br><br>
		
		Adresse e-mail : <b>sb-dxcf5792106@personal.example.com</b><br>
		Mot de passe : <b>d2r7UIl$</b>
	</div>
<?php
if (!empty($servers)) {
	foreach ($servers as $server) {
?>
	<!--begin::Card-->
	<div class="card card-custom gutter-b">
		<div class="card-body">
			<!--begin::Top-->
			<div class="d-flex">
				<!--begin: Info-->
				<div class="flex-grow-1">
					<!--begin::Title-->
					<div class="d-flex align-items-center justify-content-between flex-wrap mt-2">
						<!--begin::User-->
						<div class="mr-3">
							<!--begin::Name-->
							<a href="#" class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3">
							<?=$server["ip"]?>
							</a>
							<!--end::Name-->
						</div>
						<!--begin::User-->
						<!--begin::Actions-->
						<div class="my-lg-0 my-1">
							<a href="/ServerSettings.php?id=<?=$server["id"]?>" class="btn btn-sm btn-primary font-weight-bolder text-uppercase">Configurer</a>
						</div>
						<!--end::Actions-->
					</div>
					<!--end::Title-->
					<!--begin::Content-->
					<div class="d-flex align-items-center flex-wrap justify-content-between">
						<!--begin::Description-->
						<div class="flex-grow-1 font-weight-bold text-dark-50 py-2 py-lg-2 mr-5">
							Expire le <?=date("d/m/Y à H:i:s", $server["expiration"])?>
						</div>
						<!--end::Description-->
					</div>
					<!--end::Content-->
				</div>
				<!--end::Info-->
			</div>
			<!--end::Top-->
			<!--begin::Separator-->
			<div class="separator separator-solid my-7"></div>
			<!--end::Separator-->
			<!--begin::Bottom-->
			<div class="d-flex align-items-center flex-wrap">
				<!--begin: Item-->
				<div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
					<span class="mr-4">
					<i class="flaticon-piggy-bank icon-2x text-muted font-weight-bold"></i>
					</span>
					<div class="d-flex flex-column text-dark-75">
						<span class="font-weight-bolder font-size-sm">Prix mensuel</span>
						<span class="font-weight-bolder font-size-h5"><?=$offers[$server["type"]]["price"]?><span class="text-dark-50 font-weight-bold">€</span></span>
					</div>
				</div>
				<!--end: Item-->
				<!--begin: Item-->
				<div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
					<span class="mr-4">
					<i class="flaticon-confetti icon-2x text-muted font-weight-bold"></i>
					</span>
					<div class="d-flex flex-column text-dark-75">
						<span class="font-weight-bolder font-size-sm">RAM</span>
						<span class="font-weight-bolder font-size-h5"><?=$offers[$server["type"]]["ram"]?> <span class="text-dark-50 font-weight-bold">Go</span></span>
					</div>
				</div>
				<!--end: Item-->
				<!--begin: Item-->
				<div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
					<span class="mr-4">
					<i class="flaticon-pie-chart icon-2x text-muted font-weight-bold"></i>
					</span>
					<div class="d-flex flex-column text-dark-75">
						<span class="font-weight-bolder font-size-sm">CPU</span>
						<span class="font-weight-bolder font-size-h5"><span class="text-dark-50 font-weight-bold"><?=$offers[$server["type"]]["cpu"]?>x</span>4GHz</span>
					</div>
				</div>
				<!--end: Item-->
				<!--begin: Item-->
				<div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
					<span class="mr-4">
					<i class="flaticon-file-2 icon-2x text-muted font-weight-bold"></i>
					</span>
					<div class="d-flex flex-column text-dark-75">
						<span class="font-weight-bolder font-size-sm">Stockage</span>
						<span class="font-weight-bolder font-size-h5"><?=$offers[$server["type"]]["ssd"]?> <span class="text-dark-50 font-weight-bold">Go</span></span>
					</div>
				</div>
				<!--end: Item-->
			</div>
			<!--end::Bottom-->
		</div>
	</div>
	<!--end::Card-->
<?php
	}
} else {
?>
	<div class="alert alert-warning">
		Vous n'avez aucun serveur. <a href="/" title="Accueil">Cliquez ici pour en commander un.</a>
	</div>
<?php
}
?>
</div>
<!--end::Container-->
<?php
require "inc/Layout/Panel/End.php";