<?php

/*
=======================================================
== Items Page
=======================================================
 */

ob_start(); // Output Buffering Start
session_start();
$pageTitle = "Items"; // Page Title

// Check If There's A Session With Your Username
if (isset($_SESSION['Username'])) {
    include "init.php";

    // Check If Request Contain Do Statement Or Not If False Return To Manage Page
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    // Start Manage Page
    if ($do == 'Manage') { // Manage Members Page

        $stmt = $con->prepare("SELECT 
                                        items.*, 
                                        categories.Name AS Category_Name, 
                                        users.Username 
                                    FROM 
                                        items
                                    INNER JOIN 
                                        categories 
                                    ON 
                                        categories.ID = items.Cat_ID
                                    INNER JOIN 
                                        users 
                                    ON 
                                        users.UserID = items.Member_ID
                                    ORDER BY
                                        Item_ID DESC ");

        // Execute The Statement
        $stmt->execute();

        // Assign To Variable
        $items = $stmt->fetchAll();
        ?>

        <h1 class="text-center">Manage Items</h1>
        <div class="container items">
        <?php if (!empty($items)) { ?>
            <div class="table-responsive">
                <table class="main-table manage-items text-center table table-bordered">
                    <tr>
                        <td>#ID</td>
                        <td>ItemIMG</td>
                        <td>Username</td>
                        <td>Description</td>
                        <td>Price</td>
                        <td>Adding Date</td>
                        <td>Category</td>
                        <td>Username</td>
                        <td>Control</td>
                    </tr>

                    <?php
                    foreach ($items as $item) {
                        echo "<tr>";
                        echo "<td>" . $item['Item_ID'] . "</td>";
                        echo "<td>";
                        if (empty($item['Item_IMG'])) {
                            echo "<img src='uploads/items/default.png' alt='' />";
                        } else {
                            echo "<img src='uploads/items/" . $item['Item_IMG'] . "' alt='' />";
                        }
                        echo "</td>";
                        echo "<td>" . $item['Name'] . "</td>";
                        echo "<td>" . $item['Description'] . "</td>";
                        echo "<td>" . $item['Price'] . "</td>";
                        echo "<td>" . $item['Add_Date'] . "</td>";
                        echo "<td>" . $item['Category_Name'] . "</td>";
                        echo "<td>" . $item['Username'] . "</td>";
                        echo "<td>
                            <a href='items.php?do=Edit&itemid=" . $item['Item_ID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                            <a href='items.php?do=Delete&itemid=" . $item['Item_ID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete</a>";
                        if ($item["Approve"] == 0) {
                            echo "<a href='items.php?do=Approve&itemid=" . $item['Item_ID'] . "' class='btn btn-info activate'><i class='fa fa-check'></i> Approve</a>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
            <a href="items.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> New Item</a>
            <?php
            } else {
                echo '<div class="col-md-12 text-center">';
                    echo '<h2 class="no-items">No Items</h2>';
                echo '</div>';
                echo '<div class="col-md-12 text-center">';
                    echo '<a href="items.php?do=Add" class="btn btn-primary no-items-button"><i class="fa fa-plus"></i> New Item</a>';
                echo '</div>';
            }?>
        </div>

    <?php
    } elseif ($do == 'Add') { // Add Page ?>

        <h1 class="text-center">Add New Item</h1>
        <div class="container">
            <form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">

                <!-- Start Name Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10 col-md-6">
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            required="required"
                            placeholder="Name Of The Item"
                        />
                    </div>
                </div>
                <!-- End Name Field -->

                <!-- Start Description Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10 col-md-6">
                        <input
                            type="text"
                            name="description"
                            class="form-control"
                            required="required"
                            placeholder="Description Of The Item"
                        />
                    </div>
                </div>
                <!-- End Description Field -->

                <!-- Start Price Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Price</label>
                    <div class="col-sm-10 col-md-6">
                        <input
                            type="text"
                            name="price"
                            class="form-control"
                            required="required"
                            placeholder="Price Of The Item"
                        />
                    </div>
                </div>
                <!-- End Price Field -->

                <!-- Start Country Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Country</label>
                    <div class="col-sm-10 col-md-6">
                        <input
                            type="text"
                            name="country"
                            class="form-control"
                            required="required"
                            placeholder="Country Of Made"
                        />
                    </div>
                </div>
                <!-- End Country Field -->

                <!-- Start Status Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Status</label>
                    <div class="col-sm-10 col-md-6">
                        <select name="status">
                            <option value="0">...</option>
                            <option value="1">New</option>
                            <option value="2">Like New</option>
                            <option value="3">Used</option>
                            <option value="4">Very Old</option>
                        </select>
                    </div>
                </div>
                <!-- End Status Field -->

                <!-- Start Categories Field -->
                <div class="form-group form-group-lg all-cats">
                    <label class="col-sm-2 control-label">Category</label>
                    <div class="col-sm-10 col-md-6">
                        <select name="category">
                            <option value="0">...</option>
                            <?php
                            $allCats = getAllFrom("*", "categories", "WHERE Parent = 0", "", "ID");
                            foreach ($allCats as $cat) {
                                echo "<option value='" . $cat['ID'] . "'>" . $cat['Name'] ."</option>";
                                $childCats = getAllFrom("*", "categories", "WHERE Parent = {$cat['ID']}", "", "ID");
                                foreach ($childCats as $child) {
                                    echo "<option class='child' value='" . $child['ID'] . "'>- " . $child['Name'] ."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <!-- End Categories Field -->

                <!-- Start Members Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Member</label>
                    <div class="col-sm-10 col-md-6">
                        <select name="member">
                            <option value="0">...</option>
                            <?php
                                $allMembers = getAllFrom('*', 'users', '', '', 'UserID');
                                foreach ($allMembers as $user){
                                    echo "<option value='" . $user['UserID'] . "'>" . $user['Username'] ."</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <!-- End Members Field -->

                <!-- Start Tags Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Tags</label>
                    <div class="col-sm-10 col-md-6">
                        <input
                                type="text"
                                name="tags"
                                class="form-control"
                                placeholder="Separate Tags With Comma (,)"
                        />
                    </div>
                </div>
                <!-- End Tags Field -->

                <!-- Start Item Image Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Item Image</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="file" name="image" class="form-control custom-form-control" required="required"/>
                    </div>
                </div>
                <!-- End Item Image Field -->

                <!-- Start Submit Field -->
                <div class="form-group form-group-lg">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input
                            type="submit"
                            value="Add Item"
                            class="btn btn-primary btn-lg"
                        />
                    </div>
                </div>
                <!-- End Submit Field -->
            </form>
        </div>

    <?php
    } elseif ($do == 'Insert') { // Insert Page

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo '<h1 class="text-center">Insert Item</h1>';
            echo '<div class="container" >';

            // Upload Variables
            $itemIMGName = $_FILES['image']['name'];
            $itemIMGSize = $_FILES['image']['size'];
            $itemIMGTmp  = $_FILES['image']['tmp_name'];
            $itemIMGType = $_FILES['image']['type'];

            // List For Allowed File Types To Upload
            $itemsIMGAllowedExtensions = array("jpeg", "jpg", "png", "gif");

            // Get Avatar Extension
            $explode = explode('.', $itemIMGName);
            $itemIMGExtension = strtolower(end($explode));

            // Get Variable From The Form
            $name       = $_POST['name'];
            $desc       = $_POST['description'];
            $price      = $_POST['price'];
            $country    = $_POST['country'];
            $status     = $_POST['status'];
            $cat        = $_POST['category'];
            $member     = $_POST['member'];
            $tags       = $_POST['tags'];

            // Validate The Form
            $formErrors = array();
            if (empty($name)) {
                $formErrors[] = "Name Can't Be <strong>Empty</strong>";
            }
            if (empty($desc)) {
                $formErrors[] = "Description Can't Be <strong>Empty</strong>";
            }
            if (empty($price)) {
                $formErrors[] = "Price Can't Be <strong>Empty</strong>";
            }
            if (empty($country)) {
                $formErrors[] = "Country Can't Be <strong>Empty</strong>";
            }
            if ($status == 0)  {
                $formErrors[] = "You Must Choose The <strong>Status</strong>";
            }
            if ($member == 0)  {
                $formErrors[] = "You Must Choose The <strong>Member</strong>";
            }
            if ($cat == 0)  {
                $formErrors[] = "You Must Choose The <strong>Category</strong>";
            }

            if (!empty($itemIMGName) && !in_array($itemIMGExtension, $itemsIMGAllowedExtensions)) {
                $formErrors[] = "This Extension Is Not <strong>Allowed</strong>";
            }

            if (empty($itemIMGName)) {
                $formErrors[] = "Item Image Is <strong>Required</strong>";
            }

            if ($itemIMGSize > 4194304) {
                $formErrors[] = "Avatar Can't Be Larger Than <strong>4MB</strong>";
            }

            // Loop Into Errors Array And Echo It
            foreach ($formErrors as $error) {
                echo "<div class='alert alert-danger'>" . $error . "</div>";
            }

            // Check If There's No Error Proceed The Update Operation
            if (empty($formErrors)) {

                $image = rand(0, 10000000000) . '_' . $itemIMGName;
                move_uploaded_file($itemIMGTmp, "uploads\items\\" . $image);

                // Insert User Info In Database
                $stmt = $con->prepare("INSERT INTO 
                    items(Name, Description, Price, Country_Made, Status, Add_Date, Cat_ID, Member_ID, Tags, Item_IMG) 
                VALUES(:zname, :zdesc, :zprice, :zcountry, :zstatus, now(), :zcat, :zmember, :ztags, :zitemimg)");
                $stmt->execute(array(
                    'zname'     => $name,
                    'zdesc'     => $desc,
                    'zprice'    => $price,
                    'zcountry'  => $country,
                    'zstatus'   => $status,
                    'zcat'      => $cat,
                    'zmember'   => $member,
                    'ztags'     => $tags,
                    'zitemimg'  => $image
                ));

                // Echo Success Message
                $theMsg = "<div class='alert alert-success'><strong>" . $stmt->rowCount() . "</strong> Record Inserted</div>";
                redirectFunc($theMsg, 'back');
            }
        } else {
            echo '<div class="container" >';
            $theMsg = "<div class='alert alert-danger'>Sorry You Can't Browse This Page Directly</div>";
            redirectFunc($theMsg);
            echo "</div>";
        }
        echo "</div>";

    } elseif ($do == 'Edit') { // Edit Page

        // Check If you Get The ItemID Is Numeric & Get The Integer Value Of It
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

        // Select All Data Depend On This ID
        $stmt = $con->prepare("SELECT * FROM items WHERE Item_ID = ?");

        // Execute Query
        $stmt->execute(array($itemid));

        // Fetch The Data
        $item = $stmt->fetch();

        // The Row Count
        $count = $stmt->rowCount();

        // If There's Such ID
        if ($count > 0) { ?>

            <h1 class="text-center">Edit Item</h1>
            <div class="container">
                <form class="form-horizontal" action="?do=Update" method="POST">
                    <input type="hidden" name="itemid" value="<?php echo $itemid ?>" />
                    <!-- Start Name Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10 col-md-6">
                            <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    required="required"
                                    placeholder="Name Of The Item"
                                    value="<?php echo $item["Name"]?>"
                            />
                        </div>
                    </div>
                    <!-- End Name Field -->

                    <!-- Start Description Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10 col-md-6">
                            <input
                                    type="text"
                                    name="description"
                                    class="form-control"
                                    required="required"
                                    placeholder="Description Of The Item"
                                    value="<?php echo $item["Description"]?>"
                            />
                        </div>
                    </div>
                    <!-- End Description Field -->

                    <!-- Start Price Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Price</label>
                        <div class="col-sm-10 col-md-6">
                            <input
                                    type="text"
                                    name="price"
                                    class="form-control"
                                    required="required"
                                    placeholder="Price Of The Item"
                                    value="<?php echo $item["Price"]?>"
                            />
                        </div>
                    </div>
                    <!-- End Price Field -->

                    <!-- Start Country Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Country</label>
                        <div class="col-sm-10 col-md-6">
                            <input
                                    type="text"
                                    name="country"
                                    class="form-control"
                                    required="required"
                                    placeholder="Country Of Made"
                                    value="<?php echo $item["Country_Made"]?>"
                            />
                        </div>
                    </div>
                    <!-- End Country Field -->

                    <!-- Start Status Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="status">
                                <option value="1" <?php if ($item['Status'] == 1) { echo  'selected';}?> >New</option>
                                <option value="2" <?php if ($item['Status'] == 2) { echo  'selected';}?> >Like New</option>
                                <option value="3" <?php if ($item['Status'] == 3) { echo  'selected';}?> >Used</option>
                                <option value="4" <?php if ($item['Status'] == 4) { echo  'selected';}?> >Very Old</option>
                            </select>
                        </div>
                    </div>
                    <!-- End Status Field -->

                    <!-- Start Categories Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Category</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="category">
                                <?php
                                $stmt2 = $con->prepare("SELECT * FROM categories");
                                $stmt2->execute();
                                $cats = $stmt2->fetchAll();
                                foreach ($cats as $cat){
                                    echo "<option value='" . $cat['ID'] . "'";
                                    if ($item['Cat_ID'] == $cat['ID']) { echo  'selected' ;}
                                    echo ">" . $cat['Name'] ."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- End Categories Field -->

                    <!-- Start Members Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Member</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="member">
                                <?php
                                $stmt = $con->prepare("SELECT * FROM users");
                                $stmt->execute();
                                $users = $stmt->fetchAll();
                                foreach ($users as $user){
                                    echo "<option value='" . $user['UserID'] . "'";
                                    if ($item['Member_ID'] == $user['UserID']) { echo  'selected' ;}
                                    echo ">" . $user['Username'] ."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- End Members Field -->

                    <!-- Start Tags Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Tags</label>
                        <div class="col-sm-10 col-md-6">
                            <input
                                    type="text"
                                    name="tags"
                                    class="form-control"
                                    placeholder="Separate Tags With Comma (,)"
                                    value="<?php echo $item["Tags"]?>"
                            />
                        </div>
                    </div>
                    <!-- End Tags Field -->

                    <!-- Start Submit Field -->
                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input
                                    type="submit"
                                    value="Save"
                                    class="btn btn-primary btn-lg"
                            />
                        </div>
                    </div>
                    <!-- End Submit Field -->
                </form>
                <?php
                $stmt = $con->prepare("SELECT
                                                comments.*, users.Username AS Member
                                            FROM
                                                comments
                                            INNER JOIN
                                                users
                                            ON
                                                users.UserID = comments.user_id
                                            WHERE item_id = ?");

                // Execute The Statement
                $stmt->execute(array($itemid));

                // Assign To Variable
                $rows = $stmt->fetchAll();
                if (!empty($rows)) {
                ?>

                <h1 class="text-center">Manage [ <?php echo $item['Name']; ?> ] Comments</h1>
                <div class="table-responsive">
                    <table class="main-table text-center table table-bordered">
                        <tr>
                            <td>Comment</td>
                            <td>User Name</td>
                            <td>Added Date</td>
                            <td>Control</td>
                        </tr>

                        <?php
                        foreach ($rows as $row) {
                            echo "<tr>";
                            echo "<td>" . $row['comment'] . "</td>";
                            echo "<td>" . $row['Member'] . "</td>";
                            echo "<td>" . $row['comment_date'] . "</td>";
                            echo "<td>
                        <a href='comments.php?do=Edit&comid=" . $row['c_id'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                        <a href='comments.php?do=Delete&comid=" . $row['c_id'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete</a>";
                            if ($row["status"] == 0) {
                                echo "<a href='comments.php?do=Approve&comid=" . $row['c_id'] . "' class='btn btn-info activate'><i class='fa fa-check'></i> Approve</a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
                <?php }?>
            </div>

        <?php }
        else { // If There's No Such ID Show Error Message
            echo '<div class="container" >';
            $theMsg = "<div class='alert alert-danger'>There's No Such ID</div>";
            redirectFunc($theMsg);
            echo "</div>";
        }

    } elseif ($do == "Update") { // Update Page

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo '<h1 class="text-center">Update Item</h1>';
            echo '<div class="container" >';

            // Get Variable From The Form
            $id         = $_POST['itemid'];
            $name       = $_POST['name'];
            $desc       = $_POST['description'];
            $price      = $_POST['price'];
            $country    = $_POST['country'];
            $status     = $_POST['status'];
            $cat        = $_POST['category'];
            $member     = $_POST['member'];
            $tags       = $_POST['tags'];

            // Validate The Form
            $formErrors = array();
            if (empty($name)) {
                $formErrors[] = "Name Can't Be <strong>Empty</strong>";
            }
            if (empty($desc)) {
                $formErrors[] = "Description Can't Be <strong>Empty</strong>";
            }
            if (empty($price)) {
                $formErrors[] = "Price Can't Be <strong>Empty</strong>";
            }
            if (empty($country)) {
                $formErrors[] = "Country Can't Be <strong>Empty</strong>";
            }
            if ($status == 0)  {
                $formErrors[] = "You Must Choose The <strong>Status</strong>";
            }
            if ($member == 0)  {
                $formErrors[] = "You Must Choose The <strong>Member</strong>";
            }
            if ($cat == 0)  {
                $formErrors[] = "You Must Choose The <strong>Category</strong>";
            }

            // Loop Into Errors Array And Echo It
            foreach ($formErrors as $error) {
                echo "<div class='alert alert-danger'>" . $error . "</div>";
            }

            // Check If There's No Error Proceed The Update Operation
            if (empty($formErrors)) {

                // Update The Database With This Info
                $stmt = $con->prepare("UPDATE 
                                                items 
                                            SET 
                                                Name = ?, 
                                                Description = ?, 
                                                Price = ?, 
                                                Country_Made = ?, 
                                                Status = ?, 
                                                Cat_ID = ?,
                                                Member_ID = ?,
                                                Tags = ?
                                            WHERE 
                                                Item_ID = ?");
                $stmt->execute(array($name, $desc, $price, $country, $status, $cat, $member, $tags, $id));

                // Echo Success Message
                $theMsg = "<div class='alert alert-success'><strong>" . $stmt->rowCount() . "</strong> Record Updated</div>";
                redirectFunc($theMsg, 'back');
            }
        } else {
            $theMsg = "<div class='alert alert-danger'>Sorry You Can't Browse This Page Directly</div>";
            redirectFunc($theMsg);
        }
        echo "</div>";

    } elseif ($do == 'Delete') { // Delete Member Page

        echo '<h1 class="text-center">Delete Item</h1>';
        echo '<div class="container" >';

        // Check If you Get The ItemID IS Numeric & Get The Integer Value Of It
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

        // Select All Data Depend On This ID
        $check = checkItem("Item_ID", "items", $itemid);

        // If There's Such ID
        if ($check > 0) {
            // Delete User Data Depend On This ID
            $stmt = $con->prepare("DELETE FROM items WHERE Item_ID = :zitemid");
            $stmt->bindParam(":zitemid", $itemid);

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

        echo '<h1 class="text-center">Approve Item</h1>';
        echo '<div class="container" >';

        // Check If you Get The ItemID IS Numeric & Get The Integer Value Of It
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

        // Select All Data Depend On This ID
        $check = checkItem("Item_ID", "items", $itemid);

        // If There's Such ID
        if ($check > 0) {
            // Delete User Data Depend On This ID
            $stmt = $con->prepare("UPDATE items SET Approve = 1 WHERE Item_ID = ?");

            // Execute Query
            $stmt->execute(array($itemid));

            $theMsg = "<div class='alert alert-success'><strong>" . $stmt->rowCount() . "</strong> Record Updated</div>";
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