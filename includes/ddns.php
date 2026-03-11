<?php

require_once 'config.php';

function DisplayDDNS()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['saveddnssettings']) || isset($_POST['applyddnssettings'])) {
            $ret = saveDDNSConfig($status);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applyddnssettings'])) {
                    if ($_POST['enabled'] == '0') {
                        exec('sudo /etc/init.d/ddns stop');
                    } else {
                        exec("ip route | grep default | awk {'print $5'}", $interface);
                        $total_interface = sizeof($interface);

                        if ($_POST['interval'] == NULL) {
                            $interval = 30;
                        } else {
                            $interval = $_POST['interval'];
                        }

                        exec('sudo rm /etc/ddns.conf');
                        if ($total_interface > 0) {
                            exec('sudo /etc/init.d/ddns stop');
                            exec('sudo /usr/sbin/noip2 -C -c /etc/ddns.conf' .
                            " -u '" . $_POST['username'] . 
                            "' -p '" . $_POST['password'] . 
                            "' -I " . $_POST['interface'] . 
                            ' -H ' . $_POST['hostname'] .
                            ' -U ' . $interval . 
                            ' > /dev/null');
                            sleep(2);
                            if (!is_file('/etc/ddns.conf')) {
                                $status->addMessage("Failed to restart DDNS", 'danger');
                            } else {
                                exec('sudo /etc/init.d/ddns restart');
                            }
                        } else {
                            $status->addMessage("No network, failed to restart DDNS.", 'danger');
                        }  
                    }   
                }
            }
        }
    }

    echo renderTemplate("ddns", compact('status'));
}

function saveDDNSConfig($status)
{

    $return = 1;
    $error = array();

    exec("sudo /usr/local/bin/uci set ddns.ddns.enabled=" . $_POST['enabled']);
    if ($_POST['enabled'] == "1") {
        exec("sudo /usr/local/bin/uci set ddns.ddns.interface=" .$_POST['interface']);
        exec("sudo /usr/local/bin/uci set ddns.ddns.server_type=" .$_POST['server_type']);
        exec("sudo /usr/local/bin/uci set ddns.ddns.username=" .$_POST['username']);
        exec("sudo /usr/local/bin/uci set ddns.ddns.password='" .$_POST['password'] . "'");
        exec("sudo /usr/local/bin/uci set ddns.ddns.interval=" .$_POST['interval']);
        exec("sudo /usr/local/bin/uci set ddns.ddns.hostname=" .$_POST['hostname']);
        if ($_POST['username'] == NULL || $_POST['password'] == NULL) {
            return false;
        }
    }
    
    exec("sudo /usr/local/bin/uci commit ddns");

    $status->addMessage('DDNS configuration updated ', 'success');
    return true;
}

