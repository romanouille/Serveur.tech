<?php
require "Pages/Layout/Start.php";
?>
<h1>Recherche</h1>

<div class="row">
	<div class="col s12 m6">
		<form method="get">
			<input type="text" class="validate" name="q" placeholder="Adresse IP, FAI, organisation, ..."><br>
			<button type="submit" class="waves-effect waves-light btn">Valider</button>
		</form>
	</div>
</div>

<?php
if (isset($data)) {
?>

<h1>AS</h1>

<?php
	if (count($data["as"]) >= 1) {
?>
<table class="striped responsive-table">
	<thead>
		<tr>
			<th>id
			<th>name
			<th>country
	</thead>
	
	<tbody>
<?php
		foreach ($data["as"] as $value) {
?>
		<tr>
			<td><a href="/as/<?=$value["id"]?>" title="<?=$value["id"]?>"><?=$value["id"]?></a>
			<td><?=htmlspecialchars($value["name"])?>
			<td><?=$value["country"]?>
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

<h1>AS RIPE</h1>

<?php
	if (count($data["ripe_as"]) >= 1) {
?>
<table class="striped responsive-table">
	<thead>
		<tr>
			<th>id
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
			<td><a href="/as/<?=$value["id"]?>" title="AS<?=$value["id"]?>"><?=$value["id"]?></a>
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

<h1>Allocations RIPE</h1>

<?php
}

require "Pages/Layout/End.php";