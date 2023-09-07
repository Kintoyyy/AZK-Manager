<?php
if (!isset($_SESSION['loggedin']) && $_SESSION['adminaccess'] == 'no') {
    header('location: login.php');
    exit;
}

$data = $API->comm('/ip/hotspot/user/print');
$srvlist = $API->comm("/ip/hotspot/print");
$getprofile = $API->comm("/ip/hotspot/user/profile/print");

//remove function (working)
if (isset($_POST['remove'])) {
    $API->comm("/ip/hotspot/user/remove", array(".id" => $_POST['remove']));
    echo "<script>window.location.href = 'index.php?page=users;</script>";
    exit;
}
//add function (working)
if (isset($_POST['add'])) {
    $API->comm("/ip/hotspot/user/add", array(
        "server" => $_POST['server'],
        "name" => $_POST['username'],
        "password" => $_POST['password'],
        "profile" => $_POST['profile'],
        "limit-uptime" => $_POST['uptime'],
        "comment" => "byAPI"
    )
    );
    echo '<script>window.location.href = "index.php?page=users";</script>';
    exit;
}
//disable function (Working)
if (isset($_POST['disable'])) {
    $id = explode(",", ($_POST['disable']));
    $API->comm("/ip/hotspot/user/set", array(
        ".id" => $id[0],
        "disabled" => $id[1],
    )
    );
    echo "<script>window.location.href = 'index.php?page=users;</script>";
    exit;
}

//edit function (not Working)
if (isset($_POST['edit'])) {
    $API->comm("/ip/hotspot/user/set", array(
        ".id" => $_POST['edit'],
        "name" => $_POST['username'],
        "password" => $_POST['password'],
        "profile" => $_POST['profile'],
        "server" => $_POST['server'],
        //"mac-address" => $_POST['mac-address'], // <-- Problem
        "limit-uptime" => $_POST['limit-uptime']
    )
    );
    echo "<script>window.location.href = 'index.php?page=users;</script>";
    exit;
}

//Paid function (Working)
if (isset($_POST['paid'])) {
    $user = explode(",", $_POST['paid']);
    $comment = "Users," . $user[1] . "," . $user[2] . "," . $user[3] . "," . $user[4] . "," . $user[5];
    $API->comm("/ip/hotspot/user/set", array(
        ".id" => $user[0],
        "comment" => $comment,
    )
    );

    echo "<script>window.location.href = 'index.php?page=users;</script>";
}
?>

<link rel="stylesheet" type="text/css" href="../src/css/datatables.min.css?ver=<?php echo rand(); ?>" />
<script type="text/javascript" src="../src/js/datatables.min.js?ver=<?php echo rand(); ?>"></script>

<div class="container py-5">
    <div class=" mb-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSub">
            Add Users
        </button>
    </div>
    <div class="row" style="overflow-x:auto;">
        <table id="Users" class="table table-hover table-sm" width="100%">
            <thead>
                <tr>
                    <th>Name <div class="badge"></div></th>
                    <th>Profile</th>
                    <th>Server</th>
                    <th>Limit</th>
                    <th>Comment</th>
                    <th width=50>Edit</th>
                </tr>
            </thead>
            <tbody>
                
                <?php
                // var_dump($data);
                foreach ($data as $client) {
                    if (isset($client['profile'])) {
                        echo '<tr>';
                        echo '<td><label class="form-check-label" for="check_list">' . $client['name'] . '</label></td>';
                        echo '<td>' . $client['profile'] . '</td>';
                        echo '<td>' . ($client['server'] ?? ' ') . '</td>';
                        echo '<td>' . ($client['limit-uptime'] ?? '') . '</td>';
                        echo '<td>' . ($client['comment'] ?? '') . '</td>';
                        echo '<td>';
                        echo '<button data-bs-toggle="modal" data-bs-target="#editSub" onclick="edit(\''
                            . $client['.id'] . '\',\'' . $client['name'] . '\',\'' . ($client['password'] ?? '') . '\',\''
                            . ($client['limit-uptime'] ?? '') . '\',\'' . $client['profile'] . '\',\'' . ($client['server'] ?? '')
                            . '\',\'' . ($client['mac-address'] ?? ' ') . '\',\'' . $client['disabled'] . '\')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i> Edit</button>';
                        echo '</td>';
                        echo '</tr>';
                    }
                }                
                ?>
            </tbody>
        </table>
    </div>
</div>



<!--add-->
<div class="modal fade" id="addSub" tabindex="-1" aria-labelledby="addSubLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubLabel">Add</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <!--Name-->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Username</label>
                        </div>
                        <input type="text" aria-label="Limit" class="form-control" value="" name="username">
                    </div>
                    <!--Password-->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Password</label>
                        </div>
                        <input type="text" aria-label="Limit" class="form-control" value="" name="password">
                    </div>
                    <!--Limit-->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Limit Uptime</label>
                        </div>
                        <input type="text" placeholder="1d1h" class="form-control" value="" name="uptime">
                    </div>
                    <!--Profile-->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="profile">Profile</label>
                        </div>
                        <select class="form-select" name="profile" name="profile" value="">
                            <?php $TotalReg = count($getprofile);
                            for ($i = 0; $i < $TotalReg; $i++) {
                                echo "<option>" . $getprofile[$i]['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <!--Server-->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="server">Server</label>
                        </div>
                        <select class="form-select" name="server" name="server" value="">
                            <option>all</option>
                            <?php $TotalReg = count($srvlist);
                            for ($i = 0; $i < $TotalReg; $i++) {
                                echo "<option>" . $srvlist[$i]['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--edit-->
<div class="modal fade" id="editSub" tabindex="-1" aria-labelledby="editSubLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h5 class="modal-title" id="editSubLabel">Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <span id="editmodal" class="badge badge-primary"></span>
                    <!--Name-->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Username</label>
                        </div>
                        <input type="text" aria-label="Username" class="form-control" value="" id="username"
                            name="username">
                    </div>
                    <!--Password-->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Password</label>
                        </div>
                        <input type="text" aria-label="Password" class="form-control" value="" id="password"
                            name="password">
                    </div>
                     <!--Limit-->
                     <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Limit Uptime</label>
                        </div>
                        <input type="text" aria-label="Limit" class="form-control" value="" name="uptime" id="uptime">
                    </div>
                    <!--Profile-->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="profile">Profile</label>
                        </div>
                        <select class="form-select" id="profile" name="profile" value="">
                            <?php $TotalReg = count($getprofile);
                            for ($i = 0; $i < $TotalReg; $i++) {
                                echo "<option>" . $getprofile[$i]['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <!--Server-->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="server">Server</label>
                        </div>
                        <select class="form-select" name="server" id="server" value="">
                            <option>all</option>
                            <?php $TotalReg = count($srvlist);
                            for ($i = 0; $i < $TotalReg; $i++) {
                                echo "<option>" . $srvlist[$i]['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="remove" id="remove" value="" class="btn btn-danger"><i
                            class="fas fa-trash"></i> Delete</button>
                    <button type="submit" name="disable" id="disable" value=""
                        class="btn btn-secondary">Disable</button>
                    <button type="submit" name="edit" id="edit" value="" class="btn btn-success"><i
                            class="fas fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#Users').DataTable();
    });

    function edit(id, name, password, uptime, profile, server, mac, disable) {
        if(server == ""){
            server = "all";
        }
        $('#id,#remove,#edit').val(id);
        $('#username').val(name);
        $('#password').val(password);
        $('#uptime').val(uptime);
        $('#profile').val(profile);
        $('#server').val(server);
        $('#mac-address').val(mac);
        if (disable == "true") {
            $('#disable').val(id + ",false");
            $('#disable').html('<i class="fas fa-unlock"></i> Enable');
        } else {
            $('#disable').val(id + ",true");
            $('#disable').html('<i class="fas fa-lock"></i> Disable');
        }
    }
</script>