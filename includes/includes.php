<?php
require_once 'includes/config.php';
require_once 'includes/autoload.php';

require_once 'includes/CSRF.php';
require_once 'includes/session.php';
require_once 'includes/defaults.php';
require_once 'includes/locale.php';
require_once 'includes/functions.php';

// Default page actions
require_once 'includes/dashboard.php';
require_once 'includes/login.php';
require_once 'includes/dct.php';
require_once 'includes/authenticate.php';
require_once 'includes/admin.php';
require_once 'includes/dhcp.php';
require_once 'includes/hostapd.php';
// require_once 'includes/adblock.php';
// require_once 'includes/data_usage.php';
require_once 'includes/system.php';
require_once 'includes/sysstats.php';
require_once 'includes/configure_client.php';
require_once 'includes/networking.php';
// require_once 'includes/themes.php';
require_once 'includes/about.php';
require_once 'includes/openvpn.php';
require_once 'includes/wireguard.php';
// require_once 'includes/torproxy.php';
require_once 'includes/basic.php';
require_once 'includes/interfaces.php';
require_once 'includes/modbus.php';
require_once 'includes/s7.php';
require_once 'includes/fx.php';
require_once 'includes/io.php';
require_once 'includes/server.php';
require_once 'includes/ddns.php';
require_once 'includes/bacnet.php';
require_once 'includes/datadisplay.php';
require_once 'includes/detection.php';
require_once 'includes/macchina.php';
require_once 'includes/opcua.php';
require_once 'includes/lorawan.php';
require_once 'includes/terminal.php';
require_once 'includes/gps.php';
require_once 'includes/mc.php';
require_once 'includes/firewall.php';
require_once 'includes/ascii.php';
require_once 'includes/bacnet_client.php';
require_once 'includes/nodered.php';
require_once 'includes/docker.php';
require_once 'includes/iec104.php';
require_once 'includes/opcua_client.php';
require_once 'includes/bacnet_router.php';
require_once 'includes/backup_update.php';
require_once 'includes/chirpstack.php';
require_once 'includes/dnp3.php';
require_once 'includes/dnp3_client.php';
require_once 'includes/modbus_slave.php';
require_once 'includes/things_wing.php';
require_once 'includes/modbus_router.php';
require_once 'includes/backup_restore.php';
require_once 'includes/iotedge.php';
require_once 'includes/ethernetip.php';
require_once 'includes/hmi.php';
require_once 'includes/restapi.php';
require_once 'includes/mbus_client.php';
require_once 'includes/snmp_client.php';
require_once 'includes/iec1107.php';
require_once 'includes/scheduled.php';
require_once 'includes/dlms.php';
require_once 'includes/iec61850_client.php';
require_once 'includes/system_param.php';

$model = getModel();
$target = getTarget();
$hostname = getHostname();
$output = $return = 0;
$page = $_SERVER['PATH_INFO'];

$theme_url = getThemeOpt();
$toggleState = getSidebarState();
//$bridgedEnabled = getBridgedState();

?>