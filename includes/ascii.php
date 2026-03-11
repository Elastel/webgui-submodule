<?php

function DisplayAscii()
{
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED && (!empty($_POST['saveasciisettings']) || !empty($_POST['applyasciisettings']))) {
        saveAsciiConfig($status);

        if (!empty($_POST['applyasciisettings'])) {
            restartDctService();
        }
    }

    if (!empty($_POST['upload']) && !empty($_FILES['upload_file']['name'])) {
        handleFileUpload($status, $_FILES['upload_file']);
    }

    echo renderTemplate("ascii", compact('status'));
}


function saveAsciiConfig($status)
{
    $data = $_POST['table_data'] ?? '';
    if (!empty($data)) {
        if (file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data) === false) {
            $status->addMessage('Failed to save configuration', 'danger');
            return;
        }

        exec('sudo /usr/sbin/set_config ' . escapeshellarg(ELASTEL_DCT_CONFIG_JSON) . ' dct ascii');
        $status->addMessage('dct configuration updated', 'success');
    } else {
        $status->addMessage('No data provided for configuration', 'warning');
    }
}

function handleFileUpload($status, $file)
{
    if (is_uploaded_file($file['tmp_name'])) {
        save_import_file('ascii', $status, $file);
    } else {
        $status->addMessage('Failed to upload file', 'danger');
    }
}

function restartDctService()
{
    exec('sudo /etc/init.d/dct restart > /dev/null 2>&1');
}
