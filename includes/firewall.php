<?php

require_once 'includes/functions.php';

function saveFirewallConfig()
{
    /*general*/
    $arrGeneral = array('synflood_protect', 'drop_invalid', 'input', 'output', 'forward');

    foreach ($arrGeneral as $info) {
        exec("sudo /usr/local/bin/uci set firewall.general.$info=$_POST[$info]");
    }

    /*forwards*/
    // $arrForwards = array("name", "proto", "src_port", "dest_ip", "dest_port", "enabled");
    $data = $_POST['table_forwards_data'];
    $arrForwards = json_decode($data, true);

    exec("sudo /usr/sbin/uci_get_count firewall forwards", $countForwards);

    if ($countForwards[0] == null || strlen($countForwards[0]) <= 0) {
        $countForwards[0] = 0;
    }
    $i = 0;
    foreach ($arrForwards as $list=>$things) {
        if (is_array($things)) {
            exec("sudo /usr/local/bin/uci delete firewall.@forwards[$i]");
            exec("sudo /usr/local/bin/uci add firewall forwards");
            foreach ($things as $key=>$val) {
                if ($val == '-') {
                    continue;
                }
                
                if ($key == "enabled") {
                    if ($val == "true") {
                        exec("sudo /usr/local/bin/uci set firewall.@forwards[$i].$key=1");
                    } else {
                        exec("sudo /usr/local/bin/uci set firewall.@forwards[$i].$key=0");
                    }
                } else {
                    exec("sudo /usr/local/bin/uci set firewall.@forwards[$i].$key='$val'");
                }  
            }
        }
        $i++;
    }

    if (number_format($countForwards[0]) > $i) {
        for ($j = $i; $j < number_format($countForwards[0]); $j++) {
            exec("sudo /usr/local/bin/uci delete firewall.@forwards[$i]");
        }
    }

    /*traffic*/
    // $arrTraffic = array("name", "proto", "src", "src_mac", "src_ip", "src_port", "dest", 
    //                 "dest_ip", "dest_port", "action", "enabled");

    $data = $_POST['table_traffic_data'];
    $arrTraffic = json_decode($data, true);

    exec("sudo /usr/sbin/uci_get_count firewall traffic", $countTraffic);

    if ($countTraffic[0] == null || strlen($countTraffic[0]) <= 0) {
        $countTraffic[0] = 0;
    }
    $i = 0;
    foreach ($arrTraffic as $list=>$things) {
        if (is_array($things)) {
            exec("sudo /usr/local/bin/uci delete firewall.@traffic[$i]");
            exec("sudo /usr/local/bin/uci add firewall traffic");
            foreach ($things as $key=>$val) {
                if ($val == '-') {
                    continue;
                }
                
                if ($key == "enabled") {
                    if ($val == "true") {
                        exec("sudo /usr/local/bin/uci set firewall.@traffic[$i].$key=1");
                    } else {
                        exec("sudo /usr/local/bin/uci set firewall.@traffic[$i].$key=0");
                    }
                } else {
                    exec("sudo /usr/local/bin/uci set firewall.@traffic[$i].$key='$val'");
                }  
            }
        }
        $i++;
    }

    if (number_format($countTraffic[0]) > $i) {
        for ($j = $i; $j < number_format($countTraffic[0]); $j++) {
            exec("sudo /usr/local/bin/uci delete firewall.@traffic[$i]");
        }
    }
}

/**
 *
 */
function DisplayFirewall()
{
    $status = new \ElastPro\Messages\StatusMessage;
    if (isset($_POST['savefirewallsettings']) || isset($_POST['applyfirewallsettings'])) {
        saveFirewallConfig($status);
        exec('sudo /usr/local/bin/uci commit firewall');
        
        if (isset($_POST['applyfirewallsettings'])) {
            exec('sudo /etc/init.d/firewall restart');
        }
    }

    echo renderTemplate(
        "firewall", compact(
            "status"
        )
    );
}

