<?php
session_start();
if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] !== false) {
    header('location: login.php');
    exit;
}
error_reporting(0);
ini_set('display_errors', 0);

require_once "api/routeros_api.class.php";
require_once "config/config.php";

$API = new RouterosAPI();
$API->debug = false; //debug
$API->connect(MT_SERVER, MT_USERNAME, MT_PASSWORD, MT_PORT);

$sellers = $API->comm("/system/script/print");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="src/kint.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js" integrity="sha256-+8RZJua0aEWg+QVVKg4LEzEEm/8RFez5Tb4JBNiV5xA=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" rel="stylesheet" />
</head>

<body>
    <?php echo '    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
<div class="container-fluid">
    <a class="navbar-brand h1" href="#">
        <img src="src/kint.ico" alt="" width="30" height="24" class="d-inline-block align-text-top">
        Juanfi Agent Manager
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="https://github.com/Kintoyyy">Github</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.facebook.com/kint.oyyy508/">Facebook</a>
            </li>
        </ul>
        <form class="d-flex">
            <a type="button" class="btn btn-block btn-outline-info" data-bs-toggle="modal" data-bs-target="#scriptInfoModal">Scripts</a>
            <a href="password_reset.php" class="btn btn-block btn-outline-warning mx-2">Reset Password</a>
            <a href="logout.php" class="btn btn-block btn-outline-danger">Sign Out</a>
        </form>
    </div>
</div>
</nav>'; ?>
    <div class="container">
        <div id="topstats" class="row mt-3">
        </div>
        <div class="row">
            <div class="col col-xl-4 col-lg-5">
                <div class="card shadow ">
                    <div class="card-body">
                        <canvas id="PieChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col col-yl-4">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <canvas id="BarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <?php include "lib/table.php"; ?>
        <?php include "lib/generate.php";
        echo '<div class="text-center align-self-center my-5 " id=crdts>Made by <a class="text-decoration-none" href=https://kintoyyy.github.io/Me/ id=kintoyyy>kintoyyy ❤</a></div>'; ?>

    </div>
    <div class="modal fade" id="scriptInfoModal" tabindex="-1" aria-labelledby="scriptInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body">
                    <?php include "lib/scripts.php"; ?>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    $(document).ready(function() {
        $('#topstats').load('lib/topstats.php');
        setInterval(
            function() {
                $('#topstats').load('lib/topstats.php');
            }, <?php echo $REFRESH ?>);

    });
    const PieChart = new Chart(document.getElementById('PieChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: [
                <?php echo $Vname; ?>
            ],
            datasets: [{
                label: 'Sales',
                data: [
                    <?php echo $source; ?>
                ],
                backgroundColor: [
                    <?php echo $color; ?>
                ],
                hoverOffset: 20
            }]
        },
        options: {

        }
    });
    //Bar Chart
    const BarChart = new Chart(document.getElementById('BarChart'), {
        type: 'bar',
        data: {
            labels: [
                <?php echo $Vname; ?>
            ],
            datasets: [{
                label: 'Last Sales',
                data: [
                    <?php echo $lastSales; ?>
                ],
                backgroundColor: [
                    '#767676'
                ],
                borderWidth: 1,
                barThickness: 20,
                barPercentage: 0.1,

            }, {
                label: 'Current Sales',
                data: [
                    <?php echo $source; ?>
                ],
                backgroundColor: [
                    <?php echo $color; ?>
                ],
                borderWidth: 1,

            }]
        },
        options: {
            scales: {
                y: {
                    ticks: {
                        callback: function(value, index, ticks) {
                            return '<?php echo $CURRENCY; ?>' + value;
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';

                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('en-US', {
                                    style: 'currency',
                                    currency: 'PHP'
                                }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>

</html>