<?php

require_once 'config.php';

/**
 * Displays info about the RaspAP project
 */
function DisplayMc()
{
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savemcsettings']) || isset($_POST['applymcsettings'])) {
            saveMcConfig($status);
            
            if (isset($_POST['applymcsettings'])) {
                sleep(2);
                exec('sudo /etc/init.d/dct restart > /dev/null');
            }
        }
    }

    if ( isset($_POST['upload']) ) {
        if (strlen($_FILES['upload_file']['name']) > 0) {
            if (is_uploaded_file($_FILES['upload_file']['tmp_name'])) {
                save_import_file('mc', $status, $_FILES['upload_file']);
            } else {
                $status->addMessage('fail to upload file', 'danger');
            }
        }
    }

    echo renderTemplate("mc", compact('status'));
}

function saveMcConfig($status)
{
    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct mc');

    $status->addMessage('dct configuration updated ', 'success');
    return true;
}
