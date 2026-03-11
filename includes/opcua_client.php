<?php

require_once 'config.php';

function DisplayOpcuaClient()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['saveopcuaclisettings']) || isset($_POST['applyopcuaclisettings'])) {
            $ret = saveOpcuaClientConfig($status);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applyopcuaclisettings'])) {
                    exec('sudo /etc/init.d/dct restart >/dev/null'); 
                }
            }
        }
    }

    if ( isset($_POST['upload']) ) {
        if (strlen($_FILES['upload_file']['name']) > 0) {
            if (is_uploaded_file($_FILES['upload_file']['tmp_name'])) {
                save_import_file('opcuacli', $status, $_FILES['upload_file']);
            } else {
                $status->addMessage('fail to upload file', 'danger');
            }
        }
    }

    echo renderTemplate("opcua_client", compact('status'));
}

function saveOpcuaClientConfig($status)
{
    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct opcuacli');

    $status->addMessage('OPC UA Client configuration updated ', 'success');
    return true;
}

