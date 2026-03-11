<?php

require_once 'config.php';

function DisplayBACnetRouter()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savebacnetroutersettings']) || isset($_POST['applybacnetroutersettings'])) {
            $ret = saveBACnetRouterConfig($status);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applybacnetroutersettings'])) {
                    exec('sudo /etc/init.d/bacnet_router restart >/dev/null'); 
                }
            }
        }
    }

    exec("ip -o link show | awk -F': ' '{print $2}'", $tmp);
    sort($tmp);

    $interface_list = array();
    foreach ($tmp as $value) {
        if ($value == 'eth1' || $value == 'docker0' ||  $value == 'lo' ||
            strstr($value, 'veth') != NULL || strstr($value, '@NONE') != NULL)
            continue;

        $interface_list["$value"] = $value;
    }

    exec("pgrep router-mstp", $pid);
    if ($pid != null) {
        $routerStatus = "Running";
        $statusIcon = "up";
    } else {
        $routerStatus = "Stop";
        $statusIcon = "down";
    }

    echo renderTemplate("bacnet_router", compact('status', 'interface_list', 'routerStatus', 'statusIcon'));
}

function saveBACnetRouterConfig($status)
{
    exec("sudo /usr/local/bin/uci set bacnet_router.bacnet.enabled=" . $_POST['enabled']);
    exec("sudo /usr/local/bin/uci set bacnet_router.bacnet.mode=" . $_POST['mode']);
    exec("sudo /usr/local/bin/uci set bacnet_router.bacnet.ifname=" .$_POST['ifname']);
    exec("sudo /usr/local/bin/uci set bacnet_router.bacnet.port=" .$_POST['port']);
    exec("sudo /usr/local/bin/uci set bacnet_router.bacnet.interface=" .$_POST['interface']);
    exec("sudo /usr/local/bin/uci set bacnet_router.bacnet.baudrate=" .$_POST['baudrate']);
    exec("sudo /usr/local/bin/uci set bacnet_router.bacnet.mac=" .$_POST['mac']);
    exec("sudo /usr/local/bin/uci set bacnet_router.bacnet.max_master=" .$_POST['max_master']);
    exec("sudo /usr/local/bin/uci set bacnet_router.bacnet.frames=" .$_POST['frames']);
    exec("sudo /usr/local/bin/uci commit bacnet_router");

    $status->addMessage('BACnet configuration updated ', 'success');
    return true;
}

