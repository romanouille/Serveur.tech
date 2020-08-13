<?php
require "Pages/Layout/Start.php";
?>
<h1>Créer un compte</h1>

<?php
if (isset($messages)) {
?>
<div class="card <?=$success ? "blue darken-4" : "red darken-4"?> white-text">
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

if (!isset($success) || !$success) {
?>
<form method="post">
	<div class="row">
		<div class="input-field col m6 s12">
			<input type="email" class="validate" name="email" placeholder="Adresse e-mail" maxlength="255" value="<?=isset($_POST["email"]) && is_string($_POST["email"]) ? htmlspecialchars($_POST["email"]) : ""?>" required><br>
			<input type="password" class="validate" name="password" placeholder="Mot de passe" maxlength="72" value="<?=isset($_POST["password"]) && is_string($_POST["password"]) ? htmlspecialchars($_POST["password"]) : ""?>" required><br>
			<input type="password" class="validate" name="password2" placeholder="Confirmez le mot de passe" maxlength="72" value="<?=isset($_POST["password2"]) && is_string($_POST["password2"]) ? htmlspecialchars($_POST["password2"]) : ""?>" required><br>
			<input type="text" class="validate" name="firstname" placeholder="Prénom" maxlength="255" value="<?=isset($_POST["firstname"]) && is_string($_POST["firstname"]) ? htmlspecialchars($_POST["firstname"]) : ""?>" required><br>
			<input type="text" class="validate" name="lastname" placeholder="Nom" maxlength="255" value="<?=isset($_POST["lastname"]) && is_string($_POST["lastname"]) ? htmlspecialchars($_POST["lastname"]) : ""?>" required><br>
			<input type="text" class="validate" name="country" placeholder="Pays" maxlength="255" value="<?=isset($_POST["country"]) && is_string($_POST["country"]) ? htmlspecialchars($_POST["country"]) : ""?>" required><br>
			<input type="text" class="validate" name="address" placeholder="Adresse" maxlength="255" value="<?=isset($_POST["address"]) && is_string($_POST["address"]) ? htmlspecialchars($_POST["address"]) : ""?>" required><br>
			<input type="text" class="validate" name="postalcode" placeholder="Code postal" maxlength="255" value="<?=isset($_POST["postalcode"]) && is_string($_POST["postalcode"]) ? htmlspecialchars($_POST["postalcode"]) : ""?>" required><br>
			<input type="text" class="validate" name="city" placeholder="Ville" maxlength="255" value="<?=isset($_POST["city"]) && is_string($_POST["city"]) ? htmlspecialchars($_POST["city"]) : ""?>" required><br>
			<input type="text" class="validate" name="phone" placeholder="Numéro de téléphone" maxlength="255" value="<?=isset($_POST["phone"]) && is_string($_POST["phone"]) ? htmlspecialchars($_POST["phone"]) : ""?>" required><br>
			<?=$recaptcha->generate()?><br>
			<button type="submit" class="waves-effect waves-light btn">Valider</button><br><br>
			
			<a href="/account/login" title="J'ai déjà un compte" class="waves-effect waves-light btn">J'ai déjà un compte</a>
		</div>
	</div>
</form>
<?php
}

require "Pages/Layout/End.php";