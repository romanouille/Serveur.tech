<?php
require "Pages/Layout/Start.php";
?>
<h1>Annonces BGP</h1>

<?php
if (count($data["bgp"]) >= 1) {
?>
<table class="striped responsive-table">
	<thead>
		<tr>
			<th>version
			<th>block
			<th>block_start
			<th>block_end
			<th>origin
			<th>origin_name
			<th>origin_country
	</thead>
	
	<tbody>
<?php
	foreach ($data["bgp"] as $value) {
?>
		<tr>
			<td><?=$value["version"]?>
			<td><?=$value["block"]?>
			<td><a href="/ip/<?=$value["block_start"]?>" title="<?=$value["block_start"]?>"><?=$value["block_start"]?></a>
			<td><a href="<?=$value["block_end"]?>" title="<?=$value["block_end"]?>"><?=$value["block_end"]?></a>
			<td><a href="/as/<?=$value["origin"]?>" title="AS<?=$value["origin"]?>">AS<?=$value["origin"]?>
			<td><?=htmlspecialchars($value["origin_name"])?>
			<td><?=$value["origin_country"]?>
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

<h1>Blocs</h1>

<?php
if (count($data["blocks"]) >= 1) {
?>
<table class="striped responsive-table">
	<thead>
		<tr>
			<th>version
			<th>block
			<th>block_start
			<th>block_end
			<th>country
			<th>lir
			<th>created
			<th>rir
	</thead>
	
	<tbody>
<?php
	foreach ($data["blocks"] as $value) {
?>
		<tr>
			<td><?=$value["version"]?>
			<td><?=$value["block"]?>
			<td><a href="/ip/<?=$value["block_start"]?>" title="<?=$value["block_start"]?>"><?=$value["block_start"]?></a>
			<td><a href="/ip/<?=$value["block_end"]?>" title="<?=$value["block_end"]?>"><?=$value["block_end"]?></a>
			<td><?=$value["country"]?>
			<td><?=$value["lir"]?>
			<td><?=date("d/m/Y H:i:s", $value["created"])?>
			<td><?=$rirList[$value["rir"]]?>
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
			<th>org
			<th>org_name
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
			<td><a href="/org/<?=$value["org"]?>" title="<?=$value["org"]?>"><?=$value["org"]?></a>
			<td><?=htmlspecialchars($value["org_name"])?>
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
?>

<h1>Routes</h1>

<?php
if (count($data["routes"]) >= 1) {
?>
<table class="striped responsive-table">
	<thead>
		<tr>
			<th>version
			<th>block
			<th>block_start
			<th>block_end
			<th>description
			<th>origin
			<th>created
			<th>modified
	</thead>
	
	<tbody>
<?php
	foreach ($data["routes"] as $value) {
?>
		<tr>
			<td><?=$value["version"]?>
			<td><?=$value["block"]?>
			<td><a href="/ip/<?=$value["block_start"]?>" title="<?=$value["block_start"]?>"><?=$value["block_start"]?></a>
			<td><a href="/ip/<?=$value["block_end"]?>" title="<?=$value["block_end"]?>"><?=$value["block_end"]?></a>
			<td><?=htmlspecialchars($value["description"])?>
			<td><a href="/as/<?=$value["origin"]?>" title="AS<?=$value["origin"]?>">AS<?=$value["origin"]?></a>
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