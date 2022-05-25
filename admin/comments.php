<?php

/*
=======================================================
== Manage Comments Page
== You Can Edit | Delete Approve Comments From Here
=======================================================
 */

ob_start(); // Output Buffering Start
session_start();
$pageTitle = "Comments"; // Page Title

// Check If There's A Session With Your Username
if (isset($_SESSION['Username'])) {
    include "init.php";

    // Check If Request Contain Do Statement Or Not If False Return To Manage Page
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    // Start Manage Page
    if ($do == 'Manage') { // Manage Members Page

        // Select All User Except Admins
        $stmt = $con->prepare("SELECT 
                                            comments.*, items.Name AS Item_Name, users.Username AS Member
                                        FROM 
                                            comments
                                        INNER JOIN
                                            items
                                        ON  
                                            items.Item_ID = comments.item_id
                                        INNER JOIN
                                            users       
                                        ON  
                                            users.UserID = comments.user_id
                                        ORDER BY
                                            c_id DESC");

        // Execute The Statement
        $stmt->execute();

        // Assign To Variable
        $comments = $stmt->fetchAll();
        ?>

        <h1 class="text-center">Manage Comments</h1>
        <div class="container comments">
        <?php if (!empty($comments)) { ?>
            <div class="table-responsive">
                <table class="main-table text-center table table-bordered">
                    <tr>
                        <td>#ID</td>
                        <td>Comment</td>
                        <td>Item Name</td>
                        <td>User Name</td>
                        <td>Added Date</td>
                        <td>Control</td>
                    </tr>

                    <?php
                    foreach ($comments as $comment) {
                        echo "<tr>";
                        echo "<td>" . $comment['c_id'] . "</td>";
                        echo "<td>" . $comment['comment'] . "</td>";
                        echo "<td>" . $comment['Item_Name'] . "</td>";
                        echo "<td>" . $comment['Member'] . "</td>";
                        echo "<td>" . $comment['comment_date'] . "</td>";
                        echo "<td>
                            <a href='comments.php?do=Edit&comid=" . $comment['c_id'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                            <a href='comments.php?do=Delete&comid=" . $comment['c_id'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete</a>";
                        if ($comment["status"] == 0) {
                            echo "<a href='comments.php?do=Approve&comid=" . $comment['c_id'] . "' class='btn btn-info activate'><i class='fa fa-check'></i> Approve</a>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
            <?php
            } else {
                echo '<div class="col-md-12 text-center">';
                    echo '<h2 class="no-items">No Comments</h2>';
                echo '</div>';
            }?>
        </div>
    <?php
    } elseif ($do == 'Edit') { // Edit Page

        // Check If you Get The CommentID IS Numeric & Get The Integer Value Of It
        $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0;

        // Select All Data Depend On This ID
        $stmt = $con->prepare("SELECT * FROM comments WHERE c_id = ?");

        // Execute Query
        $stmt->execute(array($comid));

        // Fetch The Data
        $row = $stmt->fetch();

        // The Row Count
        $count = $stmt->rowCount();

        // If There's Such ID
        if ($count > 0) { ?>

            <h1 class="text-center">Edit Comment</h1>
            <div class="container">
                <form class="form-horizontal" action="?do=Update" method="POST">
                    <input type="hidden" name="comid" value="<?php echo $comid ?>" />

                    <!-- Start Comment Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Comment</label>
                        <div class="col-sm-10 col-md-6">
                            <textarea class="form-control" name="comment"><?php echo $row['comment']; ?></textarea>
                        </div>
                    </div>
                    <!-- End Comment Field -->

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
            echo '<h1 class="text-center">Update Comment</h1>';
            echo '<div class="container" >';

            // Get Variable From The Form
            $comid     = $_POST['comid'];
            $comment   = $_POST['comment'];

            // Update The Database With This Info
            $stmt = $con->prepare("UPDATE comments SET comment = ? WHERE c_id = ?");
            $stmt->execute(array($comment, $comid));

            // Echo Success Message
            $theMsg = "<div class='alert alert-success'><strong>" . $stmt->rowCount() . "</strong> Record Updated</div>";
            redirectFunc($theMsg, 'back');

        } else {
            $theMsg = "<div class='alert alert-danger'>Sorry You Can't Browse This Page Directly</div>";
            redirectFunc($theMsg);
        }
        echo "</div>";

    } elseif ($do == 'Delete') { // Delete Member Page
        echo '<h1 class="text-center">Delete Comment</h1>';
        echo '<div class="container" >';

        // Check If you Get The CommentID IS Numeric & Get The Integer Value Of It
        $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0;

        // Select All Data Depend On This ID
        $check = checkItem("c_id", "comments", $comid);

        // If There's Such ID
        if ($check > 0) {
            // Delete User Data Depend On This ID
            $stmt = $con->prepare("DELETE FROM comments WHERE c_id = :zcomid");
            $stmt->bindParam(":zcomid", $comid);

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
    } elseif ($do == "Approve") { // Activate Page
        echo '<h1 class="text-center">Approve Comment</h1>';
        echo '<div class="container" >';

        // Check If you Get The CommentID IS Numeric & Get The Integer Value Of It
        $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0;

        // Select All Data Depend On This ID
        $check = checkItem("c_id", "comments", $comid);

        // If There's Such ID
        if ($check > 0) {
            // Delete User Data Depend On This ID
            $stmt = $con->prepare("UPDATE comments SET status = 1 WHERE c_id = ?");

            // Execute Query
            $stmt->execute(array($comid));

            $theMsg = "<div class='alert alert-success'><strong>" . $stmt->rowCount() . "</strong> Record Approved</div>";
            redirectFunc($theMsg, 'back');
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