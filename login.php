<?php
ob_start();
session_start();
$pageTitle = 'Login'; // For Page Title

if (isset($_SESSION['user'])) {
    header('Location: index.php');
}
include 'init.php';

// Check If User Coming From HTTP Post Request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $user = $_POST['username'];
        $pass = $_POST['password'];
        $hashedPass = sha1($pass);

        // Check If The User Exist In Database And Admin
        $stmt = $con->prepare("SELECT UserID, Username, Password FROM users WHERE Username = ? AND Password = ?");

        // Execute Query
        $stmt->execute(array($user, $hashedPass));

        $get = $stmt->fetch();

        // The Row Count
        $count = $stmt->rowCount();

        // If Count > 0 This Mean The Database Contain Record About This Username
        if ($count > 0 ) {
            $_SESSION['user'] = $user; // Register Session Name
            $_SESSION['uid'] = $get['UserID']; // Register User ID In Session
            header('Location: index.php'); // Redirect To Dashboard Page
            exit();
        }
    } else {
        $formErrors = array();

        $username   = $_POST['username'];
        $password   = $_POST['password'];
        $password2  = $_POST['password2'];
        $email      = $_POST['email'];

        // Filter And Validation For User Name
        if (isset($username)) {
            $filteredUser = filter_var($username, FILTER_SANITIZE_STRING);

            if (empty($filteredUser)) {
                $formErrors[] = "<strong>Username</strong> Can't Be <strong>Empty</strong>";
            } else if (strlen($filteredUser) < 4) {
                $formErrors[] = "<strong>Username</strong> Can't Be Less Than <strong>4 Characters</strong>";
            } elseif (strlen($filteredUser) > 20 ) {
                $formErrors[] = "<strong>Username</strong> Can't Be More Than <strong>20 Characters</strong>";
            }
        }

        // Check 2 Passwords Match And Hash It
        if (isset($password) && isset($password2)) {

            if (empty($password)) {
                $formErrors[] = "<strong>Password</strong> Can't Be <strong>Empty</strong>";
            }

            if (sha1($password) !== sha1($password2)) {
                $formErrors[] = "<strong>Password</strong> Is <strong>Not Match</strong>";
            }
        }

        // Filter And Validation For Email
        if (isset($email)) {
            $filteredEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

            if (filter_var($filteredEmail, FILTER_VALIDATE_EMAIL) != true) {
                $formErrors[] = "<strong>Email</strong> Is <strong>Not Valid</strong>";
            }
        }

        // Check If There's No Error Proceed The User Add
        if (empty($formErrors)) {

            // Check If User Exist In Database
            $check = checkItem("Username", "users", $username);
            if ($check == 1) {
                $formErrors[] = "Sorry This <strong>Username Is Exist</strong>";
            } else {

                // Insert User Info In Database
                $stmt = $con->prepare("INSERT INTO 
                    users(Username, Password, Email, RegStatus, Date) 
                    VALUES(:zuser, :zpass, :zmail, 0, now()) ");
                $stmt->execute(array(
                    'zuser' => $username,
                    'zpass' => sha1($password),
                    'zmail' => $email
                ));

                // Echo Success Message
                $successMsg = 'Congrats You Are Now Registered User';
            }
        }
    }
}
?>

<div class="container login-page">
    <h1 class="text-center">
        <span class="selected" data-class="login">Login</span> |
        <span data-class="signup">SignUp</span>
    </h1>
    <!-- Start Login Form -->
    <form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="input-container">
            <input
                class="form-control"
                type="text"
                name="username"
                autocomplete="off"
                placeholder="Username"
                required />
        </div>
        <div class="input-container">
            <input
                class="form-control"
                type="password"
                name="password"
                autocomplete="new-password"
                placeholder="Password"
                required />
        </div>
        <input
            class="btn btn-primary btn-block"
            name="login"
            type="submit"
            value="Login" />
    </form>
    <!-- End Login Form -->

    <!-- Start SignUp Form -->
    <form class="signup" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="input-container">
            <input
                pattern=".{4,20}"
                title="Username Between 4 & 20 Characters"
                class="form-control"
                type="text"
                name="username"
                autocomplete="off"
                placeholder="Username"
                required />
        </div>
        <div class="input-container">
            <input
                minlength="6"
                class="form-control"
                type="password"
                name="password"
                autocomplete="new-password"
                placeholder="Type a Complex Password"
                required />
        </div>
        <div class="input-container">
            <input
                minlength="6"
                class="form-control"
                type="password"
                name="password2"
                autocomplete="new-password"
                placeholder="Type a Password Again"
                required />
        </div>
        <div class="input-container">
            <input
                class="form-control"
                type="email"
                name="email"
                placeholder="Type a Valid E-Mail"
                required />
        </div>
        <input
            class="btn btn-success btn-block"
            name="signup"
            type="submit"
            value="SignUp" />
    </form>
    <!-- End SignUp Form -->
    <div class="text-center the-errors">
        <?php
            if (!empty($formErrors)) {
                foreach ($formErrors as $error) {
                    echo "<div class='msg error'>" . $error . "</div>";
                }
            }
            if (isset($successMsg)) {
                echo '<div class="msg success">' . $successMsg . '</div>';
            }
        ?>
    </div>
</div>

<?php
include $tmpl . 'footer.php';
ob_end_flush();
?>