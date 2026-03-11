<?php

require_once 'config.php';

function DisplayLorawan()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savesettings']) || isset($_POST['applysettings'])) {
            saveLorawanConfig($status);  
            
            if (isset($_POST['applysettings'])) {
                sleep(1);
                exec('sudo /etc/init.d/loragw restart > /dev/null');
            }
        }
    }

    exec("sudo /usr/local/bin/uci get loragw.loragw.gateway_id", $tmp);
    $gateway_eui = strtoupper($tmp[0]);

    echo renderTemplate("lorawan", compact('status', 'gateway_eui'));
}

function SaveLorawanUpload($status, $file, $file_name)
{
    define('KB', 1024);
    $tmp_destdir = '/tmp/';
    $auth_flag = 0;

    try {
        // If undefined or multiple files, treat as invalid
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters');
        }

        $upload = \ElastPro\Uploader\FileUpload::factory('lorawan', $tmp_destdir);
        $upload->set_max_file_size(64*KB);
        $upload->set_allowed_mime_types(array('lorawan' => 'text/plain'));
        $upload->file($file);

        $validation = new validation;
        $upload->callbacks($validation, array('check_name_length'));
        $results = $upload->upload();

        if (!empty($results['errors'])) {
            throw new RuntimeException($results['errors'][0]);
        }

        // Valid upload, get file contents
        $tmp_serverconfig = $results['full_path'];

        $path = "/etc/basic_station";
        if (!is_dir($path)) {
            exec("sudo /bin/mkdir -p " . $path);
        }

        // Move processed file from tmp to destination
        system("sudo mv $tmp_serverconfig /etc/basic_station/" . $file_name, $return);

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

function saveLorawanConfig($status)
{  
    if ($_POST['type'] != '0') {
        if ($_POST['gateway_ID'] == '') {
            $status->addMessage('Gateway ID cannot be empty', 'danger');
            return;
        }
    }

    if ($_POST['type'] == "1") {
        $general = array('server_address', 'serv_port_up', 'serv_port_down', 'gateway_ID',
        'keepalive_interval', 'stat_interval');

        $radio = array('radio0_enable', 'radio0_frequency', 'radio0_tx', 'radio0_tx_min', 'radio0_tx_max',
            'radio1_enable', 'radio1_frequency', 'radio1_tx');

        $json_string = file_get_contents("/etc/global_conf.json");
        $data = json_decode($json_string, true);
        foreach ($general as $info) {
            if ($_POST[$info] != '') {
                if ($info == 'serv_port_up' || $info == 'serv_port_down' || 
                    $info == 'keepalive_interval' || $info == 'stat_interval') {
                        $data['gateway_conf'][$info] = intval($_POST[$info]);
                } else {
                    $data['gateway_conf'][$info] = $_POST[$info];
                }
            }
        }

        if ($_POST['radio0_enable'] == '1') {
            $data['SX130x_conf']['radio_0']['enable'] = true;
            if ($_POST['radio0_frequency'] != '') {
                $data['SX130x_conf']['radio_0']['freq'] = intval($_POST['radio0_frequency']);
            }

            if ($_POST['radio0_tx'] == '1') {
                $data['SX130x_conf']['radio_0']['tx_enable'] = true;
                if ($_POST['radio0_tx_min'] != '') {
                    $data['SX130x_conf']['radio_0']['tx_freq_min'] = intval($_POST['radio0_tx_min']);
                }

                if ($_POST['radio0_tx_max'] != '') {
                    $data['SX130x_conf']['radio_0']['tx_freq_max'] = intval($_POST['radio0_tx_max']);
                }
            } else {
                $data['SX130x_conf']['radio_0']['tx_enable'] = false;
            }
        } else {
            $data['SX130x_conf']['radio_0']['enable'] = false;
        }

        if ($_POST['radio1_enable'] == '1') {
            $data['SX130x_conf']['radio_1']['enable'] = true;
            if ($_POST['radio1_frequency'] != '') {
                $data['SX130x_conf']['radio_1']['freq'] = intval($_POST['radio1_frequency']);
            }

            if ($_POST['radio1_tx'] == '1') {
                $data['SX130x_conf']['radio_1']['tx_enable'] = true;
            } else {
                $data['SX130x_conf']['radio_1']['tx_enable'] = false;
            }
        } else {
            $data['SX130x_conf']['radio_1']['enable'] = false;
        }
        
        for ($i = 0; $i < 8; $i++) {
            $data['SX130x_conf']['chan_multiSF_' . $i]['enable'] = true;
        }
        
        // $channels = array('channel_enable', 'channel_radio', 'channel_if');
        // for ($i = 0; $i < 8; $i++) {
        //     if ($_POST['channel_enable' . $i] == '1') {
        //         $data['SX130x_conf']['chan_multiSF_' . $i]['enable'] = true;
        //         if ($_POST['channel_radio' . $i] != '') {
        //             $data['SX130x_conf']['chan_multiSF_' . $i]['radio'] = intval($_POST['channel_radio' . $i]);
        //         }

        //         if ($_POST['channel_if' . $i] != '') {
        //             $data['SX130x_conf']['chan_multiSF_' . $i]['if'] = intval($_POST['channel_if' . $i]);
        //         }
        //     } else {
        //         $data['SX130x_conf']['chan_multiSF_' . $i]['enable'] = false;
        //     }
        // }

        $json_strings = json_encode($data);
        file_put_contents("/tmp/global_conf.json", $json_strings);
        exec("sudo mv /tmp/global_conf.json /etc/global_conf.json");
        exec("sudo /usr/local/bin/uci set loragw.loragw.frequency=" .$_POST['frequency']);
        exec("sudo /usr/local/bin/uci delete loragw.loragw.lora_ca");
        exec("sudo /usr/local/bin/uci delete loragw.loragw.lora_crt");
        exec("sudo /usr/local/bin/uci delete loragw.loragw.lora_key");
        exec("sudo /usr/local/bin/uci set loragw.loragw.gateway_id=" .$_POST['gateway_ID']);
    } else if ($_POST['type'] == "2") {
        $json_string = file_get_contents("/etc/basic_station/station.conf");
        $data = json_decode($json_string, true);
        $data['station_conf']['routerid'] = $_POST['gateway_ID'];
        $data['station_conf']['euiprefix'] = $_POST['gateway_ID'];
        $json_strings = json_encode($data);
        file_put_contents("/tmp/station.conf", $json_strings);
        exec("sudo mv /tmp/station.conf /etc/basic_station/station.conf");

        $general = array('protocol', 'uri', 'auth_mode', 'client_token');
        exec("sudo /usr/local/bin/uci set loragw.loragw.protocol=" .$_POST['protocol']);
        exec("sudo /usr/local/bin/uci set loragw.loragw.uri=" .$_POST['uri']);

        if ($_POST['protocol'] == 'lns') {
            $protocol = 'tc';
            exec("sudo rm /etc/basic_station/cups*");
        } else if ($_POST['protocol'] == 'cups') {
            $protocol = 'cups';
            exec("sudo rm /etc/basic_station/tc*");
        }

        $cfg[] = $_POST['uri'];
        $tmp_path = '/tmp/uri';
        file_put_contents($tmp_path, $cfg);
        chmod($tmp_path, 0755);
        exec("sudo mv /tmp/uri /etc/basic_station/$protocol.uri");
        
        exec("sudo /usr/local/bin/uci set loragw.loragw.auth_mode=" .$_POST['auth_mode']);

        if (strlen($_FILES['lora_ca']['name']) > 0) {
            if (is_uploaded_file($_FILES['lora_ca']['tmp_name'])) {
                SaveLorawanUpload($status, $_FILES['lora_ca'], "$protocol.trust");
            }
            $fileName = $_FILES['lora_ca']['name'];
            exec('sudo /usr/local/bin/uci set loragw.loragw.lora_ca=' . $fileName);
        }

        if ($_POST['auth_mode'] == '1') {
            if (strlen($_FILES['lora_crt']['name']) > 0) {
                if (is_uploaded_file($_FILES['lora_crt']['tmp_name'])) {
                    SaveLorawanUpload($status, $_FILES['lora_crt'], "$protocol.crt");
                }

                $certName = $_FILES['lora_crt']['name'];
                exec('sudo /usr/local/bin/uci set loragw.loragw.lora_crt=' . $certName);
            }

            if (strlen($_FILES['lora_key']['name']) > 0) {
                if (is_uploaded_file($_FILES['lora_key']['tmp_name'])) {
                    SaveLorawanUpload($status, $_FILES['lora_key'], "$protocol.key");
                }

                $keyName = $_FILES['lora_key']['name'];
                exec('sudo /usr/local/bin/uci set loragw.loragw.lora_key=' . $keyName);
            }
        } else if ($_POST['auth_mode'] == '2') {
            if (strlen($_FILES['lora_key']['name']) > 0) {
                if (is_uploaded_file($_FILES['lora_key']['tmp_name'])) {
                    SaveLorawanUpload($status, $_FILES['lora_key'], "$protocol.key");
                }

                $keyName = $_FILES['lora_key']['name'];
                exec('sudo /usr/local/bin/uci set loragw.loragw.lora_key=' . $keyName);
            }
            exec("sudo rm /etc/basic_station/*.crt");
        }
        exec("sudo /usr/local/bin/uci set loragw.loragw.gateway_id=" .$_POST['gateway_ID']);
    } else {
        exec("sudo /usr/local/bin/uci delete loragw.loragw.gateway_id");
        exec("sudo /usr/local/bin/uci delete loragw.loragw.lora_ca");
        exec("sudo /usr/local/bin/uci delete loragw.loragw.lora_crt");
        exec("sudo /usr/local/bin/uci delete loragw.loragw.lora_key");
    }

    $status->addMessage('lorawan configuration updated ', 'success');
    exec("sudo /usr/local/bin/uci set loragw.loragw.type=" .$_POST['type']);
    exec("sudo /usr/local/bin/uci commit loragw");
}
