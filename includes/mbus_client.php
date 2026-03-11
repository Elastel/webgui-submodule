<?php

require_once 'config.php';

function DisplayMbusClient()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savembusclisettings']) || isset($_POST['applymbusclisettings'])) {
            $ret = saveMbusClientConfig($status);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applymbusclisettings'])) {
                    exec('sudo /etc/init.d/dct restart >/dev/null'); 
                }
            }
        }
    }

    echo renderTemplate("mbus_client", compact('status'));
}

function saveMbusClientConfig($status)
{
    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct mbuscli');

    $status->addMessage('Mbus Rules configuration updated ', 'success');
    return true;
}

