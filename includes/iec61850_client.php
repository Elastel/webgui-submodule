<?php

require_once 'config.php';

function DisplayIec61850Client()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['saveiec61850clisettings']) || isset($_POST['applyiec61850clisettings'])) {
            $ret = saveIec61850ClientConfig($status);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applyiec61850clisettings'])) {
                    exec('sudo /etc/init.d/dct restart >/dev/null');
                    $status->addMessage('Configuration applied.', 'success');
                }
            }
        }
    }

    echo renderTemplate("iec61850_client", compact('status'));
}

function saveIec61850ClientConfig($status)
{
    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct iec61850cli');

    $status->addMessage('Configuration updated.', 'success');
    return true;
}

