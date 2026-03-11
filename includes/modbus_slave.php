<?php

require_once 'config.php';

function DisplayModbusSlave()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savemodbusslavesettings']) || isset($_POST['applymodbusslavesettings'])) {
            $ret = saveModbusSlaveConfig($status);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applymodbusslavesettings'])) {
                    exec('sudo /etc/init.d/dct restart >/dev/null'); 
                }
            }
        }
    }

    echo renderTemplate("modbus_slave", compact('status'));
}

function saveModbusSlaveConfig($status)
{
    exec("sudo /usr/local/bin/uci set dct.modbus_slave.enabled=" . $_POST['modbus_slave_enabled']);
    exec("sudo /usr/local/bin/uci set dct.modbus_slave.proto=" . $_POST['proto']);
    if ($_POST['proto'] == 'RTU') {
        exec("sudo /usr/local/bin/uci set dct.modbus_slave.interface=" .$_POST['interface']);
        exec("sudo /usr/local/bin/uci set dct.modbus_slave.baudrate=" .$_POST['baudrate']);
        exec("sudo /usr/local/bin/uci set dct.modbus_slave.databit=" .$_POST['databit']);
        exec("sudo /usr/local/bin/uci set dct.modbus_slave.stopbit=" .$_POST['stopbit']);
        exec("sudo /usr/local/bin/uci set dct.modbus_slave.parity=" .$_POST['parity']);
    } else {
        exec("sudo /usr/local/bin/uci set dct.modbus_slave.port=" .$_POST['port']);
    }

    exec("sudo /usr/local/bin/uci set dct.modbus_slave.slave_id=" .$_POST['slave_id']);

    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct modbus_slave_point');

    exec('sudo uci commit dct');
    
    $status->addMessage('Modbus Slave configuration updated ', 'success');
    return true;
}

