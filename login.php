<?php
session_start();
include "db/_pdo.php";
$db_file = "db/sqlite-database.sqlite3";
PDO_Connect("sqlite:$db_file");

$username = $password = '';
$username_err = $password_err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty(trim($_POST['username']))) {
        $username_err = 'Please enter username.';
    } else {
        $username = trim($_POST['username']);
    }
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter your password.';
    } else {
        $password = trim($_POST['password']);
    }
    if (empty($username_err) && empty($password_err)) {
        $user = PDO_FetchRow("SELECT * FROM users WHERE username = :username", array("username" => "$username"));
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['adminaccess'] = $user['adminaccess'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['mikrotik'] = 1;
                header('location: index.php');
            } else {
                $password_err = 'Invalid password';
            }
        } else {
            $username_err = "Username does not exists.";
        }
        
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign in</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="src/kint.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/css/bootstrap.min.css?ver=<?php echo rand(); ?>">
</head>
<body>
    <main>
        <section class="container wrapper py-5" style="max-width: 500px;">
            <div class="text-center">
                <img src="src/logo.png" class="rounded" alt="..." style="width: 150px;">
                <h2 class="display-4 pt-5">AZK Manager</h2>
            </div>

            <form method="POST">
                <div class="form-group mb-3">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?php echo $username ?>">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" value="<?php echo $password ?>">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group d-grid gap-2">
                    <input type="submit" class="btn col btn-outline-primary" value="login">
                </div>
            </form>
        </section>
    </main>
</body>
</html>