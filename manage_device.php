<?php
/**
 * PHP API usage example
 *
 * contributed by: Art of WiFi
 * description:    example basic PHP script to disable/enable a device, returns true upon success
 */

/**
 * using the composer autoloader
 */
require_once 'vendor/autoload.php';
require_once 'config.php';


function manage_device($device_id, $operation) {

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

    $result = NULL;

    switch($operation) {
        case "enable":
            $result = $conn->disable_ap($device_id, false);
            break;
        case "disable":
            $result = $conn->disable_ap($device_id, true);
            break;
        default:
            print "Error: invalid operation requested.  'enable' or 'disable' is all this method supports.";
            break;
    }
    
    echo json_encode($result, JSON_PRETTY_PRINT);
}

// This runs when we're invoked directly from the CLI
// if (count($argv) != 3) {
//     print("Usage: manage_device.php device_id (enable|disable)");
//     return;
// }

// $device_id = $argv[1];
// $operation = $argv[2];
// manage_device($device_id, $operation);