<?php

require_once 'config.php';

function DisplayDetectionConfig()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savedetectionsettings']) || isset($_POST['applydetectionsettings'])) {
            saveDetectionConfig($status);  
            
            if (isset($_POST['applydetectionsettings'])) {
                if ($_POST['enabled'] == "1") {
                    exec('sudo /etc/init.d/failover restart > /dev/null');
                } else {
                    exec('sudo /etc/init.d/failover stop > /dev/null');
                }

                $status->addMessage('Configuration applied.', 'success');
            }
        }
    }
    
    exec("sudo /usr/local/bin/uci get network.detection.enabled", $enabled);
    exec("sudo /usr/local/bin/uci get network.detection.primary_addr", $primary_addr);
    exec("sudo /usr/local/bin/uci get network.detection.secondary_addr", $secondary_addr);
    exec("sudo /usr/local/bin/uci get network.detection.detect_period", $detect_period);
    exec("sudo /usr/local/bin/uci get network.detection.enabled_reboot", $enabled_reboot);
    exec("sudo /usr/local/bin/uci get network.detection.reboot_inter", $reboot_inter);

    echo renderTemplate("detection", compact(
        'status', 
        'primary_addr', 
        'secondary_addr', 
        'detect_period',
        'enabled_reboot', 
        'reboot_inter',
        'enabled'
    ));
}

function saveDetectionConfig($status)
{

    $return = 1;
    $error = array();

    exec("sudo /usr/local/bin/uci set network.detection.enabled=" .$_POST['enabled']);
    if ($_POST['enabled'] == "1") {
        exec("sudo /usr/local/bin/uci set network.detection.primary_addr=" .$_POST['primary_addr']);
        exec("sudo /usr/local/bin/uci set network.detection.secondary_addr=" .$_POST['secondary_addr']);
        exec("sudo /usr/local/bin/uci set network.detection.detect_period=" .$_POST['detect_period']);
        if ($_POST['enabled_reboot'] == "1") {
            exec("sudo /usr/local/bin/uci set network.detection.enabled_reboot=" .$_POST['enabled_reboot']);
            exec("sudo /usr/local/bin/uci set network.detection.reboot_inter=" .$_POST['reboot_inter']);
        } else {
            exec("sudo /usr/local/bin/uci set network.detection.enabled_reboot=0");
        }
    }
    
    exec("sudo /usr/local/bin/uci commit network");

    $status->addMessage('Configuration updated.', 'success');
    return true;
 
}

