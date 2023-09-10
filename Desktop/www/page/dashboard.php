<?php
if ( !isset( $_SESSION[ 'loggedin' ] ) || $_SESSION[ 'adminaccess' ] === 'no' ) {
  header( 'location: logout.php' );
  exit;
}

$getinterface = $API->comm( "/interface/print" );

$interface = $_SESSION[ 'interface' ] ?? $getinterface[ 0 ][ 'name' ];

if ( isset( $_POST[ 'selectiface' ] ) ) {
  $interface               = $_POST[ 'interFace' ];
  $_SESSION[ 'interface' ] = $interface;
}
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
          <div id="stats1"
            class="row no-gutters align-items-center">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xl col-md-6 mb-2">
      <div class="card bg-light shadow h-100 ">
        <div class="card-body">
          <div id="pppoe"
            class="row no-gutters align-items-center">
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl col-md-6 mb-2">
      <div class="card bg-light shadow h-100 ">
        <div class="card-body">
          <div id="usage"
            class="row no-gutters align-items-center">
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl col-md-6 mb-2">
      <div class="card bg-light shadow h-100 ">
        <div class="card-body">
          <div id="stats2"
            class="row no-gutters align-items-center">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <div class="card bg-light shadow h-100 ">
        <div class="card-body">
          <form method="post"
            class="p-2">
            <ul class="pagination pagination-sm">
              <li>
                <h3 class="text-center">
                  <?= $interface ?>
                </h3>
              </li>
              <li class="px-3">
                <select name="interFace"
                  class="form-control">
                  <option value="none"
                    selected
                    disabled
                    hidden>Select Interface</option>
                  <?php
                  foreach ( $getinterface as $interfaces ) {
                    echo '<option value="' . $interfaces[ 'name' ] . '">' . $interfaces[ 'name' ] . '</option>';
                  }
                  ?>
                </select>
              </li>
              <li>
                <button class="btn btn-block btn-outline-primary"
                  name="selectiface">Apply</button>
              </li>
            </ul>
          </form>
          <div id="trafficMonitor"></div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<script src="../src/js/jquery-3.6.1.min.js?ver=<?php echo rand(); ?>"></script>
<script src="../src/js/highcharts.js?ver=<?php echo rand(); ?>"></script>

<script type="text/javascript">
  var interface = "<?= $interface ?>";
  var mt = "<?= $_SESSION[ 'mikrotik' ] ?>";
  var chart;


  $(document).ready(function () {
    requestDatta(interface);
    requestData(interface);
    Highcharts.setOptions({
      global: {
        useUTC: false
      },
      chart: {
        height: 300,

      },
    });

    chart = new Highcharts.Chart({
      chart: {
        renderTo: 'trafficMonitor',
        animation: Highcharts.svg,
        type: 'areaspline',
        events: {
          load: function () {
            setInterval(function () {
              requestDatta(interface);
              requestData(interface);
              chart.setTitle({
                text: ''
              });
            }, 2000);
          }
        }
      },
      title: {
        text: ''
      },

      xAxis: {
        type: 'datetime',
        tickPixelInterval: 150,
        maxZoom: 20 * 1000,
      },
      yAxis: {
        minPadding: 0.2,
        maxPadding: 0.2,
        title: {
          text: null
        },
        labels: {
          formatter: function () {
            var bytes = this.value;
            var sizes = ['bps', 'kbps', 'Mbps', 'Gbps', 'Tbps'];
            if (bytes == 0) return '0 bps';
            var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
          },
        },
      },
      series: [{
        name: 'Download',
        data: [],
        marker: {
          symbol: 'diamond'
        }
      }, {
        name: 'Upload',
        data: [],
        marker: {
          symbol: 'diamond'
        }
      }],

      tooltip: {
        formatter: function () {
          var value = [];
          $.each(this.points, function (i, e) {
            var bytes = e.y;
            var sizes = ["bps", "kbps", "Mbps", "Gbps", "Tbps"];
            if (bytes == 0) {
              value.push("<b>" + this.series.name + ":</b> 0 bps")
            };
            var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            value.push("<b>" + this.series.name + ":</b> " + parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i])
          });
          return "<b>JuanFi Agent Manager</b><br><b>" + interface + "</b><br/><b>Time: </b>" + Highcharts.dateFormat("%H:%M:%S", new Date(this.x)) + "<br />" + value.join("<br/> ")
        },
        shared: true
      },
    });
  });

  Highcharts.theme = {
    colors: ["#28a745", "#dc3545"],
    chart: {
      backgroundColor: 'rgba(0, 0, 0, 0)',
      borderColor: 'rgba(0, 0, 0, 0)',
      borderWidth: 1,
      plotBackgroundColor: 'rgba(0, 0, 0, 0)',
      height: '200px'
    },
    title: {
      text: '',
      style: {
        color: '#E9EBEE',
        font: 'bold 14px "Trebuchet MS", Verdana, sans-serif, Roboto,"Seggoe UI"'
      }
    },

    yAxis: {
      gridLineColor: "rgba(0, 0, 0, 0.46)",
      gridLineWidth: 1,
    },
    subtitle: {
      style: {
        color: '#E9EBEE',
        font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
      }
    },
    plotOptions: {
      series: {
        fillOpacity: 0.1
      }
    },
    tooltip: {
      backgroundColor: 'rgba(254, 254, 254, 0.75)',
      borderColor: 'white',
      style: {
        color: '#3E3E3E'
      }
    },
    legend: {
      itemStyle: {
        font: '9pt Trebuchet MS, Verdana, sans-serif',
        color: '#454d55'
      },
      itemHiddenStyle: {
        color: '#E9EBEE'
      }
    },
    credits: {
      enabled: 0,
    }

  };
  var highchartsOptions = Highcharts.setOptions(Highcharts.theme);
</script>
<script src="../src/js/stats.js?ver=<?php echo rand(); ?>"></script>