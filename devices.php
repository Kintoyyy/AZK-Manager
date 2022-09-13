<?php require_once('core/initialize.php'); ?>
<?php

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    redirect_to('login.php');
    exit;
}else{
    $user = $_SESSION['id'];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty(trim($_POST['ipaddress']))) {
        $error = 'Ip address is required.';
    } elseif (empty(trim($_POST['username'])) ) {
        $error = 'Ip address is required.';
    }elseif (empty(trim($_POST['password'])) ) {
        $error = 'Password is required.';
    } else {
        
        $ipaddress = trim($_POST['ipaddress']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $port = 8728;   
        
        $sql = "INSERT INTO devices (serverip,musername,mpassword,mport,userid) VALUES(?,?,?,?,?)";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([$ipaddress,$username,$password,$port,$user]);

        if($result){
            echo 'Success';
        }else{
            echo 'Failed';
        }
        
    }


    
  
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Device - Juanfi Agent Manager</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="src/kint.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <main>
        <section  class="container wrapper py-lg-5" style="max-width: 500px;">
        <div class="text-center">
        <img src="src/logo.png" class="rounded" alt="..." style="width: 150px;">
        <h2 class="display-5 pt-5">Add Device</h2>
      </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group mb-3 ">
                    <label>IP Address / Server Ip</label>
                    <input type="text" name="ipaddress" class="form-control">
                    <span class="help-block"></span>
                </div>
                <div class="form-group mb-10 ">
                    <label>Mikrotik Username</label>
                    <input type="text" name="username" class="form-control" >
                    <span class="help-block"></span>
                </div>
                <div class="form-group mb-3 ">
                    <label>Mikrotik Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password">
                    <span class="help-block"></span>
                </div>
                <div class="form-group mb-3 ">
                    <label>Mikrotik Port</label>
                    <input type="text" name="port" class="form-control" value="8728" readonly>
                    <span class="help-block"></span>
                </div>
                <div class="form-group  d-grid gap-2">
                    <input type="submit" class="btn btn-block btn-primary" value="Submit">
                    <a class="btn btn-block btn-link bg-light" href="index.php">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>