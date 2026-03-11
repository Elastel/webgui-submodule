<?php

require_once 'config.php';

function DisplayInterfaces()
{   
    $model = getModel();
    $target = getTarget();
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['saveinterfacesettings']) || isset($_POST['applyinterfacesettings'])) {
            saveInterfaceConfig($status, $model);
            
            if (isset($_POST['applyinterfacesettings'])) {
                sleep(2);
                exec('sudo /etc/init.d/dct restart > /dev/null');
            }
        }
    }

    echo renderTemplate('interfaces', compact('status', 'model', 'target'));
}

function saveComConfig($status, $model)
{
    if ($model == "EG500" || $model == "EG410" || $model == "EG510" || $model == "EC212") {
        $count = 2;
    } else {
        $count = 4;
    }

    $data = array();
    $arr_option = array();
    $arr_key = array();

    if (file_exists('/etc/elastel_config.json')) {
        $fileContent = file_get_contents('/etc/elastel_config.json');
        $config = json_decode($fileContent, true);
    }
    
    if (array_key_exists('com_key', $config)) {
        $arr_key = $config['com_key'];
    } else {
        $arr_key = $config['com_option'];
    }

    $arr_option = $config['com_option'];

    for ($i = 1; $i <= $count; $i++) {
        if ($_POST['com_enabled' . $i] == '1') {
            for ($j = 0; $j < count($arr_option); $j++) {
                $data[$arr_option[$j] . $i] = $_POST[$arr_key[$j] . $i];
            }
        } else {
            $data['enabled' . $i] = $_POST['com_enabled' . $i] ?? '0';
        }
    }

    $json_data = json_encode($data);
    
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $json_data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct com');
}

function saveFileUploadInterface($status, $file, $index)
{
    define('KB', 1024);
    $tmp_destdir = '/tmp/';
    $auth_flag = 0;

    try {
        // If undefined or multiple files, treat as invalid
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters');
        }

        $upload = \ElastPro\Uploader\FileUpload::factory('interfaces', $tmp_destdir);
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

        $path = "/etc/ssl/interfaces$index";
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

function saveTcpConfig($status)
{
    $count = 5;

    $data = array();
    $arr_option = array();
    $arr_key = array();

    if (file_exists('/etc/elastel_config.json')) {
        $fileContent = file_get_contents('/etc/elastel_config.json');
        $config = json_decode($fileContent, true);
    }
    
    if (array_key_exists('tcp_server_key', $config)) {
        $arr_key = $config['tcp_server_key'];
    } else {
        $arr_key = $config['tcp_server_option'];
    }

    $arr_option = $config['tcp_server_option'];

    for ($i = 1; $i <= $count; $i++) {
        if ($_POST['tcp_enabled' . $i] == '1') {
            for ($j = 0; $j < count($arr_option); $j++) {
                if ($arr_key[$j] == 'certificate' || $arr_key[$j] == 'private_key' || $arr_key[$j] == 'trust_crt')
                    continue;

                $data[$arr_option[$j] . $i] = $_POST[$arr_key[$j] . $i];
            }

            if ($data['security_policy' . $i] != '0') {
                if (strlen($_FILES['certificate' . $i]['name']) > 0) {
                    if (is_uploaded_file($_FILES['certificate' . $i]['tmp_name'])) {
                        saveFileUploadInterface($status, $_FILES['certificate' . $i], $i);
                    }
                    $certName = $_FILES['certificate' . $i]['name'];
                    $data['certificate' . $i] = $certName;
                }

                // get uri
                if ($_POST['uri' . $i] == null) {
                    $certFile = $data['certificate' . $i];
                    $uri_path = "/etc/ssl/interfaces$i/$certFile";
                    if (!is_dir($uri_path)) {
                        exec("data=$(openssl x509 -in $uri_path -inform der -noout -text | grep URI) && echo $" . '{data#*URI:}' . " | awk -F ' ' '{print $0}'", $uri);
                        if (strlen($uri[0]) > 0) {
                            $data['uri' . $i] = $uri[0];
                        }
                    }
                } else {
                    $data['uri' . $i] = $_POST['uri' . $i];
                }

                if (strlen($_FILES['private_key' . $i]['name']) > 0) {
                    if (is_uploaded_file($_FILES['private_key' . $i]['tmp_name'])) {
                        saveFileUploadInterface($status, $_FILES['private_key' . $i], $i); 
                    }
    
                    $keyName = $_FILES['private_key' . $i]['name'];
                    $data['private_key' . $i] = $keyName;
                }
    
                if (strlen($_FILES['trust_crt' . $i]['name'][0]) > 0) {
                    $count = count($_FILES['trust_crt' . $i]['name']);
                    for ($j = 0; $j < $count; $j++) {
                        if (is_uploaded_file($_FILES['trust_crt' . $i]['tmp_name'][$j])) {
                            $tmp_config = $_FILES['trust_crt' . $i]['tmp_name'][$j];
                            system("sudo mv $tmp_config /etc/ssl/interfaces$i/" . $_FILES['trust_crt' . $i]['name'][$j]);
                            system("sudo chmod 644 /etc/ssl/interfaces$i/" . $_FILES['trust_crt' . $i]['name'][$j]);
                            $trustName .= $_FILES['trust_crt' . $i]['name'][$j];
                            if ($i < ($count - 1))
                                $trustName .= ";";
                        }    
                    }
                    
                    $data['trust_crt' . $i] = $trustName;
                }
            }
        } else {
            $data['enabled' . $i] = $_POST['tcp_enabled' . $i] ?? '0';
        }
    }

    $json_data = json_encode($data);
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $json_data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct tcp_server');
}

function saveInterfaceConfig($status, $model)
{
    saveComConfig($status, $model);
    saveTcpConfig($status);

    $status->addMessage('dct configuration updated ', 'success');
}

