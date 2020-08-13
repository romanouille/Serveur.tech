<?php
require "Pages/Layout/Start.php";
?>
<h1>AS<?=$match[1]?></h1>

<table class="striped responsive-table">
	<thead>
		<tr>
			<th>name
			<th>country
	</thead>
	
	<tbody>
<?php
foreach ($data["as"] as $value) {
?>
		<tr>
			<td><?=$value["name"]?>
			<td><?=$value["country"]?>
<?php
}
?>
	</tbody>
</table>

<h1>Données RIPE</h1>

<?php
if (count($data["ripe_as"]) >= 1) {
?>
<table class="striped responsive-table">
	<thead>
		<tr>
			<th>org
			<th>sponsoring_org
			<th>description
			<th>remarks
			<th>created
			<th>modified
	</thead>
	
	<tbody>
<?php
	foreach ($data["ripe_as"] as $value) {
?>
		<tr>
			<td><a href="/org/<?=$value["org"]?>" title="<?=$value["org"]?>"><?=$value["org"]?></a>
			<td><a href="/org/<?=$value["sponsoring_org"]?>" title="<?=$value["sponsoring_org"]?>"><?=$value["sponsoring_org"]?></a>
			<td><?=nl2br(htmlspecialchars($value["description"]))?>
			<td><?=nl2br(htmlspecialchars($value["remarks"]))?>
			<td><?=date("d/m/Y H:i:s", $value["created"])?>
			<td><?=date("d/m/Y H:i:s", $value["modified"])?>
<?php
	}
?>
	</tbody>
</table>
<?php
} else {
?>
<div class="card">
	<div class="card-content">
		Il n'y a pas de données à afficher.
	</div>
</div>
<?php
}
?>

<h1>Annonces BGP</h1>

<?php
if (count($data["bgp"]) >= 1) {
?>
<table>
	<thead>
		<tr>
			<th>version
			<th>block
			<th>block_start
			<th>block_end
	</thead>
	
	<tbody>
<?php
	foreach ($data["bgp"] as $value) {
?>
		<tr>
			<td><?=$value["version"]?>
			<td><?=$value["block"]?>
			<td><a href="/ip/<?=$value["block_start"]?>" title="<?=$value["block_start"]?>"><?=$value["block_start"]?></a>
			<td><a href="/ip/<?=$value["block_end"]?>" title="<?=$value["block_end"]?>"><?=$value["block_end"]?></a>
<?php
	}
?>
	</tbody>
</table>
<?php
} else {
?>
<div class="card">
	<div class="card-content">
		Il n'y a pas de données à afficher.
	</div>
</div>
<?php
}
?>
<?php
require "Pages/Layout/End.php";