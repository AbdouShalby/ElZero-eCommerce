<?php
ob_start(); // Output Buffering Start

    header('Location: ../../../index.php');
    exit();

ob_end_flush();
?>