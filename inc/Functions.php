<?php
function renderPage() {
	$page = ob_get_contents();
	ob_end_clean();
	
	echo $page;
}

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

function random(int $length) : string {
	$chars = str_split("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
	$result = "";
	
	for ($i = 1; $i <= $length; $i++) {
		$result .= $chars[rand(0, count($chars)-1)];
	}
	
	return $result;
}