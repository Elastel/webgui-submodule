<?php

require_once 'config.php';

function DisplayTerminal()
{

    $status = new \ElastPro\Messages\StatusMessage;
    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['saveterminalsettings']) || isset($_POST['applyterminalsettings'])) {
            exec("sudo /usr/local/bin/uci set terminal.terminal.port=" .$_POST['port']);
            exec("sudo /usr/local/bin/uci set terminal.terminal.interface=" .$_POST['interface']);
	        exec("sudo /usr/local/bin/uci commit terminal");

            if (isset($_POST['applyterminalsettings'])) {
                exec("sudo /etc/init.d/terminal restart");
                $status->addMessage('Restart terminal successfully', 'info');
            }
        }
    }

    if ( isset($_POST['upload']) ) {
        if (strlen($_FILES['upload_file']['name']) > 0) {
            if (is_uploaded_file($_FILES['upload_file']['tmp_name'])) {
                SaveUploadFile($status, $_FILES['upload_file']);
            } else {
                $status->addMessage('fail to upload file', 'danger');
            }
        }
    }

    exec('sudo /usr/local/bin/uci get terminal.terminal.port', $port);
    exec('sudo /usr/local/bin/uci get terminal.terminal.interface', $interface);
    exec("ip -o link show | awk -F': ' '{print $2}'", $tmp);
    sort($tmp);

    $interface_list = array();
    foreach ($tmp as $value) {
        $interface_list["$value"] = $value;
    }

    if ($port[0] == null) {
        $prot[0] = '7681';
    }

    if ($interface[0] == null) {
        $interface[0] = 'br0';
    }

    if ($interface[0] != null) {
        exec('ifconfig ' . $interface[0] . ' | grep -Eo "([0-9]+[.]){3}[0-9]+" | grep -v "255.255."', $ip);
    } else {
        exec('ifconfig br0 | grep -Eo "([0-9]+[.]){3}[0-9]+" | grep -v "255.255."', $ip);
    }

    echo renderTemplate('terminal', compact(
        'status',
        'port',
        'interface',
        'ip',
        'interface_list'
    ));
}

function SaveUploadFile($status, $file)
{
    define('KB', 1024);
    $tmp_destdir = '/tmp/';
    $auth_flag = 0;

    try {
        // If undefined or multiple files, treat as invalid
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters');
        }

        $upload = \ElastPro\Uploader\FileUpload::factory('terminal' . $num, $tmp_destdir);
        $upload->set_max_file_size(10*1024*KB);
        $upload->set_allowed_mime_types(array());
        $upload->file($file);
        $validation = new validation;
        $upload->callbacks($validation, array('check_name_length'));
        $results = $upload->upload();

        if (!empty($results['errors'])) {
            throw new RuntimeException($results['errors'][0]);
        }

        // Valid upload, get file contents
        $file_path = $results['full_path'];
        $new_file_path = "/tmp/terminal/" . $file['name'];
        system("sudo mv $file_path $new_file_path");
        
        if (file_exists($new_file_path)) {
            system("sudo chmod -R 755 /tmp/terminal");
            $status->addMessage('file uploaded successfully:' . $new_file_path, 'info');
        } else {
            $status->addMessage('fail to upload file', 'danger');
        }

        return $status;
    } catch (RuntimeException $e) {
        $status->addMessage($e->getMessage(), 'danger');
        return $status;
    }
}

