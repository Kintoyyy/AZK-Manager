<?php
if (!isset($_SESSION['loggedin'])) {
    header('location: login.php');
    exit;
}

if ($_SESSION['adminaccess'] == 'yes') {

    $error = "";
    if (isset($_POST['Remove'])) {
        $id = $_POST['Remove'];
        PDO_Execute("DELETE FROM settings WHERE id = '$id'");
    }
    if (isset($_POST['add'])) {
        if ($_POST['username'] != "" && $_POST['name'] != "" && $_POST['password'] != "" && $_POST['ipaddress'] != "") {
            PDO_Execute(
                "INSERT INTO settings (name,ipaddress,username,password,port,currency,interface,share) 
            VALUES (:name,:ipaddress,:username,:password,:port,:currency,:interface,:share)",
                array("name" => $_POST['name'], "ipaddress" => $_POST['ipaddress'], "username" => $_POST['username'], "password" => $_POST['password'], "port" => $_POST['port'], "currency" => $_POST['currency'], "interface" => $_POST['interface'], "share" => $_POST['shared'])
            );
        } else {
            $error = '<div class="alert alert-warning">Enter some values!</div>';
        }
    }

    if (isset($_POST['update'])) {
        $MikroTik = PDO_FetchRow("SELECT * FROM settings WHERE id = :id", array("id" => $_POST['update']));
        $id = $_POST['update'];
        $name = $_POST['name'] ? $_POST['name'] : $MikroTik['name'];
        $username = $_POST['username'] ? $_POST['username'] : $MikroTik['username'];
        $ipaddress = $_POST['ipaddress'] ? $_POST['ipaddress'] : $MikroTik['ipaddress'];
        $password = $_POST['password'] ? $_POST['password'] : $MikroTik['password'];
        $port = $_POST['port'] ? $_POST['port'] : $MikroTik['port'];
        $currency = $_POST['currency'] ? $_POST['currency'] : $MikroTik['currency'];
        $interface = $_POST['interface'] ? $_POST['interface'] : $MikroTik['interface'];
        $shared = $_POST['shared'] ? $_POST['shared'] : $MikroTik['shared'];
        PDO_Execute("UPDATE settings SET password = '$password',name = '$name', ipaddress = '$ipaddress',port = '$port' ,currency = '$currency' ,interface = '$interface',share = '$shared' WHERE id = '$id'");
    }
    $mikroTik = PDO_FetchAll("SELECT * FROM settings");
    include "page/header.php";
?>
    <section class="container wrapper py-lg-5 sm" style="max-width: 700px;">
        <div class="text-center">
            <h4 class="display-4 pt-5">Edit MikroTik</h4>
        </div>
        <form method="POST" id="myform">
            <?= $error ?>
            <div class="row">
                <div class="form-group col">
                    <label>Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="" placeholder="Kintoyyy" />
                </div>
                <div class="form-group col">
                    <label>Ip Address</label>
                    <input type="text" name="ipaddress" id="address" class="form-control" value="" placeholder="192.168.150.254" />
                </div>

                <div class="form-group col-3">
                    <label>Port</label>
                    <input type="port" name="port" id="pass" class="form-control" value="8728" maxlength="5" />
                </div>
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
                    <input type="text" name="interface" id="interface" class="form-control" value="" placeholder="ether-1" />
                </div>
                <div class="form-group col">
                    <label for="customRange2" class="form-label">Share: <span id="percentage">50</span>%</label>
                    <input type="range" class="form-range" min="1" max="100" name="shared" id="shared" value="" oninput="$('#percentage').html(this.value);">
                </div>
            </div>
            <div id="button" class="my-3 d-grid gap-2">
                <button class="btn btn-success btn-block " name="add">Add MikroTik</button>
                <a class="btn btn-secondary btn-block" href="index.php">Back</a>
            </div>
        </form>
        <div style="overflow-x:auto;">
            <table id="Active" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width=120>Name</th>
                        <th>Address</th>
                        <th>Username</th>
                        <th>port</th>
                        <th width=140>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mikroTik as $index => $MT) : ?>
                        <tr>
                            <td><?= $MT['name']; ?></td>
                            <td><?= $MT['ipaddress']; ?></td>
                            <td><?= $MT['username']; ?></td>
                            <td><?= $MT['port']; ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm text-center" onclick="edit('<?= $MT['name'] ?>','<?= $MT['ipaddress']; ?>','<?= $MT['username']; ?>','<?= $MT['port']; ?>','<?= $MT['id']; ?>','<?= $MT['currency']; ?>','<?= $MT['interface']; ?>','<?= $MT['share']; ?>')">Edit</i></button>
                                <button form="myform" class="btn btn-danger btn-block btn-sm" name="Remove" value="<?= $MT['id']; ?>">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </section>
    <script src="../src/js/jquery-3.6.1.min.js?ver=<?php echo rand(); ?>"></script>
    <script>
        function edit(name, address, user, port, id, currency, interface, share) {
            $('#name').val(name);
            $('#address').val(address);
            $('#user').val(user);
            $('#port').val(port);
            $('#currency').val(currency);
            $('#interface').val(interface);
            $('#shared').val(share);
            $('#percentage').html(share);
            $('#button').html('<button form="myform" class="btn btn-success btn-block " value="' + id + '" name="update">Apply</button><button class="btn btn-secondary btn-block" onclick="clear()">Clear</button>');
        }

        function clear() {
            $('#myform').closest('form').find("input[type=text], textarea").val("");
            $('#button').html('<button class="btn btn-success btn-block " name="add">Add MikroTik</button><a class="btn btn-secondary btn-block" href="index.php">Back</a>');
        }
    </script>

    </html>

<?php } ?>