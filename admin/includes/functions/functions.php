<?php

/*
 * Get All Function v2.0
 * Function To Get All Records From Database Table
 */
function getAllFrom($field, $table, $where = NULL, $and = NULL, $orderField, $ordering = "DESC") {
    global $con;
    $getAll = $con->prepare("SELECT $field FROM $table $where $and ORDER BY $orderField $ordering");
    $getAll->execute();
    $all = $getAll->fetchAll();
    return $all;
}

/*
 * Title Function v1.0
 * Title Function That Echo The Page Title In Case The Page
 * Has The Variable $pageTitle And Echo Default Title For The Other Pages
 */
// Function To Set The Page Title From Variable Value
function getTitle() {
    global $pageTitle;
    if (isset($pageTitle)) {
        echo $pageTitle;
    } else {
        echo "Default";
    }
}

/*
 * Redirect Function v2.0 [ Function Accept Parameters ]
 * $theMsg = Echo The Error Message [ Error | Success | Warning ]
 * $toWhere = If Still Null It Will Redirect To Index.php Else If it Has Valued It Will Back To HTTP REFERER
 * $seconds = Seconds Before Redirecting [ 3 Seconds By Default ]
 */
function redirectFunc($theMsg, $toWhere = null, $seconds = 3) {
    if ($toWhere === null) {
        $toWhere = 'index.php';
        $link = "Home Page";
    } else {
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== ''){
            $toWhere = $_SERVER['HTTP_REFERER'];
            $link = "Previous Page";
        } else {
            $toWhere = "index.php";
            $link = "Home Page";
        }
    }

    echo $theMsg;
    echo "<div class='alert alert-info'>You Will Be Redirected To $link After <strong>$seconds</strong> Seconds.</div>";
    header("refresh:$seconds;url=$toWhere");
    exit();
}


/*
 * Check Items Function v1.0
 * Function To Check Items In Database [ Function Accept Parameters ]
 * $select = The Item To Select [ Example: user, item, category ]
 * $from = The Table To Select From [ Example: *, user, items, categories ]
 * $value = The Value Of Select [ Example: Abdou, box, electronics ]
 */
function checkItem($select, $from, $value) {
    global $con;
    $statement = $con->prepare("SELECT $select FROM $from WHERE $select = ?");
    $statement->execute(array($value));
    return $statement->rowCount();
}


/*
 * Count Number Of Items Function v1.0
 * Function To Count Number Of Items Rows
 * $item = The Item To Count
 * $table = The Table To Choose From
 */
function countItems($item, $table) {
    global $con;
    $stmt2 = $con->prepare("SELECT COUNT($item) FROM $table");
    $stmt2->execute();
    return $stmt2->fetchColumn();
}


/*
 * Get The Latest Records Function v1.0
 * Function To Get The Latest Items From Database [ User, Items, Comments ]
 * $select = Field To Select
 * $table = The Table To Choose From
 * $order = The Desc Ordering
 * $limit = Number Of Records To Get
 */
function getLatest($select, $table, $order, $limit = 5) {
    global $con;
    $getStmt = $con->prepare("SELECT $select FROM $table ORDER BY $order DESC LIMIT $limit");
    $getStmt->execute();
    $rows = $getStmt->fetchAll();
    return $rows;
}


/*
 * Get The Latest Records With Condition Function v2.0
 * Function To Get The Latest Items From Database With Condition [ Users Without Admins, Items Without Approved ]
 * $select = Field To Select
 * $table = The Table To Choose From
 * $order = The Desc Ordering
 * $limit = Number Of Records To Get
 */
function getLatestWhere($select, $table, $where, $value, $order, $limit = 5) {
    global $con;
    $getStmt = $con->prepare("SELECT $select FROM $table WHERE $where = $value ORDER BY $order DESC LIMIT $limit");
    $getStmt->execute();
    $rows = $getStmt->fetchAll();
    return $rows;
}