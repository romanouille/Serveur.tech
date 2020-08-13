<?php
ini_set("memory_limit", -1);

set_include_path("../");
chdir("../");

$dev = false;

require "Core/Functions.php";
require "Core/ReseauIo.class.php";
require "Core/Cache.class.php";
require "Core/Init.php";