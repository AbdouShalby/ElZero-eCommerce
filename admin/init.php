<?php

include "connect.php";

// Routes
$tmpl   = "includes/templates/"; // Template Directory
$lang   = "includes/languages/"; // Languages Directory
$func   = "includes/functions/"; // Functions Directory
$css    = "layout/css/"; // CSS Directory
$js     = "layout/js/"; // JS Directory


// Include The Important Files
include $func . "functions.php";
include $lang . "english.php";
include $tmpl . "header.php";

// Include Navbar In All Pages Expect The One With $noNavbar Variable
if (!isset($noNavbar)) { include $tmpl . "navbar.php"; }