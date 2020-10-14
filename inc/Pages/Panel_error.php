<?php
$breadcrumb = "Erreur ".http_response_code();
require "inc/Layout/Panel/Start.php";
?>

<div class="container">
	<div class="card card-custom gutter-b">
		<div class="card-body">
			<?=isset($errorMessage) ? $errorMessage : $breadcrumb?>
		</div>
	</div>
</div>

<?php
require "inc/Layout/Panel/End.php";