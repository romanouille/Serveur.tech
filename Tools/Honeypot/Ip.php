<?php
for ($i = 137; $i <= 139; $i++) {
	for ($y = 0; $y <= 255; $y++) {
		shell_exec("ip addr add dev dummy1 81.16.$i.$y/32");
	}
}