<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Edit these codes for each outlet
$codes = array(
    "1" => array(
        "on" => 1316147,
        "off" => 1316156
    ),
    "2" => array(
        "on" => 1316291,
        "off" => 1316300
    ),
    "3" => array(
        "on" => 1316611,
        "off" => 1316620
    ),
    "4" => array(
        "on" => 1318147,
        "off" => 1318156
    ),
    "5" => array(
        "on" => 1324291,
        "off" => 1324300
    ),
);

// Path to the codesend binary (current directory is the default)
$codeSendPath = './codesend';

// This PIN is not the first PIN on the Raspberry Pi GPIO header!
// Consult https://projects.drogon.net/raspberry-pi/wiringpi/pins/
// for more information.
$codeSendPIN = "0";

// Pulse length depends on the RF outlets you are using. Use RFSniffer to see what pulse length your device uses.
$codeSendPulseLength = "189";

if (!file_exists($codeSendPath)) {
    error_log("$codeSendPath is missing, please edit the script", 0);
    die(json_encode(array('success' => false)));
}

$outletLight = $_GET['outletId'];
$outletStatus = $_GET['outletStatus'];

if ($outletLight == "6") {
    // 6 is all 5 outlets combined
    if (function_exists('array_column')) {
        // PHP >= 5.5
        $codesToToggle = array_column($codes, $outletStatus);
    } else {
        $codesToToggle = array();
        foreach ($codes as $outletCodes) {
            array_push($codesToToggle, $outletCodes[$outletStatus]);
        }
    }
} else {
    // One
    $codesToToggle = array($codes[$outletLight][$outletStatus]);
}

foreach ($codesToToggle as $codeSendCode) {
    shell_exec($codeSendPath . ' ' . $codeSendCode . ' -p ' . $codeSendPIN . ' -l ' . $codeSendPulseLength);
    sleep(.25);
}

die(json_encode(array('success' => true)));
?>
