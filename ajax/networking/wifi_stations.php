<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/session.php';
require_once '../../includes/config.php';
require_once '../../includes/defaults.php';
require_once '../../includes/functions.php';
require_once '../../includes/wifi_functions.php';

$networks = [];
$network  = null;
$ssid     = null;

$enabled = $_GET['enable'];

if (isset($enabled)) {
    exec("sudo /usr/local/bin/uci set wifi.wifi_client.enabled=" . $enabled);
    exec("sudo /usr/local/bin/uci commit wifi");
    switchWifiMode($enabled);
}

// getWifiInterface();
knownWifiStations($networks);
nearbyWifiStations($networks, !isset($_REQUEST["refresh"]));
connectedWifiStations($networks);
sortNetworksByRSSI($networks);
foreach ($networks as $ssid => $network) $networks[$ssid]["ssidutf8"] = ssid2utf8( $ssid ); 

$connected = array_filter($networks, function($n) { return $n['connected']; } );
$known     = array_filter($networks, function($n) { return !$n['connected'] && $n['configured']; } );
$nearby    = array_filter($networks, function($n) { return !$n['configured']; } );

exec("sudo /usr/local/bin/uci get wifi.wifi_client.enabled", $wifi_client_enable);

echo renderTemplate('wifi_stations', compact('networks', 'connected', 'known', 'nearby', 'wifi_client_enable'), true);
