<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();

include_once('../api/routeros_api.class.php');
include "../db/_pdo.php";

$API = new RouterosAPI();
PDO_Connect("sqlite:../db/sqlite-database.sqlite3");
$MikroTik = PDO_FetchRow("SELECT * FROM settings WHERE id = :id", array("id" => $_SESSION['mikrotik']));
$API->connect($MikroTik['ipaddress'], $MikroTik['username'], $MikroTik['password']);

$data = array();


if (isset($_POST['generateUser'])) {
    $type = $_POST['generateUser'];
    $obj = json_decode($_POST['data'], true);

    if ($type == "qrCode") {
    }
    if ($type == "vcCode") {
        for ($i = 1; $i <= $obj['quantity']; $i++) {
            $code =  genCharacters($obj['char'], $obj['length'], $obj['prefix']);
            $API->comm("/ip/hotspot/user/add", array(
                "server" => $obj['server'],
                "name" =>  $code,
                "password" => ($obj['type'] == 'vc') ? $code : "",
                "profile" => $obj['profile'],
                "limit-uptime" => $obj['duration'],
                "limit-bytes-total" => $obj['data'],
                "disabled" => $obj['pending'],
                "comment" => ($obj['validity'] / 60) . "m," . $obj['price'] . ",0," . $obj['name'],
            ));
            $data[] = array(
                "id" => $i,
                "name" => $obj['name'],
                "code" => $code,
                "price" => $obj['price'],
                "duration" => secondsToWords($obj['duration']),
                "validity" => secondsToWords($obj['validity']),
                "data" => $obj['data'] == "0" ? "0" : byteFormat($obj['data']),
                "color" => $obj['color'],
            );
        }
    } else {
        return "Error!";
    }
}


if (isset($_POST['addTemplate'])) {
    $obj = json_decode($_POST['data'], true);
    PDO_Execute(
        "INSERT INTO hs_Profiles ( name,prefix, length, char, type, price, data, duration, validity, profile, server, color)  
    VALUES ( :name,:prefix, :length, :char, :type, :price, :data, :duration, :validity, :profile, :server, :color)",
        array(
            "name" => "0",
            "prefix" => $obj['prefix'],
            "length" => $obj['length'],
            "char" => $obj['char'],
            "type" => $obj['type'],
            "price" => $obj['price'],
            "data" => $obj['data'],
            "duration" => $obj['duration'],
            "validity" => $obj['validity'],
            "profile" => $obj['profile'],
            "server" => $obj['server'],
            "color" => $obj['color'],
        )
    );
    $data = array("status" => "Template added!");
}

// Random Strring generator
function genCharacters($type, $len = 5, $prefix = 0)
{
    switch ($type) {
        case '1':
            $char = "0123456789abcdefghijklmnopqrstuvwxyz";
            break;
        case '2':
            $char = "abcdefghijklmnopqrstuvwxyz";
            break;
        case '3':
            $char = "0123456789";
            break;
        case '4':
            $char = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
            break;
        case '5':
            $char = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            break;
        case '6':
            $char = "ABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&";
            break;
        case '7':
            $char = "abcdefghijklmnopqrstuvwxyz1234567890";
            break;
        case '8':
            $char = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%&";
            break;
        default:
            $char = "0123456789abcdefghijklmnopqrstuvwxyz";
            break;
    }
    return $prefix . substr(str_shuffle($char), - ($len - strlen($prefix)));
}



$API->disconnect();


// Format and convert Seconds
function secondsToWords($seconds)
{
    $days = (int)($seconds / 86400);
    $hours = (int)(($seconds - ($days * 86400)) / 3600);
    $mins = (int)(($seconds - $days * 86400 - $hours * 3600) / 60);
    $secs = (int)($seconds - ($days * 86400) - ($hours * 3600) - ($mins * 60));
    return ($days ? $days . ($days > 1 ? ' days ' : ' day ') : "") . ($hours ? $hours . " hours " : "") . ($mins ? $mins . " mins " : "") . ($secs ? $secs . " secs" : "");
}
// Format data bytes
function byteFormat($size, $speed = 'd', $precision = 2)
{
    $base = log($size, 1024);
    if ($speed == 'd') {
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
    } elseif ($speed == 's') {
        $suffixes = array('bps', 'kbps', 'Mbps', 'Gbps', 'Tbps', 'Pbps', "Ebps");
    }
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

//Sqlite functions
if (isset($_POST['profile'])) {
    if ($_POST['profile'] == 'remove') {
        PDO_Execute("DELETE FROM hs_profiles WHERE id = :id", array("id" => $_POST['id']));
        $data = array("status" => "Deleted Profile " . $_POST['id']);
    } else {
        $data =  PDO_FetchAll("SELECT * FROM hs_profiles WHERE id = :id", array("id" => $_POST['id']))[0];
    }
}

print json_encode($data, JSON_PRETTY_PRINT);
