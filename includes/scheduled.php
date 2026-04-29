<?php

require_once 'config.php';

function DisplayScheduled()
{   
    $arrInfo = array('mode', 'time', 'weekday', 'monthday', 'task', 'custom_command');
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savescheduledsettings']) || isset($_POST['applyscheduledsettings'])) {
            saveScheduledConfig($arrInfo, $status);  
            
            if (isset($_POST['applyscheduledsettings'])) {
                exec('sudo /etc/init.d/schedule restart > /dev/null');
                $status->addMessage('Configuration applied.', 'success');
            }
        }
    }

    exec("sudo /usr/local/bin/uci get system.scheduled.enabled", $tmp);
    $scheduled['enabled'] = $tmp[0] ?? 0;
    if ($scheduled['enabled'] == 1) {
        foreach ($arrInfo as $info) {
            unset($tmp);
            exec("sudo /usr/local/bin/uci get system.scheduled.$info", $tmp);
            $scheduled[$info] = $tmp[0] ?? '';
        }
    }

    exec("pgrep scheduled", $pid);
    if ($pid != null) {
        $routerStatus = "Running";
        $statusIcon = "up";
    } else {
        $routerStatus = "Stop";
        $statusIcon = "down";
    }

    echo renderTemplate("scheduled", compact(
        'status',
        'scheduled',
        'routerStatus',
        'statusIcon'
    ));
}

function saveScheduledConfig($arrInfo, $status)
{
    exec("sudo /usr/local/bin/uci set system.scheduled.enabled=" .$_POST['enabled']);
    if ($_POST['enabled'] == "1") {
        foreach ($arrInfo as $info) {
            if ($info == 'time') {
                // validate time format HH:MM:SS
                if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d):([0-5]\d)$/', $_POST[$info])) {
                    $status->addMessage('Invalid time format. Please use HH:MM:SS.', 'danger');
                    return false;
                }
            }
            exec("sudo /usr/local/bin/uci set system.scheduled.$info=$_POST[$info]");
        }
    } 
    
    exec("sudo /usr/local/bin/uci commit system");

    $status->addMessage('Configuration updated.', 'success');
    return true;
}