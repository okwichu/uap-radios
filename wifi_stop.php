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

// Batch disable.  Nite!
foreach($devices as $d) {
    echo("Disabling AP $d: ");
    manage_device("disable", $d);
    print("DONE\n");
}

?>