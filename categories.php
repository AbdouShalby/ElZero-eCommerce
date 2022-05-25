<?php
ob_start();
session_start();
$pageTitle = 'Category'; // For Page Title
include "init.php";
?>

<div class="container categories-items">
    <h1 class="text-center">Show Category Items</h1>
    <div class="row">
        <?php
        if (isset($_GET['pageid']) && is_numeric($_GET['pageid'])) {
            $category = intval($_GET['pageid']);
            $allItems = getAllFrom("*", "items", "WHERE Cat_ID = {$category}", "AND Approve = 1", "Item_ID", 'ASC');
            foreach ($allItems as $item) {
                $iImg = 'admin\uploads\items\\' .$item['Item_IMG'];
                echo '<div class="col-sm-6 col-md-3">';
                    echo '<div class="thumbnail item-box">';
                        echo '<span class="price-tag">$' . $item['Price'] . '</span>';
                        if (empty($item['Item_IMG'])){
                            echo '<a href="items.php?itemid=' . $item['Item_ID'] . '"><img class="img-responsive img-thumbnail center-block" src="item.png" alt="Default"></a>';
                        } else {
                            echo '<a href=items.php?itemid=' . $item['Item_ID'] . '><img class="img-responsive img-thumbnail center-block" src="' . $iImg . '" alt="Item Image"></a>';
                        }
                        echo '<div class="caption">';
                            echo '<h3><a href="items.php?itemid=' . $item['Item_ID'] . '">' . $item['Name'] . '</a></h3>';
                            echo '<p>' . $item['Description'] . '</p>';
                            echo '<div class="date">' . $item['Add_Date'] . '</div>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="custom-message-error">Error 404 Page Not Found</div>';
        }
        ?>
    </div>
</div>

<?php
include $tmpl . "footer.php";
ob_end_flush();
?>