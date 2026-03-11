<?php

require_once 'config.php';

function DisplayDnp3Client()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savednp3clisettings']) || isset($_POST['applydnp3clisettings'])) {
            $ret = saveDnp3ClientConfig($status);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applydnp3clisettings'])) {
                    exec('sudo /etc/init.d/dct restart >/dev/null'); 
                }
            }
        }
    }

    if ( isset($_POST['upload']) ) {
        if (strlen($_FILES['upload_file']['name']) > 0) {
            if (is_uploaded_file($_FILES['upload_file']['tmp_name'])) {
                save_import_file('dnp3cli', $status, $_FILES['upload_file']);
            } else {
                $status->addMessage('fail to upload file', 'danger');
            }
        }
    }

    echo renderTemplate("dnp3_client", compact('status'));
}

function saveDnp3ClientConfig($status)
{
    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct dnp3cli');

    $status->addMessage('OPC UA Client configuration updated ', 'success');
    return true;
}

