<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";

if (!isset($_SESSION["2fa"]) || !$_SESSION["2fa"]) {
	header("Location: /Auth.php");
	exit;
}

require "inc/Start.php";
?>
<!-- ***** BANNER ***** -->
<div class="top-header exapath-w">
	<div class="total-grad-inverse"></div>
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="wrapper">
					<div class="heading">Espace client</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- ***** YOUR CONTENT ***** -->
<section class="balancing sec-normal bg-white pb-80">
	<div class="h-services">
		<div class="container">
			<div class="randomline">
				<div class="bigline"></div>
				<div class="smallline"></div>
			</div>
			
			<div class="row">
				<div class="col-md-4">
					<ul class="list-group">
						<li class="list-group-item">Cras justo odio</li>
						<li class="list-group-item">Dapibus ac facilisis in</li>
						<li class="list-group-item">Morbi leo risus</li>
						<li class="list-group-item">Porta ac consectetur ac</li>
						<li class="list-group-item">Vestibulum at eros</li>
					</ul>
				</div>
				
				<div class="col-md-8">
					Hello world.
				</div>
			</div>
		</div>
	</div>
</section>
<?php
require "inc/End.php";