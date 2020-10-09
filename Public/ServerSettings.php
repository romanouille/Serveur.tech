<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";
require "inc/Server.class.php";

if (!isset($user) || !$_SESSION["2fa"]) {
	$_SESSION = [];
	
	header("Location: /Auth.php");
	exit;
}

if (!isset($_GET["id"]) || !is_string($_GET["id"]) || !is_numeric($_GET["id"])) {
	http_response_code(400);
	require "inc/Pages/Error.php";
	exit;
}

$server = new Server($_GET["id"]);
if (!$server->exists()) {
	http_response_code(404);
	require "inc/Pages/Error.php";
	exit;
}

if (!$user->hasServer($_GET["id"])) {
	http_response_code(403);
	require "inc/Pages/Error.php";
	exit;
}

if (count($_POST) > 0) {
	$messages = [];
	
	if (!isset($_POST["motd"]) || !is_string($_POST["motd"]) || empty(trim($_POST["motd"]))) {
		$messages[] = "Vous devez spécifier le MOTD.";
	} else {
		$_POST["motd"] = normalizeString(trim($_POST["motd"]), "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789àâäéèêëïîôöùûüÿçÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ§&é\"'(-è_çà)^$*ù!:;,\/°+£¨µ%§.? ");
		
		if (empty($_POST["motd"])) {
			$messages[] = "Les caractères spécifiés dans le MOTD ne sont pas autorisés.";
		} elseif (strlen($_POST["motd"]) > 255) {
			$messages[] = "Le MOTD ne doit pas dépasser 255 caractères.";
		}
	}
	
	if (!isset($_POST["max-players"]) || !is_string($_POST["max-players"]) || !is_numeric($_POST["max-players"])) {
		$messages[] = "Vous devez spécifier le nombre de slots.";
	} elseif ($_POST["max-players"] < 1 || $_POST["max-players"] > 9999) {
		$messages[] = "Le nombre de slots doit être supérieur ou égal à 1 et inférieur ou égal à 9999.";
	}
	
	if (!isset($_POST["difficulty"]) || !is_string($_POST["difficulty"]) || empty(trim($_POST["difficulty"]))) {
		$messages[] = "Vous devez spécifier la difficulté.";
	} elseif (!in_array($_POST["difficulty"], ["peaceful", "easy", "normal", "hard"])) {
		$messages[] = "La difficulté spécifiée est incorrecte.";
	}
	
	if (!isset($_POST["level-name"]) || !is_string($_POST["level-name"]) || empty(trim($_POST["level-name"]))) {
		$messages[] = "Vous devez spécifier le nom de la map.";
	} else {
		$_POST["level-name"] = trim(normalizeString($_POST["level-name"], "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"));
		
		if (empty($_POST["level-name"])) {
			$messages[] = "Les caractères du nom de la map doivent être alphanumériques.";
		}
	}
	
	if (isset($_POST["level-seed"]) && is_string($_POST["level-seed"]) && !empty($_POST["level-seed"])) {
		$_POST["level-seed"] = trim($_POST["level-seed"]);
		
		if (empty($_POST["level-seed"])) {
			$messages[] = "Le seed spécifié est incorrect.";
		}
	}
	
	if (!isset($_POST["level-type"]) || !is_string($_POST["level-type"]) || empty(trim($_POST["level-type"]))) {
		$messages[] = "Vous devez spécifier le type de map à utiliser.";
	} elseif (!in_array($_POST["level-type"], ["default", "flat", "largeBiomes", "amplified", "buffet"])) {
		$messages[] = "Le type de map spécifié est incorrect.";
	}
	
	if (!isset($_POST["gamemode"]) || !is_string($_POST["gamemode"])) {
		$messages[] = "Vous devez spécifier le gamemode.";
	} elseif (!in_array($_POST["gamemode"], [0, 1, 2, 3])) {
		$messages[] = "Le gamemode spécifié est incorrect.";
	}
	
	if (!isset($_POST["white-list"]) || !is_string($_POST["white-list"]) || !in_array($_POST["white-list"], [0, 1])) {
		$messages[] = "Vous devez spécifier si la whitelist doit être activée ou non.";
	}
	
	if (!isset($_POST["online-mode"]) || !is_string($_POST["online-mode"]) || !in_array($_POST["online-mode"], [0, 1])) {
		$messages[] = "Vous devez spécifier si les versions crackées sont autorisées ou non.";
	}
	
	if (!isset($_POST["generate-structures"]) || !is_string($_POST["generate-structures"]) || !in_array($_POST["generate-structures"], [0, 1])) {
		$messages[] = "Vous devez spécifier si le serveur doit générer des structures ou non.";
	}
	
	if (!isset($_POST["enable-command-block"]) || !is_string($_POST["enable-command-block"]) || !in_array($_POST["enable-command-block"], [0, 1])) {
		$messages[] = "Vous devez spécifier si les command blocks sont activés ou non.";
	}
	
	if (!isset($_POST["allow-nether"]) || !is_string($_POST["allow-nether"]) || !in_array($_POST["allow-nether"], [0, 1])) {
		$messages[] = "Vous devez spécifier si le nether est activé ou non.";
	}
	
	if (!isset($_POST["pvp"]) || !is_string($_POST["pvp"]) || !in_array($_POST["pvp"], [0, 1])) {
		$messages[] = "Vous devez spécifier si le PVP est activé ou non.";
	}
	
	if (!isset($_POST["spawn-npcs"]) || !is_string($_POST["spawn-npcs"]) || !in_array($_POST["spawn-npcs"], [0, 1])) {
		$messages[] = "Vous devez spécifier si les villageois sont activés ou non.";
	}
	
	if (!isset($_POST["spawn-animals"]) || !is_string($_POST["spawn-animals"]) || !in_array($_POST["spawn-animals"], [0, 1]));
	
	if (!isset($_POST["hardcore"]) || !is_string($_POST["hardcore"]) || !in_array($_POST["hardcore"], [0, 1])) {
		$messages[] = "Vous devez spécifier si le mode hardcore est activé ou non.";
	}
	
	if (!isset($_POST["rcon-password"]) || !is_string($_POST["rcon-password"]) || empty(trim($_POST["rcon-password"]))) {
		$messages[] = "Vous devez spécifier le mot de passe RCON.";
	} else {
		$_POST["rcon-password"] = normalizeString($_POST["rcon-password"], "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
		if (empty($_POST["rcon-password"])) {
			$messages[] = "Le mot de passe RCON doit être composé uniquement de caractères alphanumériques.";
		} elseif (strlen($_POST["rcon-password"]) > 32) {
			$messages[] = "Le mot de passe RCON doit se composer d'au maximum 32 caractères.";
		}
	}
	
	if (!$captcha->check()) {
		$messages[] = "Vous devez prouver que vous n'êtes pas un robot.";
	}
	
	if (empty($messages)) {
		$server = new Server($_GET["id"], true);
		$server->updateServerProperties($_POST["rcon-password"], $_POST["motd"], $_POST["max-players"], $_POST["difficulty"], $_POST["level-name"], $_POST["level-seed"], $_POST["level-type"], $_POST["gamemode"], $_POST["white-list"], $_POST["online-mode"], $_POST["generate-structures"], $_POST["enable-command-block"], $_POST["allow-nether"], $_POST["pvp"], $_POST["spawn-npcs"], $_POST["spawn-monsters"], $_POST["spawn-animals"], $_POST["hardcore"]);
		
		$messages[] = "Les paramètres ont été enregistrés.";
	}
}

$config = $server->getConfig();
$breadcrumb = "Serveur #{$_GET["id"]}";

require "inc/Layout/Panel/Start.php";
?>
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
							<a href="#" class="btn btn-sm btn-light-success font-weight-bolder text-uppercase mr-3">Réinstaller</a>
							<a href="#" class="btn btn-sm btn-info font-weight-bolder text-uppercase">Changer d'offre</a>
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
						<a class="nav-link active" data-toggle="tab" href="#kt_apps_projects_view_tab_3">
							<span class="nav-icon mr-2">
								<span class="svg-icon mr-3">
									<!--begin::Svg Icon | path:assets/media/svg/icons/Devices/Display1.svg-->
									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
										<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
											<rect x="0" y="0" width="24" height="24"></rect>
											<path d="M11,20 L11,17 C11,16.4477153 11.4477153,16 12,16 C12.5522847,16 13,16.4477153 13,17 L13,20 L15.5,20 C15.7761424,20 16,20.2238576 16,20.5 C16,20.7761424 15.7761424,21 15.5,21 L8.5,21 C8.22385763,21 8,20.7761424 8,20.5 C8,20.2238576 8.22385763,20 8.5,20 L11,20 Z" fill="#000000" opacity="0.3"></path>
											<path d="M3,5 L21,5 C21.5522847,5 22,5.44771525 22,6 L22,16 C22,16.5522847 21.5522847,17 21,17 L3,17 C2.44771525,17 2,16.5522847 2,16 L2,6 C2,5.44771525 2.44771525,5 3,5 Z M4.5,8 C4.22385763,8 4,8.22385763 4,8.5 C4,8.77614237 4.22385763,9 4.5,9 L13.5,9 C13.7761424,9 14,8.77614237 14,8.5 C14,8.22385763 13.7761424,8 13.5,8 L4.5,8 Z M4.5,10 C4.22385763,10 4,10.2238576 4,10.5 C4,10.7761424 4.22385763,11 4.5,11 L7.5,11 C7.77614237,11 8,10.7761424 8,10.5 C8,10.2238576 7.77614237,10 7.5,10 L4.5,10 Z" fill="#000000"></path>
										</g>
									</svg>
									<!--end::Svg Icon-->
								</span>
							</span>
							<span class="nav-text font-weight-bold">Contrôle</span>
						</a>
					</li>
					<li class="nav-item mr-3">
						<a class="nav-link" data-toggle="tab" href="#kt_apps_projects_view_tab_4">
							<span class="nav-icon mr-2">
								<span class="svg-icon mr-3">
									<!--begin::Svg Icon | path:assets/media/svg/icons/Home/Globe.svg-->
									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
										<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
											<rect x="0" y="0" width="24" height="24"></rect>
											<path d="M13,18.9450712 L13,20 L14,20 C15.1045695,20 16,20.8954305 16,22 L8,22 C8,20.8954305 8.8954305,20 10,20 L11,20 L11,18.9448245 C9.02872877,18.7261967 7.20827378,17.866394 5.79372555,16.5182701 L4.73856106,17.6741866 C4.36621808,18.0820826 3.73370941,18.110904 3.32581341,17.7385611 C2.9179174,17.3662181 2.88909597,16.7337094 3.26143894,16.3258134 L5.04940685,14.367122 C5.46150313,13.9156769 6.17860937,13.9363085 6.56406875,14.4106998 C7.88623094,16.037907 9.86320756,17 12,17 C15.8659932,17 19,13.8659932 19,10 C19,7.73468744 17.9175842,5.65198725 16.1214335,4.34123851 C15.6753081,4.01567657 15.5775721,3.39010038 15.903134,2.94397499 C16.228696,2.49784959 16.8542722,2.4001136 17.3003976,2.72567554 C19.6071362,4.40902808 21,7.08906798 21,10 C21,14.6325537 17.4999505,18.4476269 13,18.9450712 Z" fill="#000000" fill-rule="nonzero"></path>
											<circle fill="#000000" opacity="0.3" cx="12" cy="10" r="6"></circle>
										</g>
									</svg>
									<!--end::Svg Icon-->
								</span>
							</span>
							<span class="nav-text font-weight-bold">Paramètres</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#kt_apps_projects_view_tab_1">
							<span class="nav-icon mr-2">
								<span class="svg-icon mr-3">
									<!--begin::Svg Icon | path:assets/media/svg/icons/General/Notification2.svg-->
									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
										<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
											<rect x="0" y="0" width="24" height="24"></rect>
											<path d="M13.2070325,4 C13.0721672,4.47683179 13,4.97998812 13,5.5 C13,8.53756612 15.4624339,11 18.5,11 C19.0200119,11 19.5231682,10.9278328 20,10.7929675 L20,17 C20,18.6568542 18.6568542,20 17,20 L7,20 C5.34314575,20 4,18.6568542 4,17 L4,7 C4,5.34314575 5.34314575,4 7,4 L13.2070325,4 Z" fill="#000000"></path>
											<circle fill="#000000" opacity="0.3" cx="18.5" cy="5.5" r="2.5"></circle>
										</g>
									</svg>
									<!--end::Svg Icon-->
								</span>
							</span>
							<span class="nav-text font-weight-bold">Notifications</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<!--end::Header-->
		<!--begin::Body-->
		<div class="card-body px-0">
			<div class="tab-content pt-5">
				<!--begin::Tab Content-->
				<!--end::Tab Content-->
				<!--begin::Tab Content-->
				<div class="tab-pane active" id="kt_apps_projects_view_tab_3" role="tabpanel">
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
?>
					<form method="post" class="form">
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Version</label>
							<div class="col-lg-9 col-xl-6">
								<select name="version" class="form-control">
<?php
foreach ($serversVersions as $type=>$versions) {
	foreach ($versions as $version) {
?>
								<option value="<?=$type?>_<?=$version?>"<?=$config["version"] == $type."_".$version ? " selected" : ""?>><?=$type?> <?=$version?></option>
<?php
	}
}
?>								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">MOTD</label>
							<div class="col-lg-9 col-xl-6">
								<input class="form-control form-control-lg form-control-solid" type="text" name="motd" value="<?=htmlspecialchars($config["motd"])?>" required>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Slots</label>
							<div class="col-lg-9 col-xl-6">
								<input class="form-control form-control-lg form-control-solid" type="number" name="max-players" value="<?=htmlspecialchars($config["max-players"])?>" required>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Difficulté</label>
							<div class="col-lg-9 col-xl-6">
								<select name="difficulty" class="form-control">
									<option value="peaceful"<?=$config["difficulty"] == "peaceful" ? " selected" : ""?>>Paisible</option>
									<option value="easy"<?=$config["difficulty"] == "easy" ? " selected" : ""?>>Facile</option>
									<option value="normal"<?=$config["difficulty"] == "normal" ? " selected" : ""?>>Normal</option>
									<option value="hard"<?=$config["difficulty"] == "hard" ? " selected" : ""?>>Difficile</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Nom de la map</label>
							<div class="col-lg-9 col-xl-6">
								<input class="form-control form-control-lg form-control-solid" type="text" name="level-name" value="<?=htmlspecialchars($config["level-name"])?>" required>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Seed de la map</label>
							<div class="col-lg-9 col-xl-6">
								<input class="form-control form-control-lg form-control-solid" type="text" name="level-seed" value="<?=htmlspecialchars($config["level-seed"])?>">
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Type de map</label>
							<div class="col-lg-9 col-xl-6">
								<select name="level-type" class="form-control">
									<option value="default"<?=$config["level-type"] == "default" ? " selected" : ""?>>default</option>
									<option value="flat"<?=$config["level-type"] == "flat" ? " selected" : ""?>>flat</option>
									<option value="largeBiomes"<?=$config["level-type"] == "largeBiomes" ? " selected" : ""?>>largeBiomes</option>
									<option value="amplified"<?=$config["level-type"] == "amplified" ? " selected" : ""?>>amplified</option>
									<option value="buffet"<?=$config["level-type"] == "buffet" ? " selected" : ""?>>buffet</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Gamemode</label>
							<div class="col-lg-9 col-xl-6">
								<select name="gamemode" class="form-control">
									<option value="0"<?=$config["gamemode"] == 0 ? " selected" : ""?>>Survie (0)</option>
									<option value="1"<?=$config["gamemode"] == 1 ? " selected" : ""?>>Créatif (1)</option>
									<option value="2"<?=$config["gamemode"] == 2 ? " selected" : ""?>>Aventure (2)</option>
									<option value="3"<?=$config["gamemode"] == 3 ? " selected" : ""?>>Spectateur (3)</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Whitelist</label>
							<div class="col-lg-9 col-xl-6">
								<select name="white-list" class="form-control">
									<option value="1"<?=$config["white-list"] ? " selected" : ""?>>Activée</option>
									<option value="0"<?=!$config["white-list"] ? " selected" : ""?>>Désactivée</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Versions crackées</label>
							<div class="col-lg-9 col-xl-6">
								<select name="online-mode" class="form-control">
									<option value="1"<?=$config["online-mode"] ? " selected" : ""?>>Autorisées</option>
									<option value="0"<?=!$config["online-mode"] ? " selected" : ""?>>Interdites</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Génération des structures</label>
							<div class="col-lg-9 col-xl-6">
								<select name="generate-structures" class="form-control">
									<option value="1"<?=$config["generate-structures"] ? " selected" : ""?>>Activée</option>
									<option value="0"<?=!$config["generate-structures"] ? " selected" : ""?>>Désactivée</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Command blocks</label>
							<div class="col-lg-9 col-xl-6">
								<select name="enable-command-block" class="form-control">
									<option value="1"<?=$config["enable-command-block"] ? " selected" : ""?>>Activés</option>
									<option value="0"<?=!$config["enable-command-block"] ? " selected" : ""?>>Désactivés</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Nether</label>
							<div class="col-lg-9 col-xl-6">
								<select name="allow-nether" class="form-control">
									<option value="1"<?=$config["allow-nether"] ? " selected" : ""?>>Activé</option>
									<option value="0"<?=!$config["allow-nether"] ? " selected" : ""?>>Désactivé</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">PVP</label>
							<div class="col-lg-9 col-xl-6">
								<select name="pvp" class="form-control">
									<option value="1"<?=$config["pvp"] ? " selected" : ""?>>Activé</option>
									<option value="0"<?=!$config["pvp"] ? " selected" : ""?>>Désactivé</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Villageois</label>
							<div class="col-lg-9 col-xl-6">
								<select name="spawn-npcs" class="form-control">
									<option value="1"<?=$config["spawn-npcs"] ? " selected" : ""?>>Activés</option>
									<option value="0"<?=!$config["spawn-npcs"] ? " selected" : ""?>>Désactivés</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Monstres</label>
							<div class="col-lg-9 col-xl-6">
								<select name="spawn-monsters" class="form-control">
									<option value="1"<?=$config["spawn-monsters"] ? " selected" : ""?>>Activés</option>
									<option value="0"<?=!$config["spawn-monsters"] ? " selected" : ""?>>Désactivés</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Animaux</label>
							<div class="col-lg-9 col-xl-6">
								<select name="spawn-animals" class="form-control">
									<option value="1"<?=$config["spawn-animals"] ? " selected" : ""?>>Activés</option>
									<option value="0"<?=!$config["spawn-animals"] ? " selected" : ""?>>Désactivés</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Hardcore</label>
							<div class="col-lg-9 col-xl-6">
								<select name="hardcore" class="form-control">
									<option value="1"<?=$config["hardcore"] ? " selected" : ""?>>Activé</option>
									<option value="0"<?=!$config["hardcore"] ? " selected" : ""?>>Désactivé</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label text-right">Mot de passe RCON</label>
							<div class="col-lg-9 col-xl-6">
								<input class="form-control form-control-lg form-control-solid" type="text" name="rcon-password" value="<?=htmlspecialchars($config["rcon_password"])?>" required>
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
				</div>
				<!--end::Tab Content-->
				<!--begin::Tab Content-->
				<div class="tab-pane" id="kt_apps_projects_view_tab_4" role="tabpanel">
					<div class="row">
						<div class="col-lg-9 col-xl-6 offset-xl-3">
							<!--begin::Notice-->
							<div class="alert alert-custom alert-light-success fade show mb-9" role="alert">
								<div class="alert-icon"><i class="flaticon-warning"></i></div>
								<div class="alert-text">Lorem ipsum dolor sit amet.</div>
								<div class="alert-close">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true"><i class="ki ki-close"></i></span>
									</button>
								</div>
							</div>
							<!--end::Notice-->
						</div>
					</div>
				</div>
				<!--end::Tab Content-->
				<!--begin::Tab Content-->
				<div class="tab-pane" id="kt_apps_projects_view_tab_1" role="tabpanel">
					<div class="container">
						<div class="timeline timeline-3">
							<div class="timeline-items">
								<div class="timeline-item">
									<div class="timeline-media">
										<i class="flaticon2-shield text-danger"></i>
									</div>
									<div class="timeline-content">
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div class="mr-2">
												<a href="#" class="text-dark-75 text-hover-primary font-weight-bold">
												Lorem ipsum dolor sit amet.
												</a>
												<span class="text-muted ml-2">
												Aujourd'hui
												</span>
												<span class="label label-light-success font-weight-bolder label-inline ml-2">nouveau</span>
											</div>
										</div>
										<p class="p-0">
											Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,
											totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto.
										</p>
									</div>
								</div>
								<div class="timeline-item">
									<div class="timeline-media">
										<i class="flaticon2-shield text-danger"></i>
									</div>
									<div class="timeline-content">
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div class="mr-2">
												<a href="#" class="text-dark-75 text-hover-primary font-weight-bold">
												Lorem ipsum dolor sit amet.
												</a>
												<span class="text-muted ml-2">
												Aujourd'hui
												</span>
												<span class="label label-light-danger font-weight-bolder label-inline ml-2">en attente</span>
											</div>
										</div>
										<p class="p-0">
											Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,
											totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto.
										</p>
									</div>
								</div>
								<div class="timeline-item">
									<div class="timeline-media">
										<i class="flaticon2-layers text-warning"></i>
									</div>
									<div class="timeline-content">
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div class="mr-2">
												<a href="#" class="text-dark-75 text-hover-primary font-weight-bold">
												Lorem ipsum dolor sit amet.
												</a>
												<span class="text-muted ml-2">
												Aujourd'hui
												</span>
												<span class="label label-light-warning font-weight-bolder label-inline ml-2">terminé</span>
											</div>
										</div>
										<p class="p-0">
											Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,
											totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto.
										</p>
									</div>
								</div>
								<div class="timeline-item">
									<div class="timeline-media">
										<i class="flaticon2-notification fl text-primary"></i>
									</div>
									<div class="timeline-content">
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div class="mr-2">
												<a href="#" class="text-dark-75 text-hover-primary font-weight-bold">
												Lorem ipsum dolor sit amet.
												</a>
												<span class="text-muted ml-2">
												Aujourd'hui
												</span>
												<span class="label label-light-primary font-weight-bolder label-inline ml-2">délivré</span>
											</div>
										</div>
										<p class="p-0">
											Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,
											totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto.
										</p>
									</div>
								</div>
							</div>
						</div>
						<!--end::Timeline-->
					</div>
				</div>
				<!--end::Tab Content-->
			</div>
		</div>
		<!--end::Body-->
	</div>
	<!--end::Card-->
</div>
<?php
require "inc/Layout/Panel/End.php";