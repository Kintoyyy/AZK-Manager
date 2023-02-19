
function requestDatta(iface) {
    $.ajax({
        url: './page/traffic/AZK.php?iface=' + iface + '&MT=' + mt,
        datatype: "json",
        success: function (data) {
            var midata = JSON.parse(data);
            if (midata.length > 0) {
                var TX = parseInt(midata[0].data);
                var RX = parseInt(midata[1].data);
                var x = (new Date()).getTime();
                shift = chart.series[0].data.length > 19;
                chart.series[0].addPoint([x, TX], true, shift);
                chart.series[1].addPoint([x, RX], true, shift);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.error("Status: " + textStatus + " request: " + XMLHttpRequest);
            console.error("Error: " + errorThrown);
        }
    });
}
function requestData(iface) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const obj = JSON.parse(this.responseText);
            $('#hotpsot').html('<div class="col mr-2"><div class="fw-bold text-primary">Hotspot Users</div><div class="mb-0 fw-bold">' + obj.hsActive + '</div></div><div class="col mr-2"><div class="fw-bold text-primary">Vouchers</div><div class="mb-0 fw-bold text-gray-800">' + obj.hsAllUsers + '</div></div>')
            $('#sales1').html('<div class="col mr-2"><div class="fw-bold text-primary">Monthly</div><div class="mb-0 fw-bold text-gray-800">' + obj.monthlySales + '</div></div><div class="col mr-2"><div class="fw-bold text-primary">Today</div><div class="mb-0 fw-bold text-gray-800">' + obj.dailySales + '</div></div>');
            $('#stats1').html('<div class="col mr-2"><div class="fw-bold text-info">CPU</div><div class="row no-gutters align-items-center"> <div class="col-auto"> <div class="mb-0 mr-3 fw-bold text-gray-800">' + obj.stats['cpu-load'] + ' %</div></div><div class="col"> <div class="progress progress-sm mr-2"> <div class="progress-bar bg-info" role="progressbar" style="width: ' + obj.stats['cpu-load'] + '%" aria-valuemin="0" aria-valuemax="100"></div></div></div></div></div><div class="col mr-2"> <div class="fw-bold text-info">RAM </div><div class="row no-gutters align-items-center"> <div class="col-auto"> <div class="mb-0 mr-3 fw-bold text-gray-800"> ' + obj.memory + '%</div></div><div class="col"> <div class="progress progress-sm mr-2"> <div class="progress-bar bg-info" role="progressbar" style="width: ' + obj.memory + '%" aria-valuemin="0" aria-valuemax="100"></div></div></div></div></div>');
            $('#pppoe').html(' <div class="col mr-2"> <div class="fw-bold text-secondary"> PPPoE Users</div><div class="mb-0 fw-bold text-gray-800">' + obj.pppoeActive + '</div></div><div class="col mr-2"> <div class="fw-bold text-secondary"> PPPoE Clients</div><div class="mb-0 fw-bold text-gray-800">' + obj.pppoeUsers + '</div></div>');
            $('#usage').html('<div class="col mr-2"> <div class="fw-bold text-success"> Download Usage</div><div class="mb-0 fw-bold">' + obj.downloadquota + '</div></div><div class="col mr-2"> <div class="fw-bold text-danger"> Upload Usage</div><div class="mb-0 fw-bold text-gray-800">' + obj.uploadquota + '</div></div>');
            $('#stats2').html('<div class="col mr-2"> <div class="fw-bold text-warning">MikroTik</div><div class="mb-0 fw-bold text-gray-800">' + obj.identity + '</div></div><div class="col mr-2"> <div class="fw-bold text-warning"> Router </div><div class="mb-0 fw-bold text-gray-800">' + obj.stats['board-name'] + " " + obj.stats['version'] + '</div></div>');
        }
    };
    xmlhttp.open("GET", '/page/traffic/AZK.php?get-stats&interface=' + iface, true);
    xmlhttp.send();
}

function requestData1() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const obj = JSON.parse(this.responseText);
            $('#hotpsot').html('<div class="col mr-2"><div class="fw-bold text-primary">Hotspot Users</div><div class="mb-0 fw-bold">' + obj.hsActive + '</div></div><div class="col mr-2"><div class="fw-bold text-primary">Vouchers</div><div class="mb-0 fw-bold text-gray-800">' + obj.hsAllUsers + '</div></div>')
            $('#sales1').html('<div class="col mr-2"><div class="fw-bold text-primary">Monthly</div><div class="mb-0 fw-bold text-gray-800">' + obj.monthlySales + '</div></div><div class="col mr-2"><div class="fw-bold text-primary">Today</div><div class="mb-0 fw-bold text-gray-800">' + obj.dailySales + '</div></div>');
            $('#pppoe').html(' <div class="col mr-2"> <div class="fw-bold text-primary"> PPPoE Users</div><div class="mb-0 fw-bold text-gray-800">' + obj.pppoeActive + '</div></div><div class="col mr-2"> <div class="fw-bold text-primary"> PPPoE Clients</div><div class="mb-0 fw-bold text-gray-800">' + obj.pppoeUsers + '</div></div>');
        }
    };
    xmlhttp.open("GET", '/page/traffic/AZK.php?get-stats', true);
    xmlhttp.send();
}