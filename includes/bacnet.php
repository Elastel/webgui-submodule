<?php

require_once 'config.php';

function DisplayBACnet()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savebacnetsettings']) || isset($_POST['applybacnetsettings'])) {
            $ret = saveBACnetConfig($status);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applybacnetsettings'])) {
                    exec('sudo /etc/init.d/dct restart >/dev/null'); 
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

    echo renderTemplate("bacnet", compact('status', 'interface_list'));
}

function saveBACnetConfig($status)
{
    exec("sudo /usr/local/bin/uci set dct.bacnet.enabled=" . $_POST['enabled']);
    exec("sudo /usr/local/bin/uci set dct.bacnet.proto=" . $_POST['proto']);
    if ($_POST['proto'] == 0) {
        exec("sudo /usr/local/bin/uci set dct.bacnet.ifname=" .$_POST['ifname']);
        exec("sudo /usr/local/bin/uci set dct.bacnet.port=" .$_POST['port']);
    } else {
        exec("sudo /usr/local/bin/uci set dct.bacnet.interface=" .$_POST['interface']);
        exec("sudo /usr/local/bin/uci set dct.bacnet.baudrate=" .$_POST['baudrate']);
        exec("sudo /usr/local/bin/uci set dct.bacnet.mac=" .$_POST['mac']);
        exec("sudo /usr/local/bin/uci set dct.bacnet.max_master=" .$_POST['max_master']);
        exec("sudo /usr/local/bin/uci set dct.bacnet.frames=" .$_POST['frames']);
    }
    
    exec("sudo /usr/local/bin/uci set dct.bacnet.device_id=" .$_POST['device_id']);
    exec("sudo /usr/local/bin/uci set dct.bacnet.object_name=" .$_POST['object_name']);
    exec("sudo /usr/local/bin/uci commit dct");

    
    $status->addMessage('BACnet configuration updated ', 'success');
    return true;
}

