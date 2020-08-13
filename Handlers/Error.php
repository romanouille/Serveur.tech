<?php
$pageTitle = "Erreur ".http_response_code();
$pageDescription = "Erreur ".http_response_code();

require "Pages/Error.php";
exit;