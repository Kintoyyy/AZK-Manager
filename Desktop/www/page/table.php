<?php
if ( !isset( $_SESSION[ 'loggedin' ] ) || $_SESSION[ 'adminaccess' ] === 'no' ) {
  header( 'location: logout.php' );
  exit;
}
error_reporting(0);
ini_set('display_errors', 0);
$sellers = $API->comm("/system/script/print");
// Remaining vouchers
$hsAllUsers = $API->comm("/ip/hotspot/user/print");
$remVouchers = array();
foreach ($hsAllUsers as $codes) {
    if (isset(explode(",", $codes['comment'])[3])) {
        $remVouchers[] = array('name' => explode(",", $codes['comment'])[3], 'type' => explode(",", $codes['comment'])[1], 'disabled' => $codes['disabled'], 'id' => $codes['.id']);
        $typVouchers[] = array('type' => explode(",", $codes['comment'])[1]);
    }
}
$types = array_unique($typVouchers, SORT_REGULAR);
//release function
if (isset($_GET['release'])) {
    $vendor = $API->comm("/system/script/print", array("?name" => ($_GET['release']),));
    $getdata = explode(",", $vendor[0]['comment']);
    $API->comm("/system/script/set", array(".id" => $vendor[0][".id"], "source" => "0", "comment" => "VendoSales," . $getdata[1] . "," . $vendor[0]["source"] . "," . $getdata[3]));
    $API->comm('/system/script/run', array(".id" => $vendor[0][".id"]));
    echo "<script>window.location.href = 'index.php?page=sales';</script>";
}
//remove function working
if (isset($_GET['remove'])) {
    $vendor = $API->comm("/system/script/print", array("?name" => ($_GET['remove']),));
    $API->comm("/system/script/remove", array(".id" => $vendor[0][".id"],));
    foreach ($remVouchers as $value) {
        if ($value['name'] == $_GET['remove']) {
            $API->comm("/ip/hotspot/user/remove", array(".id" => $value['id']));
        }
    }
    echo "<script>window.location.href = 'index.php?page=sales';</script>";
}
// get values for seller
for ($i = 0; $i < count($sellers); $i++) {
    if (isset($sellers[$i]['comment']) && explode(",", ($sellers[$i]['comment']))[0] == 'VendoSales') {
        $lastSales = $lastSales . '"' . explode(",", ($sellers[$i]['comment']))[2] . '",';
        $color = $color . '"hsl(' . rand(0, 360) . ', 100%, 80%)",';
        $source = $source . '"' . $sellers[$i]['source'] . '",';
        $Vname = $Vname . '"' . $sellers[$i]['name'] . '",';
    }
}
//remove pending vouchers
if (isset($_GET['pending'])) {
    foreach ($remVouchers as $value) {
        if ($value['name'] == $_GET['pending'] && $value['disabled'] == "true") {
            $API->comm("/ip/hotspot/user/set", array(".id" =>  $value['id'], "disabled" =>  "false"));
        }
    }
    echo "<script>window.location.href = 'index.php?page=sales';</script>";
}
?>


<script type="text/javascript" src="../src/js/datatables.min.js?ver=<?php echo rand(); ?>"></script>
<div class="row" style="overflow-x:auto;">
    <p class=""></p>
    <form method="post">
        <table class="table table-hover table-sm" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th style="width:10%">Seller</th>
                    <th style="width:7%">Curent</th>
                    <th style="width:12%"><?php echo '<span class="badge mx-1 text-dark fw-bold bg-info">' . (100 - $MikroTik['share']) . '%</span> / <span class="badge fw-bold  text-dark bg-warning">' . $MikroTik['share'] . '%</span>' ?>
                    </th>
                    <th style="width:5%">Monthly</th>
                    <th style="width:5%">Daily</th>
                    <th style="width:13%">Last release</th>
                    <th>Remaining Voucher's</th>
                    <th>Edit Seller</th>
                </tr>
            </thead>
            <tbody>
                <?php $Totalsellers = count($sellers);
                for ($i = 0; $i < $Totalsellers; $i++) {
                    if (isset($sellers[$i]['comment']) && explode(",", ($sellers[$i]['comment']))[0] == 'VendoSales') {
                        echo '<tr>';
                        echo '<td class=" fw-bold ">' . $sellers[$i]['name'] . '</td>';
                        echo '<td>' . $MikroTik['currency'] . number_format($sellers[$i]['source']) . '</td>';
                        echo '<td><h2 class="badge mx-1 text-dark fw-bold bg-info">' . $MikroTik['currency'] . number_format($sellers[$i]['source'] * (100 - $MikroTik['share']) * .01) . '</h2>
                                                <h2 class="badge fw-bold  text-dark bg-warning">' . $MikroTik['currency'] . number_format($sellers[$i]['source'] * $MikroTik['share'] * .01) . '</h2></td>';
                        echo '<td>' . $MikroTik['currency'] . number_format(explode(",", ($sellers[$i]['comment']))[1]) . '</td>';
                        echo '<td >' . $MikroTik['currency'] . number_format(explode(",", ($sellers[$i]['comment']))[3]) . '</td>';
                        echo '<td>' . $MikroTik['currency'] . number_format(explode(",", ($sellers[$i]['comment']))[2]) . ' - <span class="badge bg-warning text-dark">' . (explode(" ", $sellers[$i]['last-started'])[0]) . '</span></td>';
                        echo '</td><td>';

                        $count = 0;
                        foreach ($remVouchers as $value) {
                            if ($value['name'] == $sellers[$i]['name'] && $value['disabled'] == "false") {
                                $count++;
                            }
                        }
                        if ($count != 0) {
                            echo '<h1 class="badge mx-1 fw-bold bg-primary">Total - ' . number_format($count) . ' pcs
                                </h4>';
                        } else {
                            echo '<h2 class="badge mx-1 fw-bold bg-danger">No Vouchers!</h2>';
                        }
                        $tag = 0;
                        foreach ($remVouchers as $value) {
                            if ($value['name'] == $sellers[$i]['name'] && $value['disabled'] == "true") {
                                $tag++;
                            }
                        }
                        if ($tag != 0) {
                            echo '<h1 class="badge mx-1 fw-bold bg-secondary">Pending - ' . number_format($tag) . ' pcs</h4>';
                        }
                        foreach ($types as $t) {
                            $c = 0;
                            foreach ($remVouchers as $value) {
                                if ($value['name'] == $sellers[$i]['name'] && $value['type'] == $t['type'] && $value['disabled'] == "false") {
                                    $c++;
                                }
                            }
                            if ($c != 0) {
                                if ($c < 50) {
                                    echo '<h2 class="badge mx-1 fw-bold rounded-pill bg-warning">' . $MikroTik['currency'] . number_format($t['type']) . ' - ' . $c . ' pcs</h2>';
                                } else {
                                    echo '<h2 class="badge mx-1 fw-bold rounded-pill bg-success">' . $MikroTik['currency'] . number_format($t['type']) . ' - ' . number_format($c) . ' pcs</h2>';
                                }
                            }
                        }
                ?>

                        </td>
                        <td>

                            <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                <a class="btn btn-success btn-icon-split btn-sm" onClick="javascript: return confirm('Release <?= $sellers[$i]['name'] ?> Sales?');" href='index.php?page=sales&release=<?= $sellers[$i]['name'] ?>'>
                                    <span class="icon text-white-50">
                                        <i class="fas fa-hand-holding-usd"></i>
                                    </span>
                                </a>
                                <a class="btn btn-secondary btn-icon-split btn-sm" onClick="javascript: return confirm('Release pending  <?= $sellers[$i]['name'] ?> vouchers?');" href='index.php?page=sales&pending=<?= $sellers[$i]['name'] ?>'>
                                    <span class="icon text-white-50">
                                        <i class="fas fa-file-upload"></i>
                                    </span>
                                </a>
                                <a class="btn btn-danger btn-icon-split btn-sm" onClick="javascript: return confirm('Delete <?= $sellers[$i]['name'] ?> and vouchers?');" href='index.php?page=sales&remove=<?= $sellers[$i]['name'] ?>'>
                                    <span class="icon text-white-50">
                                        <i class="fas fa-trash"></i>
                                    </span>
                                </a>
                            </div>

                        </td>
                <?php
                    }
                }; ?>
                </tr>
            </tbody>
        </table>
    </form>
</div>