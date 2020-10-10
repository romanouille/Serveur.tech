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

$config = $server->getConfig();
$breadcrumb = "Serveur #{$_GET["id"]} | Console";

if (count($_POST) > 0) {
	$messages = [];
	
	if (!isset($_POST["token"]) || !is_string($_POST["token"]) || $_POST["token"] != $token) {
		$messages[] = "Le formulaire est invalide.";
	}
	
	if (!$captcha->check()) {
		$messages[] = "Vous devez prouver que vous n'êtes pas un robot.";
	}
	
	if (!isset($_POST["command"]) || !is_string($_POST["command"]) || empty(trim($_POST["command"]))) {
		$messages[] = "Vous devez spécifier la commande à envoyer.";
	} else {
		$_POST["command"] = trim($_POST["command"]);
	}
	
	if (empty($messages)) {
		$server = new Server($_GET["id"], false, $server->getRconPassword());
		$server->rcon->sendCommand($_POST["command"]);
		$messages[] = "La commande a été exécutée.";
		
		sleep(3);
	}
}

$server->sshAuth();

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

	<pre>
<?=$server->loadConsole()?>
	</pre>
	
	<form method="post">
		<input type="hidden" name="token" value="<?=$token?>">
		<?=$captcha->create()?><br>
		<div class="row">
			<div class="col-md-11">
				<input type="text" name="command" class="form-control" placeholder="Commande à exécuter">
			</div>
			
			<div class="col-md-1">
				<button type="button" class="btn btn-light-primary font-weight-bold btn-sm" onclick="this.disabled=true;document.getElementsByTagName('form')[0].submit()">Valider</button>
			</div>
		</div>
	</form>
</div>

<?php
require "inc/Layout/Panel/Tabs_end.php";
require "inc/Layout/Panel/End.php";