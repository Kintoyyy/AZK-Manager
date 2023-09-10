<?php
if ( !isset( $_SESSION[ 'loggedin' ] )) {
  header( 'location: logout.php' );
  exit;
}


$mikroTik = PDO_FetchAll("SELECT * FROM settings");
if (isset($_POST['set'])) {
    $MT =  $_POST['set'];
    $_SESSION['mikrotik'] = $MT;
    $_SESSION['interface'] = PDO_FetchRow("SELECT * FROM settings WHERE id = :id", array("id" => $_SESSION['mikrotik']))['interface'];
}

if (PDO_FetchAll("SELECT COUNT(*) FROM settings")[0]['COUNT(*)'] != 0) {
    $API = new RouterosAPI();
    $API->debug = false; //debug
    $MikroTik = PDO_FetchRow("SELECT * FROM settings WHERE id = :id", array("id" => $_SESSION['mikrotik']));
    $API->connect($MikroTik['ipaddress'], $MikroTik['username'], $MikroTik['password']);
} else {
    echo "<script>window.location.href = '/page/setup.php';</script>";
}


?>
<script src="src/js/popper.min.js?ver=<?php echo rand(); ?>"></script>
<script src="src/js/bootstrap.min.js?ver=<?php echo rand(); ?>"></script>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
    <div class="container-fluid">
        <a class="navbar-brand h1" href="index.php">
            <img src="src/kint.ico" alt="" width="30" height="24" class="d-inline-block align-text-top">
            AZK Manager
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=qr_scan">Scan</a>
                </li>
                <?php
                if ($_SESSION['adminaccess'] == "yes") {
                ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=sales">Sales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=active_table">Active</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=users">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=generate">Generate</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=subscriber">Subscribers</a>
                    </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://github.com/Kintoyyy/AZK-Manager">Github</a>
                </li>

                <?php
                }
                ?>
            </ul>
            <form class="d-flex position-relative" method="post" action="<?= $_SERVER["REQUEST_URI"]; ?>">
                <div class="form-check form-switch ms-auto mt-2 me-3">
                    <label class="form-check-label ms-3" for="lightSwitch">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-brightness-high" viewBox="0 0 16 16">
                            <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z" />
                        </svg>
                    </label>
                    <input class="form-check-input" type="checkbox" id="lightSwitch" />
                </div>
                <?php
                if ($_SESSION['adminaccess'] == "yes") {
                ?>
                    <div class="dropdown dropstart me-2">
                        <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            MikroTik
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <?php foreach ($mikroTik as $index => $mt) : ?>
                                <li><button class="dropdown-item" name="set" value="<?= $mt['id'] ?>"><?= $mt['name']; ?></button></li>
                            <?php endforeach; ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="index.php?page=settings">Settings</a></li>
                        </ul>
                    </div>
                <?php
                }
                ?>
                <div class="dropdown dropstart">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        User Settings
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="index.php?page=edit_users">Manage Account</a></li>
                        <?php
                        if ($_SESSION['adminaccess'] == "yes") {
                        ?>
                            <li><a class="dropdown-item" href="index.php?page=script">Script</a></li>
                        <?php
                        }
                        ?>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout.php">Sign Out</a></li>
                    </ul>
                </div>
            </form>
        </div>
    </div>
</nav>