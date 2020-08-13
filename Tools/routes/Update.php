<?php
function update(int $timestamp) {
	shell_exec("sudo birdc show route | zip > routes4-$timestamp.zip");
	shell_exec("sudo birdc6 show route | zip > routes6-$timestamp.zip");
}

function logs(string $text) {
	echo date("[H:i:s] ")."$text\n";
}

logs("Hello world.");


while(1) {
	$time = time();
	
	logs("Nouvelle version : $time");
	update($time);
	
	file_put_contents("last_version.txt", $time);
	
	$list = glob("routes*");
	
	foreach ($list as $value) {
		$file = $value;
		
		$value = explode("-", $value)[1];
		$value = explode(".", $value)[0];
		
		if (time()-$value > 60) {
			logs("Del -> $file");
			unlink($file);
		}
	}
	
	sleep(1);
}