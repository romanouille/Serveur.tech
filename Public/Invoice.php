<?php
set_include_path("../");
chdir("../");

require "inc/Init.php";

if (!isset($user) || !$_SESSION["2fa"]) {
	header("Location: /Auth.php");
	exit;
}

if (!isset($_GET["id"]) || !is_string($_GET["id"]) || !is_numeric($_GET["id"])) {
	http_response_code(400);
	require "inc/Pages/Error.php";
	exit;
}

$invoice = $user->getInvoice($_GET["id"]);
if (empty($invoice)) {
	http_response_code(404);
	require "inc/Pages/Error.php";
	exit;
}

if ($_SESSION["admin"]) {
	$user = new User($invoice["owner"]);
}
$userProfile = $user->getProfile();

require "inc/fpdf.class.php";
require "inc/PDF_Invoice.php";

$pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
$pdf->AddPage();
$pdf->addSociete( "Romuald Richard",
                  "8 La bergerie\n" .
                  "28120 Nonvilliers-Grandhoux\n".
                  "SIRET 83828793600010\n" .
                  "TVA non applicable - article 293 B du CGI");
$pdf->fact_dev( "Facture #".strtoupper(dechex($invoice["microtime"])));
$pdf->addDate(date("d/m/Y", substr($invoice["microtime"], 0, -4)));
$pdf->addClientAdresse((!empty($userProfile["company_name"]) ? utf8_decode($userProfile["company_name"]) : utf8_decode($userProfile["first_name"])." ".utf8_decode($userProfile["last_name"]))."\n".utf8_decode($userProfile["address1"])."\n".(!empty($userProfile["address2"]) ? utf8_decode($userProfile["address2"])."\n" : "").$userProfile["postal_code"]." ".utf8_decode($userProfile["city"])."\n".utf8_decode($countries[$userProfile["country"]]));
$pdf->addReglement("PayPal");
$cols=array( "REFERENCE"    => 23,
             "DESIGNATION"  => 78,
             "QUANTITE"     => 22,
             "P.U. HT"      => 26,
             "MONTANT H.T." => 30,
             "TVA"          => 11 );
$pdf->addCols( $cols);
$cols=array( "REFERENCE"    => "L",
             "DESIGNATION"  => "L",
             "QUANTITE"     => "C",
             "P.U. HT"      => "R",
             "MONTANT H.T." => "R",
             "TVA"          => "C" );
$pdf->addLineFormat( $cols);
$pdf->addLineFormat($cols);

$y    = 109;
$line = array( "REFERENCE"    => "MC-".$invoice["type"],
               "DESIGNATION"  => "Serveur Minecraft :\n- {$offers[$invoice["type"]]["ram"]} Go RAM\n- {$offers[$invoice["type"]]["cpu"]} coeur".($offers[$invoice["type"]]["cpu"] > 1 ? "s" : "")." CPU\n- {$offers[$invoice["type"]]["ssd"]} Go SSD",
               "QUANTITE"     => "1",
               "P.U. HT"      => $offers[$invoice["type"]]["price"],
               "MONTANT H.T." => $offers[$invoice["type"]]["price"],
               "TVA"          => "0" );
$size = $pdf->addLine( $y, $line );
$y   += $size + 2;
        

$pdf->addTVAs($offers[$invoice["type"]]["price"]);
$pdf->addCadreEurosFrancs();
$pdf->Output();