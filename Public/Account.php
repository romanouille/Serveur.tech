<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";

if (!isset($user) || !$_SESSION["2fa"]) {
	$_SESSION = [];
	
	header("Location: /Auth.php");
	exit;
}

$breadcrumb = "Mon compte";

require "inc/Layout/Panel/Start.php";
?>

<div class="container">
	<div class="card">
		<div class="card-body">
			<a href="/AccountProfile.php" title="Mon profil" class="btn btn-light-primary font-weight-bold btn-sm">Mon profil</a>&nbsp;
			<a href="/AccountPassword.php" title="Modifier mon mot de passe" class="btn btn-light-primary font-weight-bold btn-sm">Modifier mon mot de passe</a>
		</div>
	</div>
</div>

<?php
require "inc/Layout/Panel/End.php";