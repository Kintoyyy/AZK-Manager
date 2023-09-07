<?php
if (!isset($_SESSION['loggedin']) && $_SESSION['adminaccess'] == 'no') {
    header('location: login.php');
    exit;
}

$profile = PDO_FetchAll("SELECT * FROM hs_profiles");

include_once "page/function/function.php";

$err = "";
if (isset($_POST['purchase'])) {
    if (array_search($_POST['ip'], array_column($API->comm("/ip/hotspot/host/print"), 'address')) !== FALSE) {
        $id = PDO_FetchAll("SELECT * FROM hs_profiles WHERE id = :id", array("id" => $_POST['purchase']))[0];
        $code = genCharacters($id['char'], $id['length'], $id['prefix']);
        $API->comm("/ip/hotspot/user/add", array(
            "server" => $id['server'],
            "name" => isset($_POST['useMac'])  ? $_POST['mac'] : $code,
            "password" => isset($_POST['useMac'])  ? $_POST['mac'] : (($id['type'] == 'vc') ? $code : ""),
            "profile" => $id['profile'],
            "limit-uptime" => $id['duration'],
            "limit-bytes-total" => $id['data'],
            "disabled" => "false",
            "comment" => ($id['validity'] / 60) . "m," . $id['price'] . ",0," . $_SESSION['username'],
        ));
        $API->comm("/ip/hotspot/active/login", array(
            "user" => isset($_POST['useMac']) ? $_POST['mac'] : $code,
            "password" => isset($_POST['useMac'])  ? $_POST['mac'] : (($id['type'] == 'vc') ? $code : ""),
            "ip" => $_POST['ip']
        ));
        echo "<script>window.location.href = 'index.php?page=qr_scan';</script>";
    } else {
        $err = "errorAlert('Ip not found! Please check if user is connected to the wifi');";
    }
}


?>
<div class="container">
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a class="text-decoration-none" href="/index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Qr Scanner</li>
        </ol>
    </nav>
    <div>
        <div id="video-container" class="example-style-1 mt-2 " style="overflow: hidden;">
            <video id="qr-video" width="100%"></video>
        </div>
    </div>
    <div class="alert alert-danger text-center mt-2" role="alert" id="alert" style="display: none;">
    </div>
    <div class="alert alert-danger text-center" role="alert" id="cam-has-camera">
    </div>
    <code id="instrctions">
        The browser is blocking the camera access<br>
        1.) goto: chrome://flags/#unsafely-treat-insecure-origin-as-secure<br>
        2.) enable the #unsafely-treat-insecure-origin-as-secure<br>
        3.) add the host ip address: http://<span class="fw-bold" id="host"></span><br>
        4.) click relaunch to restart the browser
    </code>
    <div class="row mt-4">
        <div class="col">
            <label for="cam-list" class="form-label a">Choose camera: <span hidden id="cam-qr-result">None</span></label>
            <select id="cam-list" class="form-select">
                <option value="environment" selected>Primary</option>
                <option value="user">Secondary</option>
            </select>
        </div>
        <div class="col">
            <label for="file-selector" class="form-label a">Scan from File: <span hidden id="file-qr-result">None</span></label>
            <input class="form-control" type="file" id="file-selector">
        </div>
    </div>
    <div class="d-flex gap-2 mt-2">
        <button class="col btn btn-secondary" id="flash-toggle">ðŸ“¸ Flash: <span id="flash-state">off</span></button>
    </div>
    <div class="d-flex gap-2 mt-2">
        <button class="col btn btn-secondary" style="display: none;" data-bs-toggle="modal" data-bs-target="#approveModal" id="approveModal-toggle">Scan</button>
    </div>
</div>

<div class="modal fade" id="approveModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content  bg-dark">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="userIp"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeApprove"></button>
            </div>
            <div class="modal-body">
                <form method="POST" class="row">
                    <input hidden id="ip" name="ip">
                    <input hidden id="mac" name="mac">
                    <?php foreach ($profile as $index => $P) : ?>
                        <div class="row m-auto mb-3">
                            <div class="card bg-dark">
                                <h5 class="card-header"><?= $MikroTik['currency'] . $P['price']; ?> - <?= secondsToWords($P['duration']); ?><?=  $P['data'] == 0 ? '' : byteFormat($P['data'])?></h5>
                                <div class="card-body d-grid gap-2 text-sm">
                                    <p class="card-title">
                                        Duration: <span class="text-primary fw-bold"><?= secondsToWords($P['duration']); ?></span><br>
                                        Validity: <span class="text-primary fw-bold"><?= secondsToWords($P['validity']); ?></span> <small class="text-secondary"> ( Pausable )</small><br>
                                        Data: <span class="text-primary fw-bold"><?= $P['data'] == "0" ? 'Unlimited' : byteFormat($P['data']) ?></span><br>
                                        <?php if ($_SESSION['adminaccess'] == "yes") { ?>
                                            <!--Server: <span class="text-primary fw-bold"><?= $P['server']; ?></span><br>
                                            hs profile: <span class="text-primary fw-bold"><?= $P['profile']; ?></span>-->
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" name="useMac">
                                        <label class="form-check-label">
                                            Use Mac address
                                        </label>
                                    </div>
                                <?php } ?>
                                </p>
                                <button type="submit" onClick="javascript: return confirm('Confirm this Purchase?');" name="purchase" value="<?= $P['id']; ?>" class="btn btn-primary">Purchase <?= $MikroTik['currency'] . $P['price']; ?> - <?= secondsToWords($P['duration']); ?></button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="module">
    import QrScanner from "../src/js/qr-scanner.min.js?ver=<?php echo rand(); ?>";

    const video = document.getElementById('qr-video');
    const videoContainer = document.getElementById('video-container');
    const camQrResult = document.getElementById('cam-qr-result');
    const fileSelector = document.getElementById('file-selector');
    const fileQrResult = document.getElementById('file-qr-result');
    var lastResult = 0;

    function errorAlert(text,alert = "alert-primary") {
        $("#alert").show();
        $("#alert").addClass(alert);
        $("#alert").text(text);
        setTimeout(function() {
            $("#alert").hide()
        }, 10000)
    }

    function setResult(label, result) {
        if (result.data !== lastResult) {
            lastResult = result.data;
            const data = Object.fromEntries(new URLSearchParams(result.data.replace('"juanfi://purchasevoucher?', '')));
            if (data.hasOwnProperty('ip')) {
                $('#approveModal').modal('show');
                <?= $err ?>
                $('#userIp,#approveModal-toggle').text("Approve user: " + data.ip.replace('\"', ''));
                $('#ip').val(data.ip.replace('\"', ''));
                $('#mac').val(data.mac.replaceAll(":", ""));
            } else {
                $("#approveModal-toggle").hide()
                $("#approveModal").modal("hide");
                errorAlert('Invalid QR Code!');
            }
        }
    }

    $('#host').text(window.location.hostname);

    document.getElementById('closeApprove').addEventListener('click', event => {
        $("#approveModal-toggle").show()
    });

    // ####### Web Cam Scanning #######

    const scanner = new QrScanner(video, result => setResult(camQrResult, result), {
        onDecodeError: error => {
            camQrResult.textContent = error;
            camQrResult.style.color = 'inherit';
        },
        highlightScanRegion: true,
        highlightCodeOutline: true,
    });

    const updateFlashAvailability = () => {
        scanner.hasFlash().then(hasFlash => {
            flashToggle.style.display = hasFlash ? 'inline-block' : 'none';
        });
    };

    scanner.start().then(() => {
        updateFlashAvailability();
        QrScanner.listCameras(true).then(cameras => cameras.forEach(camera => {
            const option = document.createElement('option');
            option.value = camera.id;
            option.text = camera.label;
            camList.add(option);
        }));
    });

    QrScanner.hasCamera().then(hasCamera => {
        document.getElementById('cam-has-camera').textContent = hasCamera ? '' : 'No Camera Detected!';
        document.getElementById('cam-has-camera').style.display = hasCamera ? 'none' : '';
        document.getElementById('instrctions').style.display = hasCamera ? 'none' : '';
        document.getElementById('video-container').style.display = hasCamera ? '' : 'none';

    });

    scanner.setInversionMode("both");

    document.getElementById('cam-list').addEventListener('change', event => {
        scanner.setCamera(event.target.value).then(updateFlashAvailability);
    });

    document.getElementById('flash-toggle').addEventListener('click', () => {
        scanner.toggleFlash().then(() => document.getElementById('flash-state').textContent = scanner.isFlashOn() ? 'on' : 'off');
    });


    // ####### File Scanning #######



    fileSelector.addEventListener('change', event => {
        const file = fileSelector.files[0];
        if (!file) {
            return;
        }
        QrScanner.scanImage(file, {
                returnDetailedScanResult: true
            })
            .then(result => {
                setResult(fileQrResult, result)
            })
            .catch(e => setResult(fileQrResult, {
                data: e || 'No QR code found.'
            }));
    });
</script>

<style>
    #video-container.example-style-1 .scan-region-highlight-svg,
    #video-container.example-style-1 .code-outline-highlight {
        stroke: #64a2f3 !important;
    }

    #flash-toggle {
        display: none;
    }
</style>