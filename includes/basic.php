<?php

require_once 'config.php';

function DisplayBasic()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savebasicsettings']) || isset($_POST['applybasicsettings'])) {
            saveBasicConfig($status);  
            
            if (isset($_POST['applybasicsettings'])) {
                sleep(2);
                exec('sudo /etc/init.d/dct restart > /dev/null');
            }
        }
    }

    echo renderTemplate("basic", compact('status'));
}

function saveBasicConfig($status)
{
    $data = array();
    $data['enabled'] = $_POST['enabled'];
    if ($_POST['enabled'] == "1") {
        $data['collect_period'] = $_POST['collect_period'];
        $data['report_period'] = $_POST['report_period'];
        $data['batch_reporting'] = $_POST['batch_reporting'] ?? '0';
        $data['cache_enabled'] = $_POST['cache_enabled'] ?? '0';
        $data['cache_day'] = $_POST['cache_day'];
        $data['minute_enabled'] = $_POST['minute_enabled'] ?? '0';
        $data['minute_period'] = $_POST['minute_period'];
        $data['hour_enabled'] = $_POST['hour_enabled'] ?? '0';
        $data['day_enabled'] = $_POST['day_enabled'] ?? '0';
    }

    $json_data = json_encode($data);
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $json_data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct basic');
    $status->addMessage('dct configuration updated ', 'success');
}

