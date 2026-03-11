<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';

$type = $_GET['type'];

if (isset($type)) {
    if ($type == "lorawan") {
        exec("sudo /usr/local/bin/uci get loragw.loragw.type", $type);
        $json_strings['type'] = $type[0];

        $json_string = file_get_contents("/etc/global_conf.json");
        $data = json_decode($json_string, true);
        $general = array('server_address', 'serv_port_up', 'serv_port_down', 'gateway_ID',
        'keepalive_interval', 'stat_interval');

        foreach ($general as $info) {
            if ($data['gateway_conf'][$info] != '') {
                $json_strings[$info] = $data['gateway_conf'][$info];
            }
        }

        if ($data['SX130x_conf']['radio_0']['enable'] == 'true') {
            $json_strings['radio0_enable'] = '1';
            $json_strings['radio0_frequency'] = $data['SX130x_conf']['radio_0']['freq'];
            if ( $data['SX130x_conf']['radio_0']['tx_enable'] == 'true') {
                $json_strings['radio0_tx'] = '1';
                if ($data['SX130x_conf']['radio_0']['tx_freq_min'] != '') {
                    $json_strings['radio0_tx_min'] = $data['SX130x_conf']['radio_0']['tx_freq_min'];
                }

                if ($data['SX130x_conf']['radio_0']['tx_freq_max'] != '') {
                    $json_strings['radio0_tx_max'] = $data['SX130x_conf']['radio_0']['tx_freq_max'];
                }
            } else {
                $json_strings['radio0_tx'] = '0';
            }
        } else {
            $json_strings['radio0_enable'] = '0';
        }

        if ($data['SX130x_conf']['radio_1']['enable'] == 'true') {
            $json_strings['radio1_enable'] = '1';
            $json_strings['radio1_frequency'] = $data['SX130x_conf']['radio_1']['freq'];
            if ( $data['SX130x_conf']['radio_1']['tx_enable'] == 'true') {
                $json_strings['radio1_tx'] = '1';
            } else {
                $json_strings['radio1_tx'] = '0';
            }
        } else {
            $json_strings['radio1_enable'] = '0';
        }

        $channels = array('channel_enable', 'channel_radio', 'channel_if');
        for ($i = 0; $i < 8; $i++) {
            if ($data['SX130x_conf']['chan_multiSF_' . $i]['enable'] == 'true') {
                $json_strings['channel_enable' . $i] = '1';
            } else {
                $json_strings['channel_enable' . $i] = '0';
            }

            $json_strings['channel_radio' . $i] = $data['SX130x_conf']['chan_multiSF_' . $i]['radio'];
            $json_strings['channel_if' . $i] = $data['SX130x_conf']['chan_multiSF_' . $i]['if'];

        }

        exec('sudo /usr/local/bin/uci get loragw.loragw.frequency', $freq);
        $json_strings['frequency'] = $freq[0] != null ? $freq[0] : '0';
// basic_station
        $general = array('protocol', 'uri', 'auth_mode', 'client_token');
        $json_string = file_get_contents("/etc/basic_station/station.conf");
        $data = json_decode($json_string, true);
        $json_strings['gateway_ID_station'] = $data['station_conf']['routerid'];
        exec("sudo /usr/local/bin/uci get loragw.loragw.protocol", $protocol);
        $json_strings['protocol'] = $protocol[0];
        if ($protocol[0] == 'lns') {
            exec("cat /etc/basic_station/tc.uri", $uri);
            $proto_name = 'tc';
        } else if ($protocol[0] == 'cups') {
            exec("cat /etc/basic_station/cups.uri", $uri);
            $proto_name = 'cups';
        }
            

        $json_strings['uri'] = $uri[0];
        exec("sudo /usr/local/bin/uci get loragw.loragw.auth_mode", $auth_mode);
        $json_strings['auth_mode'] = $auth_mode[0];

        if (file_exists("/etc/basic_station/$proto_name.trust")) {
            exec("sudo /usr/local/bin/uci get loragw.loragw.lora_ca", $lora_ca);
            $json_strings['lora_ca'] = $lora_ca[0];
        }

        if (file_exists("/etc/basic_station/$proto_name.crt")) {
            exec("sudo /usr/local/bin/uci get loragw.loragw.lora_crt", $lora_crt);
            $json_strings['lora_crt'] = $lora_crt[0];
        }

        if (file_exists("/etc/basic_station/$proto_name.key")) {
            exec("sudo /usr/local/bin/uci get loragw.loragw.lora_key", $lora_key);
            $json_strings['lora_key'] = $lora_key[0];
        }
    }

    echo json_encode($json_strings);
}