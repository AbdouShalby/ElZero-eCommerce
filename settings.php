<?php
ob_start();
session_start();
$pageTitle = 'Settings'; // For Page Title
include 'init.php';

$stmt = $con->prepare("SELECT * FROM users WHERE UserID = ? LIMIT 1");
// Execute Query
$stmt->execute(array($sessionID));
// Fetch The Data
$row = $stmt->fetch();
// The Row Count
$count = $stmt->rowCount();

// If There's Such ID
if ($count > 0) { ?>

    <h1 class="text-center">Edit Information</h1>
    <div class="container">
        <div class="col-md-3 profile-image">
            <?php
            if (empty($image['UserIMG'])){
                echo '<img class="img-responsive img-thumbnail center-block" src="user.png" alt="Default">';
            } else {
                echo '<a href="' . $uImg . '" target="_blank"><img class="img-responsive img-thumbnail center-block" src="' . $uImg . '" alt="Item Image"></a>';
            }
            ?>
        </div>
        <div class="col-md-9 profile-info">
            <form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="userid" value="<?php echo $sessionID ?>" />

                <!-- Start Username Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Username</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="username" class="form-control custom-form-control" value="<?php echo $row["Username"]?>" autocomplete="off" required="required"/>
                    </div>
                </div>
                <!-- End Username Field -->

                <!-- Start Password Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="hidden" name="oldpassword" value="<?php echo $row["Password"]?>" />
                        <input type="password" name="newpassword" class="form-control custom-form-control" autocomplete="new-password" placeholder="Leave Blank If You Dont Want To Change" />
                    </div>
                </div>
                <!-- End Password Field -->

                <!-- Start Email Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="email" name="email" class="form-control custom-form-control" value="<?php echo $row["Email"]?>" required="required"/>
                    </div>
                </div>
                <!-- End Email Field -->

                <!-- Start FullName Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Full Name</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="full" class="form-control custom-form-control" value="<?php echo $row["FullName"]?>" required="required"/>
                    </div>
                </div>
                <!-- End FullName Field -->

                <!-- Start Submit Field -->
                <div class="form-group form-group-lg">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" value="Save" class="btn btn-primary btn-lg"/>
                    </div>
                </div>
                <!-- End Submit Field -->
            </form>
        </div>
    </div>

<?php } else { // If There's No Such ID Show Error Message
    echo '<div class="container" >';
    $theMsg = "<div class='alert alert-danger'>There's No Such ID</div>";
    redirectFunc($theMsg);
    echo "</div>";
}

include $tmpl . 'footer.php';
ob_end_flush();
?>