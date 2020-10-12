<?php
set_include_path("../");
chdir("../");

require "inc/Admin.class.php";
require "inc/Init.php";

if (!isset($user) || !$_SESSION["2fa"]) {
	$_SESSION = [];
	
	header("Location: /Auth.php");
	exit;
}

if (!$_SESSION["admin"]) {
	http_response_code(403);
	require "inc/Pages/Error.php";
	exit;
}

$ticketsList = Admin::getTicketsList();

$breadcrumb = "Liste des tickets";

require "inc/Layout/Panel/Start.php";
?>
<div class=" container ">
	<!--begin::Card-->
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
	<!--end::Card-->
</div>
<?php
require "inc/Layout/Panel/End.php";