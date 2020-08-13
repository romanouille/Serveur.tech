<?php
require "Pages/Layout/Start.php";
?>
<h1>Connexion</h1>
<?php
if (isset($messages)) {
?>
<div class="card red darken-4 white-text">
	<div class="card-content">
<?php
	foreach ($messages as $nb=>$message) {
		if ($nb > 0) {
			echo "<br>";
		}
		
		echo $message;
	}
?>
	</div>
</div>
<?php
}
?>
<form method="post">
	<div class="row">
		<div class="input-field col m6 s12">
			<input type="email" class="validate" name="email" placeholder="Adresse e-mail" value="<?=isset($_POST["email"]) && is_string($_POST["email"]) ? htmlspecialchars($_POST["email"]) : ""?>" required><br>
			<input type="password" class="validate" name="password" placeholder="Mot de passe" value="<?=isset($_POST["password"]) && is_string($_POST["password"]) ? htmlspecialchars($_POST["password"]) : ""?>" required><br>
			<?=$recaptcha->generate()?><br>
			<button type="submit" class="waves-effect waves-light btn">Valider</button><br><br>
			
			<a href="/account/register" title="Créer un compte" class="waves-effect waves-light btn">Créer un compte</a>
		</div>
	</div>
</form>
<?php
require "Pages/Layout/End.php";