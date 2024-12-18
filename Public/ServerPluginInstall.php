<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";
require "inc/Server.class.php";

if (!isset($user) || !$session["has2fa"]) {
	header("Location: /Auth.php");
	exit;
}

if (!isset($_GET["id"]) || !is_string($_GET["id"]) || !is_numeric($_GET["id"])) {
	http_response_code(400);
	require "inc/Pages/Panel_error.php";
	exit;
}

if (!isset($_GET["plugin"]) || !is_string($_GET["plugin"]) || !is_numeric($_GET["plugin"])) {
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

if (!Server::isPluginsAutoInstallAvailable()) {
	http_response_code(503);
	require "inc/Pages/Panel_error.php";
	exit;
}

if (!$server->pluginExists($_GET["plugin"])) {
	http_response_code(404);
	$errorMessage = "Ce plugin n'existe pas.";
	require "inc/Pages/Panel_error.php";
	exit;
}

$serverConfig = $server->getConfig();
$server->sshAuth();
$isStarted = $server->isStarted();
$pluginData = $server->getPluginData($_GET["plugin"]);

if (count($_POST) > 0) {
	$messages = [];
	
	if (!isset($_POST["token"]) || !is_string($_POST["token"]) || $_POST["token"] != $token) {
		$messages[] = "Le formulaire est invalide.";
	}
	
	if (!$captcha->check()) {
		$messages[] = "Vous devez prouver que vous n'êtes pas un robot.";
	}
	
	if (empty($messages)) {
		$server->installPlugin($_GET["plugin"]);
		$messages[] = "Le plugin a été installé. Pensez à redémarrer votre serveur.";
	}
}


$breadcrumb = "Plugin \"".htmlspecialchars($pluginData["name"])."\" | Serveur #{$_GET["id"]}";

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

	<h1><?=htmlspecialchars($pluginData["name"])?></h1><br>
	<?=htmlspecialchars($pluginData["description"])?>
	<?=!empty($pluginData["versions"]) ? "<br>Versions : {$pluginData["versions"]}" : ""?>
	<br><br>
	
	<form method="post">
		<input type="hidden" name="token" value="<?=$token?>">
		<?=$captcha->create()?><br>
		<button type="button" class="btn btn-light-primary font-weight-bold btn-sm" onclick="this.disabled=true;document.getElementsByTagName('form')[0].submit()">Installer</button>
</div>

<?php
require "inc/Layout/Panel/Tabs_end.php";
require "inc/Layout/Panel/End.php";