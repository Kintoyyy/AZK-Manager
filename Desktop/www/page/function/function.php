<?php
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

// Random Strring generator
function genCharacters($type, $len = 5, $prefix = 0)
{
    switch ($type) {
        case '1':
            $char = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
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
            $char = "ABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&1234567890";
            break;
        case '8':
            $char = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%&";
            break;
        default:
            $char = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            break;
    }
    return $prefix . substr(str_shuffle($char), - ($len - strlen($prefix)));
}
