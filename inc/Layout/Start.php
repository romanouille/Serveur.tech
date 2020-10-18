<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="cache-control" content="max-age=604800, public" />
		<title>Serveur.tech : hébergement de serveurs Minecraft</title>
		<meta name="description" content="Hébergeur de serveur Minecraft français incluant panneau d'administration, anti-DDoS, FTP et MySQL.">
<?php
if ($_SERVER["REMOTE_ADDR"] != "127.0.0.1") {
?>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-180603057-1"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'UA-180603057-1');
		</script>
<?php
}
?>
		<link rel="icon" href="/assets/media/logos/logo.png">
		<!-- Fonts -->
		<link href="fonts/cloudicon/cloudicon.css" rel="stylesheet" media="none" onload="if(media!='all')media='all'">
		<link href="fonts/fontawesome/css/all.css" rel="stylesheet" media="none" onload="if(media!='all')media='all'">
		<link href="fonts/opensans/opensans.css" rel="stylesheet" media="none" onload="if(media!='all')media='all'">
		<!-- CSS styles -->
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/owl.carousel.min.css" rel="stylesheet" media="none" onload="if(media!='all')media='all'">
		<link href="css/animate.min.css" rel="stylesheet" media="none" onload="if(media!='all')media='all'">
		<link href="css/style.min.css" rel="stylesheet">
		<link href="css/filter.css" rel="stylesheet">
		<!-- Custom color styles -->
		<link href="css/colors/pink.css" rel="stylesheet" title="pink" media="none" onload="if(media!='all')media='all'">
		<link href="css/colors/blue.css" rel="stylesheet" title="blue" media="none" onload="if(media!='all')media='all'">
		<link href="css/colors/green.css" rel="stylesheet" title="green" media="none" onload="if(media!='all')media='all'">
		<meta property="og:type" content="website">
		<meta property="og:url" content="https://<?=$_SERVER["HTTP_HOST"]?>">
		<meta property="og:description" content="Hébergeur de serveur Minecraft français incluant panneau d'administration, anti-DDoS, FTP et MySQL.">
		<meta property="og:image" content="/assets/media/logos/logo.png">
		<script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@type": "Product",
      "name": "Hébergement de serveurs Minecraft",
      "image": [
        "https://serveur.tech/assets/media/logos/logo.png"
       ],
      "description": "Hébergeur de serveur Minecraft français incluant panneau d'administration, anti-DDoS, FTP et MySQL.",
      "sku": "mc",
      "mpn": "mc",
      "brand": {
        "@type": "Brand",
        "name": "Serveur.tech"
      },
      "offers": {
        "@type": "AggregateOffer",
        "url": "https://serveur.tech/",
        "priceCurrency": "EUR",
        "lowPrice": "4.99",
		"highPrice": "14.99"
      }
    }
    </script>
	</head>
	<body>
		<div id="spinner-area">
			<div class="spinner">
				<div class="double-bounce1"></div>
				<div class="double-bounce2"></div>
				<div class="spinner-txt"></div>
			</div>
		</div>
		<!-- ***** UPLOADED MENU FROM HEADER.HTML ****** -->
		<header id="header"> </header>
