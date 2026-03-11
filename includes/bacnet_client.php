<?php

require_once 'config.php';

function DisplayBACnetClient()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savebacclisettings']) || isset($_POST['applybacclisettings'])) {
            $ret = saveBACnetClientConfig($status);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applybacclisettings'])) {
                    exec('sudo /etc/init.d/dct restart >/dev/null'); 
                }
            }
        }
    }

    if ( isset($_POST['upload']) ) {
        if (strlen($_FILES['upload_file']['name']) > 0) {
            if (is_uploaded_file($_FILES['upload_file']['tmp_name'])) {
                save_import_file('baccli', $status, $_FILES['upload_file']);
            } else {
                $status->addMessage('fail to upload file', 'danger');
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

    echo renderTemplate("bacnet_client", compact('status', 'interface_list'));
}

function saveBACnetClientConfig($status)
{
    exec("sudo /usr/local/bin/uci set dct.bacnet_client.enabled=" . $_POST['enabled']);
    exec("sudo /usr/local/bin/uci set dct.bacnet_client.proto=" . $_POST['proto']);
    if ($_POST['proto'] == 0) {
        if (strlen($_POST['ip_address']) > 3)
            exec("sudo /usr/local/bin/uci set dct.bacnet_client.ip_address=" .$_POST['ip_address']);
        else
        exec("sudo /usr/local/bin/uci delete dct.bacnet_client.ip_address");

        exec("sudo /usr/local/bin/uci set dct.bacnet_client.ifname=" .$_POST['ifname']);
        exec("sudo /usr/local/bin/uci set dct.bacnet_client.port=" .$_POST['port']);
        exec("sudo /usr/local/bin/uci set dct.bacnet_client.bbmd_enabled=" . $_POST['bbmd_enabled']);
        if ($_POST['bbmd_enabled']) {
            exec("sudo /usr/local/bin/uci set dct.bacnet_client.bbmd_ip=" .$_POST['bbmd_ip']);
            exec("sudo /usr/local/bin/uci set dct.bacnet_client.bbmd_port=" .$_POST['bbmd_port']);
            exec("sudo /usr/local/bin/uci set dct.bacnet_client.bbmd_time=" .$_POST['bbmd_time']);
        }
    } else {
        exec("sudo /usr/local/bin/uci set dct.bacnet_client.interface=" .$_POST['interface']);
        exec("sudo /usr/local/bin/uci set dct.bacnet_client.baudrate=" .$_POST['baudrate']);
        exec("sudo /usr/local/bin/uci set dct.bacnet_client.mac=" .$_POST['mac']);
        exec("sudo /usr/local/bin/uci set dct.bacnet_client.max_master=" .$_POST['max_master']);
        exec("sudo /usr/local/bin/uci set dct.bacnet_client.frames=" .$_POST['frames']);
    }

    exec("sudo /usr/local/bin/uci set dct.bacnet_client.device_id=" .$_POST['device_id']);
    exec("sudo /usr/local/bin/uci set dct.bacnet_client.collect_mode=" .$_POST['collect_mode']);

    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct baccli');

    if ($_POST['enabled'] == "1") {
        if ($_POST['port'] == NULL || (int)($_POST['port']) > 65535) {
            return false;
        }
    }
    
    $status->addMessage('BACnet Rules configuration updated ', 'success');
    return true;
}

