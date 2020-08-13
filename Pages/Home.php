<?php
require "Pages/Layout/Start.php";
?>
<h1>Serveurs dédiés</h1>
<table class="striped">
	<thead>
		<tr>
			<th>CPU
			<th>RAM
			<th>Disques
			<th>Connexion
			<th>Prix
			<th>
	</thead>
	<tbody>
		<tr>
			<td>Intel Xeon E5-2670 V1 8c/16t @ 2.6GHz
			<td>32 Go
			<td>2x 1To 7200RPM RAID 1
			<td>1Gbps best-effort
			<td>49.99€/mois
			<td><a class="waves-effect waves-light btn">Commander</a>
		<tr>
			<td>Intel Xeon E5-2690 V2 10c/20t @ 3GHz
			<td>64 Go
			<td>2x 1.2 To 10000RPM RAID 1
			<td>1Gbps best-effort
			<td>79.99€/mois
			<td><a class="waves-effect waves-light btn">Commander</a>
		<tr>
			<td>2x Intel Xeon E5-2690 V2 10c/20t @ 3GHz (= 20c/40t)
			<td>128 Go
			<td>2x 1.2 To 10000 RPM RAID 1
			<td>1Gbps best-effort
			<td>99.99€/mois
			<td><a class="waves-effect waves-light btn">Commander</a>
	</tbody>
</table>
<h1>Services LIR</h1>
<table class="striped">
	<thead>
		<tr>
			<th>Type
			<th>Prix
			<th>
	</thead>
	<tbody>
		<tr>
			<td>Bloc IPv6 /48
			<td>99.99€/an
			<td><a class="waves-effect waves-light btn">Commander</a>
		<tr>
			<td>Autonomous System
			<td>99.99€/an
			<td><a class="waves-effect waves-light btn">Commander</a>
	</tbody>
</table>
<?php
require "Pages/Layout/End.php";