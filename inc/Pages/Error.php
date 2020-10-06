<?php
$code = http_response_code();

require "inc/Layout/Start.php";
?>
<!-- ***** BANNER ***** -->
<div class="top-header exapath-w">
	<div class="total-grad-inverse"></div>
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="wrapper">
					<div class="heading">Erreur <?=$code?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- ***** YOUR CONTENT ***** -->
<section class="balancing sec-normal bg-white pb-80">
	<div class="h-services">
		<div class="container">
			<div class="randomline">
				<div class="bigline"></div>
				<div class="smallline"></div>
			</div>
			
<?php
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
?>
		</div>
	</div>
</section>
<?php
require "inc/Layout/End.php";