<?php
set_include_path("../");
chdir("../");

require "inc/Admin.class.php";
require "inc/Init.php";

if (!isset($user) || !$session["has2fa"]) {
	header("Location: /Auth.php");
	exit;
}

if (!$session["admin"]) {
	http_response_code(403);
	require "inc/Pages/Panel_error.php";
	exit;
}

$ticketsList = Admin::getTicketsList();

$breadcrumb = "Liste des tickets";

require "inc/Layout/Panel/Start.php";
?>
<div class="container">
<?php
if (!empty($ticketsList)) {
?>
	<div class="card card-custom gutter-b">
		<div class="card-body">
<?php
	foreach ($ticketsList as $id=>$value) {
		if ($id > 0) {
			echo "<br>";
		}
?>
			<a href="/AdminTicket.php?phone=<?=$value["phone"]?>" title="<?=$value["phone"]?>" target="_blank"><?=htmlspecialchars($value["first_name"])." ".htmlspecialchars($value["last_name"])?> (<?=$value["phone"]?>)</a> - <?=!$value["seen"] ? "<b>Non lu</b>" : "Lu"?>
<?php
	}
?>
		</div>
	</div>
<?php
} else {
?>
			<div class="alert alert-info">Il n'y a aucun ticket pour le moment.</div>
<?php
}
?>

</div>
<?php
require "inc/Layout/Panel/End.php";