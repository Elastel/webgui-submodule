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
                    $status->addMessage('Configuration applied.', 'success'); 
                }
            }
        }
    }

    echo renderTemplate("dnp3", compact('status'));
}

function saveDnp3Config($status)
{
    for ($i = 1; $i <= 4; $i++) {
        exec("sudo /usr/local/bin/uci set dct.dnp3_server.enabled$i=" . $_POST['dnp3_enabled'.$i]);
        exec("sudo /usr/local/bin/uci set dct.dnp3_server.proto$i=" . $_POST['proto'.$i]);
        if ($_POST['proto'.$i] == 'RTU') {
            exec("sudo /usr/local/bin/uci set dct.dnp3_server.interface$i=" .$_POST['interface'.$i]);
            exec("sudo /usr/local/bin/uci set dct.dnp3_server.baudrate$i=" .$_POST['baudrate'.$i]);
            exec("sudo /usr/local/bin/uci set dct.dnp3_server.databit$i=" .$_POST['databit'.$i]);
            exec("sudo /usr/local/bin/uci set dct.dnp3_server.stopbit$i=" .$_POST['stopbit'.$i]);
            exec("sudo /usr/local/bin/uci set dct.dnp3_server.parity$i=" .$_POST['parity'.$i]);
        } else {
            exec("sudo /usr/local/bin/uci set dct.dnp3_server.port$i=" .$_POST['port'.$i]);
        }

        exec("sudo /usr/local/bin/uci set dct.dnp3_server.slave_address$i=" .$_POST['slave_address'.$i]);
        exec("sudo /usr/local/bin/uci set dct.dnp3_server.master_address$i=" .$_POST['master_address'.$i]);
    }

    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct dnp3');

    exec('sudo uci commit dct');
    
    $status->addMessage('Configuration updated.', 'success');
    return true;
}

