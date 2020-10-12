<?php
set_include_path("../");
chdir("../");

require "inc/Admin.class.php";
require "inc/Init.php";

if (!isset($user) || !$_SESSION["2fa"]) {
	$_SESSION = [];
	
	header("Location: /Auth.php");
	exit;
}

if (!$_SESSION["admin"]) {
	http_response_code(403);
	require "inc/Pages/Error.php";
	exit;
}

if (!isset($_GET["phone"]) || !is_string($_GET["phone"]) || empty($_GET["phone"]) || !is_numeric($_GET["phone"])) {
	http_response_code(400);
	require "inc/Pages/Error.php";
	exit;
}

$ticketUser = new User($_GET["phone"]);
if (!$ticketUser->exists()) {
	http_response_code(404);
	require "inc/Pages/Error.php";
	exit;
}


if (count($_POST) > 0) {
	$messages = [];
	
	if (!isset($_POST["token"]) || !is_string($_POST["token"]) || $_POST["token"] != $token) {
		$messages[] = "Le formulaire est invalide.";
	}
	
	if (!isset($_POST["message"]) || !is_string($_POST["message"]) || empty(trim($_POST["message"]))) {
		$messages[] = "Vous devez spécifier le message à envoyer.";
	} else {
		$_POST["message"] = trim($_POST["message"]);
	}
	
	if (empty($messages)) {
		Admin::replyToTicket($_GET["phone"], $_POST["message"]);
	}
}

$messages = Admin::loadTicket($_GET["phone"]);
$userProfile = $user->getProfile();

$breadcrumb = "Ticket pour {$_GET["phone"]}";

require "inc/Layout/Panel/Start.php";
?>

<div class=" container ">
	<!--begin::Chat-->
	<div class="d-flex flex-row">
		<!--begin::Aside-->
		<div class="flex-row-auto offcanvas-mobile w-350px w-xl-400px offcanvas-mobile-on" id="kt_chat_aside">
			<!--begin::Card-->
			<div class="card card-custom">
				<!--begin::Body-->
				<div class="card-body">
					<!--begin:Users-->
					<div class="mt-7 scroll scroll-pull ps ps--active-y" style="height: 620px; overflow: hidden;">
						<!--begin:User-->
						<div class="d-flex align-items-center justify-content-between mb-5">
							<div class="d-flex align-items-center">
								<!--<div class="symbol symbol-circle symbol-50 mr-3">
									<img alt="Pic" src="assets/media/users/300_12.jpg">
								</div>-->
								<div class="d-flex flex-column">
									<a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-lg"><?=htmlspecialchars($userProfile["first_name"])." ".htmlspecialchars($userProfile["last_name"])?></a>
									<span class="text-muted font-weight-bold font-size-sm">Utilisateur</span>
								</div>
							</div>
							<!--<div class="d-flex flex-column align-items-end">
								<span class="text-muted font-weight-bold font-size-sm">35 mins</span>
							</div>-->
						</div>
						<!--end:User-->
						<!--<div class="ps__rail-x" style="left: 0px; bottom: 0px;">
							<div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
						</div>
						<div class="ps__rail-y" style="top: 0px; height: 620px; right: -2px;">
							<div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 300px;"></div>
						</div>-->
					</div>
					<!--end:Users-->
				</div>
				<!--end::Body-->
			</div>
			<!--end::Card-->
		</div>
		<div class="offcanvas-mobile-overlay"></div>
		<!--end::Aside-->
		<!--begin::Content-->
		<div class="flex-row-fluid ml-lg-8" id="kt_chat_content">
			<!--begin::Card-->
			<div class="card card-custom">
				<!--begin::Header-->
				<div class="card-header align-items-center px-4 py-3">
					<div class="text-left flex-grow-1">
					</div>
					<div class="text-center flex-grow-1">
						<div class="text-dark-75 font-weight-bold font-size-h5"><?=htmlspecialchars($userProfile["first_name"])." ".htmlspecialchars($userProfile["last_name"])?></div>
						<!--<div>
							<span class="label label-sm label-dot label-success"></span>
							<span class="font-weight-bold text-muted font-size-sm">Active</span>
						</div>-->
					</div>
					<div class="text-right flex-grow-1">
					</div>
				</div>
				<!--end::Header-->
				<!--begin::Body-->
				<div class="card-body">
					<!--begin::Scroll-->
					<div class="scroll scroll-pull ps ps--active-y" style="height: 500px; overflow: hidden;">
						<!--begin::Messages-->
						<div class="messages">
<?php
foreach ($messages as $message) {
	if ($message["owner"] != 0) {
?>
							<!--begin::Message In-->
							<div class="d-flex flex-column mb-5 align-items-start">
								<div class="d-flex align-items-center">
									<!--<div class="symbol symbol-circle symbol-40 mr-3">
										<img alt="Pic" src="assets/media/users/300_12.jpg">
									</div>-->
									<div>
										<a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6"><?=htmlspecialchars($userProfile["first_name"])." ".htmlspecialchars($userProfile["last_name"])?></a>
										<span class="text-muted font-size-sm"><?=date("d/m/Y H:i:s", $message["timestamp"])?></span>
									</div>
								</div>
								<div class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">
									<?=htmlspecialchars($message["content"])?>
								</div>
							</div>
							<!--end::Message In-->
<?php
	} else {
?>
							<!--begin::Message Out-->
							<div class="d-flex flex-column mb-5 align-items-end">
								<div class="d-flex align-items-center">
									<div>
										<span class="text-muted font-size-sm"><?=date("d/m/Y H:i:s", $message["timestamp"])?></span>
										<a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">Vous</a>
									</div>
									<!--<div class="symbol symbol-circle symbol-40 ml-3">
										<img alt="Pic" src="assets/media/users/300_21.jpg">
									</div>-->
								</div>
								<div class="mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">
									<?=htmlspecialchars($message["content"])?>
								</div>
							</div>
							<!--end::Message Out-->
<?php
	}
}
?>
						</div>
						<!--end::Messages-->
						<!--<div class="ps__rail-x" style="left: 0px; bottom: 0px;">
							<div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
						</div>
						<div class="ps__rail-y" style="top: 0px; height: 472px; right: -2px;">
							<div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 210px;"></div>
						</div>-->
					</div>
					<!--end::Scroll-->
				</div>
				<!--end::Body-->
				<!--begin::Footer-->
				
				<form method="post">
					<input type="hidden" name="token" value="<?=$token?>">
					<div class="card-footer align-items-center">
						<!--begin::Compose-->
						<textarea class="form-control border-0 p-0" name="message" rows="2" placeholder="Écrivez un message" autofocus></textarea>
						<div class="d-flex align-items-center justify-content-between mt-5">
							<!--<div class="mr-3">
								<a href="#" class="btn btn-clean btn-icon btn-md mr-1"><i class="flaticon2-photograph icon-lg"></i></a>
								<a href="#" class="btn btn-clean btn-icon btn-md"><i class="flaticon2-photo-camera  icon-lg"></i></a>
							</div>-->
							<div>
								<button type="submit" class="btn btn-primary btn-md text-uppercase font-weight-bold chat-send py-2 px-6">Envoyer</button>
							</div>
						</div>
						<!--begin::Compose-->
					</div>
				</form>
				<!--end::Footer-->
			</div>
			<!--end::Card-->
		</div>
		<!--end::Content-->
	</div>
	<!--end::Chat-->
</div>
<?php
require "inc/Layout/Panel/End.php";