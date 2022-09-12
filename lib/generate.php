<?php
$API = new RouterosAPI();
$API->debug = false;

if ($API->connect(MT_SERVER, MT_USERNAME, MT_PASSWORD, MT_PORT)) {

    $srvlist = $API->comm("/ip/hotspot/print");
    $getprofile = $API->comm("/ip/hotspot/user/profile/print");

    $vendo = ($_POST['vendo']);
    $qty = ($_POST['qty']);
    $price = ($_POST['price']);
    $server = ($_POST['server']);
    $TagColor = ($_POST['TagColor']);
    $user = ($_POST['user']);
    $prefix = ($_POST['prefix']);
    $userl = ($_POST['userl']) - strlen($prefix);
    $char = ($_POST['char']);
    $profile = ($_POST['profile']);
    $duration = (((int)$_POST['durM']) * 1 + ((int)$_POST['durH']) * 60 + ((int)$_POST['durD']) * 60 * 24);
    $timelimit = (((int)$_POST['limM']) * 1 + ((int)$_POST['limH']) * 60 + ((int)$_POST['limD']) * 60 * 24) * 60;
    $hsIp = $API->comm("/ip/hotspot/profile/print", array("?name" => ($API->comm("/ip/hotspot/print", array("?name" => ($_POST['server']))))[0]['profile']));
    $datalimit = ($_POST['datalimit']) * ($_POST['mbgb']);
    if ($timelimit == "") {
        $timelimit = $duration * 60;
    } else {
        $timelimit = $timelimit;
    }
    if ($hsIp[0]['hotspot-address'] == "") {
        $ip = '';
    } else {
        $ip = '<td style="font-size:12px" colspan="3">Login: http://<span style="font-weight:700">' . $hsIp[0]['hotspot-address'] . '</span></td>';
    }
    $d = floor($duration / 1440);
    $h = floor(($duration - $d * 1440) / 60);
    $m = $duration - ($d * 1440) - ($h * 60);
}
?>
<div class="card shadow container" style="overflow-x:auto;">
    <div class="my-3" width="100%">
        <script function9></script>
        <form id="rst" method="post" action="index.php">
            <div class="row mb-3">
                <div class="col rst">
                    <label class="form-label fw-bold" for="Vendo">Vendo Name</label>
                    <!--<input class="form-control reset" type="Text" name="vendo" min="1" max="16" value="<?php echo $vendo; ?>" id="Vendo">-->
                    <input class="form-control reset" type="Text" name="vendo" min="1" max="16" value="<?php echo $vendo; ?>" id="Vendo" list="vendolist">
                    <datalist id="vendolist">
                        <?php $Totalsellers = count($sellers);
                        for ($i = 0; $i < $Totalsellers; $i++) {
                            if (isset($sellers[$i]['comment']) && explode(",", ($sellers[$i]['comment']))[0] == 'VendoSales') {
                                echo '<option value="' . $sellers[$i]['name'] . '">';
                            }
                        };
                        ?>
                    </datalist>

                </div>
                <div class="col">
                    <label class="form-label fw-bold" for="Quantity">Quantity</label>
                    <input class="form-control" type="number" name="qty" min="1" max="10000" id="Quantity" value="<?php echo $qty; ?>" required="1">
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label fw-bold" for="Prefix">Prefix
                    </label>
                    <input class="form-control" type="text" size="4" maxlength="3" autocomplete="off" name="prefix" value="<?php echo $prefix; ?>">
                </div>

                <div class="col">
                    <div class="row">
                        <div class="col">
                            <label class="form-label fw-bold" for="char">length
                            </label>
                            <div class="input-group mb-3">
                                <select class="form-select" id="userl" name="userl" required="1">
                                    <option>6</option>
                                    <option>7</option>
                                    <option>8</option>
                                    <option>9</option>
                                    <option>10</option>
                                </select>
                                <select class="form-select " name="char" id="char" required="1">
                                    <option value="1">Random A3B2C5</option>
                                    <option value="2">Random abcdef</option>
                                    <option value="3">Random 123456</option>
                                    <option value="4">Random ABCDEF</option>
                                    <option value="5">Random aBcDef</option>
                                    <option value="6">Random aBc234</option>
                                    <option value="7">Random @B$CDE%</option>
                                    <option value="8">Random @B1C%E3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label fw-bold">Type</label>
                            <div class="col">
                                <select class="form-select" onchange="defUserl();" id="user" name="user" required="1">
                                    <option value="vc">Username = Password</option>
                                    <option value="up">Username Only</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row  mb-3 ">
                <div class="col">
                    <label class="form-label fw-bold" for="Price">Voucher Price</label>
                    <div class="row">
                        <div class="col">
                            <input class="form-control Clear-all " type="number" name="price" value="<?php echo $price; ?>" id="Price" required="1">
                        </div>
                    </div>
                </div>

                <div class="col">
                    <label class="form-label fw-bold" for="Data">Voucher Data
                    </label>
                    <div class="input-group mb-3">
                        <input class="form-control" type="number" min="0" max="9999" name="datalimit" value="<?php echo ((int)$_POST['datalimit']); ?>">
                        <select class="form-select" name="mbgb" id="mbgb" required="1">
                            <option value=1048576>MB</option>
                            <option value=1073741824>GB</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="mb-3 row ">
                <div class="col input-group">
                    <div class="w-100">
                        <label class="form-label fw-bold" for="Duration">Voucher Duration
                            <label>
                    </div>
                    <input class="col form-control" type="number" size="4" placeholder="Days" autocomplete="off" name="durD" value="<?php echo ((int)$_POST['durD']); ?>">
                    <input class="col form-control" type="number" size="4" placeholder="Hours" autocomplete="off" name="durH" value="<?php echo ((int)$_POST['durH']); ?>">
                    <input class="col form-control" type="number" size="4" placeholder="Minutes" autocomplete="off" name="durM" value="<?php echo ((int)$_POST['durM']); ?>">
                </div>
                <div class="col input-group">
                    <div class="w-100">
                        <label class="form-label fw-bold" for="Duration">Voucher Validity
                            <label>
                    </div>
                    <input class="col form-control" type="number" size="4" placeholder="Days" autocomplete="off" name="limD" value="<?php echo ((int)$_POST['limD']); ?>">
                    <input class="col form-control" type="number" size="4" placeholder="Hours" autocomplete="off" name="limH" value="<?php echo ((int)$_POST['limH']); ?>" v>
                    <input class="col form-control" type="number" size="4" placeholder="Minutes" autocomplete="off" name="limM" value="<?php echo ((int)$_POST['limM']); ?>">
                </div>
            </div>


            <div class="row mb-3 ">
                <div class="col ">
                    <label class="form-label fw-bold" for="Profile">Profile
                    </label>
                    <select class="form-select" onchange="GetVP();" id="profile" name="profile" required="1">
                        <?php $TotalReg = count($getprofile);
                        for ($i = 0; $i < $TotalReg; $i++) {
                            echo "<option>" . $getprofile[$i]['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col">
                    <label class="form-label fw-bold" for="Server">Server
                    </label>
                    <select class="form-select" name="server" id="server" required="1" value="C-Hotspot-Vlan-70">
                        <option>all</option>
                        <?php $TotalReg = count($srvlist);
                        for ($i = 0; $i < $TotalReg; $i++) {
                            echo "<option>" . $srvlist[$i]['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col">
                    <label class="form-label fw-bold" for="TagColor">Tag Color</label>
                    <input class="form-control" id="TagColor" type="color" name="TagColor" value="#ffea8f">
                </div>

            </div>


            <div class="gap-2 d-grid mb-3">
                <button type="submit" name="save" class="btn btn-success" title="Generate User"><i class="fa fa-save"></i> Generate</button>
            </div>

        </form>
        <div class="d-flex gap-2">
            <button class="col btn btn-danger" onclick="rst()" value="Reset form "><i class="fas fa-trash"></i> Clear All</button>
            <button class="col btn btn-warning text-white" onclick='printvoucher()'><i class="fas fa-ticket-alt"></i> Print</button>
        </div>
    </div>
</div>
<!--Print Location-->
<div hidden>
    <div id="PrintBody">
        <?php
        if (isset($_POST['qty'])) {
            $API->comm("/system/script/add", array("name" => "$vendo", "source" => "0", "comment" => "VendoSales,0,0"));
            $API->comm("/system/scheduler/add", array("name" => "Reset " . $vendo . " Income", "interval" => "4w2d", "on-event" => ":local getVendorScript [/system script get [find name=" . $vendo . "] comment];:local vendorArray [:toarray [:pick \$getVendorScript ([:find \$getVendoScript \",\"]) [:len \$getVendorScript]]];:local getLastSales [:pick \$vendorArray 1];/system script set [find name=" . $vendo . "] comment=\"VendoSales,0,\$getLastSales\";", "start-date" => "sep/01/2022", "start-time" => "00:00:00"));
            for ($i = 1; $i <= $qty; $i++) {
                if ($char == "1") {
                    $u[$i] = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), -$userl);
                } elseif ($char == "2") {
                    $u[$i] = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), -$userl);
                } elseif ($char == "3") {
                    $u[$i] = substr(str_shuffle("0123456789"), -$userl);
                } elseif ($char == "4") {
                    $u[$i] = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), -$userl);
                } elseif ($char == "5") {
                    $u[$i] = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), -$userl);
                } elseif ($char == "6") {
                    $u[$i] = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), -$userl);
                } elseif ($char == "7") {
                    $u[$i] = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&"), -$userl);
                } elseif ($char == "8") {
                    $u[$i] = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&1234567890"), -$userl);
                }
                $u[$i] = "$prefix$u[$i]";
            }
            for ($i = 1; $i <= $qty; $i++) {
                if ($user == "up") {
                    $vc = '';
                } elseif ($user == "vc") {
                    $vc = $u[$i];
                }
                $API->comm("/ip/hotspot/user/add", array(
                    "server" => "$server",
                    "name" => "$u[$i]",
                    "password" => "$vc",
                    "profile" => "$profile",
                    "limit-uptime" => "$timelimit",
                    "limit-bytes-total" => "$datalimit",
                    "comment" => $duration . "m," . $price . ",0," . $vendo,
                ));
                echo '
        <table class="voucher" style="width:155px">
            <tr>
                <td style="font-weight:700;border-right:1px solid #000;background-color:' . $TagColor . ' " class="rotate" id="voucher1" rowspan="4">
                    <span>' . $CURRENCY . ' ' . $price . '</span></td>
                <td style="font-weight:700" colspan="2">' . $vendo . '<td>
            <tr>
                <td style="width:100%;font-weight:700;font-size:18px;text-align:center">' . $u[$i] . '</td>
            </tr>
            <tr>
                <td style="font-size:11px">Duration: ' . $d . 'D ' . $h . 'H ' . $m . 'M</td>
            </tr>
            <tr>' . $ip . '</tr>
        </table>';
            }
        }
        ?>
    </div>
</div>

<script>
    $("#profile").val("<?php if (isset($_POST['profile'])) {echo($_POST['profile']);} else {echo"default";}?>");
    $("#server").val("<?php if (isset($_POST['server'])) {echo($_POST['server']);} else {echo"all";}?>");
    $("#user").val("<?php if (isset($_POST['user'])) {echo($_POST['user']);} else {echo"vc";}?>");
    $("#userl").val("<?php if (isset($_POST['userl'])) {echo($_POST['userl']);} else {echo"6";}?>");
    $("#char").val("<?php if (isset($_POST['char'])) {echo($_POST['char']);} else {echo"1";}?>");
    $("#mbgb").val("<?php if (isset($_POST['mbgb'])) {echo($_POST['mbgb']);} else {echo"1048576";}?>");
    $("#TagColor").val("<?php if (isset($_POST['TagColor'])) {echo($_POST['TagColor']);} else {echo"#ffea8f";}?>");

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    function rst() {
        document.getElementById("rst").reset()
    }

    function printvoucher() {
        let today = ((new Date).getMonth() + 1 + "").padStart(2, "0") + "_" + ((new Date).getDate() + "").padStart(2, "0") + "_" + (new Date).getFullYear()
        var t = $("#Vendo").val() + "_" + $("#Price").val() + "_" + $("#Quantity").val() + "pcs_" + today,
            e = document.getElementById("PrintBody")
        if ("" === e.textContent.trim());
        else {
            var o = document.getElementById("PrintBody").innerHTML,
                n = window.open("", "", "height=800, width=800")
            n.document.write("<link href=src/voucher.css rel=stylesheet>"), setTimeout(function() {
                n.document.write("<html><body><title>" + t + "</title>" + o + "<p>Generator by github.com/Kintoyyy</p></body></html>"), n.print()
            }, 100)
        }
    }
</script>