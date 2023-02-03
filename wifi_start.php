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
array_filter($devices);

// Batch enable.  Good morning!
print("\n");
foreach($devices as $d) {
    echo("Enabling AP $d: ");
    manage_device($d, "enable");
    print("\n");
}

?>