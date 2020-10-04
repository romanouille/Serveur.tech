<?php




$server = new MinecraftServer("193.3.44.3", "hhoerDV3sp3500", false);
$server->stop();
exit;
$server->setServerProperties(false, 25575, "", "survival", false, false, "", "world", "Hello world", 25565, true, true, "easy", 256, 60000, true, 20, true, true, false, true, 10, 256, "", true, 25565, true, true, 4, false, "", 100, "hhoerDV3sp3500");
$server->create("Vanilla", "1.16.3");