<?php
session_start();
$noNavbar = ''; // For No Navbar
$pageTitle = 'Dashboard Login'; // For Page Title
if (isset($_SESSION['Username'])) { // Check If There's A Session With Your Username
    header('Location: dashboard.php'); // Redirect To Dashboard Page
}
include "init.php";

// Check If User Coming From HTTP Post Request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['user'];
    $password = $_POST['pass'];
    $hashedPass = sha1($password);

    // Check If The User Exist In Database And Admin
    $stmt = $con->prepare("SELECT UserID, Username, Password FROM users WHERE Username = ? AND Password = ? AND GroupID = 1 LIMIT 1");

    // Execute Query
    $stmt->execute(array($username, $hashedPass));

    // Fetch The Data
    $row = $stmt->fetch();

    // The Row Count
    $count = $stmt->rowCount();

    // If Count > 0 This Mean The Database Contain Record About This Username
    if ($count > 0 ) {
        $_SESSION['Username'] = $username; // Register Session Name
        $_SESSION['ID'] = $row['UserID']; // Register Session UserID
        header('Location: dashboard.php'); // Redirect To Dashboard Page
        exit();
    }
}
?>
    <form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <h4 class="text-center">Admin Login</h4>
        <input class="form-control" type="text" name="user" placeholder="Username" autocomplete="off" />
        <input class="form-control" type="password" name="pass" placeholder="Password" autocomplete="new-password" />
        <input class="btn btn-primary btn-block" type="submit" value="Login" />
    </form>

<?php include $tmpl . "footer.php"; ?>