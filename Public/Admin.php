<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";

if (!isset($user) || !$session["has2fa"]) {
	http_response_code(401);
	$errorMessage = "Vous devez être connecté afin d'accéder à cette page.";
	require "inc/Pages/Error.php";
	exit;
}

if (!$session["admin"]) {
	http_response_code(403);
	$errorMessage = "Vous devez être authentifié en tant qu'administrateur afin d'accéder à cette page.";
	require "inc/Pages/Error.php";
	exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>Admin - Serveur.tech</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
		<style>
* {
	padding:0;
	margin:0;
	font-family:Roboto, sans-serif
}

a {
	text-decoration:none;
	color:inherit
}

h1 {
	font-size:30px
}

body {
	background:#f9f9f9;
	font-size:12px
}

#menu {
	height:120px;
	background:#3c4041;
	background-image:-moz-linear-gradient(#3c4041, #242627);
	background-image:-webkit-gradient(linear, 0% 0%, 0% 100%, from(#3c4041), to(#242627));
	background-image:-webkit-linear-gradient(#3c4041, #242627);
	background-image:-o-linear-gradient(#3c4041, #242627);
	border-bottom:1px solid #6cab2e;
	margin:0;
	color:#fff;
	border-bottom:solid 4px #5f9729
}

#menu-right {
	display:inline;
	float:right;
	padding:10px 10px
}

#menu-right a {
	background:#494D4E;
	border-radius:10px;
	color:#dddddd;
	padding:3px 7px
}

#menu-right a:hover {
	background:#5f9729
}

#menu #title {
	padding:25px 50px;
	width:50%
}

#menu #tabs {
	list-style:none;
	height:40px;
	margin:0;
	padding-left:10px
}

#menu #tabs li {
	display:inline;
	float:left;
	margin-right:10px
}

#menu #tabs a {
	display:block;
	padding:11px 11px 6px 11px;
	color:#f9f9f9;
	border-top-left-radius:3px;
	border-top-right-radius:3px;
}

#menu #tabs .active {
	background:#5f9729;
	border-bottom:3px solid #5f9729;
	border-top:1px solid #6cab2e;
	border-right:1px solid #6cab2e;
}

#content {
	padding:10px 20px 10px 20px
}

.block {
	margin:10px 0 10px;
	padding:10px;
	background:#fff;
	border:1px solid #e1e1e1;
	border-radius:3px
}

table {
	width:100%;
	border-collapse:collapse
}

table tr:nth-child(2n-1) {
	background:#f5f5f5
}

th, td {
	padding:5px
}

tr td:last-child {
	width:50%
}

table thead th {
	border-bottom:1px solid #a1a6a8;
	border-top:1px solid #a1a6a8;
	color:#6699cc;
	background:#fff;
	text-align:left
}

.block h1 {
	margin-bottom:20px
}

.task-details {
	width:auto;
	margin-bottom:30px
}

.task-details tr {
	background:#fff !important
}

.task-details tr td:first-child {
	font-weight:bold
}

.comment {
	margin-top:50px
}

.comment .left {
	display:inline-block;
	width:300px;
	border:1px solid #e1e1e1;
	border-radius:3px;
	padding:10px;
	margin-right:15px
}

.comment .right {
	display:inline-block;
	vertical-align:top;
	margin-top:5px
}

@media (max-width:400px) {
	#content {
		padding:10px 0 10px 0
	}
}

ul {
	margin-left:25px
}

		</style>
	</head>
	
	<body>
		<div id="menu">
			<div id="menu-right">
                <a href="/" title="Retour au site">Retour au site</a>
			</div>
			
			<h1 id="title">Admin</h1>
			
			<ul id="tabs">
				<li><a href="/Admin.php" class="active" title="Liste des tâches">Liste des tâches</a>
				<li><a href="/ClientArea.php" class="active" title="Retour au panel">Retour au panel</a>
			</ul>
		</div>
		
		
		
		<div id="content">
			<div class="block">
				Vous êtes authentifié en tant qu'administrateur.
            </div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>17/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Global
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<p>					
					Aujourd'hui, suite à plusieurs retours de différents testeurs, il a été fait :<br><br>
					
					<ul>
						<li>Correction d'un bug au niveau du prix de l'offre MC-2
						<li>Correction d'un bug lors de la validation d'un paiement, celui-ci était lié au fait que le script était relié à l'ancien système de sessions
						<li>Correction d'un bug au niveau du générateur de server.properties, celui-ci était lié au fait que l'enregistrement du gamemode était "trop" sécurisé provoquant en conséquence un enregistrement vierge de cette variable
					</ul>
					<br><br>
					
					Il a également été fait :<br>
					<ul>
						<li>Mise en place d'une authentification supplémentaire destinée aux administrateurs
						<li>Liaison entre les tâches et le site
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>17/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Global
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<p>					
					Ce soir, il a été fait :<br><br>
					
					<ul>
						<li>Refonte intégrale du système de sessions du site, le système de sessions natif de PHP ne fonctionnant pas correctement sur le serveur de production
						<li>Révision du code source gérant la double-authentification
						<li>Modification du système d'authentification en tant qu'administrateur
						<li>Mise en production des machines virtuelles destinées aux serveurs de jeu (3 hôtes au total pour 24 machines virtuelles)
					</ul>
					<br><br>
					
					A faire :<br>
					<ul>
						<li>Mettre en place une authentification supplémentaire destinée aux administrateurs
						<li>Soumettre le site à des beta-testeurs
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>16/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Global
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<p>					
					Aujourd'hui, il a été fait :<br><br>
					
					<ul>
						<li>Relier le site Internet à Reseau.io dans le but de limiter les inscriptions uniquement aux IP Françaises
						<li>Réimporter les plugins en base de données et sur les serveurs de stockage
						<li>Mettre en place les backups automatiques sur le serveur hôte hébergeant les serveurs MC-1
						<li>Mettre en place une adresse e-mail de contact
						<li>Développement d'un script de vérification automatique permettant de vérifier si les identifiants FTP/RCON/MySQL sont valides pour chaque serveur
					</ul>
					<br><br>
					
					A faire :<br>
					<ul>
						<li>/
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>15/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Global
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<p>					
					Aujourd'hui, il a été fait :<br><br>
					
					<ul>
						<li>Mise à jour du site commercial
						<li>Mise en place d'un serveur hôte qui hébergera des serveurs MC-1
						<li>Mise en ligne du site en bêta fermée : <a href="/" title="Index" target="_blank"><b>https://serveur.tech/</b></a>, le mot de passe est <b>OpX234</b>
					</ul>
					<br><br>
					
					A faire :<br>
					<ul>
						<li>Relier le site Internet à Reseau.io dans le but de limiter les inscriptions uniquement aux IP Françaises
						<li>Réimporter les plugins en base de données et sur les serveurs de stockage
						<li>Mettre en place les backups automatiques sur le serveur hôte hébergeant les serveurs MC-1
						<li>Mettre en place une adresse e-mail de contact
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>14/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Global
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<p>					
					Aujourd'hui, il a été fait :<br><br>
					
					<ul>
						<li>Patch d'un bug dans le module de reset des serveurs
						<li>Création d'un système de sessions en base de données permettant de détruire les sessions non voulues (par exemple en cas de piratage du compte)
						<li>Révision intégrale du code source de back-end
						<li>Développement d'une fonction allégant significativement les rendus HTML du site
						<li>Création d'une page permettant de modifier les informations personnelles du compte
						<li>Création d'une page permettant de modifier le mot de passe du compte
						<li>Création du bouton de déconnexion
						<li>Développement de l'installateur automatique de plugins (JAR/ZIP supportés)
					</ul>
					<br><br>
					
					A faire :<br>
					<ul>
						<li>Revoir le site commercial
						<li>Relier le site Internet à Reseau.io dans le but de limiter les inscriptions uniquement aux IP Françaises
						<li>Mettre en ligne le site en bêta fermée
						<li>Réimporter les plugins en base de données et sur les serveurs de stockage
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>13/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Global
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<p>					
					Aujourd'hui, il a été fait :<br><br>
					
					<ul>
						<li>Création d'un troisième serveur hôte, celui-ci hébergera 8 serveurs MC-2
						<li>Création du serveur SQL2 qui sera destiné aux offres MC-2 et MC-3
						<li>Création d'un serveur de backup ayant 3x2 To (RAID 1)
						<li>Création de 8 VM pour serveurs MC-1
						<li>Création de 8 VM pour serveurs MC-2
						<li>Création de 6 VM pour serveurs MC-3
						<li>Liaison serveurs<->serveur de backup
						<li>Mise en place d'un backup automatique quotidien pour les serveurs MC-2 et MC-3
						<li>Mise en place d'un backup automatique quotidien pour les bases de données hébergées dans SQL1 et SQL2
					</ul>
					<br><br>
					
					A faire :<br>
					<ul>
						<li>Installation auto de plugins
						<li>Révision intégrale du code source de back-end
						<li>Revoir le site commercial
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>12/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Global
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<p>					
					Aujourd'hui, il a été fait :<br><br>
					
					<ul>
						<li>Développement de la liaison entre le site et les serveurs MySQL (génération automatique d'utilisateur et base de donnée, changement automatique de mot de passe à la demande, reset automatique des données après expiration...)
						<li>Développement des boutons de démarrage/redémarrage/arrêt
						<li>Développement de l'outil de support (<a href="/demoserveurtechticket.png" title="Demo">Cliquez ici pour voir une capture d'écran</a>)
						<li>Développement des outils d'administration
						<li>Mise en production d'un serveur hôte qui hébergera 6 serveurs MC-2
						<li>Téléchargement automatique des plugins toujours en cours
					</ul>
					<br><br>
					
					A faire :<br>
					<ul>
						<li>Installation auto de plugins
						<li>Création du serveur SQL2 destiné aux serveurs MC-2
						<li>Création d'un troisième serveur hôte
						<li>Mettre en production les serveurs de backup
						<li>Révision intégrale du code source de back-end
						<li>Revoir le site commercial
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>11/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Global
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<p>					
					Aujourd'hui, il a été fait :<br><br>
					
					<ul>
						<li>Plusieurs modifications sur le panel dans le but d'ajouter à l'avenir de nouvelles fonctionnalités
						<li>Plusieurs patchs de bugs qui pouvaient être provoqués lors d'un échec de connexion SSH/RCON
						<li>Plusieurs patchs de bugs au niveau du modèle de création de serveur hôte
						<li>Création d'une VM sur GCR61 hébergeant un serveur MariaDB (MySQL) pour les serveurs de jeu hébergés à cet emplacement
						<li>Création côté front-end de l'outil d'ajout de plugins (<a href="/demoserveurtechplugins.png" title="Demo">Cliquez ici pour voir une capture d'écran</a>)
						<li>Plusieurs patchs de bugs au niveau de l'outil d'import de plugins via le site officiel Spigot
					</ul>
					<br><br>
					
					A faire :<br>
					<ul>
						<li>Installation auto de plugins
						<li>Liaison du site avec les serveurs MySQL
						<li>Boutons de démarrage/redémarrage/arrêt
						<li>Tickets
						<li>Panneau d'administration
						<li>Mettre en production des serveurs hôtes pour MC-2 et MC-3
						<li>Mettre en production les serveurs de backup
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>10/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Global
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<p>					
					Aujourd'hui, il a été fait :<br><br>
					
					<ul>
						<li>Mise en place de l'infrastructure GCR61
						<li>Finalisation du générateur de server.properties (<a href="demoserveurtechparametres.png" title="Demo">Cliquez ici pour voir une capture d'écran</a>)
						<li>Finalisation du sélectionneur de versions
						<li>Ajout d'un onglet "Console" permettant de voir la console du serveur en direct. Il est possible d'intéragir avec la console (envoyer des commandes). (<a href="/demoserveurtechconsole.png" title="Demo">Cliquez ici pour voir une capture d'écran</a>)
						<li>Ajout d'un onglet "FTP" permettant de voir les logins FTP du serveur. Un bouton a été ajouté permettant de regénérer le mot de passe FTP en cas de vol. (<a href="/demoserveurtechftp.png" title="Demo">Cliquez ici pour voir une capture d'écran</a>)
						<li>Développement d'une ébauche de la partie "plugins"
						<li>Développement d'un script permettant de télécharger l'intégralité des plugins Spigot/Craftbukkit via le site officiel. Les sécurités anti-bots ont été bypassées sans difficulté, le script va rester en activité jusqu'à ce qu'il termine le téléchargement des plugins (environ 40 000 plugins au total).
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>09/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Infrastructure
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Infrastructure GCR61</h1>
				<p>					
					L'infrastructure GCR61 sera mise en service demain (le 09/10/2020). Une coupure de quelques dizaines de minutes du panneau d'administration est à prévoir.<br>
					Seront installés :<br><br>
					
					<ul>
						<li>1x Serveur HP Proliant ML350 G9
						<li>1x Routeur VDSL TP-Link
						<li>1x Switch 48 ports Netgear
						<li>1x Routeur de coeur de réseau Mikrotik
						<li>2x Onduleurs 1400W GreenCell
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>08/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Site Internet
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Site Internet</h1>
				<p>					
					Aujourd'hui, il a été fait :<br>
					<ul>
						<li>Développement du module de renouvellement des serveurs
						<li>Création d'une tâche automatique générant les logins des nouveaux conteneurs de serveurs
						<li>Création d'une tâche automatique affectant les serveurs expirés permettant de faire un reset (suppression des données, génération de nouveaux logins...)
						<li>Création d'une tâche automatique avertissant les clients par SMS lorsqu'un service est proche de sa date d'expiration
						<li>Fusion de 2 classes dans le back-end
						<li>Ajout d'un formulaire permettant de modifier le mot de passe RCON d'un serveur
						<li>Téléchargement de l'intégralité des versions serveur de Forge (28 au total)
					</ul>
					
					<br><br>
					
					A faire :<br>
					<ul>
						<li>Uploader les versions de Forge sur le serveur de stockage
						<li>Finalisation du téléchargement des versions
						<li>Installation automatique de n'importe quelle version de serveur
						<li>Installation auto de plugins
						<li>Console
						<li>Whitelist
						<li>FTP
						<li>MySQL
						<li>Boutons de démarrage/redémarrage/arrêt
						<li>Tickets
						<li>Panneau d'administration
						<li>Mettre en production l'infrastructure MC-1 dans l'emplacement GCR61
						<li>Mettre en production des serveurs hôtes pour MC-2 et MC-3
						<li>Mettre en production les serveurs de backup
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>07/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Site Internet
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Site Internet</h1>
				<p>					
					Aujourd'hui, il a été fait :<br>
					<ul>
						<li>Mise en place d'un serveur de stockage où seront hébergés les assets liés aux serveurs de jeu (versions, mods, plugins, etc)
						<li>Téléchargement de l'intégralité des versions (474 au total) publiés par Mojang de Minecraft sur le serveur de stockage. Il reste à télécharger les serveurs moddés et fonctionnant sous plugins, tels que Spigot, Craftbukkit, Forge, Sponge Forge, ...
						<li>Intégration sur le front-end d'un sélecteur de versions dont l'installation sera automatique : (<a href="/demoserveurtechselectversions.PNG" title="Demo">Cliquez ici pour voir une capture d'écran</a>)
					</ul>
					
					<br><br>
					
					A faire :<br>
					<ul>
						<li>Développement du module de renouvellement des serveurs
						<li>Mise en place d'une tâche cron qui avertira automatiquement par SMS des prochaines dates de renouvellement pour les clients
						<li>Finalisation du téléchargement des versions
						<li>Installation automatique de n'importe quelle version de serveur
						<li>Installation auto de plugins
						<li>Console
						<li>Whitelist
						<li>FTP
						<li>MySQL
						<li>Boutons de démarrage/redémarrage/arrêt
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>06/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Site Internet
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Site Internet</h1>
				<p>					
					Aujourd'hui, il a été fait :<br>
					<ul>
						<li>Mise en place d'un reCaptcha sur les pages gourmandes en ressources
						<li>Mise à jour de la procédure d'import des classes SSH et RCON
						<li>Finalisation du système de facturation, les factures sont automatiquement générées sous format PDF une fois un paiement reçu
						<li>Début de création du panel (<a href="demoserveurtechpanel.png" title="Screenshot">Cliquez ici pour voir un screenshot</a>)<br>
						<li>Mise en place d'un générateur de configuration Minecraft utilisable via le panel
						<li>Mise en place d'un listing des factures créées
						<li>Début de développement du site commercial (<a href="demoserveurtech.png" title="Screenshot">Cliquez ici pour voir un screenshot</a>)
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>05/10/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Site Internet
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Site Internet</h1>
				<p>
					Le développement du site Internet commercial a été repris de 0, après plusieurs mois sans activité.<br>
					La date de début du développement du site a été le 04/10/2020.<br><br>
					
					Ce qui a été développé côté interne :<br>
					<ul>
						<li>Gestion automatique de serveur de jeu via connexion SSH gérée par PHP
						<li>Gestion automatique de serveur Minecraft via connexion RCON gérée par PHP
						<li>Sauvegarde automatique de serveur de jeu
						<li>Serveur FTP pour serveur de jeu
						<li>Générateur de configuration pour serveur Minecraft
					</ul>
					<br><br>
					
					Ce qui a été développé côté site Internet :<br>
					<ul>
						<li>Création de compte
						<li>Connexion
						<li>Mot de passe oublié
						<li>Authentification à 2 facteurs via SMS (activé obligatoirement sur tous les comptes)
						<li>Panier
						<li>Gestionnaire de paiement utilisant l'API PayPal
						<li>Générateur automatique de facture au format PDF
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>08/08/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Global
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>RIPE</h1>
				<p>
					Les blocs suivants ont été obtenus auprès du RIPE : 193.3.44.0/24, 2a10:5d40::/29.
					Un AS est actuellement en cours d'obtention.<br><br>
					
					De plus, il a été décidé de passer par un transitaire possédant un anti-DDoS. En effet, les risques financiers face à un DDoS sont beaucoup trop élevés.				
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>04/08/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Global
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Jour J pour Netsplit</h1>
				<p>
					Aujourd'hui, la somme d'environ 20000€ a été reçue auprès de la société.<br><br>
					
					Voici les équipements qui ont été commandés :<br>
					<ul>
						<li>Serveur 88 coeurs 768 Go RAM DDR4 + 4x4 To SSD RAID 1<br>
						<li>Routeur de backbone Mikrotik, capable de supporter un nombre importants de paquets (et peut donc résister face à des attaques diverses)
						<li>2x Onduleurs 1400W
					</ul>
					<br><br>
					
					Désormais nous attendons la réponse du fournisseur de transit Hurricane Electric pour fournir un accès Internet au réseau...
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>04/08/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Financier
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Réception des fonds des IPs</h1>
				<p>
					La somme des IPs a été reçue à ce jour.<br>L'augmentation du capital social de la société commence dès aujourd'hui.
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>03/08/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Technique
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Nouveau LIR</h1>
				<p>
					A ce jour, un nouveau LIR a été créé auprès du RIPE sous l'identifiant fr.netsplit28.<br>
					Les demandes suivantes ont été effectuées :
					<ul>
						<li>Bloc IPv4 /24
						<li>Bloc IPv6 /29
						<li>ASN 32 bits
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>30/08/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Technique
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Modifications auprès du RIPE</h1>
				<p>
					Un second LIR va être créé auprès du RIPE sous l'identifiant fr.netsplit28.<br>
					Ce LIR permettra d'acquérir un bloc IPv4 /24 et un bloc IPv6 /29.<br><br>
					
					Le coût de création du nouveau LIR est de 2700€ (taxes non comprises étant donné que la société ne paye pas la TVA).<br>
					La facture est accessible <a href="/20027515.pdf" title="Facture">ici</a>.
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>28/08/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Financier
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Suivi des fonds des IPs</h1>
				<p>
					A ce jour, le compte bancaire de la société a intégralement été réinitialisé, suite à une demande de la part de la société. Les données bancaires telles que l'IBAN et le BIC ont été modifiés, le plus important étant que le code BIC QNTOFRP1XXX supporte les transactions internationales, contrairement au BIC TRZOFR21XXX qui n'est plus attribué à la société.<br>
					Les nouvelles coordonnées bancaires ont été transmises à la société gérant l'escrow des fonds des IPs, une confirmation de la bonne prise en compte des changements a été reçue ce jour à 16:39.<br><br>
					
					Un ticket auprès de Qonto informait que la transaction pouvait durer jusqu'à plusieurs semaines. En revanche, il est indiqué que sur la nouvelle plateforme Qonto, les virements SWIFT s'effectuent en 1 à 3 jours ouvrés. Les fonds peuvent donc potentiellement être reçus plus rapidement que prévu.<br><br>
					
					<h2>A propos de l'augmentation de capital social</h2><br>
					Un devis a été créé auprès d'un prestataire juridique expliquant le fonctionnement des apports numéraires dans le capital social. Il s'avère que la procédure d'augmentation de capital sociale nécessitera de payer la somme de 699€. Voici un détail de la somme :
					<ul>
						<li>Dossier complet (269€) comportant :
						<li>- Vérification du dossier par un formaliste
						<li>- Assistance par email et téléphone
						<li>- Traitement prioritaire
						<li>- Suivi personnalisé et gestion du Greffe
						<li>----------
						<li>Frais d'enregistrement au greffe (195,84€)
						<li>Frais d'annonce légale (150€)
					</ul><br>
					
					Total TTC : 699€
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>27/08/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Régression
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Financier
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Problème de débloquage des fonds des IPs</h1>
				<p>
					La somme de 23552$ a été envoyée auprès de la banque Qonto sur le BIC TRZOFR21XXX (Treezor).<br>
					Il s'avère que le BIC en question est relié à un prestataire bancaire qui ne supporte pas les virements en devises étrangères (SWIFT).<br>
					En conséquence, le virement va être rejeté par la banque de destination, et les fonds vont être retournés à l'expéditeur.<br>
					Une réclamation auprès de Qonto a été créée. Suite à cette réclamation, Qonto a proposé de créer un compte sur leur "nouvelle plateforme", supportant les virements SWIFT. La date de mise en service du compte est prévue pour demain.<br><br>
					
					En conclusion, il est possible que les fonds soient reçus avec plusieurs semaines de retard.
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>19/08/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Financier
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Placement des fonds des IPs dans un escrow</h1>
				<p>
					Le contrat de location des adresses IP est terminé.<br>
					La somme de 23552$ a ce soir été placée dans un escrow et sera débloquée dans les prochaines semaines.<br><br>
					
					Une fois l'argent débloqué, il est prévu d'augmenter le capital social de la société à 20 000€.
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>15/08/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Offres
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Ajout des offres VPS + Fin proche de la location des IPs</h1>
				<p>
					<p>En supplément des serveurs dédiés lames, des VPS seront commercialisés.<br><br>
					<h2>Offres de VPS</h2>
					<table class="striped">
						<thead>
							<tr>
								<th>vCPU
								<th>RAM
								<th>Disque
								<th>Connexion
								<th>Prix
						</thead>
						<tbody>
							<tr>
								<td>1x 2.2GHz
								<td>8 Go
								<td>40 Go
								<td>100Mbps
								<td>4,99€/mois
							<tr>
								<td>2x 2.2GHz
								<td>16 Go
								<td>60 Go
								<td>100Mbps
								<td>9,99€/mois
							<tr>
								<td>4x 2.2GHz
								<td>24 Go
								<td>80 Go
								<td>100Mbps
								<td>14,99€/mois
							<tr>
								<td>8x 2.2GHz
								<td>32 Go
								<td>100 Go
								<td>100Mbps
								<td>19,99€/mois
						</tbody>
					</table><br><br>
					
					Hôte :
					<ul>
						<li>HPE ProLiant ML350 Gen9
						<li>2x Intel Xeon E5-2699 V4 22-Core 2.20Ghz (= 44 coeurs, 88 threads)
						<li>768 Go RAM DDR4 2400MHz
						<li>P440ar 2GB FBWC (SAS/SATA) RAID Kit
						<li>2x Alimentation HP 1400W
					</ul>
					
					<br><br>
					Matériel supplémentaire nécessaire pour l'hôte :
					<ul>
						<li>4x Samsung SSD 860 EVO 4 To
						<li>2x Onduleur Green Cell® UPS 2000VA (1400W)
					</ul><br><br>
					
					Locations :
					<ul>
						<li>Transit IP Hurricane Electric 1Gbps (situé à Telehouse 2)
						<li>Anti-DDoS Voxility 1Tbps + Transit IP 1Gbps
					</ul><br><br>
					
					Le contrat de location des IPs se termine dans 2 jours.
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>14/08/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Offres
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
				
				<h1>Modifications des offres de serveurs dédiés</h1>
				<p>
					L'hébergeur Français <b style="color:#C00">Firstheberg</b> possède des offres de serveurs dédiés battant intégralement les offres de Netsplit. Cet hébergeur est à surveiller particulièrement. En effet, celui-ci possède des offres plus intéressantes que celles de gros hébergeurs tels que OVH ou Online (Scaleway/Free).<br>
					En conséquence, les offres de 2019 et prévisions d'achats ont été modifiés.<br><br>
					
					<h2>Offres de serveurs dédiés</h2>
					<table class="striped">
						<thead>
							<tr>
								<th>CPU
								<th>RAM
								<th>Disques
								<th>Connexion
								<th>Prix
						</thead>
						<tbody>
							<tr>
								<td>Intel Xeon @ 2GHz 4 coeurs
								<td>8 Go
								<td>2x 146 Go 10000RPM RAID 1
								<td>1Gbps best-effort
								<td>14,99€/mois
								
							<tr>
								<td>Intel Xeon @ 2GHz 6 coeurs
								<td>16 Go
								<td>2x 300 Go 10000RPM RAID 1
								<td>1Gbps best-effort
								<td>19,99€/mois
								
							<tr>
								<td>Intel Xeon @ 2GHz 8 coeurs
								<td>24 Go
								<td>2x 600 Go 10000RPM RAID 1
								<td>1Gbps best-effort
								<td>24,99€/mois
								
							<tr>
								<td>Intel Xeon @ 2GHz 10 coeurs
								<td>32 Go
								<td>2x 900 Go 10000RPM RAID 1
								<td>1Gbps best-effort
								<td>29,99€/mois
								
							<tr>
								<td>Intel Xeon @ 2GHz 12 coeurs
								<td>48 Go
								<td>2x 1,2 To 10000RPM RAID 1
								<td>1Gbps best-effort
								<td>34,99€/mois
								
						</tbody>
					</table>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>11/08/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Offres
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
			
				<h1>Offres de serveurs dédiés</h1>
				
				<p>
					<table>
						<thead>
							<tr>
								<th>CPU
								<th>RAM
								<th>Disques
								<th>Connexion
								<th>Prix
							</tr>
						</thead>
						
						<tbody>
							<tr>
								<td>Intel Xeon E5-2670 V1 8c/16t @ 2.6GHz
								<td>32 Go
								<td>2x 1To 7200RPM RAID 1
								<td>1Gbps best-effort
								<td>49.99€/mois
							<tr>
								<td>Intel Xeon E5-2690 V2 10c/20t @ 3GHz
								<td>64 Go
								<td>2x 1.2 To 10000RPM RAID 1
								<td>1Gbps best-effort
								<td>79.99€/mois
							<tr>
								<td>2x Intel Xeon E5-2690 V2 20c/40t @ 3GHz
								<td>128 Go
								<td>2x 1.2 To 10000 RPM RAID 1
								<td>1Gbps best-effort
								<td>99.99€/mois
						</tbody>
					</table>
					
					<br><br>
					
					<h1>Services LIR</h1>
					<table class="striped">
						<thead>
							<tr>
								<th>Type
								<th>Prix
						</thead>
						
						<tbody>
							<tr>
								<td>Bloc IPv6 /48
								<td>99.99€/an
							<tr>
								<td>Autonomous System
								<td>99.99€/an
						</tbody>
					</table>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>10/08/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Amélioration
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Infrastructure
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
			
				<h1>Prévisions d'achat</h1>
				
				<p>
					Les prix affichés sont hors taxe, car la société ne paye pas la TVA.<br>
					Il est prévu d'acquérir le matériel suivant :<br><br>
					<ul>
						<li><b>1x</b> <a href="https://www.dell.com/fr-fr/work/shop/povw/poweredge-m1000e" target="_blank">Dell PowerEdge M1000e</a> (au total <b>934.80€</b>)
						<li><b>16x</b> <a href="https://i.dell.com/sites/csdocuments/Shared-Content_data-Sheets_Documents/fr/fr/Dell-PowerEdge-M620-Spec-Sheet_FR.pdf" target="_blank">Dell PowerEdge M620 V2</a> (au total <b>5417.28€</b>)
						<li><b>6x</b> <a href="https://www.amazon.fr/AmazonBasics-Power-Cord-10-Black/dp/B07177GJ3P/ref=sr_1_5?__mk_fr_FR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&dchild=1&keywords=c%C3%A2ble+d%27alimentation+pc&qid=1597077379&sr=8-5" target="_blank">câbles d'alimentation</a> (au total <b>65.34€</b>)
						<li><b>1x</b> <a href="https://www.senetic.fr/product/CCR1009-7G-1C-1S+" target="_blank">Mikrotik CCR1009-7G-1C-1S+</a> (au total <b>420,81€</b>)
						<li>Total : <b>6838.23€</b>
						<br><br>
						
						Nombre de serveurs par boîtier : 16
						
						<br><br>
						Configuration des lames :<br><br>
						<ul>
							<li>Intel Xeon E5-2670 V1 8c/16t 2.6GHz
							<li>32 Go RAM DDR3 1866MHz
							<li>Contrôleur RAID H710 Mini 512MB NV (SAS/SATA)
							<li>2x HDD 1 To 7200RPM
						</ul>
					</ul>
				</p>
			</div>
			
			<div class="block">
				<table class="task-details">
					<tbody>
						<tr>
							<td>Date d'ouverture
							<td>01/08/2020
						</tr>
						
						<tr>
							<td>Type de tâche
							<td>Divers
						</tr>
						
						<tr>
							<td>Catégorie
							<td>Technique
						</tr>
						
						<tr>
							<td>État
							<td>Fermé
						</tr>
					</tbody>
				</table>
			
				<h1>Fin des annonces BGP du bloc IPv4</h1>
				
				<p>
                    Les annonces BGP du bloc IPv4 <b>81.16.136.0/22</b> ont été cessées par les locataires du bloc.
				</p>
				
				
				<!--<div class="comment">
					<div class="left">
						<b>Admin</b><br>
						<i>10/04/2020 à 15:41</i>
					</div>
					
					<div class="right">
                        X
					</div>
				</div>-->
			</div>
			
		</div>
	</body>
</html>