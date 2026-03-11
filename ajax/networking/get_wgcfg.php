<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';

$type = $_GET['type'];

if (isset($type)) {
    if ($type == "settings") {
        exec("sudo /usr/local/bin/uci get wireguard.wg.type", $type);
        exec("sudo /usr/local/bin/uci get wireguard.wg.role", $role);
        if ($type[0] != NULL) {
            $wgdata['type'] = $type[0];
        } else {
            $wgdata['type'] = 'off';
        }

        if ($wgdata['type'] != 'off') {
            if ($role[0] != NULL) {
                $wgdata['role'] = $role[0];
            } else {
                $wgdata['role'] = 'client';
            }

            if ($wgdata['type'] == 'wg') {
                exec('sudo /usr/local/bin/uci get wireguard.wg.wg_file', $wg_file);
                if ($wg_file[0] != NULL) {
                    exec('sudo /usr/bin/basename /etc/wireguard/wg0.conf', $tmp);
                    if (strlen($tmp[0]) > 0) {
                        $wgdata['wg'] = $wg_file[0];
                    }
                }
            }
        }

        echo json_encode($wgdata); 
    } else if ($type == "download") {
        exec('sudo cat '. RASPI_WIREGUARD_PATH.'client.conf', $return);
        echo implode(PHP_EOL,$return);
    }  
}

