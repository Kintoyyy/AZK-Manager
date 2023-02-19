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
    echo "<script>window.location.href = 'index.php?page=subscriber';</script>";
}
//add function (working)
if (isset($_POST['add'])) {
    $comment = "subscriber," . $_POST['due'] . "," . $_POST['price'] . "," . "unpaid" . "," . $_POST['number'] . "," . $_POST['facebook'];
    $API->comm("/ip/hotspot/user/add", array(
        "server" => $_POST['server'],
        "name" => $_POST['username'],
        "password" => $_POST['password'],
        "profile" => $_POST['profile'],
        "comment" => "$comment",
    )
    );
    echo "<script>window.location.href = 'index.php?page=subscriber';</script>";
}
//disable function (Working)
if (isset($_POST['disable'])) {
    $id = explode(",", ($_POST['disable']));
    $API->comm("/ip/hotspot/user/set", array(
        ".id" => $id[0],
        "disabled" => $id[1],
    )
    );
    echo "<script>window.location.href = 'index.php?page=subscriber';</script>";
}

//edit function (not Working)
if (isset($_POST['edit'])) {
    $comment = "subscriber," . $_POST['due'] . "," . $_POST['price'] . "," . $_POST['status'] . "," . $_POST['number'] . "," . $_POST['facebook'];
    $API->comm("/ip/hotspot/user/set", array(
        ".id" => $_POST['edit'],
        "name" => $_POST['username'],
        "password" => $_POST['password'],
        "profile" => $_POST['profile'],
        "server" => $_POST['server'],
        //"mac-address" => $_POST['mac-address'], // <-- Problem
        "comment" => $comment,
    )
    );
    echo "<script>window.location.href = 'index.php?page=subscriber';</script>";
}

//Paid function (Working)
if (isset($_POST['paid'])) {
    $user = explode(",", $_POST['paid']);
    $comment = "subscriber," . $user[1] . "," . $user[2] . "," . $user[3] . "," . $user[4] . "," . $user[5];
    $API->comm("/ip/hotspot/user/set", array(
        ".id" => $user[0],
        "comment" => $comment,
    )
    );

    echo "<script>window.location.href = 'index.php?page=subscriber';</script>";
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
        <table id="Subscriber" class="table table-hover table-sm" width="100%">
            <thead>
                <tr>
                    <th>Name <div class="badge"></div>
                    </th>
                    <th>Status</th>
                    <th>Plan</th>
                    <th>Price</th>
                    <th>Next Due</th>
                    <th>Server</th>
                    <th>Mac Address</th>
                    <th>Contact no:</th>
                    <th>Facebook</th>
                    <th width=50>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($data as $client) {
                    if (isset($client['comment']) && explode(",", $client['comment'])[0] == "subscriber") {
                        echo '<tr>';
                        echo '<td><label class="form-check-label" for="check_list">' . $client['name'] . '</label></td>';
                        echo '<td>';
                        if ($client['disabled'] == "false") {
                            switch (explode(",", $client['comment'])[3]) {
                                case "paid":
                                    echo "<span class='badge mx-1 fw-bold rounded-pill bg-success'>" . explode(",", $client['comment'])[3] . "</span>";
                                    break;
                                case "unpaid":
                                    echo "<span class='badge mx-1 fw-bold rounded-pill bg-warning'>" . explode(",", $client['comment'])[3] . "</span>";
                                    break;
                                default:
                                    echo "<span class='badge mx-1 fw-bold rounded-pill bg-danger'>Error!</span>";
                            }
                        } else {
                            echo "<span class='badge mx-1 fw-bold rounded-pill bg-secondary'>Disabled</span>";
                        }
                        echo '</td>';
                        echo "<td>" . $client['profile'] . "</td>";
                        echo "<td>" . $MikroTik['currency'] . explode(",", $client['comment'])[2] . "</td>";
                        echo "<td>" . number_format(explode(",", $client['comment'])[1]) . "</td>";
                        echo "<td>" . $client['server'] = $client['server'] ?? ' ' . "</td>";
                        echo "<td>" . $client['mac-address'] = $client['mac-address'] ?? ' ' . "</td>";
                        echo "<td>" . explode(",", $client['comment'])[4] . "</td>";
                        echo "<td><a href='https://www.facebook.com/" . explode(",", $client['comment'])[5] . "'>@" . explode(",", $client['comment'])[5] . "</a></td>";
                        echo '<td><button data-bs-toggle="modal" data-bs-target="#editSub" 
                                            onclick="edit(\'' . $client['.id'] .
                            '\',\'' . $client['name'] .
                            '\',\'' . $client['password'] .
                            '\',\'' . (explode(",", $client['comment'])[3] ? explode(",", $client['comment'])[3] : 'Error') .
                            '\',\'' . explode(",", $client['comment'])[4] .
                            '\',\'' . explode(",", $client['comment'])[5] .
                            '\',\'' . explode(",", $client['comment'])[2] .
                            '\',\'' . explode(",", $client['comment'])[1] .
                            '\',\'' . $client['profile'] .
                            '\',\'' . $client['server'] .
                            '\',\'' . $client['mac-address'] .
                            '\',\'' . $client['disabled'] .
                            '\')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i> Edit</button></td>';
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
                        <input type="password" aria-label="Limit" class="form-control" value="" name="password">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Number</label>
                        </div>
                        <input type="number" class="form-control" value="" name="number">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Facebook</label>
                        </div>
                        <input type="text" class="form-control" value="" name="facebook">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Price</label>
                        </div>
                        <input type="number" class="form-control" value="" name="price">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Due Date</label>
                        </div>
                        <!--<input type="text" class="form-control" value="" name="due">-->
                        <select class="form-select" name="due" value="">
                            <?php for ($i = 1; $i < 32; $i++) {
                                echo "<option value='" . $i . "'>" . $i . "</option>";
                            }
                            ?>
                        </select>
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
                        <input type="text" class="form-control" value="" id="username" name="username">
                        <select class="form-select col-3" id="status" name="status" value="">
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                        </select>
                        <input type="text" class="form-control col-2" value="" id="id" disabled>

                    </div>
                    <!--Password-->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Password</label>
                        </div>
                        <input type="password" aria-label="Limit" class="form-control" value="" id="password"
                            name="password">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Number</label>
                        </div>
                        <input type="text" class="form-control" value="" id="number" name="number" maxlength="12">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Facebook</label>
                        </div>
                        <input type="text" class="form-control" value="" id="facebook" name="facebook">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Price</label>
                        </div>
                        <input type="number" class="form-control" value="" id="price" name="price">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Due Date</label>
                        </div>
                        <select class="form-select" id="due" name="due" value="">
                            <?php for ($i = 1; $i < 32; $i++) {
                                echo "<option value='" . $i . "'>" . $i . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Mac Address</label>
                        </div>
                        <input type="text" class="form-control" value="" id="mac-address" name="mac-address"
                            maxlength="17">
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
        $('#Subscriber').DataTable();
    });

    function edit(id, name, password, status, contact, fb, price, due, profile, server, mac, disable) {
        $('#id,#remove,#edit').val(id);
        $('#username').val(name);
        $('#password').val(password);
        $('#status').val(status);
        $('#number').val(contact);
        $('#facebook').val(fb);
        $('#price').val(price);
        $('#due').val(due);
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