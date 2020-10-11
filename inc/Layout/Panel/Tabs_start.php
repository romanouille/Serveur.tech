<div class=" container ">
	<!--begin::Card-->
	<div class="card card-custom gutter-b">
		<div class="card-body">
			<div class="d-flex">
				<!--begin: Info-->
				<div class="flex-grow-1">
					<!--begin: Title-->
					<div class="d-flex align-items-center justify-content-between flex-wrap">
						<div class="mr-3">
							<!--begin::Name-->
							<a href="#" class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3">
							Serveur #<?=$_GET["id"]?>
							</a>
							<!--end::Name-->
							<!--begin::Contacts-->
							<div class="d-flex flex-wrap my-2">
								<a href="#" class="text-muted text-hover-primary font-weight-bold">
									<span class="svg-icon svg-icon-md svg-icon-gray-500 mr-1">
										<!--begin::Svg Icon | path:assets/media/svg/icons/Map/Marker2.svg-->
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
												<rect x="0" y="0" width="24" height="24"></rect>
												<path d="M9.82829464,16.6565893 C7.02541569,15.7427556 5,13.1079084 5,10 C5,6.13400675 8.13400675,3 12,3 C15.8659932,3 19,6.13400675 19,10 C19,13.1079084 16.9745843,15.7427556 14.1717054,16.6565893 L12,21 L9.82829464,16.6565893 Z M12,12 C13.1045695,12 14,11.1045695 14,10 C14,8.8954305 13.1045695,8 12,8 C10.8954305,8 10,8.8954305 10,10 C10,11.1045695 10.8954305,12 12,12 Z" fill="#000000"></path>
											</g>
										</svg>
										<!--end::Svg Icon-->
									</span>
									<?=$offers[$config["type"]]["location"]?>
								</a>
							</div>
							<!--end::Contacts-->
						</div>
						<div class="my-lg-0 my-1">
<?php
if ($server->isStarted()) {
?>
							<a href="#" class="btn btn-sm btn-info font-weight-bolder text-uppercase">Redémarrer</a>
							<a href="#" class="btn btn-sm btn-info font-weight-bolder text-uppercase">Redémarrage forcé</a>
							<a href="#" class="btn btn-sm btn-info font-weight-bolder text-uppercase">Arrêter</a>
							<a href="#" class="btn btn-sm btn-info font-weight-bolder text-uppercase">Arrêt forcé</a>
<?php
} else {
?>
							<a href="#" class="btn btn-sm btn-info font-weight-bolder text-uppercase">Démarrer</a>
<?php
}
?>
						</div>
					</div>
					<!--end: Title-->
					<!--begin: Content-->
					<div class="d-flex align-items-center flex-wrap justify-content-between">
						<div class="flex-grow-1 font-weight-bold text-dark-50 py-5 py-lg-2 mr-5">
							Lorem ipsum dolor sit amet.
						</div>
						<div class="d-flex flex-wrap align-items-center py-2">
							<div class="d-flex align-items-center mr-10">
								<div class="mr-6">
									<div class="font-weight-bold mb-2">Date de création</div>
									<span class="btn btn-sm btn-text btn-light-primary text-uppercase font-weight-bold">3 août 2020</span>
								</div>
								<div class="">
									<div class="font-weight-bold mb-2">Date d'expiration</div>
									<span class="btn btn-sm btn-text btn-light-danger text-uppercase font-weight-bold">3 septembre 2020</span>
								</div>
							</div>
						</div>
					</div>
					<!--end: Content-->
				</div>
				<!--end: Info-->
			</div>
			<div class="separator separator-solid my-7"></div>
			<!--begin: Items-->
			<div class="d-flex align-items-center flex-wrap">
				<!--begin: Item-->
				<div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
					<span class="mr-4">
					<i class="flaticon-piggy-bank icon-2x text-muted font-weight-bold"></i>
					</span>
					<div class="d-flex flex-column text-dark-75">
						<span class="font-weight-bolder font-size-sm">Coût</span>
						<span class="font-weight-bolder font-size-h5">2.99<span class="text-dark-50 font-weight-bold">€</span></span>
					</div>
				</div>
				<!--end: Item-->
				<!--begin: Item-->
				<div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
					<span class="mr-4">
					<i class="flaticon-confetti icon-2x text-muted font-weight-bold"></i>
					</span>
					<div class="d-flex flex-column text-dark-75">
						<span class="font-weight-bolder font-size-sm">CPU</span>
						<span class="font-weight-bolder font-size-h5"><span class="text-dark-50 font-weight-bold">1x</span>2.4GHz</span>
					</div>
				</div>
				<!--end: Item-->
				<!--begin: Item-->
				<div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
					<span class="mr-4">
					<i class="flaticon-pie-chart icon-2x text-muted font-weight-bold"></i>
					</span>
					<div class="d-flex flex-column text-dark-75">
						<span class="font-weight-bolder font-size-sm">RAM</span>
						<span class="font-weight-bolder font-size-h5">1<span class="text-dark-50 font-weight-bold"> Go</span></span>
					</div>
				</div>
				<!--end: Item-->
				<!--begin: Item-->
				<div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
					<span class="mr-4">
					<i class="flaticon-file-2 icon-2x text-muted font-weight-bold"></i>
					</span>
					<div class="d-flex flex-column flex-lg-fill">
						<span class="text-dark-75 font-weight-bolder font-size-sm">Stockage</span>
						<span class="font-weight-bolder font-size-h5">100<span class="text-dark-50 font-weight-bold"> Go</span></span>
					</div>
				</div>
				<!--end: Item-->
			</div>
			<!--begin: Items-->
		</div>
	</div>
	<!--end::Card-->
	<!--begin::Card-->
	<div class="card card-custom gutter-bs">
		<!--begin::Header-->
		<div class="card-header card-header-tabs-line">
			<div class="card-toolbar">
				<ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
					<li class="nav-item mr-3">
						<a class="nav-link<?=$_SERVER["PHP_SELF"] == "/ServerSettings.php" ? " active" : ""?>" href="/ServerSettings.php?id=<?=$_GET["id"]?>">
							<span class="nav-text font-weight-bold"><i class="flaticon-settings icon-1x"></i> Paramètres</span>
						</a>
					</li>
					<li class="nav-item mr-3">
						<a class="nav-link<?=$_SERVER["PHP_SELF"] == "/ServerConsole.php" ? " active" : ""?>" href="/ServerConsole.php?id=<?=$_GET["id"]?>">
							<span class="nav-text font-weight-bold"><i class="flaticon2-console icon-1x"></i> Console</span>
						</a>
					</li>
					<li class="nav-item mr-3">
						<a class="nav-link<?=$_SERVER["PHP_SELF"] == "/ServerFtp.php" ? " active" : ""?>" href="/ServerFtp.php?id=<?=$_GET["id"]?>">
							<span class="nav-text font-weight-bold"><i class="flaticon2-files-and-folders icon-1x"></i> FTP</span>
						</a>
					</li>
					<li class="nav-item mr-3">
						<a class="nav-link<?=$_SERVER["PHP_SELF"] == "/ServerPlugins.php" ? " active" : ""?>" href="/ServerPlugins.php?id=<?=$_GET["id"]?>">
							<span class="nav-text font-weight-bold"><i class="flaticon2-menu-4 icon-1x"></i> Plugins</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<!--end::Header-->
		<!--begin::Body-->
		<div class="card-body px-0">
			<div class="tab-content pt-5">
				<div class="tab-pane active" role="tabpanel">
