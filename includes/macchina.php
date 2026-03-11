<?php

require_once 'config.php';

function DisplayMacchina()
{
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savesettings']) || isset($_POST['applysettings'])) {
            $ret = saveConfig($status);

            if (isset($_POST['applysettings'])) {
                if ($_POST['enabled'] == '0') {
                    exec('sudo /etc/init.d/macchina stop');
                } else {
                    exec('sudo /etc/init.d/macchina restart');
                }
            }
        }
    }

    if (is_file("/etc/WebTunnelAgent.properties")) {
        exec("sudo /usr/local/bin/uci get macchina.macchina.domain", $domain);
        exec("sudo /usr/local/bin/uci get macchina.macchina.device_id", $device_id);
        exec("sudo /usr/local/bin/uci get macchina.macchina.url", $url);
        exec("sudo /usr/local/bin/uci get macchina.macchina.enabled_http", $enabled_http);
        exec("sudo /usr/local/bin/uci get macchina.macchina.enabled_ssh", $enabled_ssh);
    }

    exec("pgrep WebTunnelAgent", $run_status);
    exec("sudo /usr/local/bin/uci get macchina.macchina.enabled", $enabled);

    echo renderTemplate("macchina", compact(
        'status',
        'domain',
        'run_status',
        'device_id',
        'enabled',
        'url',
        'enabled_http',
        'enabled_ssh'
    ));
}

function saveConfig($status)
{
    $return = true;
    $error = array();

    exec("sudo /usr/local/bin/uci set macchina.macchina.enabled=" . $_POST['enabled']);
    if ($_POST['enabled'] == "1") {
        if ($_POST['domain']) {
            exec("sudo /usr/local/bin/uci set macchina.macchina.domain=" . $_POST['domain']);
            exec("sudo /bin/sed -i 's/webtunnel.domain =.*/webtunnel.domain = " . $_POST['domain'] . "/g' /etc/WebTunnelAgent.properties");
        } else {
            $return = false;
            $status->addMessage('error domain', 'danger');
        }

        if ($_POST['device_id']) {
            exec("sudo /usr/local/bin/uci set macchina.macchina.device_id=" . $_POST['device_id']);
            exec("sudo /bin/sed -i 's/webtunnel.deviceId =.*/webtunnel.deviceId = " . $_POST['device_id'] . "/g' /etc/WebTunnelAgent.properties");
        } else {
            $return = false;
            $status->addMessage('error device_id', 'danger');
        }

        if ($_POST['url']) {
            exec("sudo /usr/local/bin/uci set macchina.macchina.url=" . $_POST['url']);
            exec("sudo /bin/sed -i 's/webtunnel.reflectorURI =.*/webtunnel.reflectorURI = https:\/\/" . $_POST['url'] . "/g' /etc/WebTunnelAgent.properties");
        } else {
            $return = false;
            $status->addMessage('error url', 'danger');
        }

        exec("sudo /usr/local/bin/uci set macchina.macchina.enabled_http=" . $_POST['enabled_http']);
        exec("sudo /usr/local/bin/uci set macchina.macchina.enabled_ssh=" . $_POST['enabled_ssh']);

        if ($_POST['enabled_http'] == "1" && $_POST['enabled_ssh'] == "1") {
            exec("sudo /bin/sed -i 's/webtunnel.ports =.*/webtunnel.ports = 22, 80/g' /etc/WebTunnelAgent.properties");
        } else if ($_POST['enabled_http'] == "1") {
            exec("sudo /bin/sed -i 's/webtunnel.ports =.*/webtunnel.ports = 80/g' /etc/WebTunnelAgent.properties");
        } else if ($_POST['enabled_ssh'] == "1") {
            exec("sudo /bin/sed -i 's/webtunnel.ports =.*/webtunnel.ports = 22/g' /etc/WebTunnelAgent.properties");
        } else {
            exec("sudo /bin/sed -i 's/webtunnel.ports =.*/webtunnel.ports = /g' /etc/WebTunnelAgent.properties");
        }
    }

    exec("sudo /usr/local/bin/uci commit macchina");

    $status->addMessage('Macchina configuration updated', 'success');
    return $return;
}

