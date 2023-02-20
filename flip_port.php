<?php

/**
 * Device USW-Ethan-Desk has ports 2-5 set to VLAN 2 (Kidtopia-Network) whenever this is run.
 * Working off adapting port disable example https://github.com/Art-of-WiFi/UniFi-API-client/blob/master/examples/disable_switch_port.php
 * 
 * THIS DOES NOT FUNCTION YET
 */

require_once 'vendor/autoload.php';
require_once 'config.php';

function flip_ports($device) {

    $unifi_config = get_unifi_config();
    $site_id = 'default';
    $conn = new UniFi_API\Client(
        $unifi_config['controlleruser'], 
        $unifi_config['controllerpassword'], 
        $unifi_config['controllerurl'], 
        $site_id, 
        $unifi_config['controllerversion']);

    $set_debug_mode   = $conn->set_debug($unifi_config['debug']);
    $loginresults     = $conn->login();

    $data = $conn->list_devices($device);
    $existing_overrides = $data[0]->port_overrides;

    foreach ($existing_overrides as $key => $value) {
        if (!empty($value->port_idx) && in_array($value->port_idx, [2, 3, 4, 5])) {
            // $existing_overrides[$key]['mac_table']['vlan'] = 2;
            $updated_override = ['vlan' => 2];
        }
    }

    $payload = [
        'port_overrides' => $existing_overrides
    ];

    $result = $conn->set_device_settings_base($data[0]->device_id, $payload);
    echo json_encode($result, JSON_PRETTY_PRINT);
}


flip_ports("d0:21:f9:bd:e1:3d");


// This runs when we're invoked directly from the CLI
// if (count($argv) != 3) {
//     print("Usage: manage_device.php device_id (enable|disable)");
//     return;
// }

// $device_id = $argv[1];
// $operation = $argv[2];
// manage_device($device_id, $operation);