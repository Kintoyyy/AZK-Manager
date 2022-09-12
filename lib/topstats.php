<?php
require_once "../api/routeros_api.class.php";
require_once "../config/config.php";

$API = new RouterosAPI();

$API->connect(MT_SERVER, MT_USERNAME, MT_PASSWORD);
$stats = $API->comm("/system/resource/print");

$mt = $stats['0'];
$memperc = ($mt['free-memory'] / $mt['total-memory']);
$mem = ($memperc * 100);

$getclock = $API->comm("/system/clock/print");
$clock = $getclock['0'];

//get hotspot info
$hsActive = $API->comm("/ip/hotspot/active/print", array("count-only" => ""));
$hsAllUsers = $API->comm("/ip/hotspot/user/print", array("count-only" => ""));

//get pppoe info
$pppoeActive = $API->comm("/ppp/active/print", array("count-only" => ""));
$pppoeUsers = $API->comm("/ppp/secret/print", array("count-only" => ""));

$getpMonthly = $API->comm("/system/script/print", array("?name" => "pppoemonthlyincome"));
$monthlyPSales = $getpMonthly['0'];

//get sales
$getMonthly = $API->comm("/system/script/print", array("?name" => "monthlyincome"));
$getDaily = $API->comm("/system/script/print", array("?name" => "todayincome"));
$monthlySales = $getMonthly['0'];
$dailySales = $getDaily['0'];

//download and upload
$getinterface = $API->comm("/interface/print", array("?name" => "$INTERFACE"));
$getquota = $getinterface[0];
$downloadquota = $getquota["tx-byte"] / 1073741824;
$uploadquota = $getquota["rx-byte"] / 1073741824;
?>


<!-- Hotspot Info -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-success shadow h-100   py-1">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="fw-bold text-success">
                        Hotspot Users</div>
                    <div class="mb-0 fw-bold"><?= $hsActive;?></div>
                </div>
                <div class="col mr-2">
                    <div class="fw-bold text-success">
                        Vouchers</div>
                    <div class="mb-0 fw-bold text-gray-800"><?= $hsAllUsers;?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Hotspot Sales (Monthly) (Daily)-->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100   py-1">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="fw-bold text-primary">
                        Monthly</div>
                    <div class="mb-0 fw-bold text-gray-800"><?= $CURRENCY . number_format($monthlySales['source']); ?></div>
                </div>
                <div class="col mr-2">
                    <div class="fw-bold text-primary">
                        Today</div>
                    <div class="mb-0 fw-bold text-gray-800"><?= $CURRENCY . number_format($dailySales['source']); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Stats -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-info shadow h-100    py-1">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="fw-bold text-success">Download
                    </div>
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto">
                            <div class="mb-0 mr-3 fw-bold text-gray-800">
                                <?= number_format($downloadquota) . " GB"; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col mr-2">
                    <div class="fw-bold text-danger">Upload
                    </div>
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto">
                            <div class="mb-0 mr-3 fw-bold text-gray-800">
                                <?= number_format($uploadquota) . " GB"; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-info shadow h-100    py-1">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="fw-bold text-info">CPU
                    </div>
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto">
                            <div class="mb-0 mr-3 fw-bold text-gray-800">
                                <?php echo $mt['cpu-load'] . " %"; ?></div>
                        </div>
                        <div class="col">
                            <div class="progress progress-sm mr-2">
                                <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $mt['cpu-load'] . "%"; ?>" aria-valuenow="<?php echo $mt['cpu-load']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col mr-2">
                    <div class="fw-bold text-info">RAM
                    </div>
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto">
                            <div class="mb-0 mr-3 fw-bold text-gray-800">
                                <?php echo number_format($mem) . "%"; ?></div>
                        </div>
                        <div class="col">
                            <div class="progress progress-sm mr-2">
                                <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo number_format($mem) . "%"; ?>" aria-valuenow="<?php echo number_format($mem); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>