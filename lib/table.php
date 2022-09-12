<?php
//release function
if (isset($_POST['release'])) {
    $vendor = $API->comm("/system/script/print", array("?name" => ($_POST['release']),));
    $getMonthly = explode(",", $vendor[0]['comment'])[1];
    $API->comm("/system/script/set", array(".id" => $vendor[0][".id"], "source" => "0", "comment" => "VendoSales," . $getMonthly . "," . $vendor[0]["source"]));
    $API->comm('/system/script/run', array(".id" => $vendor[0][".id"]));
    header("Refresh:0");
}
//remove function working
if (isset($_POST['remove'])) {
    $vendor = $API->comm("/system/script/print", array("?name" => ($_POST['remove']),));
    $vendorShed = $API->comm("/system/scheduler/print", array("?name" => "Reset " . ($_POST['remove']) . " Income",));
    $API->comm("/system/script/remove", array(".id" => $vendor[0][".id"],));
    $API->comm("/system/scheduler/remove", array(".id" => $vendorShed[0][".id"],));
    header("Refresh:0");
}
// Remaining vouchers
$hsAllUsers = $API->comm("/ip/hotspot/user/print");
$remVouchers = array();
foreach ($hsAllUsers as $codes) {
    if (isset(explode(",", $codes['comment'])[3])) {
        $remVouchers[] = array('name' => explode(",", $codes['comment'])[3], 'type' => explode(",", $codes['comment'])[1]);
        $typVouchers[] = array('type' => explode(",", $codes['comment'])[1]);
    }
}
$types = array_unique($typVouchers, SORT_REGULAR);

// get values for seller
for ($i = 0; $i < count($sellers); $i++) {
    if (isset($sellers[$i]['comment']) && explode(",", ($sellers[$i]['comment']))[0] == 'VendoSales') {
        $lastSales = $lastSales . '"' . explode(",", ($sellers[$i]['comment']))[2] . '",';
        $color = $color . '"hsl(' . rand(0, 360) . ', 100%, 80%)",';
        $source = $source . '"' . $sellers[$i]['source'] . '",';
        $Vname = $Vname . '"' . $sellers[$i]['name'] . '",';
    }
}
?>
<div class="row">
    <div>
        <div class="card shadow mb-4">
            <div class="card-body" style="overflow-x:auto;">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Seller/Vendo</th>
                            <th>Monthly Revenue</th>
                            <th>Curent Revenue</th>
                            <th><?php echo '<span class="badge mx-1 text-dark fw-bold bg-info">'.(100 - $SHARE).'%</span> / <span class="badge fw-bold  text-dark bg-warning">'.$SHARE.'%</span>'?></th>
                            <th>Last Revenue</th>
                            <th>Last Release</th>
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
                                echo '<td>' . $CURRENCY . number_format(explode(",", ($sellers[$i]['comment']))[1]) . '</td>';
                                echo '<td>' . $CURRENCY . number_format($sellers[$i]['source']) . '</td>';
                                echo '<td><h2 class="badge mx-1 text-dark fw-bold bg-info">' . $CURRENCY . number_format($sellers[$i]['source'] * (100 - $SHARE) * .01) . '</h2>
                                                <h2 class="badge fw-bold  text-dark bg-warning">' . $CURRENCY . number_format($sellers[$i]['source'] * $SHARE * .01) . '</h2></td>';
                                echo '<td>' . $CURRENCY . number_format(explode(",", ($sellers[$i]['comment']))[2]) . '</td>';
                                if (empty($sellers[$i]['last-started'])) {
                                    $slr = "";
                                } else {
                                    $slr = (explode(" ", $sellers[$i]['last-started']))[0];
                                }
                                echo '<td><span class="badge bg-warning text-dark">' . $slr . '</span></td>';
                                echo '</td><td>';

                                $count = 0;
                                foreach ($remVouchers as $value) {
                                    if ($value['name'] == $sellers[$i]['name']) {
                                        $count++;
                                    }
                                }
                                if ($count != 0) {
                                    echo '<h1 class="badge mx-1 fw-bold bg-primary">Total - ' . number_format($count) . ' pcs</h4>';
                                } else {
                                    echo '<h2 class="badge mx-1 fw-bold bg-danger">No Vouchers!</h2>';
                                }
                                foreach ($types as $t) {
                                    $c = 0;
                                    foreach ($remVouchers as $value) {
                                        if ($value['name'] == $sellers[$i]['name'] && $value['type'] == $t['type']) {
                                            $c++;
                                        }
                                    }
                                    if ($c != 0) {
                                        if ($c < 25) {
                                            echo '<h2 class="badge mx-1 fw-bold rounded-pill bg-warning">' . $CURRENCY . $t['type'] . ' - ' . $c . ' pcs</h2>';
                                        } else {
                                            echo '<h2 class="badge mx-1 fw-bold rounded-pill bg-success">' . $CURRENCY . $t['type'] . ' - ' . number_format($c) . ' pcs</h2>';
                                        }
                                    }
                                }
                                echo '</td><td>
                      <form method="post">
                      <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                      <button type="submit" name="release" value="' . $sellers[$i]['name'] . '" class="btn btn-success btn-icon-split btn-sm">
                        <span class="icon text-white-50">
                          <i class="fas fa-check"></i>
                        </span>
                        <span class="text">Release</span>
                      </button>
                      <!--<button type="submit" name="edit" value="' . $sellers[$i]['name'] . '" class="btn btn-primary btn-icon-split btn-sm">
                        <span class="icon text-white-50">
                          <i class="fas fa-pen"></i>
                        </span>
                        <span class="text">Edit</span>
                      </button>-->
                      <button type="submit" name="remove" value="' . $sellers[$i]['name'] . '"  class="btn btn-danger btn-icon-split btn-sm">
                        <span class="icon text-white-50">
                          <i class="fas fa-trash"></i>
                        </span>
                      </button>
                      </div>
                    </form>
                             </td></tr>';
                            }
                        };
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>