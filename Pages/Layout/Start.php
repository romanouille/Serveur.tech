<!DOCTYPE html>
<html lang="fr">
	<head>
		<title><?=$pageTitle?> - Serveur.tech</title>
		<meta charset="utf-8">
		<meta property="og:title" content="<?=$pageTitle?>">
		<meta property="og:type" content="article">
		<meta property="og:url" content="https://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>">
		<meta property="og:image" content="https://<?=$_SERVER["HTTP_HOST"]?>/img/logo.png">
		<meta property="og:locale" content="fr_FR">
		<meta property="og:description" content="<?=$pageDescription?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="<?=$pageDescription?>">
		<meta name="theme-color" content="#006064">
		<link rel="canonical" href="https://<?=$_SERVER["HTTP_HOST"]?>/">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
		<style>
			.brand-logo img {
				height:64px
			}
			
			footer {
				margin-top:50px
			}
		</style>
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	</head>
	
	<body>
		<header>
			<nav class="cyan darken-4">
				<div class="nav-wrapper container ">
					<a href="/" class="brand-logo" title="Serveur.tech"><img src="<?=$staticServer?>/img/logo.png" alt="" title="Serveur.tech"></a>
					<a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
					<ul class="right hide-on-med-and-down">
						<li><a href="/account/login" title="Connexion">Connexion</a>
					</ul>
				</div>
			</nav>
			<ul class="sidenav" id="mobile-demo">
				<li><a href="/account/login" title="Connexion">Connexion</a>
			</ul>
		</header>
		
		<div class="container">
			<main>
