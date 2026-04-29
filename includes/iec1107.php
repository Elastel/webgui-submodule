<?php

require_once 'config.php';

/**
 * Displays info about the RaspAP project
 */
function DisplayIec1107()
{
    $status = new \ElastPro\Messages\StatusMessage;
    $data_type_list = array('Int', 'Float', 'String');

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['saveiec1107settings']) || isset($_POST['applyiec1107settings'])) {
            saveIec1107Config($status, $data_type_list);
            
            if (isset($_POST['applyiec1107settings'])) {
                sleep(2);
                exec('sudo /etc/init.d/dct restart > /dev/null');
                $status->addMessage('Configuration applied.', 'success');
            }
        }
    }

    echo renderTemplate("iec1107", compact('status', 'data_type_list'));
}

function saveIec1107Config($status, $data_type_list)
{
    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct iec1107');

    $status->addMessage('Configuration updated.', 'success');
    return true;
}
