<?php
ob_start();
session_start();
$pageTitle = 'Show Items'; // For Page Title
include "init.php";

// Check If you Get The ItemID Is Numeric & Get The Integer Value Of It
$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

// Select All Data Depend On This ID
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
                            WHERE 
                                Item_ID = ?
                            AND
                                Approve = 1");

// Execute Query
$stmt->execute(array($itemid));

$count = $stmt->rowCount();
if ($count > 0 ) {

// Fetch The Data
$item = $stmt->fetch();
?>

<h1 class="text-center"><?php echo $item['Name'] ?></h1>
<div class="container">
    <div class="row">
        <div class="col-md-3 item-image">
            <?php
            $iImg = 'admin\uploads\items\\' .$item['Item_IMG'];
            if (empty($item['Item_IMG'])){
                echo '<img class="img-responsive img-thumbnail center-block" src="item.png" alt="Default">';
            } else {
                echo '<a href="' . $iImg . '" target="_blank"><img class="img-responsive img-thumbnail center-block iImg" src="' . $iImg . '" alt="Item Image"></a>';
            }
            ?>
        </div>
        <div class="col-md-9 item-info">
            <h2><?php echo $item['Name'] ?></h2>
            <p><?php echo $item['Description'] ?></p>
            <ul class="list-unstyled">
                <li>
                    <i class="fa fa-calendar fa-fw"></i>
                    <span>Added Date</span> : <?php echo $item['Add_Date'] ?>
                </li>
                <li>
                    <i class="fa fa-money fa-fw"></i>
                    <span>Price</span> : $<?php echo $item['Price'] ?>
                </li>
                <li>
                    <i class="fa fa-wrench fa-fw"></i>
                    <span>Made In</span> : <?php echo $item['Country_Made'] ?></a>
                </li>
                <li>
                    <i class="fa fa-tags fa-fw"></i>
                    <span>Category</span> : <a href="categories.php?pageid=<?php echo $item['Cat_ID'] ?>"><?php echo $item['Category_Name'] ?></a>
                </li>
                <li>
                    <i class="fa fa-user fa-fw"></i>
                    <span>Added By</span> : <a href="#"><?php echo $item['Username'] ?></a>
                </li>
                <li class="tags-items">
                    <i class="fa fa-user fa-fw"></i>
                    <span>Tags</span> :
                    <?php
                        $allTags = explode(",", $item['Tags']);
                        foreach ($allTags as $tag) {
                            $tag = str_replace(' ', '', $tag);
                            $lowerTag = strtolower($tag);
                            if (!empty($tag)) {
                                echo "<a href='tags.php?name={$lowerTag}'>" . $tag . '</a>';
                            } else {
                                echo 'No Tags';
                            }
                        }
                    ?>
                </li>
            </ul>
        </div>
    </div>
    <hr class="custom-hr">
    <!-- Start Add Comment Section -->
    <?php if (isset($_SESSION['user'])) { ?>
    <div class="row">
        <div class="col-md-offset-3">
            <div class="add-comment">
                <h3>Add Your Comment</h3>
                <form action="<?php echo $_SERVER['PHP_SELF'] .'?itemid=' . $item['Item_ID'] ?>" method="POST">
                    <textarea name="comment" required></textarea>
                    <input class="btn btn-primary" type="submit" value="Add Comment">
                </form>
                <?php
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        $comment    = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
                        $itemid     = $item['Item_ID'];
                        $userid     = $_SESSION['uid'];

                        if (!empty($comment)) {
                            $stmt = $con->prepare("INSERT INTO
                                comments(comment, status, comment_date, item_id, user_id)
                                VALUES(:zcomment, 0, NOW(), :zitemid, :zuserid)");

                            $stmt->execute(array(
                                'zcomment' => $comment,
                                'zitemid' => $itemid,
                                'zuserid' => $userid
                            ));
                            if ($stmt) {
                                echo '<div class="custom-message-success"><strong>Comment Added</strong></div>';
                            }
                        } else {
                            echo '<div class="custom-message-error"><strong>Comment</strong> Can\'t Be <strong>Empty</strong></div>';
                        }
                    }
                ?>
            </div>
        </div>
    </div>
    <?php } else {
        echo '<a href="login.php">Login</a> Or <a href="login.php">Register</a> To Add Comment';
    }?>
    <!-- End Add Comment Section -->
    <hr class="custom-hr">
    <?php
    $stmt = $con->prepare("SELECT 
                                                comments.*, users.Username AS Member, users.Avatar AS UserIMG
                                            FROM 
                                                comments
                                            INNER JOIN
                                                users       
                                            ON  
                                                users.UserID = comments.user_id
                                            WHERE
                                                item_id = ? 
                                            AND 
                                                  status = 1
                                            ORDER BY
                                                c_id DESC");

    // Execute The Statement
    $stmt->execute(array($item['Item_ID']));

    // Assign To Variable
    $comments = $stmt->fetchAll();
    ?>

    <?php foreach ($comments as $comment) { ?>
        <div class="comment-box">
            <div class="row">
                <div class="col-sm-2 text-center">
                    <?php
                    $uImg = 'admin\uploads\avatars\\' .$comment['UserIMG'];
                    if (empty($comment['UserIMG'])){
                        echo '<img class="img-responsive img-thumbnail img-circle center-block" src="user.png" alt="">';
                    } else {
                        echo '<a href="' . $uImg . '" target="_blank"><img class="img-responsive img-thumbnail center-block iImg" src="' . $uImg . '" alt="Item Image"></a>';
                    }
                    ?>
                    <?php echo $comment['Member'] ?>
                </div>
                <div class="col-sm-10">
                    <p class="lead"><?php echo $comment['comment'] ?></p>
                </div>
            </div>
        </div>
        <hr class="custom-hr">
    <?php } ?>
</div>
<?php
} else {
    echo '<div class="container">';
        echo '<div class="custom-message-error">There\'s Now Such ID Or This Item Is Waiting For Approval</div>';
    echo '</div>';
}
include $tmpl . "footer.php";
ob_end_flush();
?>