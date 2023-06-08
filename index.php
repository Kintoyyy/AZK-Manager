<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('location: login.php');
    exit;
}

include "db/_pdo.php";
include "api/routeros_api.class.php";

PDO_Connect("sqlite:./db/sqlite-database.sqlite3");

$_SESSION['mikrotik'] = isset($_SESSION['mikrotik']) ? $_SESSION['mikrotik'] : "1";

include "page/header.php";

function get_that_filetime($file_url = false) {
    if (!file_exists($file_url)) {
        return '';
    }
    return filemtime($file_url);
}
?>

<script src="src/js/jquery-3.6.1.min.js?ver=<?php echo rand(); ?>"></script>
<script src="src/js/bootstrap.bundle.min.js?ver=<?php echo rand(); ?>"></script>
<script src="src/js/bootstrap.min.js?ver=<?php echo rand(); ?>"></script>
<script src="src/js/chart.min.js?ver=<?php echo rand(); ?>"></script>
<style>
        ::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0%);
        }
        ::-webkit-scrollbar-thumb {
            background: #555;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #888;
        }
    </style>
<body>
    <?php
    include "page/nav.php";
    if ($_SESSION['adminaccess'] == "yes") {
        $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
    } else {
        $page = isset($_GET['page']) ? $_GET['page'] : 'user';
    }
    include "page/" . $page . ".php";

    ?>
  <div class="text-center text-muted align-self-center mt-3" style="font-size: 15px;" id=crdts>Powered by AZK Manager v3.0.5</div>
    <div class="text-center text-muted align-self-center" style="font-size: 10px;" id=crdts>Made by <a class="text-decoration-none" href=https://github.com/Kintoyyy id=kintoyyy>kintoyyy &#128420;</a></div>
    <div class="text-center text-muted align-self-center mb-5" style="font-size: 10px;" id=crdts>Gcash: <a class="text-decoration-none" href=https://www.facebook.com/kint.oyyy508>09760009422</a></div>
</body>
<script src="src/js/switch.js?ver=<?php echo rand(); ?>"></script>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-LPJDPP6XP6"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-LPJDPP6XP6');
</script>
</html>
