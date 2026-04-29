<?php

require_once 'config.php';

/**
 * Displays info about the RaspAP project
 */
function DisplayDlms()
{
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savedlmssettings']) || isset($_POST['applydlmssettings'])) {
            saveDlmsConfig($status, $data_type_list);
            
            if (isset($_POST['applydlmssettings'])) {
                sleep(2);
                exec('sudo /etc/init.d/dct restart > /dev/null');
                $status->addMessage('Configuration applied.', 'success');
            }
        }
    }

    echo renderTemplate("dlms", compact('status'));
}

function saveDlmsConfig($status, $data_type_list)
{
    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct dlms');

    $status->addMessage('Configuration updated.', 'success');
    return true;
}
