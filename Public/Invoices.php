<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";

if (!isset($user) || !$session["has2fa"]) {
	header("Location: /Auth.php");
	exit;
}

$invoices = $user->getInvoicesList();

$breadcrumb = "Factures";

require "inc/Layout/Panel/Start.php";
?>
<!--begin::Container-->
<div class=" container ">
<?php
if (!empty($invoices)) {
	foreach ($invoices as $invoice) {
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
							Facture #<?=$invoice["id"]?> (<?=date("d/m/Y H:i:s", $invoice["timestamp"])?>)
							</a>
							<!--end::Name-->
						</div>
						<!--begin::User-->
						<!--begin::Actions-->
						<div class="my-lg-0 my-1">
							<a href="/Invoice.php?id=<?=$invoice["id"]?>" class="btn btn-sm btn-primary font-weight-bolder text-uppercase">Accéder</a>
						</div>
						<!--end::Actions-->
					</div>
					<!--end::Title-->
					<!--begin::Content-->
					<div class="d-flex align-items-center flex-wrap justify-content-between">
						<!--begin::Description-->
						<div class="flex-grow-1 font-weight-bold text-dark-50 py-2 py-lg-2 mr-5">
							Serveur Minecraft<br>
							<?=$offers[$invoice["type"]]["price"]?>€/mois<br>
							<?=$offers[$invoice["type"]]["ram"]?> Go RAM<br>
							<?=$offers[$invoice["type"]]["cpu"]?> coeurs CPU<br>
							<?=$offers[$invoice["type"]]["ssd"]?> Go SSD
						</div>
						<!--end::Description-->
					</div>
					<!--end::Content-->
				</div>
				<!--end::Info-->
			</div>
			<!--end::Top-->
		</div>
	</div>
	<!--end::Card-->
<?php
	}
} else {
?>
	<div class="alert alert-warning">
		Vous n'avez aucune facture.
	</div>
<?php
}
?>
</div>
<!--end::Container-->
<?php
require "inc/Layout/Panel/End.php";