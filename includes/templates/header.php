<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title><?php echo getTitle() ?></title>
        <link rel="stylesheet" href="<?php echo $css; ?>bootstrap.min.css" />
        <link rel="stylesheet" href="<?php echo $css; ?>font-awesome.min.css" />
        <link rel="stylesheet" href="<?php echo $css; ?>jquery-ui.css" />
        <link rel="stylesheet" href="<?php echo $css; ?>jquery.selectBoxIt.css" />
        <link rel="stylesheet" href="<?php echo $css; ?>frontend.css" />
    </head>
    <body>
    <nav class="navbar navbar-inverse">
        <div class="container">
            <?php
            if (isset($_SESSION['user'])) { // If You Print $sessionUser Variable Here It Will Be Always True Because Its Set Check int.php ?>
                <?php
                $stmt = $con->prepare("SELECT users.Avatar AS UserIMG FROM users WHERE UserID = ?");
                $stmt->execute(array($_SESSION['uid']));
                $image = $stmt->fetch();
                $uImg = 'admin\uploads\avatars\\' .$image['UserIMG'];

                ?>
                <div class="btn-group my-info pull-right">
                        <span class="btn dropdown-toggle" data-toggle="dropdown">
                            <?php
                            if (empty($image['UserIMG'])){
                                echo '<img class="img-responsive center-block my-image pull-left" src="user.png" alt="User Image">';
                            } else {
                                echo '<img class="img-responsive center-block base-image pull-left" src="' . $uImg . '" alt="User Image">';
                            }
                            ?>
                        </span>
                    <ul class="dropdown-menu">
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="newad.php">New Item</a></li>
                        <li><a href="profile.php#my-ads">My Items</a></li>
                        <li><a href="profile.php#my-comments">My Comments</a></li>
                        <li><a href="settings.php">Settings</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </div>
                <?php
            } else { ?>
                <a href="login.php">
                    <span class="navbar-login pull-right"><i class="fa fa-sign-in"></i> Login</span>
                </a>
            <?php } ?>
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php"><i class="fa fa-home"></i> Home</a>
            </div>
            <div class="collapse navbar-collapse" id="app-nav">
                <div class="btn-group my-info">
                    <span class="btn dropdown-toggle" data-toggle="dropdown">
                        <i class="navbar-categories fa fa-tags my-drop"> Categories</i>
                    </span>
                    <ul class="dropdown-menu">
                        <?php
                        $allCats = getAllFrom("*", "categories", "WHERE Parent = 0", "", "ID", "ASC");
                        foreach ($allCats as $cat) {
                            echo
                            '<li>
                                <a href="categories.php?pageid=' . $cat['ID'] . '">
                                    ' . $cat['Name'] . '
                                </a>
                            </li>';
                            $childCats = getAllFrom("*", "categories", "WHERE Parent = {$cat['ID']}", "", "ID");
                            foreach ($childCats as $child) {
                                echo
                                '<li class="child">
                                    <a href="categories.php?pageid=' . $child['ID'] . '">
                                        - ' . $child['Name'] . '
                                    </a>
                                </li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>