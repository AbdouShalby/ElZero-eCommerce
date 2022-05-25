<?php

// Error Reporting
ini_set('display_errors', 'Off');
error_reporting(E_ALL);

include "admin/connect.php";

$sessionUser = '';
if (isset($_SESSION['user'])){
    $sessionUser = $_SESSION['user'];
    $sessionID = $_SESSION['uid'];
}

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