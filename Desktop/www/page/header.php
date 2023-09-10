<?php

$random = rand();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible"
        content="IE=edge">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>AZK Manager V2</title>
    <link rel="icon"
        type="image/x-icon"
        href="src/kint.ico">
    <link rel="stylesheet"
        href="../src/css/bootstrap.min.css?ver=<?= $random ?>"
        type="text/css">
    <link rel="stylesheet"
        href="../src/css/all.min.css?ver=<?=$random; ?>"
        type="text/css">
    <link rel="stylesheet"
        href="../src/css/custom.css?ver=<?=$random; ?>"
        type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css?v=<?= $random; ?>"
        integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
        rel="stylesheet" />
</head>

<script src="src/js/jquery-3.6.1.min.js?ver=<?= $random; ?>"></script>
<script src="src/js/bootstrap.bundle.min.js?ver=<?= $random; ?>"></script>
<script src="src/js/bootstrap.min.js?ver=<?= $random; ?>"></script>
<script src="src/js/chart.min.js?ver=<?= $random; ?>"></script>


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