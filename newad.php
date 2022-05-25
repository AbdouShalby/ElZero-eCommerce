<?php
ob_start();
session_start();
$pageTitle = 'Create New Item'; // For Page Title
include "init.php";

if (isset($_SESSION['user'])) {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $formErrors = array();

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

        $name       = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $desc       = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
        $price      = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_INT);
        $country    = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
        $status     = filter_var($_POST['status'], FILTER_SANITIZE_NUMBER_INT);
        $category   = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
        $tags       = filter_var($_POST['tags'], FILTER_SANITIZE_STRING);

        if (strlen($name) < 3 ) {
            $formErrors[] = 'Item <strong>Name</strong> Must Be At Least <strong>3 Characters</strong>';
        }
        if (strlen($desc) < 10 ) {
            $formErrors[] = 'Item <strong>Description</strong> Must Be At Least <strong>10 Characters</strong>';
        }
        if (strlen($country) < 2 ) {
            $formErrors[] = 'Item <strong>Country</strong> Must Be At Least <strong>2 Characters</strong>';
        }
        if (empty($price)) {
            $formErrors[] = 'Item <strong>Price</strong> Can\'t Be <strong>Empty</strong>';
        }
        if (empty($status)) {
            $formErrors[] = 'You Must Be <strong>Select Item Status</strong>';
        }
        if (empty($category)) {
            $formErrors[] = 'You Must Be <strong>Select Item Category</strong>';
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

        // Check If There's No Error Proceed The Update Operation
        if (empty($formErrors)) {

            $image = rand(0, 10000000000) . '_' . $itemIMGName;
            move_uploaded_file($itemIMGTmp, "admin\uploads\items\\" . $image);

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
                'zcat'      => $category,
                'zmember'   => $_SESSION['uid'],
                'ztags'     => $tags,
                'zitemimg'  => $image
            ));

            // Echo Success Message
            if ($stmt) {
                $successMsg = 'Item Has Been Added';
            }
        }
    }
?>

<h1 class="text-center"><?php echo $pageTitle; ?></h1>
<div class="create-ad block">
    <div class="container">
        <div class="panel-primary">
            <div class="panel-heading"><?php echo $pageTitle; ?></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-8">
                        <form class="form-horizontal main-form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">

                            <!-- Start Name Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-3 control-label">Name</label>
                                <div class="col-sm-10 col-md-9">
                                    <input
                                        pattern=".{4,}"
                                        title="This Field Require At Least 4 Characters"
                                        type="text"
                                        name="name"
                                        class="form-control live"
                                        placeholder="Name Of The Item"
                                        data-class=".live-title"
                                        required
                                    />
                                </div>
                            </div>
                            <!-- End Name Field -->

                            <!-- Start Description Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-3 control-label">Description</label>
                                <div class="col-sm-10 col-md-9">
                                    <input
                                        pattern=".{10,}"
                                        title="This Field Require At Least 10 Characters"
                                        type="text"
                                        name="description"
                                        class="form-control live"
                                        placeholder="Description Of The Item"
                                        data-class=".live-desc"
                                        required
                                    />
                                </div>
                            </div>
                            <!-- End Description Field -->

                            <!-- Start Price Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-3 control-label">Price</label>
                                <div class="col-sm-10 col-md-9">
                                    <input
                                        type="text"
                                        name="price"
                                        class="form-control live"
                                        placeholder="Price Of The Item"
                                        data-class=".live-price"
                                        required
                                    />
                                </div>
                            </div>
                            <!-- End Price Field -->

                            <!-- Start Country Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-3 control-label">Country</label>
                                <div class="col-sm-10 col-md-9">
                                    <input
                                        type="text"
                                        name="country"
                                        class="form-control"
                                        placeholder="Country Of Made"
                                        required
                                    />
                                </div>
                            </div>
                            <!-- End Country Field -->

                            <!-- Start Item Image Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-3 control-label">Item Image</label>
                                <div class="col-sm-10 col-md-9">
                                    <input
                                            type="file"
                                            name="image"
                                            class="form-control custom-form-control"
                                            data-class=".live-img"
                                            required
                                    />
                                </div>
                            </div>
                            <!-- End Item Image Field -->

                            <!-- Start Status Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-3 control-label">Status</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="status" required>
                                        <option value="">...</option>
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
                                <label class="col-sm-3 control-label">Category</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="category" required>
                                        <option value="">...</option>
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

                            <!-- Start Tags Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-3 control-label">Tags</label>
                                <div class="col-sm-10 col-md-9">
                                    <input
                                            type="text"
                                            name="tags"
                                            class="form-control"
                                            placeholder="Separate Tags With Comma (,)"
                                    />
                                </div>
                            </div>
                            <!-- End Tags Field -->

                            <!-- Start Submit Field -->
                            <div class="form-group form-group-lg">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <input
                                        type="submit"
                                        value="Add Item"
                                        class="btn btn-primary btn-lg"
                                    />
                                </div>
                            </div>
                            <!-- End Submit Field -->
                    </div>
                    <div class="col-md-4">
                        <div class="thumbnail item-box live-preview">
                            <span class="price-tag">$<span class="live-price">0</span></span>
                            <img class="img-responsive" src="item.png" alt="">
                            <div class="caption">
                                <h3 class="live-title">Title</h3>
                                <p class="live-desc">Description</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Start Looping Through Errors -->
                <?php
                    if (!empty($formErrors)) {
                        foreach ($formErrors as $error) {
                            echo '<div class="custom-message-error">' . $error . '</div>';
                        }
                    }
                    if (isset($successMsg)) {
                        echo '<div class="custom-message-success">' . $successMsg . '</div>';
                    }
                ?>
                <!-- End Looping Through Errors -->
            </div>
        </div>
    </div>
</div>
<?php
} else {
    header('Location: login.php');
    exit();
}
include $tmpl . "footer.php";
ob_end_flush();
?>