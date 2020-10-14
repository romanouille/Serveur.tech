<!DOCTYPE html>
<html lang="fr">
	<!--begin::Head-->
	<head>
		<meta charset="utf-8">
		<title>...</title>
		<meta name="description" content="...">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">
		<!--end::Fonts-->
		<!--begin::Global Theme Styles(used by all pages)-->
		<link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css">
		<link href="/assets/plugins/custom/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css">
		<link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css">
		<!--end::Global Theme Styles-->
		<!--begin::Layout Themes(used by all pages)-->
		<!--end::Layout Themes-->
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" style="background-image: url(/assets/media/bg/bg-10.jpg)" class="quick-panel-right demo-panel-right offcanvas-right header-fixed subheader-enabled page-loading" >
		<!--begin::Main-->
		<!--begin::Header Mobile-->
		<div id="kt_header_mobile" class="header-mobile" >
			<!--begin::Logo-->
			<a href="index.html">
			<img alt="Logo" src="/assets/media/logos/logo.png" class="logo-default max-h-30px">
			</a>
			<!--end::Logo-->
			<div class="d-flex align-items-center">
				<button class="btn p-0 burger-icon burger-icon-left ml-4" id="kt_header_mobile_toggle">
				<span></span>
				</button>
				<button class="btn btn-icon btn-hover-transparent-white p-0 ml-3" id="kt_header_mobile_topbar_toggle">
					</svg><!--end::Svg Icon--></span>		
				</button>
			</div>
		</div>
		<!--end::Header Mobile-->
		<div class="d-flex flex-column flex-root">
		<!--begin::Page-->
		<div class="d-flex flex-row flex-column-fluid page">
		<!--begin::Wrapper-->
		<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
		<!--begin::Header-->
		<div id="kt_header" class="header header-fixed" >
			<!--begin::Container-->
			<div class=" container d-flex align-items-stretch justify-content-between">
				<!--begin::Left-->
				<div class="d-flex align-items-stretch mr-3">
					<!--begin::Header Logo-->
					<div class="header-logo">
						<a href="/">
						<img alt="Logo" src="/assets/media/logos/logo.png" class="logo-default max-h-40px">
						<img alt="Logo" src="/assets/media/logos/logo.png" class="logo-sticky max-h-40px">
						</a>
					</div>
					<!--end::Header Logo-->
					<!--begin::Header Menu Wrapper-->
					<div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
						<!--begin::Header Menu-->
						<div id="kt_header_menu" class="header-menu header-menu-left header-menu-mobile header-menu-layout-default" >
							<!--begin::Header Nav-->
							<ul class="menu-nav">
								<li class="menu-item">
									<a href="/" class="menu-link"><span class="menu-text">Retour au site</span><i class="menu-arrow"></i></a>
								</li>
								<li class="menu-item">
									<a href="/ClientArea.php" class="menu-link"><span class="menu-text">Liste des serveurs</span><i class="menu-arrow"></i></a>
								</li>
								<li class="menu-item">
									<a href="/Invoices.php" class="menu-link"><span class="menu-text">Factures</span><i class="menu-arrow"></i></a>
								</li>
<?php
if (!$_SESSION["admin"]) {
?>
								<li class="menu-item">
									<a href="/Ticket.php" class="menu-link"><span class="menu-text">Support</span><i class="menu-arrow"></i></a>
								</li>
<?php
} else {
?>
								<li class="menu-item">
									<a href="/AdminTicketsList.php" class="menu-link"><span class="menu-text">Tickets</span><i class="menu-arrow"></i></a>
								</li>
<?php
}
?>
							</ul>
							<!--end::Header Nav-->
						</div>
						<!--end::Header Menu-->
					</div>
					<!--end::Header Menu Wrapper-->
				</div>
			</div>
			<!--end::Container-->
		</div>
		<!--end::Header-->
		<!--begin::Content-->
		<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
		<!--begin::Subheader-->
		<div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
			<div class=" container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
				<!--begin::Info-->
				<div class="d-flex align-items-center flex-wrap mr-1">
					<!--begin::Heading-->
					<div class="d-flex flex-column">
						<!--begin::Title-->
						<h2 class="text-white font-weight-bold my-2 mr-5">
							<?=$breadcrumb?>
						</h2>
						<!--end::Title-->
						<!--begin::Breadcrumb-->
						<div class="d-flex align-items-center font-weight-bold my-2">
							<!--begin::Item-->
							<a href="/ServerSettings.php" class="opacity-75 hover-opacity-100">
							<i class="flaticon2-shelter text-white icon-1x"></i>
							</a>
							<!--end::Item-->
							<!--begin::Item-->
							<span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
							<a href="<?=$_SERVER["REQUEST_URI"]?>" class="text-white text-hover-white opacity-75 hover-opacity-100">
							<?=$breadcrumb?>
							</a>
							<!--end::Item-->
						</div>
						<!--end::Breadcrumb-->
					</div>
					<!--end::Heading-->
				</div>
				<!--end::Info-->
			</div>
		</div>
		<!--end::Subheader-->
		<div class="d-flex flex-column-fluid">
