<?php
set_include_path("../");
chdir("../");
require "inc/Init.php";

require "inc/Layout/Start.php";
?>
<!-- ***** SLIDER ***** -->
<section id="owl-demo" class="owl-carousel owl-theme scrollme">
	<div class="full h-100">
		<img class="svg opa-6 img-gaming" src="patterns/gaming.svg" alt="Gaming Servers">
		<div class="total-grad-pink-blue-intense"></div>
		<div class="vc-parent text">
			<div class="vc-child">
				<div class="top-banner">
					<div class="container animateme" data-when="span" data-from="0" data-to="0.75" data-opacity="1" data-translatey="-50">
						<div class="heading">
							Serveurs Minecraft
						</div>
						<h3 class="subheading">Votre serveur Minecraft hébergé à partir de <b class="c-pink">9,99€/mois</b><br>
						</h3>
						<a href="#pricing" class="btn btn-default-yellow-fill mr-3">Voir les prix</a>
						<!--<a href="#" class="btn btn-default-pink-fill">Learn more</a>-->
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ***** PRICING TABLES ***** -->
<section class="pricing special sec-up-slider" id="pricing">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-4 col-lg-4">
				<div class="wrapper first text-left">
					<div class="top-content">
						<img class="svg mb-3" src="fonts/svg/dedicated.svg" alt="">
						<div class="title">MC-1</div>
						<div class="fromer"></div>
						<div class="price">0,00<sup>€</sup> <span class="period">/mois</span></div>
					</div>
					<ul class="list-info">
						<li><i class="icon-cpu"></i> <span class="c-purple">CPU</span><br> <span>1x 4GHz</span></li>
						<li><i class="icon-ram"></i> <span class="c-purple">RAM DDR4</span><br> <span>1 Go</span></li>
						<li><i class="icon-drives"></i> <span class="c-purple">Disque</span><br> <span>10 Go SSD</span></li>
						<li><i class="icon-inverse"></i> <span class="c-purple">MySQL</span><br> <span>Non inclus</span></li>
						<li><i class="icon-protection"></i> <span class="c-purple">Anti-DDoS</span><br> <span>Game</span></li>
						<li><i class="icon-location"></i> <span class="c-purple">Localisation</span><br> <span>France</span></li>
						<li><a href="/Cart.php?type=1" class="btn btn-default-yellow-fill question">Commander</a>
					</ul>
				</div>
			</div>
			<div class="col-sm-12 col-md-4 col-lg-4">
				<div class="wrapper first text-left">
					<div class="top-content">
						<img class="svg mb-3" src="fonts/svg/dedicated.svg" alt="">
						<div class="title">MC-2</div>
						<div class="fromer"></div>
						<div class="price">9,99<sup>€</sup> <span class="period">/mois</span></div>
					</div>
					<ul class="list-info">
						<li><i class="icon-cpu"></i> <span class="c-purple">CPU</span><br> <span>2x 4GHz</span></li>
						<li><i class="icon-ram"></i> <span class="c-purple">RAM DDR4</span><br> <span>4 Go</span></li>
						<li><i class="icon-drives"></i> <span class="c-purple">Disque</span><br> <span>20 Go SSD</span></li>
						<li><i class="icon-inverse"></i> <span class="c-purple">MySQL</span><br> <span>Inclus</span></li>
						<li><i class="icon-protection"></i> <span class="c-purple">Anti-DDoS</span><br> <span>Game</span></li>
						<li><i class="icon-location"></i> <span class="c-purple">Localisation</span><br> <span>France</span></li>
						<li><a href="/Cart.php?type=2" class="btn btn-default-yellow-fill question">Commander</a>
					</ul>
				</div>
			</div>
			<div class="col-sm-12 col-md-4 col-lg-4">
				<div class="wrapper first text-left">
					<div class="top-content">
						<img class="svg mb-3" src="fonts/svg/dedicated.svg" alt="">
						<div class="title">MC-3</div>
						<div class="fromer"></div>
						<div class="price">14,99<sup>€</sup> <span class="period">/mois</span></div>
					</div>
					<ul class="list-info">
						<li><i class="icon-cpu"></i> <span class="c-purple">CPU</span><br> <span>3x 4GHz</span></li>
						<li><i class="icon-ram"></i> <span class="c-purple">RAM DDR4</span><br> <span>8 Go</span></li>
						<li><i class="icon-drives"></i> <span class="c-purple">Disque</span><br> <span>40 Go SSD</span></li>
						<li><i class="icon-inverse"></i> <span class="c-purple">MySQL</span><br> <span>Inclus</span></li>
						<li><i class="icon-protection"></i> <span class="c-purple">Anti-DDoS</span><br> <span>Game</span></li>
						<li><i class="icon-location"></i> <span class="c-purple">Localisation</span><br> <span>France</span></li>
						<li><a href="/Cart.php?type=3" class="btn btn-default-yellow-fill question">Commander</a>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ***** WHY CHOOSE ANTLER ***** -->
<section class="services sec-normal sec-bg3 motpath scrollme">
	<div class="container">
		<div class="service-wrap">
			<div class="row">
				<!--<div class="col-sm-12">
					<h2 class="section-heading text-white">Why Antler Gaming Servers?</h2>
					<p class="section-subheading">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</p>
				</div>-->
				<div class="col-sm-12 col-md-4 animateme" data-when="enter" data-from="1" data-to="0" data-opacity="1" data-translatey="-100" data-easeinout="0">
					<div class="service-section">
						<img class="svg" src="fonts/svg/gaming.svg" alt="">
						<div class="title">Compatible toutes versions</div>
						<p class="subtitle">
							Installez la version de votre choix, du vanilla au serveur moddé.<br><br><br><br>
						</p>
					</div>
				</div>
				<div class="col-sm-12 col-md-4 animateme" data-when="enter" data-from="1" data-to="0" data-opacity="1" data-translatey="100" data-easeinout="0">
					<div class="service-section">
						<img class="svg" src="fonts/svg/speed.svg" alt="">
						<div class="title">Activation immédiate</div>
						<p class="subtitle">
							Votre serveur est activé immédiatement après la réception du paiement.<br><br><br>
						</p>
					</div>
				</div>
				<div class="col-sm-12 col-md-4 animateme" data-when="enter" data-from="1" data-to="0" data-opacity="1" data-translatey="-100" data-easeinout="0">
					<div class="service-section">
						<!--<div class="plans badge feat bg-pink">manage</div>-->
						<img class="svg" src="fonts/svg/window.svg" alt="">
						<div class="title">Panel propriétaire</div>
						<p class="subtitle">
							Grâce à notre panel propriétaire développé en interne, nous pouvons prendre en compte vos feedbacks et mettre à jour le panel dès qu'une idée est proposée.
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ***** FEATURES ***** -->
<section id="scroll" class="history-section feat01 sec-normal pb-0">
	<div class="container">
		<div class="randomline">
			<div class="bigline"></div>
			<div class="smallline"></div>
		</div>
		<div class="sec-main sec-bg1">
			<div class="row">
				<div class="col-md-12 col-lg-6 wow animated fadeInUp fast first">
					<img class="svg" src="patterns/protectvisitors.svg" alt="Anti-DDoS">
				</div>
				<div class="col-md-12 col-lg-5 offset-lg-1">
					<div class="info-content">
						<h4>Anti-DDoS Game</h4>
						<p>Nos serveurs sont équipés de l'anti-DDoS OVH Game, vous garantissant une protection fiable contre les attaques externes.</p>
					</div>
					<!--<a href="#" class="btn btn-default-yellow-fill mt-3">Protect now</a>-->
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-12 col-lg-5">
					<div class="info-content">
						<h4>Matériel performant</h4>
						<p>Nous utilisons uniquement du matériel récent et performant afin d'assurer la meilleure qualité de service.</p>
					</div>
					<!--<a href="" class="btn btn-default-yellow-fill mt-3">Upgrade now</a>-->
				</div>
				<div class="col-md-12 col-lg-6 offset-lg-1 wow animated fadeInUp fast">
					<img class="svg second" src="patterns/performance.svg" alt="performance">
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-12 col-lg-6 wow animated fadeInUp fast">
					<img class="svg third" src="patterns/monitoring.svg" alt="monitoring 24/7/365">
				</div>
				<div class="col-md-12 col-lg-5 offset-lg-1">
					<div class="info-content">
						<h4>Monitoring</h4>
						<p>Les serveurs hôte sont constamment surveillés via des outils de monitoring afin de détecter immédiatement d'éventuelles pannes.</p>
					</div>
					<!--<a href="#" class="btn btn-default-yellow-fill mt-3">Learn more</a>-->
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ***** HELP ***** -->
<!--<section class="help pt-4 pb-80">
	<div class="container">
		<div class="service-wrap">
			<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-12">
					<div class="help-container">
						<div class="plans badge feat left bg-grey"><i class="fas fa-long-arrow-alt-left"></i></div>
						<a href="/Cart.php?id=1" class="help-item">
							<div class="img">
								<img class="svg ico" src="fonts/svg/cloudfiber.svg" height="65" alt="">
							</div>
							<div class="inform">
								<div class="title">Pas convaincu ?</div>
								<div class="description">Testez notre service en cliquant ici afin de générer un serveur MC-2 gratuit pour une durée de 24h.</div>
							</div>
						</a>
					</div>
				</div>
				<div class="col-sm-12 col-md-6 col-lg-6">
					<div class="help-container">
						<div class="plans badge feat bg-grey"><i class="fas fa-long-arrow-alt-right"></i></div>
						<a href="dedicated" class="help-item">
							<div class="img">
								<img class="svg ico" src="fonts/svg/dedicated.svg" height="65" alt="">
							</div>
							<div class="inform">
								<div class="title">Serveur payant</div>
								<div class="description">Need more power and resources?</div>
							</div>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>-->
<?php
require "inc/Layout/End.php";