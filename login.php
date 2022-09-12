<?php require_once('core/initialize.php'); ?>
<?php
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  header("location: index.php");
  exit;
}


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
    $sql = 'SELECT * FROM users WHERE username = ?';
    $stmt = $db->prepare($sql);      
    $result = $stmt->execute([$username]);
      if($result){
          $user = $stmt->fetch(PDO::FETCH_ASSOC);
          if($user){
              if (password_verify($password, $user['password'])) {
              
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                redirect_to('login.php');
    
              }else{
                echo 'Username/Password incorrect.';
              }
          }else{
            echo 'No user found.';
          }          
          
      }else{
        echo 'Something went wrong. Please try again.';
      }     
  
  }else{
    die('No username and password');
  }
}


// $password = "1234";

// $password = password_hash($password, PASSWORD_DEFAULT);
// echo $password;

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