<?php

require_once 'config.php';

function DisplayGps()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savegpssettings']) || isset($_POST['applygpssettings'])) {
            saveGpsConfig($status);  
            
            if (isset($_POST['applygpssettings'])) {
                exec('sudo /etc/init.d/gps restart > /dev/null');
            }
        }
    }

    echo renderTemplate("gps", compact(
        'status'
    ));
}

function saveGpsConfig($status)
{
    $arrInfo = array('output_mode', 'server_addr', 'server_port', 'report_mode', 'register_packet',
        'heartbeat_packet', 'report_interval', 'heartbeat_interval', 'baudrate', 'databit', 'stopbit',
        'parity', 'accuracy');

    exec("sudo /usr/local/bin/uci set gps.conf.enabled=" .$_POST['enabled']);
    if ($_POST['enabled'] == "1") {
        foreach ($arrInfo as $info) {
            exec("sudo /usr/local/bin/uci set gps.conf.$info=$_POST[$info]");
        }
    } 
    
    exec("sudo /usr/local/bin/uci commit gps");

    $status->addMessage('configuration updated ', 'success');
    return true;
}

