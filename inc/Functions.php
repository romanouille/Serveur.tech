<?php
function renderPage() {
	$page = ob_get_contents();
	ob_end_clean();
	
	echo $page;
}