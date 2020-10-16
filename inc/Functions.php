<?php
/**
 * Effectue le rendu de la page
 */
function renderPage() {
	$page = ob_get_contents();
	ob_end_clean();
	
	if ($_SERVER["PHP_SELF"] != "/Invoice.php") {
		preg_match_all("`<!--(.+)-->`isU", $page, $comments);
		$comments = $comments[0];
		
		foreach ($comments as $comment) {
			$page = str_replace($comment, "", $page);
		}
		
		$page = str_replace("  ", " ", str_replace("	", "", str_replace("\n", "", $page)));
	}
	
	echo $page;
}

/**
 * Convertie une chaine vers une nouvelle chaine possédant des caractères spécifiques whitelistés
 *
 * @param string $string Chaine à convertir
 * @param string $whitelist Whitelist
 *
 * @return string Résultat
 */
function normalizeString(string $string, string $whitelist = "abcdefghijklmnopqrstuvwxyz") {
	$whitelist = str_split($whitelist);
	$string = str_split($string);
	$result = "";
	
	foreach ($string as $char) {
		if (in_array($char, $whitelist)) {
			$result .= $char;
		}
	}
	
	return $result;
}

/**
 * Génère une chaine aléatoire
 *
 * @param int $length Taille de la chaine à générer
 *
 * @return string Chaine générée
 */
function random(int $length) : string {
	$chars = str_split("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
	$result = "";
	
	for ($i = 1; $i <= $length; $i++) {
		$result .= $chars[rand(0, count($chars)-1)];
	}
	
	return $result;
}

/**
 * Vérifie si l'IP du client est française
 *
 * @return bool Résultat
 */
function isFrenchIp() : bool {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://reseau.io/api/ip/{$_SERVER["REMOTE_ADDR"]}");
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_TIMEOUT, 3);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$page = @json_decode(curl_exec($curl), true);
	
	if (!isset($page["blocks"][0]["country"])) {
		return true;
	}
	
	return $page["blocks"][0]["country"] == "fr";
}