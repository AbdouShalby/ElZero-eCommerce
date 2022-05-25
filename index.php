<?php
ob_start();
session_start();
$pageTitle = 'Homepage'; // For Page Title
include "init.php";
?>

<div class="container home-items">
    <div class="row">
        <?php
        $allItems = getAllFrom('*', 'items', 'WHERE Approve = 1', '', 'Item_ID');
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
        ?>
    </div>
</div>

<?php
include $tmpl . "footer.php";
ob_end_flush();
?>