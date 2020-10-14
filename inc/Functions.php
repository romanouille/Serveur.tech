<?php
/**
 * Effectue le rendu de la page
 */
function renderPage() {
	$page = ob_get_contents();
	ob_end_clean();
	
	preg_match_all("`<!--(.+)-->`isU", $page, $comments);
	$comments = $comments[0];
	
	foreach ($comments as $comment) {
		$page = str_replace($comment, "", $page);
	}
	
	//$page = str_replace("  ", " ", str_replace("	", "", str_replace("\n", "", $page)));
	
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