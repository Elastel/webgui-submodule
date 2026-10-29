<?php

require_once 'config.php';

/*
 * Normalized "enabled" state of the BACnet server: '1' or '0'.
 *
 * Values other than "0"/"1" (an older bug could store "on") are migrated to
 * "1"/"0" here, because get_config stops right after the enabled option once it
 * is not exactly "1" and the page would then be rendered with only the radio
 * buttons and no settings at all.
 */
function bacnetEnabledState()
{
    $raw = strtolower(trim((string)shell_exec('uci get dct.bacnet.enabled 2>/dev/null')));

    if ($raw === '') {
        return '0';
    }

    $enabled = in_array($raw, array('1', 'on', 'true', 'yes', 'enabled'), true);

    if ($raw !== '0' && $raw !== '1') {
        exec('sudo /usr/local/bin/uci set dct.bacnet.enabled=' . ($enabled ? '1' : '0'));
        exec('sudo /usr/local/bin/uci commit dct');
    }

    return $enabled ? '1' : '0';
}

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
                    $status->addMessage('Configuration applied.', 'success');
                }
            }
        }
    }

    /* Mapping rules import: conf_im_ex writes the bacnet_point sections
       directly into UCI and restarts dct by itself. */
    if ( isset($_POST['upload']) ) {
        if (strlen($_FILES['upload_file']['name']) > 0) {
            if (is_uploaded_file($_FILES['upload_file']['tmp_name'])) {
                save_import_file('bacnet_point', $status, $_FILES['upload_file']);
            } else {
                $status->addMessage('Fail to upload file', 'danger');
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

    $enabled = bacnetEnabledState();

    echo renderTemplate("bacnet", compact('status', 'interface_list', 'enabled'));
}

function saveBACnetConfig($status)
{
    /*
     * The radio is named "bacnet_enabled" on purpose. When it was named
     * "enabled" it collided with the per-row Enable checkbox of the mapping
     * table (same name, no value) inside the same form: the browser posted
     * both "enabled=1" and "enabled=on", PHP kept the last one, "on" got
     * stored and the page came back collapsed with every field hidden.
     */
    $posted = isset($_POST['bacnet_enabled'])
        ? strtolower(trim($_POST['bacnet_enabled'])) : '';
    $enabled = in_array($posted, array('1', 'on', 'true', 'yes', 'enabled'), true)
        ? '1' : '0';

    exec("sudo /usr/local/bin/uci set dct.bacnet.enabled=" . $enabled);
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

    /* Save the mapping table: JSON -> UCI (sections of type bacnet_point).
       set_config drops every bacnet_point section and rebuilds it, so empty
       data (anything but "[]") is skipped to avoid wiping the table. */
    if (isset($_POST['table_data']) && strlen($_POST['table_data']) > 0) {
        $data = $_POST['table_data'];
        file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
        exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct bacnet_point');
    }

    exec("sudo /usr/local/bin/uci commit dct");

    
    $status->addMessage('Configuration updated.', 'success');
    return true;
}

