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

$server->sshAuth();
$isStarted = $server->isStarted();

if (count($_POST) > 0) {
	$messages = [];
	
	if (!isset($_POST["token"]) || !is_string($_POST["token"]) || $_POST["token"] != $token) {
		$messages[] = "Le formulaire est invalide.";
	}
	
	if (!$captcha->check()) {
		$messages[] = "Vous devez prouver que vous n'êtes pas un robot.";
	}
	
	if (empty($messages)) {
		$server->resetSshPassword();
		$messages[] = "Le mot de passe FTP a été regénéré.";
	}
}

$serverConfig = $server->getConfig();
$breadcrumb = "FTP | Serveur #{$_GET["id"]}";

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
	
	Serveur : <b><?=$serverConfig["ip"]?></b><br>
	Nom d'utilisateur : <b>user</b><br>
	Mot de passe : <b><?=$serverConfig["ssh_password"]?></b>
	<br><br>
	
	<a href="/net2ftp/" class="btn btn-light-primary font-weight-bold btn-sm" target="_blank">Cliquez ici pour accéder au WebFTP</a>
	<br><br>
	
	<form method="post">
		<input type="hidden" name="token" value="<?=$token?>">
		<?=$captcha->create()?><br>
		
		<button type="button" class="btn btn-light-primary font-weight-bold btn-sm" onclick="this.disabled=true;document.getElementsByTagName('form')[0].submit()">Cliquez ici pour regénérer un mot de passe</button>
	</form>
</div>

<?php
require "inc/Layout/Panel/Tabs_end.php";
require "inc/Layout/Panel/End.php";