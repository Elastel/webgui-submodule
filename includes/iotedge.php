<?php

require_once 'includes/config.php';
require_once 'includes/functions.php';

function DisplayIotedge()
{
    $iotedge_option = [
        'enabled', 
        'source', 
        'connection_string', 
        'iothub_hostname',
        'device_id',
        'global_endpoint', 
        'id_scope', 
        'attestion_method', 
        'registration_id', 
        'symmetric_key', 
        'certificate', 
        'private_key'
    ];

    $status = new \ElastPro\Messages\StatusMessage;
    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['saveiotedgesettings']) || isset($_POST['applyiotedgesettings'])) {
            saveIotedgeConfig($status, $iotedge_option);
            
            if (isset($_POST['applyiotedgesettings'])) {
                exec('sudo /etc/init.d/iotedge start &');
                exec('sudo rm /tmp/iotedgelist');
            }
        }
    }

    exec("sudo iotedge --version | awk '{print $2}'", $tmp);
    $version = $tmp[0];
    unset($tmp);
    exec('sudo iotedge system status | grep aziot-edged | grep Running -c', $tmp);
    $run_status = $tmp[0];

    $method_list = array();
    exec('uci get iotedge.iotedge.source', $source);
    if ($source[0] == 'dps') {
        $method_list = array('TPM', 'Symmetric encryption', 'X.509 certificate');
    } else {
        $method_list = array('Connection String', 'Symmetric encryption', 'X.509 certificate');
    }

    $iotedge_data = [];
    if ($run_status == '1') {
        if (file_exists('/tmp/iotedgelist') ) {
            $output = file_get_contents('/tmp/iotedgelist');
            if ($output != null) {
                $lines = explode("\n", trim($output));

                for ($i = 1; $i < count($lines); $i++) {
                    $line = $lines[$i];
                    $columns = preg_split('/\s{2,}/', $line);
                    $iotedge_data[] = [
                        'name' => $columns[0] ?? '',
                        'status' => $columns[1] ?? '',
                        'description' => $columns[2] ?? '',
                        'config' => $columns[3] ?? ''
                    ];
                }

                if (count($lines) <= 1 || strstr($lines[0], 'NAME') == false) {
                    saveIotedgeList();
                }
            } else {
                saveIotedgeList();
            }
        } else {
            saveIotedgeList();
        }
    }

    echo renderTemplate(
        'iotedge', compact(
            'status',
            'run_status',
            'method_list',
            'version',
            'iotedge_data'
        )
    );
}

function saveIotedgeList()
{
    $cmd_check = 'sudo pgrep -x iotedge';
    $output = shell_exec($cmd_check);
    if (empty($output)) {
        exec('sudo rm -f /tmp/iotedgelist');
        exec('nohup sudo iotedge list | sudo tee /tmp/iotedgelist >/dev/null 2>&1 &');
    }
}

function saveFileUploadIotedge($status, $file)
{
    define('KB', 1024);
    $tmp_destdir = '/tmp/';
    $auth_flag = 0;

    try {
        // If undefined or multiple files, treat as invalid
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters');
        }

        $upload = \ElastPro\Uploader\FileUpload::factory('iotedge', $tmp_destdir);
        $upload->set_max_file_size(64*KB);
        $upload->set_allowed_mime_types(array('text/plain', 'application/octet-stream'));
        $upload->file($file);

        $validation = new validation;
        $upload->callbacks($validation, array('check_name_length'));
        $results = $upload->upload();

        if (!empty($results['errors'])) {
            throw new RuntimeException($results['errors'][0]);
        }

        // Valid upload, get file contents
        $tmp_config = $results['full_path'];

        $path = "/etc/ssl/iotedge";
        if (!is_dir($path)) {
            exec("sudo /bin/mkdir -p " . $path);
        }

        // Move processed file from tmp to destination
        system("sudo mv $tmp_config $path/" . $file['name'], $return);

        return $status;

    } catch (RuntimeException $e) {
        $status->addMessage($e->getMessage(), 'danger');
        return $status;
    }
}

function saveIotedgeConfig($status, $iotedge_option)
{
    $data = [];

    foreach ($iotedge_option as $key) {
        if ($key != 'certificate' && $key != 'private_key') {
            if (isset($_POST[$key])) {
                $data[$key] = $_POST[$key];
            } else {
                $data[$key] = null;
            }
        }
    }

    if ($data['attestion_method'] == '2') {
        if (strlen($_FILES['certificate']['name']) > 0) {
            if (is_uploaded_file($_FILES['certificate']['tmp_name'])) {
                saveFileUploadIotedge($status, $_FILES['certificate']);
            }
            $certName = $_FILES['certificate']['name'];
            $data['certificate'] = $certName;
        }

        if (strlen($_FILES['private_key']['name']) > 0) {
            if (is_uploaded_file($_FILES['private_key']['tmp_name'])) {
                saveFileUploadIotedge($status, $_FILES['private_key']); 
            }

            $keyName = $_FILES['private_key']['name'];
            $data['private_key'] = $keyName;
        }
    }

    $jsonData = json_encode($data, JSON_PRETTY_PRINT);

    file_put_contents('/tmp/iotedge.json', $jsonData);
    exec('sudo /usr/sbin/set_config /tmp/iotedge.json iotedge iotedge');
    $status->addMessage('iotedge configuration updated ', 'success');

    return true;
}
