<?php
$breadcrumb = "Erreur ".http_response_code();
$code = http_response_code();

require "inc/Layout/Panel/Start.php";
?>

<div class="container">
	<div class="card card-custom gutter-b">
		<div class="card-body">
<?php
if (!isset($errorMessage)) {
	if ($code == 400) {
		echo "Les données que vous avez envoyé sont incorrectes.";
	} elseif ($code == 401) {
		echo "Authentification nécessaire.";
	} elseif ($code == 403) {
		echo "Accès refusé.";
	} elseif ($code == 404) {
		echo "Page introuvable.";
	} elseif ($code == 500) {
		echo "Un problème interne est survenu, veuillez contacter un administrateur.";
	} elseif ($code == 503) {
		echo "Cette section est temporairement indisponible, veuillez réessayer plus tard.";
	} else {
		echo "Erreur $code.";
	}
} else {
	echo $errorMessage;
}
?>
		</div>
	</div>
</div>

<?php
require "inc/Layout/Panel/End.php";