<?php
/**
 * Copyright (c) 2021, Art of WiFi
 *
 * This file is subject to the MIT license that is bundled with this package in the file LICENSE.md
 */

function get_unifi_config() {
    return [
        "controlleruser"     => 'uapradio',
        "controllerpassword" => 'HelloKitty_1',
        "controllerurl"      => 'https://192.168.1.1:443',
        "controllerversion"  => '4.0.0',
        "debug"              => false
    ];
}

