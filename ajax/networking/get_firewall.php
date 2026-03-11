<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';

// general

$arr_general = array('synflood_protect', 'drop_invalid', 'input', 'output', 'forward');

foreach ($arr_general as $info) {
    unset($val);
    exec('sudo /usr/local/bin/uci get firewall.general.' . $info, $val);
    $firewall_data[$info] = $val[0];
}

// forwards
$arr_forwards = array('name', 'proto', 'src_port', 'dest_ip', 'dest_port', 'enabled');

exec('sudo /usr/sbin/uci_get_count firewall forwards', $forwards_count);
$firewall_data['forwards.count'] = $forwards_count[0];

for ($i = 0; $i < number_format($forwards_count[0]); $i++) {
    foreach ($arr_forwards as $info) {
        unset($val);
        exec("sudo /usr/local/bin/uci get firewall.@forwards[$i]." . $info, $val);
        if ($info == 'enabled') {
            $firewall_data['forwards.' . $info][$i] = ($val[0] == '1') ? 'true' : 'false';
        } else {
            $firewall_data['forwards.' . $info][$i] = $val[0];
        }
        
    }
}

// traffic
$arr_traffic = array('name', 'proto', 'rule', 'src_mac', 'src_ip', 'src_port', 
                 'dest_ip', 'dest_port', 'action', 'enabled');

exec('sudo /usr/sbin/uci_get_count firewall traffic', $traffic_count);
$firewall_data['traffic.count'] = $traffic_count[0];

for ($i = 0; $i < number_format($traffic_count[0]); $i++) {
    foreach ($arr_traffic as $info) {
        unset($val);
        exec("sudo /usr/local/bin/uci get firewall.@traffic[$i]." . $info, $val);
        if ($info == 'enabled') {
            $firewall_data['traffic.' . $info][$i] = ($val[0] == '1') ? 'true' : 'false';
        } else {
            $firewall_data['traffic.' . $info][$i] = $val[0];
        } 
    }
}

echo json_encode($firewall_data);

