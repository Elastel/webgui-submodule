<?php

require_once 'config.php';

function DisplayDnp3()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savednp3settings']) || isset($_POST['applydnp3settings'])) {
            $ret = saveDnp3Config($status);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applydnp3settings'])) {
                    exec('sudo /etc/init.d/dct restart >/dev/null'); 
                }
            }
        }
    }

    echo renderTemplate("dnp3", compact('status'));
}

function saveDnp3Config($status)
{
    exec("sudo /usr/local/bin/uci set dct.dnp3_server.enabled=" . $_POST['dnp3_enabled']);
    exec("sudo /usr/local/bin/uci set dct.dnp3_server.proto=" . $_POST['proto']);
    if ($_POST['proto'] == 'RTU') {
        exec("sudo /usr/local/bin/uci set dct.dnp3_server.interface=" .$_POST['interface']);
        exec("sudo /usr/local/bin/uci set dct.dnp3_server.baudrate=" .$_POST['baudrate']);
        exec("sudo /usr/local/bin/uci set dct.dnp3_server.databit=" .$_POST['databit']);
        exec("sudo /usr/local/bin/uci set dct.dnp3_server.stopbit=" .$_POST['stopbit']);
        exec("sudo /usr/local/bin/uci set dct.dnp3_server.parity=" .$_POST['parity']);
    } else {
        exec("sudo /usr/local/bin/uci set dct.dnp3_server.port=" .$_POST['port']);
    }

    exec("sudo /usr/local/bin/uci set dct.dnp3_server.slave_address=" .$_POST['slave_address']);
    exec("sudo /usr/local/bin/uci set dct.dnp3_server.master_address=" .$_POST['master_address']);

    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct dnp3');

    exec('sudo uci commit dct');
    
    $status->addMessage('DNP3 configuration updated ', 'success');
    return true;
}

