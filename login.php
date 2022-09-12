<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  header("location: index.php");
  exit;
}
require_once "config/config.php";
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
    $sql = 'SELECT id, username, password FROM users WHERE username = ?';
    if ($stmt = $mysql_db->prepare($sql)) {
      $param_username = $username;
      $stmt->bind_param('s', $param_username);
      if ($stmt->execute()) {
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
          $stmt->bind_result($id, $username, $hashed_password);
          if ($stmt->fetch()) {
            if (password_verify($password, $hashed_password)) {
              session_start();
              $_SESSION['loggedin'] = true;
              $_SESSION['id'] = $id;
              $_SESSION['username'] = $username;
              header('location: index.php');
            } else {
              $password_err = 'Invalid password';
            }
          }
        } else {
          $username_err = "Username does not exists.";
        }
      } else {
        echo "Oops! Something went wrong please try again";
      }
      $stmt->close();
    }
    $mysql_db->close();
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
  <main>
    <section class="container wrapper py-lg-5 sm" style="max-width: 500px;">
      <div class="text-center">
        <img src="src/logo.png" class="rounded" alt="..." style="width: 150px;">
        <h2 class="display-4 pt-5">Login Juanfi Agent Manager</h2>
      </div>

      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <div class="form-group mb-3 <?php (!empty($username_err)) ? 'has_error' : ''; ?>">
          <label for="username">Username</label>
          <input type="text" name="username" id="username" class="form-control" value="<?php echo $username ?>">
          <span class="help-block"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group mb-3 <?php (!empty($password_err)) ? 'has_error' : ''; ?>">
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