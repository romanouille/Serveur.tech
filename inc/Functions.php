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