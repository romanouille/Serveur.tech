<?php
require "Pages/Layout/Start.php";
?>
<h1><?=strtoupper($match[1])?></h1>

<?php
if (count($data["org"]) >= 1) {
?>
<table class="striped responsive-table">
	<thead>
		<tr>
			<th>name
			<th>is_lir
			<th>created
			<th>modified
	</thead>
	
	<tbody>
<?php
	foreach ($data["org"] as $value) {
?>
		<tr>
			<td><?=htmlspecialchars($value["name"])?>
			<td><?=(int)$value["is_lir"]?>
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

<h1>Allocations RIPE</h1>

<?php
if (count($data["allocations"]) >= 1) {
?>
<table class="striped responsive-table">
	<thead>
		<tr>
			<th>version
			<th>block
			<th>block_start
			<th>block_end
			<th>country
			<th>netname
			<th>description
			<th>remarks
			<th>status
			<th>created
			<th>modified
	</thead>
	
	<tbody>
<?php
	foreach ($data["allocations"] as $value) {
?>
		<tr>
			<td><?=$value["version"]?>
			<td><?=$value["block"]?>
			<td><a href="/ip/<?=$value["block_start"]?>" title="<?=$value["block_start"]?>"><?=$value["block_start"]?></a>
			<td><a href="/ip/<?=$value["block_end"]?>" title="<?=$value["block_end"]?>"><?=$value["block_end"]?></a>
			<td><?=$value["country"]?>
			<td><?=htmlspecialchars($value["netname"])?>
			<td><?=nl2br(htmlspecialchars($value["description"]))?>
			<td><?=nl2br(htmlspecialchars($value["remarks"]))?>
			<td><?=$value["status"]?>
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

require "Pages/Layout/End.php";