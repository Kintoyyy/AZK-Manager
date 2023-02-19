<link rel="stylesheet" type="text/css" href="../src/css/datatables.min.css?v=<?php echo rand(); ?>" />
<script type="text/javascript" src="../src/js/datatables.min.js?ver=<?php echo rand(); ?>"></script>


<div class="container py-5"style="overflow-x:auto;">
    <table id="active" class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <th>User</th>
                <th>Ip</th>
                <th>Mac address</th>
                <th>Uptime</th>
                <th>Time</th>
                <th>Comment</th>
            </tr>        </thead>
    </table>


    <script>
        var table = $('#active').DataTable({
            ajax: './page/traffic/AZK.php?hs-active',
            // responsive: true,
            columns: [{
                    data: 'user'
                },
                {
                    data: 'address'
                },
                {
                    data: 'mac-address'
                },
                {
                    data: 'uptime'
                },
                {
                    data: 'session-time-left'
                },
                {
                    data: 'comment'
                },
            ],
            "columnDefs": [{
                "targets": "_all",
                "defaultContent": ""
            }],
        }).responsive.recalc();;
    </script>
</div>

<script>
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 1000);
</script>