<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';

$type = $_GET['type'];

if (isset($type)) {
    if ($type == "ddns") {
        $arrInfo = array('interface', 'server_type', 'username', 'password', 'hostname', 'interval');
    
        exec("/usr/local/bin/uci get ddns.ddns.enabled", $enabled);
        $ddnsdata['enabled'] = $enabled[0];
        if ($enabled[0] == "1") {
            foreach ($arrInfo as $info) {
                unset($val);
                exec("sudo /usr/local/bin/uci get ddns.ddns." . $info, $val);
                $ddnsdata[$info] = $val[0];
            }
        } 
    }

    echo json_encode($ddnsdata);
}
