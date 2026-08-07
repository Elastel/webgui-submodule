<?php
require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

$type = $_GET['type'];

$type_arr = array(
    'dnp3' => array('option' => 'dnp3_server', 'option_list' => 'dnp3'),
    'modbus_slave' => array('option' => 'modbus_slave', 'option_list' => 'modbus_slave_point'),
    'opcua' => array('option' => 'opcua', 'option_list' => 'opcuaserv')
);

if ($type == 'datadisplay') {
    $webData = '/tmp/webshow';
    $factor_list = '/tmp/factor_list';
    if (file_exists($webData)) {
        $dctdata["data"] = file_get_contents($webData);
    }

    if (file_exists($factor_list)) {
        $dctdata["factor_list"] = file_get_contents($factor_list);
    }

    echo json_encode($dctdata);
} else if ($type == 'tag_write') {
    $tagName = $_GET['tagName'];
    $value = $_GET['value'];
    if (ctype_digit($value)) {
        $cmd = "sudo /usr/sbin/tag_writer '{\"$tagName\": $value}'";
    } else {
        $cmd = "sudo /usr/sbin/tag_writer '{\"$tagName\": \"$value\"}'";
    }
    
    exec($cmd, $dctdata);
    echo $dctdata[0];
} else if (strstr($type, 'download')) {
    $pos = strpos($type, '_');
    if ($pos !== false) {
        $right = substr($type, $pos + 1);
    } else {
        $right = '';
    }
    
    exec('sudo conf_im_ex export ' . $right);
    $data = file_exists('/tmp/config_export.csv') ? file('/tmp/config_export.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    echo implode(PHP_EOL, $data);
} else if (strstr($type, 'bacdiscover')) {
    $interface = $_GET['interface'];
    if (strstr($interface, 'TCP') != null) {
        $num = filter_var($interface, FILTER_SANITIZE_NUMBER_INT);
        exec("uci get dct.tcp_server.server_port$num", $tmp);
        $port = $tmp[0];
        unset($tmp);
        exec("uci get dct.tcp_server.interface$num", $tmp);
        $iface = $tmp[0];
        exec("sudo /usr/sbin/bacnet_update 0 $iface $port");
        exec('cat /tmp/bacdiscover', $data);
        if ($data[0] != null) {
            $dctdata = json_decode($data[0]);
            echo json_encode($dctdata);
        }
    } else if (strstr($interface, 'COM') != null) {
        $num = filter_var($interface, FILTER_SANITIZE_NUMBER_INT);
        exec("uci get dct.com.baudrate$num", $tmp);
        $baudrate = $tmp[0];
        unset($tmp);
        exec("uci get dct.com.src_addr$num", $tmp);
        $src_addr = $tmp[0];
        unset($tmp);
        exec("uci get dct.com.max_master$num", $tmp);
        $max_master = $tmp[0];
        unset($tmp);
        exec("uci get dct.com.frames$num", $tmp);
        $frames = $tmp[0];
        unset($tmp);

        exec("sudo /usr/sbin/bacnet_update 1 $interface $baudrate $src_addr $max_master $frames");
        exec('cat /tmp/bacdiscover', $data);
        if ($data[0] != null) {
            $dctdata = json_decode($data[0]);
            echo json_encode($dctdata);
        }
    }
} else if (strstr($type, 'iec104discover')) {
    $interface = $_GET['interface'];
    if (strstr($interface, 'TCP') != null) {
        $num = filter_var($interface, FILTER_SANITIZE_NUMBER_INT);
        exec("uci get dct.tcp_server.server_addr$num", $tmp);
        $address = $tmp[0];
        unset($tmp);
        exec("uci get dct.tcp_server.server_port$num", $tmp);
        $port = $tmp[0];
        unset($tmp);
        exec("pgrep dctd", $pids);
        if (!empty($pids)) {
            foreach ($pids as $pid) {
                exec("sudo kill -9 $pid");
            }
        }
        sleep(1);
        exec("sudo /usr/sbin/iec104_client_scan $address $port");
        exec('cat /tmp/iec104discover', $data);
        if ($data[0] != null) {
            $arr = explode(';', $data[0]);
            $arr = array_filter($arr);
            echo json_encode($arr);
        }
    } else if (strstr($interface, 'COM') != null) {
        ;
    }
} else if (strstr($type, 'iec1107discover')) {
    $interface = $_GET['interface'];
    if (strstr($interface, 'COM') != null) {
        $comlist = get_serial_device_list();
        $device = array_search($interface, $comlist);
        exec("pgrep dctd", $pids);
        if (!empty($pids)) {
            foreach ($pids as $pid) {
                exec("sudo kill -9 $pid");
            }
        }
        sleep(1);
        exec("sudo /usr/sbin/iec1107_client_scan $device");
    }

    exec('cat /tmp/iec1107discover', $data);
    if ($data[0] != null) {
        $arr = explode(';', $data[0]);
        $arr = array_filter($arr);
        echo json_encode($arr);
    }
} else if (strstr($type, 'mbus_scan')) {
    $address = $_GET['address'];
    $interface = $_GET['interface'];
    $num = filter_var($interface, FILTER_SANITIZE_NUMBER_INT);
    exec("uci get dct.com.baudrate$num", $tmp);
    $baudrate = $tmp[0];
    unset($tmp);
    exec("uci get dct.com.frame_interval$num", $tmp);
    $frame = $tmp[0];
    $comlist = get_serial_device_list();
    $device = array_search($interface, $comlist);
    exec("pgrep dctd", $pids);
    if (!empty($pids)) {
        foreach ($pids as $pid) {
            exec("sudo kill -9 $pid");
        }
    }
    sleep(1);
    exec("sudo mbus-serial-request-data -d -b $baudrate -f $frame $device $address", $data);
    exec('sudo /etc/init.d/dct restart >/dev/null');
    if (!empty($data)) {
        if (preg_match('/<MBusData.*<\/MBusData>/s', implode(PHP_EOL, $data), $matches)) {
            $result = $matches[0];
            echo $result;
        } else {
            echo 'No MBusData found';
        }
    } else {
        echo 'No MBusData found';
    }
} else if (strstr($type, 'snmp_scan')) {
    $oid = $_GET['oid'];
    $interface = $_GET['interface'];
    $num = filter_var($interface, FILTER_SANITIZE_NUMBER_INT);
    exec("uci get dct.tcp_server.server_addr$num", $tmp);
    $address = $tmp[0];
    unset($tmp);
    exec("uci get dct.tcp_server.server_port$num", $tmp);
    $port = (!empty($tmp[0])) ? $tmp[0] : '161';
    exec("sudo snmpbulkwalk -v2c -c public -On $address:$port $oid", $data);
    if (!empty($data)) {
        echo implode(PHP_EOL, $data);
    } else {
        echo '';
    }
} else if (strstr($type, 'dlms_scan')) {
    $auth_list = [_('None'), 'Low', 'High', 'HighMd5', 'HighSha1', 'HighGmac', 'HighSha256'];
    $cmd = '';
    $uci_section = '';

    $interface = $_GET['interface'];
    $num = filter_var($interface, FILTER_SANITIZE_NUMBER_INT);
    if (strstr($interface, 'COM') != null) {
        $comlist = get_serial_device_list();
        $device = array_search($interface, $comlist);
        $uci_section = "com";
        $cmd = "sudo /usr/sbin/dlms_scan -S $device";
    } else if (strstr($interface, 'TCP') != null) {
        $uci_section = "tcp_server";
        exec("uci get dct.$uci_section.server_addr$num", $tmp);
        $address = $tmp[0];
        unset($tmp);
        exec("uci get dct.$uci_section.server_port$num", $tmp);
        $port = $tmp[0];
        unset($tmp);
        $cmd = "sudo /usr/sbin/dlms_scan -h $address -p $port";
    }

    if ($cmd == '') {
        echo '';
        return;
    }

    exec("uci get dct.$uci_section.dlms_client_address$num", $tmp);
    $client_id = $tmp[0];
    unset($tmp);
    exec("uci get dct.$uci_section.dlms_server_address$num", $tmp);
    $server_id = $tmp[0];
    unset($tmp);
    exec("pgrep dctd", $pids);
    if (!empty($pids)) {
        foreach ($pids as $pid) {
            exec("sudo kill -9 $pid");
        }
    }
    sleep(1);
    $cmd .= " -c $client_id -s $server_id";
    exec("uci get dct.$uci_section.dlms_auth$num", $tmp);
    $auth = $tmp[0];
    unset($tmp);
    if ($auth == '5') {
        $security_level_list = [_('None'), 'Authentication', 'Encryption', 'AuthenticationEncryption'];
        $client_title = exec("uci get dct.$uci_section.dlms_client_title$num");
        $invocation_counter = exec("uci get dct.$uci_section.dlms_invocation_counter$num");
        $security_level = exec("uci get dct.$uci_section.dlms_security_level$num");
        $sec_level_str = $security_level_list[$security_level];
        $cmd .= " -a HighGmac -C $sec_level_str";
        $authentication_key = exec("uci get dct.$uci_section.dlms_authentication_key$num");
        $cipher_Key = exec("uci get dct.$uci_section.dlms_cipher_Key$num");
        if ($security_level == '1') {
            $cmd .= " -A $authentication_key";
        } else if ($security_level == '2') {
            $cmd .= " -B $cipher_Key";
        } else if ($security_level == '3') {
            $cmd .= " -A $authentication_key -B $cipher_Key";
        }
        $cmd .= " -T $client_title -v $invocation_counter";
    } else if ($auth != '0') {
        exec("uci get dct.$uci_section.dlms_password$num", $tmp);
        $password = $tmp[0];
        unset($tmp);
        $auth_str = $auth_list[$auth];
        $cmd .= " -a $auth_str -P $password";
    }

    exec($cmd, $data);
    if (!empty($data)) {
        echo implode(PHP_EOL, $data);
    } else {
        echo '';
    }
} else if (strstr($type, 'iec61850cli_scan')) {
    $uci_section = "tcp_server";
    $interface = $_GET['interface'];
    $num = filter_var($interface, FILTER_SANITIZE_NUMBER_INT);
    $address = exec("uci get dct.$uci_section.server_addr$num");
    $port = exec("uci get dct.$uci_section.server_port$num");
    $cmd = "sudo /usr/sbin/iec61850cli_scan $address $port";
    $auth = exec("uci get dct.$uci_section.iec61850_auth$num");
    if ($auth == '1') {
        $password = exec("uci get dct.$uci_section.iec61850_password$num");
        $cmd .= " $password";
    } else if ($auth == '2') {
        ;
    }

    exec($cmd, $data);
    if (!empty($data)) {
        echo implode(PHP_EOL, $data);
    } else {
        echo '';
    }
} else {
    $rule = $_GET['rule'];
    if (file_exists('/etc/elastel_config.json')) {
        $fileContent = file_get_contents('/etc/elastel_config.json');
        $config = json_decode($fileContent, true);
    }
       
    if (file_exists('/tmp/factor_list'))
        $fileContentFactor = file_get_contents('/tmp/factor_list');

    if ($type == 'interface') {
        exec("/usr/sbin/get_config dct name $type 5", $data);
        $dctdata[$type] = $data[0];
        $dctdata['com_option'] = $config['com_key'];
        $dctdata['tcp_server_option'] = $config['tcp_server_key'];
        echo json_encode($dctdata);
    } else if ($type == 'server') {
        exec("/usr/sbin/get_config dct name $type 5", $data);
        $dctdata = json_decode($data[0]);
        echo json_encode($dctdata);
    } else if ($rule == '1') {
        exec("/usr/sbin/get_config dct type $type 1", $data);
        // $dctdata = json_decode($data[0]);

        $dctdata['option'] = $config[$type .'_option'];
        $dctdata[$type] = $data[0];

        echo json_encode($dctdata);
    } else if ($type == 'dnp3' || $type == 'modbus_slave' || $type == 'opcua') {
        $option_name = $type_arr[$type]['option'];
        $option_list_name = $type_arr[$type]['option_list'];

        exec("/usr/sbin/get_config dct name " . $option_name . " 1", $tmp1);
        exec("/usr/sbin/get_config dct type " . $option_list_name . " 1", $tmp2);
    
        $dctdata['option'] = $config[$option_name .'_option'];
        $dctdata['option_list'] = $config[$option_list_name .'_option'];
        if (strlen($fileContentFactor) > 0)
            $dctdata['factor_list'] = json_decode($fileContentFactor, true);

        if ($tmp1)
            $dctdata[$option_name] = $tmp1[0];

        if ($tmp2)
            $dctdata[$option_list_name] = $tmp2[0];
        
        echo json_encode($dctdata);
    } else if ($type == 'opcua_nodes') {
        exec('cat /tmp/opcua_nodes', $dctdata);
        if ($dctdata[0] != NULL){
            echo $dctdata[0];
        }  else {
            echo "{}";
        }
    } else {
        exec("/usr/sbin/get_config dct name $type 1", $data);
        $dctdata = json_decode($data[0]);
        echo json_encode($dctdata);
    }
}
