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
	require "inc/Pages/Panel_error.php";
	exit;
}

$server = new Server($_GET["id"]);
if (!$server->exists()) {
	http_response_code(404);
	require "inc/Pages/Panel_error.php";
	exit;
}

if (!$user->hasServer($_GET["id"])) {
	http_response_code(403);
	require "inc/Pages/Panel_error.php";
	exit;
}

$server->sshAuth();
$isStarted = $server->isStarted();

if (count($_POST) > 0) {
	$messages = [];
	
	if (!isset($_POST["token"]) || !is_string($_POST["token"]) || $_POST["token"] != $token) {
		$messages[] = "Le formulaire est invalide.";
	}
	
	if (!isset($_POST["search"]) || !is_string($_POST["search"]) || empty(trim($_POST["search"]))) {
		$messages[] = "Vous devez spécifier le nom du plugin à rechercher.";
	} else {
		$_POST["search"] = trim($_POST["search"]);
	}
	
	if (!$captcha->check()) {
		$messages[] = "Vous devez prouver que vous n'êtes pas un robot.";
	}
	
	if (empty($messages)) {
		$result = $server->searchPlugins($_POST["search"]);
	}
}

$serverConfig = $server->getConfig();
$breadcrumb = "Serveur #{$_GET["id"]} | Plugins";

require "inc/Layout/Panel/Start.php";
require "inc/Layout/Panel/Tabs_start.php";
?>

<div class="container">
<?php
if (isset($messages) && !empty($messages)) {
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

	<form method="post">
		<input type="hidden" name="token" value="<?=$token?>">
		<div class="row">
			<div class="col-md-7">
				<input type="text" class="form-control" name="search" placeholder="Nom du plugin" value="<?=isset($_POST["search"]) && is_string($_POST["search"]) ? htmlspecialchars($_POST["search"]) : ""?>">
			</div>
			
			<div class="col-md-3">
				<?=$captcha->create()?>
			</div>
			
			<div class="col-md-1">
				<button type="button" class="btn btn-light-primary font-weight-bold btn-sm" onclick="this.disabled=true;document.getElementsByTagName('form')[0].submit()">Rechercher</button>
			</div>
		</div>
	</form><br>
	
<?php
if (isset($result)) {
	if (!empty($result)) {
?>
	
<?php
		foreach ($result as $value) {
?>
	<div class="d-flex align-items-center flex-grow-1">
		<div class="d-flex flex-wrap align-items-center justify-content-between w-100">
			<!--begin::Info-->
			<div class="d-flex flex-column align-items-cente py-2 w-75">
				<!--begin::Title-->
				<a href="https://www.spigotmc.org/resources/<?=$value["id"]?>" target="_blank" class="text-dark-75 font-weight-bold text-hover-primary font-size-lg mb-1"><?=htmlspecialchars($value["name"])?></a>
				<!--end::Title-->
				<!--begin::Data-->
				<span class="text-muted font-weight-bold"><?=htmlspecialchars($value["description"])?><?=!empty($value["versions"]) ? "<br>Versions : {$value["versions"]}" : ""?></span>
				<!--end::Data-->
			</div>
			<!--end::Info-->
			<!--begin::Label-->
			<a href="/ServerPluginInstall.php?id=<?=$_GET["id"]?>&plugin=<?=$value["id"]?>" class="btn btn-light-primary font-weight-bold btn-sm">Installer</a>
			<!--end::Label-->
		</div>
	</div>
<?php
		}
?>
<?php
	} else {
?>
	<div class="alert alert-info">Aucun résultat pour "<?=htmlspecialchars($_POST["search"])?>".</div>
<?php
	}
}
?>
</div>

<?php
require "inc/Layout/Panel/Tabs_end.php";
require "inc/Layout/Panel/End.php";