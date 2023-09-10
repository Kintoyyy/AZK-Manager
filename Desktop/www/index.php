<?php
if ( !isset( $_ENV[ 'HTTP_SEC_CH_UA' ] ) ) { ?>
    <!DOCTYPE html>
    <html>

    <head>
        <style>
            body {
                background-color: #3498db;
                color: #fff;
                font-family: Arial, sans-serif;
                text-align: center;
                padding: 20px;
            }

            a {
                color: #fff;
                text-decoration: underline;
            }

            .badge {
                background-color: #27ae60;
                color: #fff;
                font-size: 16px;
                padding: 5px 10px;
                border-radius: 5px;
                position: absolute;
                top: 10px;
                right: 10px;
            }
        </style>
    </head>

    <body>
        <div class="badge">Online</div>
        <p>Server is running! <br>Open
            <?= "<a href='http://" . $_ENV[ 'HTTP_HOST' ] . "'>" . $_ENV[ 'HTTP_HOST' ] . "</a>" ?>
        </p>
    </body>

    </html>
    <?php
    $output = shell_exec( "start http://" . $_ENV[ 'HTTP_HOST' ] );
    exit;
}

session_start();
if ( !isset( $_SESSION[ 'loggedin' ] ) ) {
    header( 'location: login.php' );
    exit;
}

include "db/_pdo.php";
include "api/routeros_api.class.php";

PDO_Connect( "sqlite:./db/sqlite-database.sqlite3" );

$_SESSION[ 'mikrotik' ] = $_SESSION[ 'mikrotik' ] ?? "1";

include "page/header.php";
?>

<body>
    <?php
    include "page/nav.php";

    $page = $_GET[ 'page' ] ?? ( $_SESSION[ 'adminaccess' ] === "yes" ? 'dashboard' : 'user' );

    include "page/" . $page . ".php";

    ?>
    <div class="text-center text-muted align-self-center mt-3"
        style="font-size: 15px;"
        id=crdts>Powered by AZK Manager v3.0.6</div>
    <div class="text-center text-muted align-self-center"
        style="font-size: 10px;"
        id=crdts>Made by <a class="text-decoration-none"
            href=https://github.com/Kintoyyy
            id=kintoyyy>kintoyyy &#128420;</a></div>
    <div class="text-center text-muted align-self-center mb-5"
        style="font-size: 10px;"
        id=crdts>Gcash: <a class="text-decoration-none"
            href=https://www.facebook.com/kint.oyyy>09760009422</a></div>
</body>

<script src="src/js/switch.js?ver=<?= $random; ?>"></script>

<script async
    src="https://www.googletagmanager.com/gtag/js?id=G-LPJDPP6XP6"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'G-LPJDPP6XP6');
</script>

</html>