<?php
require_once 'manage_device.php';

// Read the list of devices to manage
$devices = [];    
$fp = fopen('devices.csv', 'r');

while(!feof($fp)) {
    $line = fgets($fp, 2048);
    $data = str_getcsv($line, " ");
    array_push($devices, $data[0]);
}
fclose($fp);

// Batch enable.  Good morning!
foreach($devices as $d) {
    echo("Enabling AP $d: ");
    manage_device("enable", $d);
    print("DONE\n");
}

?>