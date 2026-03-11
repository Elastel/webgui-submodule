<?php

require_once 'config.php';

function DisplaySnmpClient()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savesnmpclisettings']) || isset($_POST['applysnmpclisettings'])) {
            $ret = saveSnmpClientConfig($status);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applysnmpclisettings'])) {
                    exec('sudo /etc/init.d/dct restart >/dev/null'); 
                }
            }
        }
    }

    echo renderTemplate("snmp_client", compact('status'));
}

function saveSnmpClientConfig($status)
{
    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct snmpcli');

    $status->addMessage('SNMP Rules configuration updated ', 'success');
    return true;
}

