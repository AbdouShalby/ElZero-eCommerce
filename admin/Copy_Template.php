<?php

    /*
    =======================================================
    == Template Page
    =======================================================
     */

    ob_start(); // Output Buffering Start
    session_start();
    $pageTitle = ""; // Page Title

    // Check If There's A Session With Your Username
    if (isset($_SESSION['Username'])) {
        include "init.php";

        // Check If Request Contain Do Statement Or Not If False Return To Manage Page
        $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

        // Start Manage Page
        if ($do == 'Manage') { // Manage Members Page
            echo "Welcome Manage";

        } elseif ($do == 'Add') { // Add Page
            echo "Welcome Add";

        } elseif ($do == 'Insert') { // Insert Page
            echo "Welcome Insert";

        } elseif ($do == 'Edit') { // Edit Page
            echo "Welcome Edit";

        } elseif ($do == "Update") { // Update Page
            echo "Welcome Update";

        } elseif ($do == 'Delete') { // Delete Member Page
            echo "Welcome Delete";

        } elseif ($do == "Activate") { // Activate Page
            echo "Welcome Activate";
        }
        include $tmpl . "footer.php";
    } else { // If There's No Session With Your Username Redirect To Index Page
        header('Location: index.php');
        exit();
    }
ob_end_flush();
?>