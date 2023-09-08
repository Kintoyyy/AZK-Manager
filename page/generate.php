<?php
error_reporting(E_ALL ^ E_NOTICE);
if (!isset($_SESSION['loggedin']) && $_SESSION['adminaccess'] == 'no') {
    header('location: login.php');
    exit;
}
if ($_SESSION['adminaccess'] == 'yes') {
$profile = PDO_FetchAll("SELECT * FROM hs_profiles");

include_once "page/function/function.php";

$sellers = $API->comm("/system/script/print");
$srvlist = $API->comm("/ip/hotspot/print");
$getprofile = $API->comm("/ip/hotspot/user/profile/print");



?>
<div class="container  py-4">
    <div class="alert alert-primary h1 text-center" role="alert" id="alert" style="display: none;">
    </div>


    <?php if (count($profile) > 0) { ?>
        <div class="card shadow bg-light mb-3">
            <div class="card-body">
                <h5 class="card-title">Quick select</h5>
                <div style="overflow-x:auto;" class="pb-3">
                    <div class="d-flex flex-row flex-nowrap">
                        <?php foreach ($profile as $index => $P) : ?>
                            <div class="card col card-block mx-2 bg-light shadow" style="min-width: 300px;">
                                <h5 class="card-header text-dark" style="background-color: <?= $P['color']; ?>;"><?= $MikroTik['currency'] . $P['price']; ?></h5>
                                <div class="card-body">
                                    <p class="card-text mb-0">
                                        Duration: <span class="text-primary fw-bold"><?= secondsToWords($P['duration']); ?></span><br>
                                        Validity: <span class="text-primary fw-bold"><?= secondsToWords($P['validity']); ?></span><small class="text-secondary"> ( Pausable )</small><br>
                                        Data: <span class="text-primary fw-bold"><?= $P['data'] == 0 ? 'Unlimited' : byteFormat($P['data'], "d", 0) ?></span><br>
                                        Server: <span class="text-primary fw-bold"><?= $P['server']; ?></span><br>
                                        Profile: <span class="text-primary fw-bold"><?= $P['profile']; ?></span><br>
                                    </p>
                                    <div class="d-flex gap-2">
                                        <button href="#inputForm" class="col btn btn-primary" onclick="profile('<?= $P['id']; ?>','select')">Select <?= $MikroTik['currency'] . $P['price']; ?></button>
                                        <button class="col-2 btn btn-danger" onclick="deleteProfile('<?= $P['id']; ?>','remove')" type="button"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    <?php } ?>
    <div class="card shadow bg-light">
        <div class="card-body">
            <h5 class="card-title">Generate</h5>
            <form id="inputForm" class="needs-validation" action="<?= $_SERVER["REQUEST_URI"]; ?>">
                <div class="row">
                    <div class="col-sm rst">
                        <label class="form-label fw-bold" for="Vendo">Vendo Name</label>
                        <input class="form-control reset" type="Text" name="vendo" min="1" max="16" value="" id="vendo" list="vendolist" required>
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
                    <div class="col-sm">
                        <label class="form-label fw-bold" for="Quantity">Quantity</label>
                        <input class="form-control" type="number" name="qty" min="1" max="10000" id="quantity" value="" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-md-6 col-lg-3">
                        <label class="form-label fw-bold">Pending Print</label>
                        <div class="col  position-relative">
                            <select class="form-select" id="pending" name="pending" required="1">
                                <option disabled selected value> -- select an option -- </option>
                                <option value="true">Yes (Disabled)</option>
                                <option value="false">No (Enabled)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6  col-md-6  col-lg-3">
                        <label class="form-label fw-bold" for="prefix">Prefix
                        </label>
                        <input class="form-control" type="text" size="4" maxlength="4" autocomplete="off" name="prefix" id="prefix" value="">
                    </div>
                    <div class="col-sm">
                        <label class="form-label fw-bold" for="char">length
                        </label>
                        <div class="input-group  has-validation">
                            <select class="form-select" id="length" name="length" required="1">
                                <option>6</option>
                                <option>7</option>
                                <option>8</option>
                                <option>9</option>
                                <option>10</option>
                            </select>
                            <select class="form-select" name="char" id="char" required="1">
                                <option value="1">Random a3b2c5</option>
                                <option value="2">Random abcdef</option>
                                <option value="3">Random 123456</option>
                                <option value="4">Random ABCDEF</option>
                                <option value="5">Random aBcDef</option>
                                <option value="6">Random aBc234</option>
                                <option value="7">Random abc234</option>
                                <option value="8">Random @B1C%E3</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm">
                        <label class="form-label fw-bold">Type</label>
                        <div class="col">
                            <select class="form-select" id="type" name="type" required>
                                <option value="vc">Username = Password</option>
                                <option value="up">Username Only</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label class="form-label fw-bold" for="Price">Voucher Price</label>
                        <div class="row">
                            <div class="col">
                                <input class="form-control Clear-all " type="number" name="price" value="" id="price" required="1">
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label fw-bold" for="Data">Voucher Data
                        </label>
                        <div class="input-group ">
                            <input class="form-control" type="number" min="0" max="9999" name="datalimit" id="data" value="">
                            <select class="form-select" name="mbgb" id="mbgb" required="1">
                                <option value=1048576>MB</option>
                                <option value=1073741824>GB</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm input-group">
                        <div class="w-100">
                            <label class="form-label fw-bold" for="Duration">Voucher Duration
                                <label>
                        </div>
                        <input class="col form-control" type="number" size="4" placeholder="Days" autocomplete="off" name="durD" id="durationdays" value="">
                        <input class="col form-control" type="number" size="4" placeholder="Hours" autocomplete="off" name="durH" id="durationhrs" value="">
                        <input class="col form-control" type="number" size="4" placeholder="Minutes" autocomplete="off" name="durM" id="durationmnts" value="">
                    </div>
                    <div class="col-sm input-group">
                        <div class="w-100">
                            <label class="form-label fw-bold" for="Duration">Voucher Validity
                                <label>
                        </div>
                        <input class="col form-control" type="number" size="4" placeholder="Days" autocomplete="off" name="limD" id="validitydays" value="">
                        <input class="col form-control" type="number" size="4" placeholder="Hours" autocomplete="off" name="limH" id="validityhrs" value="">
                        <input class="col form-control" type="number" size="4" placeholder="Minutes" autocomplete="off" name="limM" id="validitymnts" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 col-sm ">
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
                    <div class="col-6 col-sm">
                        <label class="form-label fw-bold" for="Server">Server
                        </label>
                        <select class="form-select" name="server" id="server" required="1" value="C-Hotspot-Vlan-70">
                            <option value="all">All</option>
                            <?php $TotalReg = count($srvlist);
                            for ($i = 0; $i < $TotalReg; $i++) {
                                echo "<option>" . $srvlist[$i]['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm col-sm mb-2">
                        <label class="form-label fw-bold" for="TagColor">Tag Color</label>
                        <input class="form-control" id="color" type="color" name="color" value="#ffea8f">
                    </div>
                </div>
                <div class="row gap-2 m-2 ">
                    <button class="col-12 btn btn-success" type="submit" id="generate"><i class="fa fa-save"></i> Generate</button>
                </div>
            </form>
            <div class="row gap-2 m-1">
                <button class="col-12 col-sm col-lg btn btn-danger" id="reset"><i class="fas fa-trash"></i> Clear values</button>
                <button class="col-12 col-sm col-lg btn btn-primary" id="addTemp"><i class="fas fa-plus"></i> Add to template</button>
                <button class="col-12 col-sm col-lg  btn btn-warning  text-white text-white" style="display: none;" id="printbtn" type="button" data-bs-toggle="modal" data-bs-target="#print-modal"><i class="fas fa-print"></i> View Print Page</button>
            </div>
        </div>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="print-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="print-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="print-modalLabel">Print</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="color: #000;">
                <link rel="stylesheet" href="../src/voucher.css?v=<?php echo rand(); ?>">
                <div id="printPage">
                </div>
            </div>
            <div class="modal-footer">
                <div class=" row gap-2">
                    <button class="col col-sm col-lg btn btn-danger" type="button" id="clearpage"><i class="fas fa-trash"></i> Clear Print Page</button>
                    <button class="col col-sm col-lg btn btn-success" type="button" data-bs-dismiss="modal"><i class="fa fa-save"></i> Generate More</button>
                    <button class="col col-sm col-lg btn btn-warning text-white" type="button" id="printvoucher"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function deleteProfile(Id) {
        if (confirm("Are you sure you want to delete?")) {
            profile(Id, "remove");
        }
    }
    $('#clearpage').click(function() {
        $("#printPage").empty();
        $("#printbtn").hide();
        setTimeout(function() {
            $('#print-modal').modal('hide');
        }, 5000)
    })

    $('#reset').click(function() {
        $("#inputForm")[0].reset();
    })

    function errorAlert(text, alert = "alert-primary") {
        $("#alert").show();
        $("#alert").addClass(alert);
        $("#alert").text(text);
        setTimeout(function() {
            $("#alert").hide()
            $("#alert").addClass("alert-primary")
        }, 5000)
    }

    function profile(id, func) {
        $.ajax({
            url: "./page/api.php",
            type: "POST",
            async: false,
            data: {
                profile: func,
                id: id
            },
            success: function(result, textStatus) {
                var data = JSON.parse(result);
                $.each(data, function(key, value) {
                    if (key == "duration" || key == "validity") {
                        var days = Math.floor(value / (3600 * 24));
                        value -= days * 3600 * 24;
                        var hrs = Math.floor(value / 3600);
                        value -= hrs * 3600;
                        var mnts = Math.floor(value / 60);
                        value -= mnts * 60;
                        $('#' + key + "days").val(days ? days : "");
                        $('#' + key + "hrs").val(hrs ? hrs : "");
                        $('#' + key + "mnts").val(mnts ? mnts : "");
                    } else if (key == "data") {
                        if (value >= 1073741824) {
                            $('#' + key).val(Math.round(value / 1073741824));
                            $('#mbgb').val("1073741824");
                        } else {
                            $('#' + key).val(Math.round(value / 1048576));
                            $('#mbgb').val("1048576");
                        }
                    } else if (key === "status") {
                        errorAlert(value, "alert-warning");
                        location.reload();
                    } else {
                        $('#' + key).val(value);
                    }

                });
            },
            error: function(err) {
                errorAlert('error :( ' + err);
            }
        });
    }

    $('#addTemp').click(function() {
        if (confirm("Add this to template?")) {
        $.ajax({
            url: "./page/api.php",
            type: "POST",
            async: false,
            data: {
                addTemplate: '',
                data: JSON.stringify({
                    prefix: $('#prefix').val(),
                    length: $('#length').val(),
                    char: $('#char').val(),
                    type: $('#type').val(),
                    price: $('#price').val(),
                    data: ($('#data').val() * $('#mbgb').val()),
                    duration: (($('#durationdays').val() * 86400) + ($('#durationhrs').val() * 3600) + ($('#durationmnts').val() * 60)),
                    validity: (($('#validitydays').val() * 86400) + ($('#validityhrs').val() * 3600) + ($('#validitymnts').val() * 60)),
                    profile: $('#profile').val(),
                    server: $('#server').val(),
                    color: $('#color').val()
                })
            },
            success: function(result) {
                var data = JSON.parse(result);
                errorAlert(data.status, "alert-primary");
                location.reload();
            },
            error: function(err) {
                errorAlert('error :( ' + err);
            }
        });
    }
    })

    $('#inputForm').submit(function() {
        $("#alert").show().addClass("alert-primary").text('Generating ' + $('#quantity').val() + ' Vouchers please wait...');
        $.ajax({
            url: "./page/api.php",
            type: "POST",
            async: true,
            data: {
                generateUser: 'vcCode',
                data: JSON.stringify({
                    name: $('#vendo').val(),
                    quantity: $('#quantity').val(),
                    pending: $('#pending').val(),
                    prefix: $('#prefix').val(),
                    length: $('#length').val(),
                    char: $('#char').val(),
                    type: $('#type').val(),
                    price: $('#price').val(),
                    data: ($('#data').val() * $('#mbgb').val()),
                    duration: (($('#durationdays').val() * 86400) + ($('#durationhrs').val() * 3600) + ($('#durationmnts').val() * 60)),
                    validity: (($('#validitydays').val() * 86400) + ($('#validityhrs').val() * 3600) + ($('#validitymnts').val() * 60)),
                    profile: $('#profile').val(),
                    server: $('#server').val(),
                    color: $('#color').val()
                })
            },
            success: function(result) {
                const arr = JSON.parse(result);
                var divsToAppend = "";
                for (var i = 0; i < arr.length; i++) {
                    var obj = arr[i];
                    // console.log(obj)
                    errorAlert('Done generating ' + obj.id + ' codes!', "alert-success");
                    setTimeout(function() {
                        $('#print-modal').modal('show');
                    }, 1000)
 
                    divsToAppend += `
                    <table class="voucher" style="width: 120px;">
                        <tbody>
                            <tr>
                                <td class="rotate" style="font-weight: bold; border-right: 1px solid black; background-color:` + obj.color + `; -webkit-print-color-adjust: exact;" rowspan="6"><span><?= $MikroTik['currency'] ?> ` + obj.price + `</span></td>
                                <td style="font-weight: bold ;font-size: 10px;" colspan="2">` + obj.name + `</td>
                            </tr>
                            <tr>
                                <td style="width: 100%; font-weight: bold; font-size: 15px; text-align: center;">` + obj.code + `</td>
                            </tr>
                            ` + ((obj.duration >= "0") ? `<tr><td style="font-size: 8px;font-weight: bold;"> Duration:` + obj.duration + ` </td></tr>` : '') + `
                            ` + ((obj.data != "0") ? `<tr><td style="font-size: 8px;font-weight: bold;"> Data:` + obj.data + ` </td></tr>` : '') + `
                            <tr>
                                <td style="font-size: 8px;font-weight: bold;"> Validity:` + obj.validity + ` </td>
                            </tr>
                        </tbody>
                    </table>
                    `;
                }
                $('#printPage').append(divsToAppend);
                if ($('#printPage').children().length > 0) {
                    $("#printbtn").show();
                }
            },
            error: function(err) {
                errorAlert('error :( ' + err);
            }
        });
        return false;
    });

    $('#printvoucher').click(function() {
        let today = ((new Date).getMonth() + 1 + "").padStart(2, "0") + "_" + ((new Date).getDate() + "").padStart(2, "0") + "_" + (new Date).getFullYear()
        var t = $("#vendo").val() + "_" + $("#price").val() + "_" + $("#quantity").val() + "pcs_" + today,
            e = document.getElementById("printPage")
        if ("" === e.textContent.trim());
        else {
            var o = document.getElementById("printPage").innerHTML,
                n = window.open("", "", "height=800, width=800")
            n.document.write("<link href=src/voucher.css?v=<?php echo rand(); ?> rel=stylesheet>"), setTimeout(function() {
                
                    n.document.write("<html><body><title>" + t + "</title>" + o + "</body></html>"), n.print()
                }, 100)
        }
    })
</script>
<?php } ?>