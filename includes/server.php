<?php

require_once 'config.php';

function DisplayServer()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['saveserversettings']) || isset($_POST['applyserversettings'])) {
            saveServerConfig($status); 
            
            if (isset($_POST['applyserversettings'])) {
                sleep(2);
                exec('sudo /etc/init.d/dct restart > /dev/null');
            }
        }
    }

    echo renderTemplate('server', compact('status'));
}

function SaveServerUpload($status, $file, $num)
{
    define('KB', 1024);
    $tmp_destdir = '/tmp/';
    $auth_flag = 0;

    try {
        // If undefined or multiple files, treat as invalid
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters');
        }

        $upload = \ElastPro\Uploader\FileUpload::factory('server' . $num, $tmp_destdir);
        $upload->set_max_file_size(64*KB);
        $upload->set_allowed_mime_types(array('text/plain'));
        $upload->file($file);

        $validation = new validation;
        $upload->callbacks($validation, array('check_name_length'));
        $results = $upload->upload();

        if (!empty($results['errors'])) {
            throw new RuntimeException($results['errors'][0]);
        }

        // Valid upload, get file contents
        $tmp_serverconfig = $results['full_path'];

        $path = "/etc/ssl/server" . $num;
        if (!is_dir($path)) {
            exec("sudo /bin/mkdir -p " . $path);
        }

        // Move processed file from tmp to destination
        system("sudo mv $tmp_serverconfig /etc/ssl/server" . $num . "/" . $file['name'], $return);

        // if ($return ==0) {
        //     $status->addMessage('mqtt certificate uploaded successfully', 'info');
        // } else {
        //     $status->addMessage('Unable to save mqtt certificate', 'danger');
        // }
        return $status;

    } catch (RuntimeException $e) {
        $status->addMessage($e->getMessage(), 'danger');
        return $status;
    }
}

function saveServerConfig($status)
{
    for ($i = 1; $i <= 5; $i++) {
        $data['enabled' . $i] = $_POST['enabled' . $i] ?? '0';
        if ($_POST['enabled' . $i] == '1') {
            if ($_POST['certificate_type' . $i] == '1' && $_POST['proto' . $i] == '2') {
                if (strlen($_FILES['mqtt_ca' . $i]['name']) > 0) {
                    if (is_uploaded_file($_FILES['mqtt_ca' . $i]['tmp_name'])) {
                        SaveServerUpload($status, $_FILES['mqtt_ca' . $i], $i);
                    }
                    $fileName = $_FILES['mqtt_ca' . $i]['name'];
                    $data['mqtt_ca' . $i] = $fileName;
                }     
            } else if ($_POST['certificate_type' . $i] == '2'  && $_POST['proto' . $i] == '2') {
                if (strlen($_FILES['mqtt_ca' . $i]['name']) > 0) {
                    if (is_uploaded_file($_FILES['mqtt_ca' . $i]['tmp_name'])) {
                        SaveServerUpload($status, $_FILES['mqtt_ca' . $i], $i);
                    }
                    $fileName = $_FILES['mqtt_ca' . $i]['name'];
                    $data['mqtt_ca' . $i] = $fileName;
                }

                if (strlen($_FILES['mqtt_cert' . $i]['name']) > 0) {
                    if (is_uploaded_file($_FILES['mqtt_cert' . $i]['tmp_name'])) {
                        SaveServerUpload($status, $_FILES['mqtt_cert' . $i], $i);
                    }

                    $certName = $_FILES['mqtt_cert' . $i]['name'];
                    $data['mqtt_cert' . $i] = $certName;
                }

                if (strlen($_FILES['mqtt_key' . $i]['name']) > 0) {
                    if (is_uploaded_file($_FILES['mqtt_key' . $i]['tmp_name'])) {
                        SaveServerUpload($status, $_FILES['mqtt_key' . $i], $i);
                    }

                    $keyName = $_FILES['mqtt_key' . $i]['name'];
                    $data['mqtt_key' . $i] = $keyName;
                }
            }

            $serverInfo = array("proto", "encap_type", "json_format", "server_addr", "http_url", "server_port", "cache_enabled", 
                "register_packet", "register_packet_hex", "heartbeat_packet", "heartbeat_packet_hex", "heartbeat_interval",
                "mqtt_heartbeat_interval", "mqtt_pub_topic", "mqtt_sub_topic", "mqtt_username", "mqtt_password", "sparkplug_group_id",
                "sparkplug_node_id", "sparkplug_device_id", "mqtt_client_id", "mqtt_tls_enabled", "certificate_type", "mqtt_ca", 
                "mqtt_cert", "mqtt_key", "self_define_var", "var_name1_", "var_value1_", "var_name2_", "var_value2_", "var_name3_", "var_value3_", 
                "mn", "st", "pw");

            foreach ($serverInfo as $info) {
                if ($info != "mqtt_ca" && $info != "mqtt_cert" && $info != "mqtt_key") {
                    $data[$info . $i] = $_POST[$info . $i] ?? '';
                } 
            }
        }
    }

    $json_data = json_encode($data);
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $json_data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct server');

    $status->addMessage('dct configuration updated ', 'success');
}
