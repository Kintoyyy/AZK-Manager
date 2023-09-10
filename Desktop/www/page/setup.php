<?php

session_start();
include "../db/_pdo.php";
include "../api/routeros_api.class.php";
PDO_Connect("sqlite:../db/sqlite-database.sqlite3");

if (!isset($_SESSION['loggedin']) || PDO_FetchAll("SELECT COUNT(*) FROM settings")[0]['COUNT(*)'] != 0) {
    header('location: ../index.php');
    exit;
}

$API = new RouterosAPI();
$API->debug = false; //debug
$err = '';
if (isset($_POST['add'])) {
    if ($_POST['username'] != "" && $_POST['name'] != "" && $_POST['password'] != "" && $_POST['ipaddress'] != "" && $API->connect($_POST['ipaddress'], $_POST['username'], $_POST['password'])) {
        PDO_Execute(
            "INSERT INTO settings (id,name,ipaddress,username,password,currency,interface,share) 
                VALUES (:id,:name,:ipaddress,:username,:password,:currency,:interface,:share)",
            array("id" => 1, "name" => $_POST['name'], "ipaddress" => $_POST['ipaddress'], "username" => $_POST['username'], "password" => $_POST['password'], "currency" => $_POST['currency'], "interface" => $_POST['interface'], "share" => $_POST['shared'])
        );
        header('location: ../index.php');
    } else {
        $err = '<div class="alert alert-warning">Error Connection! check values..</div>';
    }
}

?>

<link rel="stylesheet" href="../src/css/bootstrap.min.css?v=<?php echo rand(); ?>">
<section class="container wrapper py-lg-5 sm" style="max-width: 700px;">
    <div class="text-center">
        <h4 class="display-4 pt-5">Add MikroTik</h4>
    </div>
    <?= $err ?>
    <form method="POST" id="myform">
        <div class="row">
            <div class="form-group col">
                <label>Name</label>
                <input type="text" name="name" id="name" class="form-control" value="" placeholder="Kintoyyy" />
            </div>
            <div class="form-group col">
                <label>Ip Address</label>
                <input type="text" name="ipaddress" id="address" class="form-control" value=""
                    placeholder="192.168.150.254" />
            </div>

                <!-- <div class="form-group col-3 hide">
                    <label>Port</label>
                    <input type="port" name="port" id="pass" class="form-control" value="8728" maxlength="5" readonly disabled/>
                </div> -->
        </div>
        <div class="row">
            <div class="form-group col">
                <label>Username</label>
                <input type="text" name="username" id="user" class="form-control" value="" placeholder="admin" />
            </div>
            <div class="form-group col">
                <label>Password</label>
                <input type="password" name="password" id="pass" class="form-control" value="" placeholder="Password" />
            </div>
        </div>
        <div class="row">
            <div class="form-group col">
                <label>Currency</label>
                <input type="text" name="currency" id="currency" class="form-control" value="₱" placeholder="₱" />
            </div>
            <div class="form-group col">
                <label>Interface</label>
                <input type="text" name="interface" id="interface" class="form-control" value=""
                    placeholder="ether-1" />
            </div>
            <div class="form-group col">
                <label for="customRange2" class="form-label">Share: <span id="percentage">50</span>%</label>
                <input type="range" class="form-range" min="1" max="100" name="shared" id="shared" value=""
                    oninput="$('#percentage').html(this.value);">
            </div>
        </div>
        <div id="button" class="my-3 d-grid gap-2">
            <button class="btn btn-success btn-block " name="add">Add MikroTik</button>
            <button class="btn btn-outline-secondary sm position-relative bottom-0 end-0"
                onclick="copyToClipboard('#script')">Copy Setup script</button>
        </div>
    </form>
    </code>
    <script src="../src/js/jquery-3.6.1.min.js"></script>
    <script>
    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        alert("paste it in winbox terminal!")
        $temp.remove();
    }
    </script>
</section>