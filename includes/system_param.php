<?php

require_once 'config.php';

function DisplaySystemParam()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savesystemparamsettings']) || isset($_POST['applysystemparamsettings'])) {
            $ret = saveSystemParamConfig($status);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applysystemparamsettings'])) {
                    exec('sudo /etc/init.d/dct restart >/dev/null');
                    $status->addMessage('Configuration applied.', 'success');
                }
            }
        }
    }

    echo renderTemplate("system_param", compact('status'));
}

function saveSystemParamConfig($status)
{
    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct system_param');

    $status->addMessage('Configuration updated.', 'success');
    return true;
}

