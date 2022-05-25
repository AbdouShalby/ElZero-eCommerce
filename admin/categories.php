<?php

/*
=======================================================
== Category Page
=======================================================
 */

ob_start(); // Output Buffering Start
session_start();
$pageTitle = "Categories"; // Page Title

// Check If There's A Session With Your Username
if (isset($_SESSION['Username'])) {
    include "init.php";

    // Check If Request Contain Do Statement Or Not If False Return To Manage Page
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    // Start Manage Page
    if ($do == 'Manage') { // Manage Members Page

        $sort = "ASC";
        $sort_array = array("ASC", "DESC");
        if (isset($_GET['sort']) && in_array($_GET['sort'], $sort_array, true)) {
            $sort = $_GET['sort'];
        }
        $cats = getAllFrom('*', 'categories', 'WHERE Parent = 0', '', 'Ordering', $sort); ?>

        <h1 class="text-center">Manage Categories</h1>
        <div class="container categories">
        <?php if (!empty($cats)) { ?>
            <div class="panel panel-default">
                <div class="panel-heading panel-heading-custom">
                    <i class="fa fa-edit"></i> Manage Categories
                    <div class="options pull-right">
                        <i class="fa fa-sort"></i> Ordering: [
                        <a class="<?php if ($sort == "ASC") { echo "active"; }?>" href="?sort=ASC">Asc</a> |
                        <a class="<?php if ($sort == "DESC") { echo "active"; }?>" href="?sort=DESC">Desc</a> ]
                        <i class="fa fa-eye"></i> View: [
                        <span class="active" data-view="classic">Classic</span> |
                        <span data-view="full">Full</span> ]
                    </div>
                </div>
                <div class="panel-body">
                    <?php
                        foreach ($cats as $cat) {
                            echo "<div class='cat'>";
                                echo "<div class='hidden-buttons'>";
                                    echo "<a href='categories.php?do=Edit&catid=" . $cat['ID'] . "' class='btn btn-xs btn-success'><i class='fa fa-edit'></i> Edit</a>";
                                    echo "<a href='categories.php?do=Delete&catid=" . $cat['ID'] . "' class='confirm btn btn-xs btn-danger'><i class='fa fa-close'></i> Delete</a>";
                                echo "</div>";
                                echo "<h3 class='cat-name'>" . $cat['Name'] . "</h3>";
                                echo "<div class='full-view'>";
                                    echo "<p>"; if ($cat['Description'] == '') { echo "This Category Has No Description"; } else { echo $cat['Description']; } echo "</p>";
                                    if ($cat['Visibility'] == 1) { echo "<span class='custom-feat disabled-feat'><i class='fa fa-eye-slash'></i> Hidden</span>"; } else { echo "<span class='custom-feat allowed-feat'>Visible</span>"; }
                                    if ($cat['Allow_Comment'] == 1) { echo "<span class='custom-feat disabled-feat'><i class='fa fa-close'></i> Comment Disabled</span>"; } else { echo "<span class='custom-feat allowed-feat'>Comment Allowed</span>"; }
                                    if ($cat['Allow_Ads'] == 1) { echo "<span class='custom-feat disabled-feat'><i class='fa fa-close'></i> Ads Disabled</span>"; } else { echo "<span class='custom-feat allowed-feat'>Ads Allowed</span>"; }
                                echo "</div>";

                                // Get Child Categories
                                $childCats = getAllFrom("*", "categories", "WHERE Parent = {$cat['ID']}", "", "ID", "ASC");
                                if (!empty($childCats)) {
                                    echo "<h4 class='child-head'>Child Categories</h4>";
                                    echo "<ul class='list-unstyled child-cats'>";
                                    foreach ($childCats as $child) {
                                        echo "<li class='child-link'>
                                            <a href='categories.php?do=Edit&catid=" . $child['ID'] . "'>" . $child['Name'] . "</a>
                                            <a href='categories.php?do=Delete&catid=" . $child['ID'] . "' class='show-delete confirm'> Delete</a>
                                        </li>";
                                    }
                                    echo "</ul>";
                                }
                            echo "</div>";
                            echo "<hr>";
                        }
                    ?>
                </div>
            </div>
            <a href="categories.php?do=Add" class="btn btn-primary add-category"><i class="fa fa-plus"></i> New Category</a>
            <?php
            } else {
                echo '<div class="col-md-12 text-center">';
                    echo '<h2 class="no-items">No Categories</h2>';
                echo '</div>';
                echo '<div class="col-md-12 text-center">';
                    echo '<a href="categories.php?do=Add" class="btn btn-primary no-items-button"><i class="fa fa-plus"></i> New Category</a>';
                echo '</div>';
            }?>
        </div>

    <?php
    } elseif ($do == 'Add') { // Add Page ?>

        <h1 class="text-center">Add New Category</h1>
        <div class="container">
            <form class="form-horizontal" action="?do=Insert" method="POST">

                <!-- Start Name Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="name" class="form-control custom-form-control" autocomplete="off" required="required" placeholder="Name Of The Category" />
                    </div>
                </div>
                <!-- End Name Field -->

                <!-- Start Description Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="description" class="form-control custom-form-control" placeholder="Describe The Category" />
                    </div>
                </div>
                <!-- End Description Field -->

                <!-- Start Ordering Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Ordering</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="ordering" class="form-control custom-form-control" placeholder="Number To Arrange The Categories" />
                    </div>
                </div>
                <!-- End Ordering Field -->

                <!-- Start Category Type Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Parent?</label>
                    <div class="col-sm-10 col-md-6">
                        <select name="parent">
                            <option value="0">None</option>
                            <?php
                                $allCats = getAllFrom('*', 'categories', 'WHERE Parent = 0', '', 'ID', 'ASC');
                                foreach ($allCats as $cat) {
                                    echo "<option value='" . $cat[ID] . "'>" . $cat['Name'] . "</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <!-- End Category Type Field -->

                <!-- Start Visibility Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Visible</label>
                    <div class="col-sm-10 col-md-6">
                        <div>
                            <input id="vis-yes" type="radio" name="visibility" value="0" checked />
                            <label for="vis-yes">Yes</label>
                        </div>
                        <div>
                            <input id="vis-no" type="radio" name="visibility" value="1" />
                            <label for="vis-no">No</label>
                        </div>
                    </div>
                </div>
                <!-- End Visibility Field -->

                <!-- Start Commenting Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Allow Commenting</label>
                    <div class="col-sm-10 col-md-6">
                        <div>
                            <input id="com-yes" type="radio" name="commenting" value="0" checked />
                            <label for="com-yes">Yes</label>
                        </div>
                        <div>
                            <input id="com-no" type="radio" name="commenting" value="1" />
                            <label for="com-no">No</label>
                        </div>
                    </div>
                </div>
                <!-- End Commenting Field -->

                <!-- Start Ads Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Allow Ads</label>
                    <div class="col-sm-10 col-md-6">
                        <div>
                            <input id="ads-yes" type="radio" name="ads" value="0" checked />
                            <label for="ads-yes">Yes</label>
                        </div>
                        <div>
                            <input id="ads-no" type="radio" name="ads" value="1" />
                            <label for="ads-no">No</label>
                        </div>
                    </div>
                </div>
                <!-- End Ads Field -->

                <!-- Start Submit Field -->
                <div class="form-group form-group-lg">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" value="Add Category" class="btn btn-primary btn-lg"/>
                    </div>
                </div>
                <!-- End Submit Field -->
            </form>
        </div>

    <?php
    } elseif ($do == "Insert") { // Insert Page

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo '<h1 class="text-center">Insert Category</h1>';
            echo '<div class="container" >';

            // Get Variable From The Form
            $name       = $_POST['name'];
            $desc       = $_POST['description'];
            $parent     = $_POST['parent'];
            $order      = $_POST['ordering'];
            $visible    = $_POST['visibility'];
            $comment    = $_POST['commenting'];
            $ads        = $_POST['ads'];

            // Check If Name Field Is Empty
            if (!isset($name)) {
                $theMsg = "<div class='alert alert-danger'>Sorry This Field Can't Be <strong>Empty</strong></div>";
                redirectFunc($theMsg, "back");
            } else {
                // Check If Category Exist In Database
                $check = checkItem("Name", "categories", $name);
                if ($check == 1) {
                    $theMsg = "<div class='alert alert-danger'>Sorry This <strong>Category Is Exist</strong></div>";
                    redirectFunc($theMsg, "back");

                } else {
                    // Insert Category Info In Database
                    $stmt = $con->prepare("INSERT INTO 
                        categories(Name, Description, Parent, Ordering, Visibility, Allow_Comment, Allow_Ads) 
                        VALUES(:zname, :zdesc, :zparent, :zorder, :zvisible, :zcomment, :zads) ");
                    $stmt->execute(array(
                        'zname'     => $name,
                        'zdesc'     => $desc,
                        'zparent'   => $parent,
                        'zorder'    => $order,
                        'zvisible'  => $visible,
                        'zcomment'  => $comment,
                        'zads'      => $ads
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

        // Check If you Get The CatID IS Numeric & Get The Integer Value Of It
        $catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;

        // Select All Data Depend On This ID
        $stmt = $con->prepare("SELECT * FROM categories WHERE ID = ?");

        // Execute Query
        $stmt->execute(array($catid));

        // Fetch The Data
        $cat = $stmt->fetch();

        // The Row Count
        $count = $stmt->rowCount();

        // If There's Such ID
        if ($count > 0) { ?>

            <h1 class="text-center">Edit Category</h1>
            <div class="container">
                <form class="form-horizontal" action="?do=Update" method="POST">
                    <input type="hidden" name="catid" value="<?php echo $catid ?>" />

                    <!-- Start Name Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="name" class="form-control custom-form-control" placeholder="Name Of The Category" value="<?php echo $cat["Name"]; ?>"/>
                        </div>
                    </div>
                    <!-- End Name Field -->

                    <!-- Start Description Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="description" class="form-control custom-form-control" placeholder="Describe The Category" value="<?php echo $cat["Description"]; ?>"/>
                        </div>
                    </div>
                    <!-- End Description Field -->

                    <!-- Start Ordering Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Ordering</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="ordering" class="form-control custom-form-control" placeholder="Number To Arrange The Categories" value="<?php echo $cat["Ordering"]; ?>"/>
                        </div>
                    </div>
                    <!-- End Ordering Field -->

                    <!-- Start Category Type Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Parent?</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="parent">
                                <option value="0">None</option>
                                <?php
                                $allCats = getAllFrom('*', 'categories', 'WHERE Parent = 0', '', 'ID', 'ASC');
                                foreach ($allCats as $c) {
                                    echo "<option value='" . $c[ID] . "'";
                                    if ($cat['Parent'] == $c['ID']) { echo ' selected';}
                                    echo ">" . $c['Name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- End Category Type Field -->

                    <!-- Start Visibility Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Visible</label>
                        <div class="col-sm-10 col-md-6">
                            <div>
                                <input id="vis-yes" type="radio" name="visibility" value="0" <?php if ($cat["Visibility"] == 0 ) { echo "checked"; }?>/>
                                <label for="vis-yes">Yes</label>
                            </div>
                            <div>
                                <input id="vis-no" type="radio" name="visibility" value="1" <?php if ($cat["Visibility"] == 1 ) { echo "checked"; }?>/>
                                <label for="vis-no">No</label>
                            </div>
                        </div>
                    </div>
                    <!-- End Visibility Field -->

                    <!-- Start Commenting Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Allow Commenting</label>
                        <div class="col-sm-10 col-md-6">
                            <div>
                                <input id="com-yes" type="radio" name="commenting" value="0" <?php if ($cat["Allow_Comment"] == 0 ) { echo "checked"; }?>/>
                                <label for="com-yes">Yes</label>
                            </div>
                            <div>
                                <input id="com-no" type="radio" name="commenting" value="1" <?php if ($cat["Allow_Comment"] == 1 ) { echo "checked"; }?>/>
                                <label for="com-no">No</label>
                            </div>
                        </div>
                    </div>
                    <!-- End Commenting Field -->

                    <!-- Start Ads Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Allow Ads</label>
                        <div class="col-sm-10 col-md-6">
                            <div>
                                <input id="ads-yes" type="radio" name="ads" value="0" <?php if ($cat["Allow_Ads"] == 0 ) { echo "checked"; }?>/>
                                <label for="ads-yes">Yes</label>
                            </div>
                            <div>
                                <input id="ads-no" type="radio" name="ads" value="1" <?php if ($cat["Allow_Ads"] == 1 ) { echo "checked"; }?>/>
                                <label for="ads-no">No</label>
                            </div>
                        </div>
                    </div>
                    <!-- End Ads Field -->

                    <!-- Start Submit Field -->
                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" value="Save" class="btn btn-primary btn-lg"/>
                        </div>
                    </div>
                    <!-- End Submit Field -->
                </form>
            </div>

        <?php
        } else { // If There's No Such ID Show Error Message
            echo '<div class="container" >';
            $theMsg = "<div class='alert alert-danger'>There's No Such ID</div>";
            redirectFunc($theMsg);
            echo "</div>";
        }

    } elseif ($do == "Update") { // Update Page

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo '<h1 class="text-center">Update Category</h1>';
            echo '<div class="container" >';

            // Get Variable From The Form
            $id         = $_POST['catid'];
            $name       = $_POST['name'];
            $desc       = $_POST['description'];
            $order      = $_POST['ordering'];
            $parent     = $_POST['parent'];
            $visible    = $_POST['visibility'];
            $comment    = $_POST['commenting'];
            $ads        = $_POST['ads'];

            // Check If Name Field Is Empty
            if (!isset($name)) {
                $theMsg = "<div class='alert alert-danger'>Sorry This Field Can't Be <strong>Empty</strong></div>";
                redirectFunc($theMsg, "back");
            } else {
                // Update The Database With This Info
                $stmt = $con->prepare("UPDATE 
                                                categories 
                                            SET 
                                                Name = ?, 
                                                Description = ?, 
                                                Ordering = ?, 
                                                Parent = ?,
                                                Visibility = ?, 
                                                Allow_Comment = ?,
                                                Allow_Ads = ?
                                            WHERE 
                                                  ID = ?");
                $stmt->execute(array($name, $desc, $order, $parent, $visible, $comment, $ads, $id));

                // Echo Success Message
                $theMsg = "<div class='alert alert-success'><strong>" . $stmt->rowCount() . "</strong> Record Updated</div>";
                redirectFunc($theMsg, 'back');
                echo "</div>";
            }
        } else {
            $theMsg = "<div class='alert alert-danger'>Sorry You Can't Browse This Page Directly</div>";
            redirectFunc($theMsg);
        }
    } elseif ($do == 'Delete') { // Delete Member Page

        echo '<h1 class="text-center">Delete Category</h1>';
        echo '<div class="container" >';

        // Check If you Get The CatID IS Numeric & Get The Integer Value Of It
        $catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;

        // Select All Data Depend On This ID
        $check = checkItem("ID", "categories", $catid);

        // If There's Such ID
        if ($check > 0) {
            // Delete User Data Depend On This ID
            $stmt = $con->prepare("DELETE FROM categories WHERE ID = :zid");
            $stmt->bindParam(":zid", $catid);

            // Execute Query
            $stmt->execute();

            $theMsg = "<div class='alert alert-success'><strong>" . $stmt->rowCount() . "</strong> Record Deleted</div>";
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