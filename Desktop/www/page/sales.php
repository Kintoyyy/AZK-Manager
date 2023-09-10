<?php
if ( !isset( $_SESSION[ 'loggedin' ] ) || $_SESSION[ 'adminaccess' ] === 'no' ) {
  header( 'location: logout.php' );
  exit;
}

if ( isset( $_POST[ 'selectiface' ] ) ) {
    $interface = $_POST[ 'interFace' ];
}

$data     = $API->comm( "/system/script/print", array( "?name" => "todayincome" ) )[ 0 ];
$exploded = array_reverse( explode( ",", str_replace( "Chart", "", $data[ "comment" ] ) ) );
array_pop( $exploded );
$exploded[] = $data[ "source" ];
$exploded   = implode( ",", $exploded );

$date = [];
for ( $i = 8; $i > 1; $i-- ) {
    $date[] = "'" . date( 'D', strtotime( $i . " days" ) ) . "'";
}
$date = implode( ",", array_reverse( $date ) );
?>

<div class="container py-2">

    <div class="row mt-2">
        <div class="col-xl col-md-6 mb-2">
            <div class="card bg-light shadow h-100 ">
                <div class="card-body">
                    <div id="hotpsot"
                        class="row no-gutters align-items-center">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-6 mb-2">
            <div class="card bg-light shadow h-100 ">
                <div class="card-body">
                    <div id="sales1"
                        class="row no-gutters align-items-center">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-6 mb-2">
            <div class="card bg-light shadow h-100 ">
                <div class="card-body">
                    <div id="pppoe"
                        class="row no-gutters align-items-center">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col col-xl-4 col-lg-5 mb-2">
            <div class="card bg-light shadow">
                <div class="card-body"
                    style="height: 300px">
                    <canvas id="PieChart"></canvas>
                </div>
            </div>
        </div>


        <div class="col col-yl-4">
            <div class="card bg-light shadow mb-2">

                <nav>
                    <ul class="nav nav-tabs"
                        id="myTab"
                        role="tablist">
                        <div class="nav nav-tabs"
                            id="nav-tab"
                            role="tablist">
                            <li class="nav-item"
                                role="presentation">
                                <button class="nav-link bg-light active"
                                    id="daily-sales"
                                    data-bs-toggle="tab"
                                    data-bs-target="#daily-sales-chart"
                                    type="button"
                                    role="tab"
                                    aria-controls="daily-sales-chart"
                                    aria-selected="true">Daily Sales</button>
                            </li>
                            <li class="nav-item"
                                role="presentation">
                                <button class="nav-link bg-light"
                                    id="seller-sales"
                                    data-bs-toggle="tab"
                                    data-bs-target="#seller-sales-chart"
                                    type="button"
                                    role="tab"
                                    aria-controls="seller-sales-chart"
                                    aria-selected="false">Seller Sales</button>
                            </li>
                        </div>
                    </ul>
                </nav>
                <div class="tab-content "
                    id="myTabContent">
                    <div class="tab-pane fade show active p-4"
                        id="daily-sales-chart"
                        role="tabpanel"
                        style="height: 260px"
                        aria-labelledby="home-tab"
                        tabindex="0">
                        <canvas id="LineChart"></canvas>
                    </div>
                    <div class="tab-pane fade p-4"
                        id="seller-sales-chart"
                        role="tabpanel"
                        style="height: 260px"
                        aria-labelledby="profile-tab"
                        tabindex="0">
                        <canvas id="BarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl col-md-6 mb-2">
            <div class="card bg-light shadow h-100 ">
                <div class="card-body">
                    <?php include "page/table.php"; ?>
                </div>
            </div>
        </div>
    </div>


</div>
<script type="text/javascript">
    $(document).ready(function () {
        requestData1("<?= $_SESSION[ 'mikrotik' ] ?>")
    });
    setInterval(function () {
        requestData1("<?= $_SESSION[ 'mikrotik' ] ?>");
    }, 10000);
</script>

<script>
    const ctx = document.getElementById("LineChart").getContext('2d');

    const LineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [<?= $date ?>],
            datasets: [{
                label: 'Daily Sales',
                data: [<?= $exploded ?>],
                fill: false,
                borderColor: '#dc3545',
                backgroundColor: '#dc3545',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    ticks: {
                        beginAtZero: true
                    }
                }
            }
        }
    });


    const pie = document.getElementById('PieChart').getContext('2d');

    const PieChart = new Chart(pie, {
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
                borderWidth: 0,
                hoverOffset: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });


    const bar = document.getElementById('BarChart');
    //Bar Chart
    const BarChart = new Chart(bar, {
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
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Total Sales',
                        fontStyle: 'bold',
                        fontSize: 20
                    },
                    ticks: {
                        beginAtZero: true,
                        callback: function (value, index, values) {
                            if (parseInt(value) >= 1000) {
                                return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            } else {
                                return '$' + value;
                            }
                        }
                    }
                }],
            },
            plugins: {
                legend: {
                    display: true,
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let label = context.dataset.label || '';

                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += "<?= $MikroTik[ 'currency' ] ?>" + context.parsed.y;
                            }
                            return label;
                        }
                    }
                }
            }
        }


    });
</script>
<script src="../src/js/stats.js?ver=<?php echo rand(); ?>"></script>
<script src="../src/js/chart.min.js?ver=<?php echo rand(); ?>"></script>