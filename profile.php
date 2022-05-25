<?php
ob_start();
session_start();
$pageTitle = 'Profile'; // For Page Title
include "init.php";

if (isset($_SESSION['user'])) {
    $getUser = $con->prepare("SELECT * FROM users WHERE Username = ?");
    $getUser->execute(array($sessionUser));
    $info = $getUser->fetch();
    $userid = $info['UserID'];
?>

<h1 class="text-center"><?php echo $info['FullName'] ?></h1>
<div class="information block">
    <div class="container">
        <div class="row">
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
                <div class="panel-primary">
                    <div class="panel-heading">My Information</div>
                    <div class="panel-body">
                        <ul class="list-unstyled">
                            <li>
                                <i class="fa fa-unlock-alt fa-fw"></i>
                                <span>Login Name</span> : <?php echo $info['Username'] ?>
                            </li>
                            <li>
                                <i class="fa fa-envelope-o fa-fw"></i>
                                <span>Email</span> : <?php echo $info['Email'] ?>
                            </li>
                            <li>
                                <i class="fa fa-user fa-fw"></i>
                                <span>Full Name</span> : <?php echo $info['FullName'] ?>
                            </li>
                            <li>
                                <i class="fa fa-calendar fa-fw"></i>
                                <span>Registered Date</span> : <?php echo $info['Date'] ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="my-ads" class="profile-ads block">
    <div class="container">
        <div class="panel-primary">
            <div class="panel-heading">My Items</div>
            <div class="panel-body">
            <?php
            $myItems = getAllFrom("*", "items", "WHERE Member_ID = $userid", "", "Item_ID");
            if (!empty($myItems)) {
                echo '<div class="row">';
                foreach ($myItems as $item) {
                    echo '<div class="col-sm-6 col-md-3">';
                        echo '<div class="thumbnail item-box">';
                        if ($item['Approve'] == 0) {
                            echo '<span class="approve-status">Waiting Approval</span>';
                        }
                        echo '<span class="price-tag">$' . $item['Price'] . '</span>';
                        echo '<img class="img-responsive" src="item.png" alt="">';
                            echo '<div class="caption">';
                                echo '<h3><a href="items.php?itemid=' . $item['Item_ID'] . '">' . $item['Name'] . '</a></h3>';
                                echo '<p>' . $item['Description'] . '</p>';
                                echo '<div class="date">' . $item['Add_Date'] . '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<div class="custom-message-error">Sorry There\'s No Items To Show, <a href="newad.php">Create New Items</a></div>';
            }
            ?>
            </div>
        </div>
    </div>
</div>

<div id="my-comments" class="profile-comments block">
    <div class="container">
        <div class="panel-primary">
            <div class="panel-heading">Latest Comments</div>
            <div class="panel-body">
                <?php
                $myComments = getAllFrom("comment", "comments", "WHERE user_id = $userid", "", "c_id");
                if (!empty($myComments)) {
                    foreach ($myComments as $comment) {
                        echo '<p>' . $comment['comment'] . '</p>';
                    }
                } else {
                    echo '<div class="custom-message-error">There\'s No Comments To Show</div>';
                }
                ?>
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