<?php
session_start();

include_once('../../api/routeros_api.class.php');
include "../../db/_pdo.php";

$API = new RouterosAPI();
$API->debug = false; //debug
PDO_Connect("sqlite:../../db/sqlite-database.sqlite3");
$MikroTik = PDO_FetchRow("SELECT * FROM settings WHERE id = :id", array("id" => $_SESSION['mikrotik']));
$API->connect($MikroTik['ipaddress'], $MikroTik['username'], $MikroTik['password'], $MikroTik['port']);


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

$data = array();

if (isset($_GET['hs-active'])) {
    $data['data'] = $API->comm("/ip/hotspot/active/print");
}

if (isset($_GET['iface'])) {
    $traffic = $API->comm("/interface/monitor-traffic", array("interface" => $_GET['iface'], "once" => "",));
    $a['name'] = 'Upload';
    $a['data'][] = $traffic[0]['rx-bits-per-second'];
    $b['name'] = 'Download';
    $b['data'][] = $traffic[0]['tx-bits-per-second'];
    array_push($data, $a);
    array_push($data, $b);
}

if (isset($_GET['get-stats'])) {
    $stats = $API->comm("/system/resource/print")[0];
    $data = array(
        'downloadquota' => isset($_GET['interface']) ? byteFormat($API->comm("/interface/print", array("?name" =>  $_GET['interface']))[0]["rx-byte"]) : '',
        'uploadquota' => isset($_GET['interface']) ? byteFormat($API->comm("/interface/print", array("?name" => $_GET['interface']))[0]["tx-byte"]) : '',
        'monthlySales' => isset($API->comm("/system/script/print", array("?name" => "monthlyincome"))['0']["source"]) ? $MikroTik['currency'] . number_format($API->comm("/system/script/print", array("?name" => "monthlyincome"))['0']["source"]) : '<a class="text-danger text-decoration-none" href="index.php?page=script#script">Script Error</a>',
        'dailySales' => isset($API->comm("/system/script/print", array("?name" => "todayincome"))['0']["source"]) ? $MikroTik['currency'] . number_format($API->comm("/system/script/print", array("?name" => "todayincome"))['0']["source"]) : '<a class="text-danger text-decoration-none" href="index.php?page=script#script">Script Error</a>',
        'pppoeActive' => $API->comm("/ppp/active/print", array("count-only" => "")),
        'pppoeUsers' => $API->comm("/ppp/secret/print", array("count-only" => "")),
        'hsActive' => number_format(floatval($API->comm("/ip/hotspot/active/print", array("count-only" => "")))),
        'hsAllUsers' => number_format(floatval($API->comm("/ip/hotspot/user/print", array("count-only" => "")))) . "pcs.",
        'memory' => number_format(($stats['free-memory'] / $stats['total-memory']) * 100),
        'stats' => $stats,
        'identity' => $API->comm("/system/identity/print")[0]['name']
    );
}


if (isset($_GET['qrpurchase'])) {
    qrpurchase($_GET['ip'], $_GET['mac'], "1");
    $data = array(
        $_GET['ip'], $_GET['mac'], "1"
    );
};


$API->disconnect();
//header('Content-Type: application/json');
print json_encode($data, JSON_PRETTY_PRINT);
