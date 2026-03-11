<?php

require_once 'config.php';

/**
 * Displays info about the RaspAP project
 */
function DisplayS7()
{
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['saves7settings']) || isset($_POST['applys7settings'])) {
            saveS7Config($status);
            
            if (isset($_POST['applys7settings'])) {
                sleep(2);
                exec('sudo /etc/init.d/dct restart > /dev/null');
            }
        }
    }

    if ( isset($_POST['upload']) ) {
        if (strlen($_FILES['upload_file']['name']) > 0) {
            if (is_uploaded_file($_FILES['upload_file']['tmp_name'])) {
                save_import_file('s7', $status, $_FILES['upload_file']);
            } else {
                $status->addMessage('fail to upload file', 'danger');
            }
        }
    }


    echo renderTemplate("s7", compact('status'));
}

function saveS7Config($status)
{
    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct s7');

    $status->addMessage('dct configuration updated ', 'success');
    return true;
}
