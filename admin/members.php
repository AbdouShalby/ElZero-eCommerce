<?php

/*
=======================================================
== Manage Members Page
== You Can Add | Edit | Delete Members From Here
=======================================================
 */

ob_start(); // Output Buffering Start
session_start();
$pageTitle = "Members"; // Page Title

// Check If There's A Session With Your Username
if (isset($_SESSION['Username'])) {
    include "init.php";

    // Check If Request Contain Do Statement Or Not If False Return To Manage Page
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    // Start Manage Page
    if ($do == 'Manage') { // Manage Members Page

        $query = "";
        if (isset($_GET["page"]) && $_GET["page"] == "Pending") {
            $query = "AND RegStatus = 0";
        }

        // Select All User Except Admins
        $stmt = $con->prepare("SELECT * FROM users WHERE GroupID != 1 $query ORDER BY UserID DESC");

        // Execute The Statement
        $stmt->execute();

        // Assign To Variable
        $rows = $stmt->fetchAll();
        ?>

        <h1 class="text-center">Manage Members</h1>
        <div class="container members">
        <?php if (!empty($rows)) { ?>
            <div class="table-responsive">
                    <table class="main-table manage-members text-center table table-bordered">
                    <tr>
                        <td>#ID</td>
                        <td>Avatar</td>
                        <td>Username</td>
                        <td>Email</td>
                        <td>Full Name</td>
                        <td>Registered Date</td>
                        <td>Control</td>
                    </tr>

                    <?php
                    foreach ($rows as $row) {
                        echo "<tr>";
                        echo "<td>" . $row['UserID'] . "</td>";
                        echo "<td>";
                        if (empty($row['Avatar'])) {
                            echo "<img src='uploads/avatars/default.png' alt='' />";
                        } else {
                            echo "<img src='uploads/avatars/" . $row['Avatar'] . "' alt='' />";
                        }
                        echo "</td>";
                        echo "<td>" . $row['Username'] . "</td>";
                        echo "<td>" . $row['Email'] . "</td>";
                        echo "<td>" . $row['FullName'] . "</td>";
                        echo "<td>" . $row['Date'] . "</td>";
                        echo "<td>
                            <a href='members.php?do=Edit&userid=" . $row['UserID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                            <a href='members.php?do=Delete&userid=" . $row['UserID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete</a>";
                        if ($row["RegStatus"] == 0) {
                            echo "<a href='members.php?do=Activate&userid=" . $row['UserID'] . "' class='btn btn-info activate'><i class='fa fa-check'></i> Activate</a>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>

                </table>
            </div>
            <a href="members.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> New Member</a>
            <?php
            } else {
                echo '<div class="col-md-12 text-center">';
                    echo '<h2 class="no-items">No Members</h2>';
                echo '</div>';
                echo '<div class="col-md-12 text-center">';
                    echo '<a href="members.php?do=Add" class="btn btn-primary no-items-button"><i class="fa fa-plus"></i> New Member</a>';
                echo '</div>';
            }?>
        </div>

    <?php } elseif ($do == 'Add') { // Add Page ?>

        <h1 class="text-center">Add New Member</h1>
        <div class="container">
            <form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">

                <!-- Start Username Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Username</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="username" class="form-control custom-form-control" autocomplete="off" required="required" placeholder="Username To Login Into Shop" />
                    </div>
                </div>
                <!-- End Username Field -->

                <!-- Start Password Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="password" name="password" class="password form-control custom-form-control" autocomplete="new-password" required="required" placeholder="Password Must Be Hard & Complex" />
                        <i class="show-pass fa fa-eye fa-2x"></i>
                    </div>
                </div>
                <!-- End Password Field -->

                <!-- Start Email Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="email" name="email" class="form-control custom-form-control" required="required" placeholder="Email Must Be Valid" />
                    </div>
                </div>
                <!-- End Email Field -->

                <!-- Start FullName Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Full Name</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="full" class="form-control custom-form-control" required="required" placeholder="Full Name Appear In Your Profile Page" />
                    </div>
                </div>
                <!-- End FullName Field -->

                <!-- Start User Avatar Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Avatar</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="file" name="avatar" class="form-control custom-form-control" required="required" />
                    </div>
                </div>
                <!-- End User Avatar Field -->

                <!-- Start Submit Field -->
                <div class="form-group form-group-lg">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" value="Add Member" class="btn btn-primary btn-lg"/>
                    </div>
                </div>
                <!-- End Submit Field -->
            </form>
        </div>

    <?php
    } elseif ($do == 'Insert') { // Insert Page

        // Insert Member Page
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo '<h1 class="text-center">Insert Member</h1>';
            echo '<div class="container" >';

            // Upload Variables
            $avatarName = $_FILES['avatar']['name'];
            $avatarSize = $_FILES['avatar']['size'];
            $avatarTmp  = $_FILES['avatar']['tmp_name'];
            $avatarType = $_FILES['avatar']['type'];

            // List For Allowed File Types To Upload
            $avatarAllowedExtensions = array("jpeg", "jpg", "png", "gif");

            // Get Avatar Extension
            $explode = explode('.', $avatarName);
            $avatarExtension = strtolower(end($explode));

            // Get Variable From The Form
            $user   = $_POST['username'];
            $pass   = $_POST['password'];
            $email  = $_POST['email'];
            $name   = $_POST['full'];

            // Hash Password
            $hashPass = sha1($_POST['password']);

            // Validate The Form
            $formErrors = array();
            if (empty($user)) {
                $formErrors[] = "Username Can't Be <strong>Empty</strong>";
            } elseif (strlen($user) < 4 ) {
                $formErrors[] = "Username Can't Be Less Than <strong>4 Characters</strong>";
            } elseif (strlen($user) > 20 ) {
                $formErrors[] = "Username Can't Be More Than <strong>20 Characters</strong>";
            }

            if (empty($name)) {
                $formErrors[] = "Full Name Can't Be <strong>Empty</strong>";
            }

            if (empty($pass)) {
                $formErrors[] = "Password Can't Be <strong>Empty</strong>";
            }

            if (empty($email)) {
                $formErrors[] = "Email Can't Be <strong>Empty</strong>";
            }

            if (!empty($avatarName) && !in_array($avatarExtension, $avatarAllowedExtensions)) {
                $formErrors[] = "This Extension Is Not <strong>Allowed</strong>";
            }

            if (empty($avatarName)) {
                $formErrors[] = "Avatar Is <strong>Required</strong>";
            }

            if ($avatarSize > 4194304) {
                $formErrors[] = "Avatar Can't Be Larger Than <strong>4MB</strong>";
            }

            // Loop Into Errors Array And Echo It
            foreach ($formErrors as $error) {
                echo "<div class='alert alert-danger'>" . $error . "</div>";
            }

            // Check If There's No Error Proceed The Update Operation
            if (empty($formErrors)) {

                $avatar = rand(0, 10000000000) . '_' . $avatarName;
                move_uploaded_file($avatarTmp, "uploads\avatars\\" . $avatar);

                // Check If User Exist In Database
                $check = checkItem("Username", "users", $user);
                if ($check == 1) {
                    $theMsg = "<div class='alert alert-danger'>Sorry This <strong>Username Is Exist</strong></div>";
                    redirectFunc($theMsg, "back");

                } else {

                    // Insert User Info In Database
                    $stmt = $con->prepare("INSERT INTO 
                    users(Username, Password, Email, FullName, RegStatus, Date, Avatar) 
                    VALUES(:zuser, :zpass, :zmail, :zname, 1, now(), :zavatar)");
                    $stmt->execute(array(
                        'zuser'     => $user,
                        'zpass'     => $hashPass,
                        'zmail'     => $email,
                        'zname'     => $name,
                        'zavatar'   => $avatar
                    ));

                    // Echo Success Message
                    $theMsg = "<div class='alert alert-success'><strong>" . $stmt->rowCount() . "</strong> Record Inserted</div>";
                    redirectFunc($theMsg, 'back');
                }
            }
        } else {
            echo '<div class="container" >';
            $theMsg = "<div class='alert alert-danger'>Sorry You Can't Browse This Page Directly</div>";
            redirectFunc($theMsg);
            echo "</div>";
        }
        echo "</div>";

    } elseif ($do == 'Edit') { // Edit Page

        // Check If you Get The UserID IS Numeric & Get The Integer Value Of It
        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

        // Select All Data Depend On This ID
        $stmt = $con->prepare("SELECT * FROM users WHERE UserID = ? LIMIT 1");

        // Execute Query
        $stmt->execute(array($userid));

        // Fetch The Data
        $row = $stmt->fetch();

        // The Row Count
        $count = $stmt->rowCount();

        // If There's Such ID
        if ($count > 0) { ?>

            <h1 class="text-center">Edit Member</h1>
            <div class="container">
                <form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="userid" value="<?php echo $userid ?>" />

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

        <?php } else { // If There's No Such ID Show Error Message
            echo '<div class="container" >';
            $theMsg = "<div class='alert alert-danger'>There's No Such ID</div>";
            redirectFunc($theMsg);
            echo "</div>";
        }
    } elseif ($do == "Update") { // Update Page

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo '<h1 class="text-center">Update Member</h1>';
            echo '<div class="container" >';

            // Get Variable From The Form
            $id     = $_POST['userid'];
            $user   = $_POST['username'];
            $email  = $_POST['email'];
            $name   = $_POST['full'];

            // Password Trick
            $pass = empty($_POST['newpassword']) ? $_POST['oldpassword'] : sha1($_POST['newpassword']);

            // Validate The Form
            $formErrors = array();
            if (empty($user)) {
                $formErrors[] = "Username Can't Be <strong>Empty</strong>";
            } elseif (strlen($user) < 4 ) {
                $formErrors[] = "Username Can't Be Less Than <strong>4 Characters</strong>";
            } elseif (strlen($user) > 20 ) {
                $formErrors[] = "Username Can't Be More Than <strong>20 Characters</strong>";
            }

            if (empty($name)) {
                $formErrors[] = "Full Name Can't Be <strong>Empty</strong>";
            }

            if (empty($email)) {
                $formErrors[] = "Email Can't Be <strong>Empty</strong>";
            }

            // Loop Into Errors Array And Echo It
            foreach ($formErrors as $error) {
                echo "<div class='alert alert-danger'>" . $error . "</div>";
            }

            // Check If There's No Error Proceed The Update Operation
            if (empty($formErrors)) {

                $stmt2 = $con->prepare("SELECT 
                                                    * 
                                                FROM 
                                                    users 
                                                WHERE 
                                                    Username = ? 
                                                AND 
                                                    UserID != ?");
                $stmt2->execute(array($user, $id));
                $count = $stmt2->rowCount();

                if ($count == 1) {
                    $theMsg = "<div class='alert alert-danger'>Sorry This <strong>Username Is Exist</strong></div>";
                    redirectFunc($theMsg, "back");
                } else {

                    // Update The Database With This Info
                    $stmt = $con->prepare("UPDATE users SET Username = ?, Email = ?, FullName = ?, Password = ? WHERE UserID = ?");
                    $stmt->execute(array($user, $email, $name, $pass, $id));

                    // Echo Success Message
                    $theMsg = "<div class='alert alert-success'><strong>" . $stmt->rowCount() . "</strong> Record Updated</div>";
                    redirectFunc($theMsg, 'back');
                }
            }
        } else {
            $theMsg = "<div class='alert alert-danger'>Sorry You Can't Browse This Page Directly</div>";
            redirectFunc($theMsg);
        }
        echo "</div>";

    } elseif ($do == 'Delete') { // Delete Member Page
        echo '<h1 class="text-center">Delete Member</h1>';
        echo '<div class="container" >';

        // Check If you Get The UserID IS Numeric & Get The Integer Value Of It
        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

        // Select All Data Depend On This ID
        $check = checkItem("userid", "users", $userid);

        // If There's Such ID
        if ($check > 0) {
            // Delete User Data Depend On This ID
            $stmt = $con->prepare("DELETE FROM users WHERE UserID = :zuserid");
            $stmt->bindParam(":zuserid", $userid);

            // Execute Query
            $stmt->execute();

            $theMsg = "<div class='alert alert-success'><strong>" . $stmt->rowCount() . "</strong> Record Deleted</div>";
            redirectFunc($theMsg, 'back');
        } else {
            echo '<div class="container" >';
            $theMsg = "<div class='alert alert-danger'>This ID In Not Exist</div>";
            redirectFunc($theMsg);
            echo "</div>";
        }
        echo "</div>";
    } elseif ($do == "Activate") { // Activate Page
        echo '<h1 class="text-center">Activate Member</h1>';
        echo '<div class="container" >';

        // Check If you Get The UserID IS Numeric & Get The Integer Value Of It
        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

        // Select All Data Depend On This ID
        $check = checkItem("userid", "users", $userid);

        // If There's Such ID
        if ($check > 0) {
            // Delete User Data Depend On This ID
            $stmt = $con->prepare("UPDATE users SET RegStatus = 1 WHERE UserID = ?");

            // Execute Query
            $stmt->execute(array($userid));

            $theMsg = "<div class='alert alert-success'><strong>" . $stmt->rowCount() . "</strong> Record Updated</div>";
            redirectFunc($theMsg);
        } else {
            $theMsg = "<div class='alert alert-danger'>This ID In Not Exist</div>";
            redirectFunc($theMsg);
        }
        echo "</div>";
    }
    include $tmpl . "footer.php";

} else { // If There's No Session With Your Username Redirect To Index Page
    header('Location: index.php');
    exit();
}
ob_end_flush();
?>