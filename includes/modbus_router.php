<?php

require_once 'config.php';

function DisplayModbusRouter()
{   
    $status = new \ElastPro\Messages\StatusMessage;
    $comlist = get_serial_device_list();

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savemodbusroutersettings']) || isset($_POST['applymodbusroutersettings'])) {
            $ret = saveModbusRouterConfig($status, $comlist);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applymodbusroutersettings'])) {
                    exec('sudo /etc/init.d/modbus_router restart >/dev/null'); 
                }
            }
        }
    }

    exec("pgrep router-modbus", $pid);
    if ($pid != null) {
        $routerStatus = "Running";
        $statusIcon = "up";
    } else {
        $routerStatus = "Stop";
        $statusIcon = "down";
    }

    $arrInfo = array('mode', 'address', 'port', 'com', 'baudrate', 'databit',
    'stopbit', 'parity');

    exec("/usr/local/bin/uci get modbus_router.modbus.enabled", $enabled);
    $modbusRouterConf['enabled'] = $enabled[0];
    if ($enabled[0] == "1") {
        foreach ($arrInfo as $info) {
            unset($val);
            exec("sudo /usr/local/bin/uci get modbus_router.modbus." . $info, $val);
            $modbusRouterConf[$info] = $val[0];
        }
    }

    $arrInfoCom = array('com', 'baudrate', 'databit', 'stopbit', 'parity');
    for ($i = 0; $i < count($comlist) - 1; $i ++) {
        $num = $i + 2;
        unset($enabled);
        exec("/usr/local/bin/uci get modbus_router.modbus.enable_com$num", $enabled);
        $modbusRouterConf['enable_com' . $num] = $enabled[0];
        if ($enabled[0] == "1") {
            foreach ($arrInfo as $info) {
                unset($val);
                exec("sudo /usr/local/bin/uci get modbus_router.modbus." . $info . $num, $val);
                $modbusRouterConf[$info . $num] = $val[0];
            }
        }
    }

    echo renderTemplate("modbus_router", compact('status', 'routerStatus', 'statusIcon', 'modbusRouterConf', 'comlist'));
}

function saveModbusRouterConfig($status, $comlist)
{
    $comName = [];
    exec("sudo /usr/local/bin/uci set modbus_router.modbus.enabled=" . $_POST['enabled']);
    exec("sudo /usr/local/bin/uci set modbus_router.modbus.mode=" . $_POST['mode']);
    exec("sudo /usr/local/bin/uci set modbus_router.modbus.address=" .$_POST['address']);
    exec("sudo /usr/local/bin/uci set modbus_router.modbus.port=" .$_POST['port']);
    exec("sudo /usr/local/bin/uci set modbus_router.modbus.com=" .$_POST['com']);
    exec("sudo /usr/local/bin/uci set modbus_router.modbus.baudrate=" .$_POST['baudrate']);
    exec("sudo /usr/local/bin/uci set modbus_router.modbus.databit=" .$_POST['databit']);
    exec("sudo /usr/local/bin/uci set modbus_router.modbus.stopbit=" .$_POST['stopbit']);
    exec("sudo /usr/local/bin/uci set modbus_router.modbus.parity=" .$_POST['parity']);
    array_push($comName, $_POST['com']);
    for ($i = 0; $i < count($comlist) - 1; $i ++) {
        $num = $i + 2;
        exec("sudo /usr/local/bin/uci set modbus_router.modbus.enable_com$num=" .$_POST['enable_com' . $num]);
        if ($_POST['enable_com' . $num]) {
            if (in_array($_POST['com' . $num], $comName)) {
                $status->addMessage('The same interface cannot be configured', 'danger');
                return false;
            } else {
                array_push($comName, $_POST['com' . $num]);
            }
            exec("sudo /usr/local/bin/uci set modbus_router.modbus.com$num=" .$_POST['com' . $num]);
            exec("sudo /usr/local/bin/uci set modbus_router.modbus.baudrate$num=" .$_POST['baudrate' . $num]);
            exec("sudo /usr/local/bin/uci set modbus_router.modbus.databit$num=" .$_POST['databit' . $num]);
            exec("sudo /usr/local/bin/uci set modbus_router.modbus.stopbit$num=" .$_POST['stopbit' . $num]);
            exec("sudo /usr/local/bin/uci set modbus_router.modbus.parity$num=" .$_POST['parity' . $num]);
        }
    }
    exec("sudo /usr/local/bin/uci commit modbus_router");

    $status->addMessage('Modbus router configuration updated ', 'success');
    return true;
}

