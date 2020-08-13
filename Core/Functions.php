<?php
function renderPage() {
	global $api, $data;
	
	if ($api) {
		header("Content-Type: application/json");
		ob_end_clean();
		echo json_encode(isset($data) ? $data : []);
		exit;
	}
	
	$data = ob_get_contents();
	ob_end_clean();
	
	$data = str_replace("> <", "><", str_replace("  ", "", str_replace("\n", "", str_replace("	", "", $data))));
	
	echo $data;
}

/**
 * Écrit du texte à l'écran
 * 
 * @param string $text Texte à afficher
 */
function logs(string $text) {
	echo date("[H:i:s] ")."$text\n";
}

/**
 * Télécharge un fichier
 * 
 * @param string $url URL
 * @param string $output Chemin de destination
 */
function download(string $url, string $output) {
	shell_exec("wget -U \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36\" -O $output $url");
}