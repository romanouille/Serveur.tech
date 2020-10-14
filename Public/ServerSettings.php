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
	
	if (!isset($_POST["version"]) || !is_string($_POST["version"]) || empty(trim($_POST["version"]))) {
		$messages[] = "Vous devez spécifier la version du serveur à utiliser.";
	} else {
		$_POST["version"] = explode("_", $_POST["version"]);
		if (count($_POST["version"]) == 2) {
			if (!isset($serversVersions[$_POST["version"][0]]) || !in_array($_POST["version"][1], $serversVersions[$_POST["version"][0]])) {
				$messages[] = "La version spécifiée est incorrecte.";
			}
		} else {
			$messages[] = "La version spécifiée est incorrecte.";
		}
	}
	
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
		if ($isStarted) {
			$server->rconAuth();
		}
		
		$server->updateServerProperties($_POST["rcon-password"], $_POST["motd"], $_POST["max-players"], $_POST["difficulty"], $_POST["level-name"], $_POST["level-seed"], $_POST["level-type"], $_POST["gamemode"], $_POST["white-list"], $_POST["online-mode"], $_POST["generate-structures"], $_POST["enable-command-block"], $_POST["allow-nether"], $_POST["pvp"], $_POST["spawn-npcs"], $_POST["spawn-monsters"], $_POST["spawn-animals"], $_POST["hardcore"]);
		$server->changeVersion($_POST["version"][0], $_POST["version"][1], in_array($_POST["version"][0], ["Forge"]));
		
		$messages[] = "Les paramètres ont été enregistrés.";
	}
}

$serverConfig = $server->getConfig();
$breadcrumb = "Serveur #{$_GET["id"]} | Paramètres";

require "inc/Layout/Panel/Start.php";
require "inc/Layout/Panel/Tabs_start.php";

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
	<input type="hidden" name="token" value="<?=$token?>">
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Version</label>
		<div class="col-lg-9 col-xl-6">
			<select name="version" class="form-control">
				<?php
foreach ($serversVersions as $type=>$versions) {
	foreach ($versions as $version) {
					?>
				<option value="<?=$type?>_<?=$version?>"<?=$serverConfig["version"] == $type."_".$version ? " selected" : ""?>><?=$type?> <?=$version?></option>
				<?php
	}
}
					?>								
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">MOTD</label>
		<div class="col-lg-9 col-xl-6">
			<input class="form-control form-control-lg form-control-solid" type="text" name="motd" value="<?=htmlspecialchars($serverConfig["motd"])?>" required>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Slots</label>
		<div class="col-lg-9 col-xl-6">
			<input class="form-control form-control-lg form-control-solid" type="number" name="max-players" value="<?=htmlspecialchars($serverConfig["max-players"])?>" required>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Difficulté</label>
		<div class="col-lg-9 col-xl-6">
			<select name="difficulty" class="form-control">
				<option value="peaceful"<?=$serverConfig["difficulty"] == "peaceful" ? " selected" : ""?>>Paisible</option>
				<option value="easy"<?=$serverConfig["difficulty"] == "easy" ? " selected" : ""?>>Facile</option>
				<option value="normal"<?=$serverConfig["difficulty"] == "normal" ? " selected" : ""?>>Normal</option>
				<option value="hard"<?=$serverConfig["difficulty"] == "hard" ? " selected" : ""?>>Difficile</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Nom de la map</label>
		<div class="col-lg-9 col-xl-6">
			<input class="form-control form-control-lg form-control-solid" type="text" name="level-name" value="<?=htmlspecialchars($serverConfig["level-name"])?>" required>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Seed de la map</label>
		<div class="col-lg-9 col-xl-6">
			<input class="form-control form-control-lg form-control-solid" type="text" name="level-seed" value="<?=htmlspecialchars($serverConfig["level-seed"])?>">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Type de map</label>
		<div class="col-lg-9 col-xl-6">
			<select name="level-type" class="form-control">
				<option value="default"<?=$serverConfig["level-type"] == "default" ? " selected" : ""?>>default</option>
				<option value="flat"<?=$serverConfig["level-type"] == "flat" ? " selected" : ""?>>flat</option>
				<option value="largeBiomes"<?=$serverConfig["level-type"] == "largeBiomes" ? " selected" : ""?>>largeBiomes</option>
				<option value="amplified"<?=$serverConfig["level-type"] == "amplified" ? " selected" : ""?>>amplified</option>
				<option value="buffet"<?=$serverConfig["level-type"] == "buffet" ? " selected" : ""?>>buffet</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Gamemode</label>
		<div class="col-lg-9 col-xl-6">
			<select name="gamemode" class="form-control">
				<option value="0"<?=$serverConfig["gamemode"] == 0 ? " selected" : ""?>>Survie (0)</option>
				<option value="1"<?=$serverConfig["gamemode"] == 1 ? " selected" : ""?>>Créatif (1)</option>
				<option value="2"<?=$serverConfig["gamemode"] == 2 ? " selected" : ""?>>Aventure (2)</option>
				<option value="3"<?=$serverConfig["gamemode"] == 3 ? " selected" : ""?>>Spectateur (3)</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Whitelist</label>
		<div class="col-lg-9 col-xl-6">
			<select name="white-list" class="form-control">
				<option value="1"<?=$serverConfig["white-list"] ? " selected" : ""?>>Activée</option>
				<option value="0"<?=!$serverConfig["white-list"] ? " selected" : ""?>>Désactivée</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Versions crackées</label>
		<div class="col-lg-9 col-xl-6">
			<select name="online-mode" class="form-control">
				<option value="1"<?=$serverConfig["online-mode"] ? " selected" : ""?>>Autorisées</option>
				<option value="0"<?=!$serverConfig["online-mode"] ? " selected" : ""?>>Interdites</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Génération des structures</label>
		<div class="col-lg-9 col-xl-6">
			<select name="generate-structures" class="form-control">
				<option value="1"<?=$serverConfig["generate-structures"] ? " selected" : ""?>>Activée</option>
				<option value="0"<?=!$serverConfig["generate-structures"] ? " selected" : ""?>>Désactivée</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Command blocks</label>
		<div class="col-lg-9 col-xl-6">
			<select name="enable-command-block" class="form-control">
				<option value="1"<?=$serverConfig["enable-command-block"] ? " selected" : ""?>>Activés</option>
				<option value="0"<?=!$serverConfig["enable-command-block"] ? " selected" : ""?>>Désactivés</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Nether</label>
		<div class="col-lg-9 col-xl-6">
			<select name="allow-nether" class="form-control">
				<option value="1"<?=$serverConfig["allow-nether"] ? " selected" : ""?>>Activé</option>
				<option value="0"<?=!$serverConfig["allow-nether"] ? " selected" : ""?>>Désactivé</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">PVP</label>
		<div class="col-lg-9 col-xl-6">
			<select name="pvp" class="form-control">
				<option value="1"<?=$serverConfig["pvp"] ? " selected" : ""?>>Activé</option>
				<option value="0"<?=!$serverConfig["pvp"] ? " selected" : ""?>>Désactivé</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Villageois</label>
		<div class="col-lg-9 col-xl-6">
			<select name="spawn-npcs" class="form-control">
				<option value="1"<?=$serverConfig["spawn-npcs"] ? " selected" : ""?>>Activés</option>
				<option value="0"<?=!$serverConfig["spawn-npcs"] ? " selected" : ""?>>Désactivés</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Monstres</label>
		<div class="col-lg-9 col-xl-6">
			<select name="spawn-monsters" class="form-control">
				<option value="1"<?=$serverConfig["spawn-monsters"] ? " selected" : ""?>>Activés</option>
				<option value="0"<?=!$serverConfig["spawn-monsters"] ? " selected" : ""?>>Désactivés</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Animaux</label>
		<div class="col-lg-9 col-xl-6">
			<select name="spawn-animals" class="form-control">
				<option value="1"<?=$serverConfig["spawn-animals"] ? " selected" : ""?>>Activés</option>
				<option value="0"<?=!$serverConfig["spawn-animals"] ? " selected" : ""?>>Désactivés</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Hardcore</label>
		<div class="col-lg-9 col-xl-6">
			<select name="hardcore" class="form-control">
				<option value="1"<?=$serverConfig["hardcore"] ? " selected" : ""?>>Activé</option>
				<option value="0"<?=!$serverConfig["hardcore"] ? " selected" : ""?>>Désactivé</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-3 col-lg-3 col-form-label text-right">Mot de passe RCON</label>
		<div class="col-lg-9 col-xl-6">
			<input class="form-control form-control-lg form-control-solid" type="text" name="rcon-password" value="<?=htmlspecialchars($serverConfig["rcon_password"])?>" required>
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
<?php
require "inc/Layout/Panel/Tabs_end.php";
require "inc/Layout/Panel/End.php";