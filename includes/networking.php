<?php

require_once 'includes/internetRoute.php';

function DisplayNetworkingConfig($type)
{
    $model = getModel();
    $status = new \ElastPro\Messages\StatusMessage;
    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savenetworksettings']) || isset($_POST['applynetworksettings'])) {
            if ($type == 'wired') {
                saveStaticConfig($status);
                if (model_category('no_buildroot')) {
                    if ($_POST['wan-multi'] == '1') {
                        exec('sudo cp /var/www/html/config/raspap-br0-member-eth0.network /etc/systemd/network/');
                    } else {
                        exec('sudo rm /etc/systemd/network/raspap-br0-member-eth0.network');
                    }
                } else {
                    if ($_POST['wan-multi'] == '1') {
                        exec('brctl addif br0 eth0');
                    } else {
                        exec('brctl delif br0 eth0');
                    }
                }
            } elseif ($type == 'lte') {
                saveLteConfig($status);
            } elseif ($type == 'wlan0') {
                saveWlan0Config($status);
            }
            $status->addMessage('Configuration updated.', 'success');

            exec('sudo /usr/local/bin/uci commit network');

            if (isset($_POST['applynetworksettings'])) {
                if ($type == 'wired') {
                    if (model_category('no_buildroot')) {
                        $cur_wired_mac = file_get_contents('/sys/class/net/eth0/address');
                        if ($cur_wired_mac != $_POST['wired_mac']) {
                            exec('sudo ifconfig eth0 down');
                            exec('sudo ifconfig eth0 hw ether ' . $_POST['wired_mac']);
                            exec('sudo ifconfig eth0 up');
                        }

                        if ($_POST['wan-multi'] == '1') {
                            exec('sudo systemctl restart systemd-networkd.service');
                        } else {
                            exec('sudo systemctl restart systemd-networkd.service');
                            exec('sudo brctl delif br0 eth0');
                            exec('sudo systemctl stop dhcpcd.service');
                            sleep(1);
                            exec('sudo systemctl start dhcpcd.service');
                        }
                    } else {
                        exec('sudo ip addr flush dev eth0');
                        sleep(1);
                        exec('sudo /etc/init.d/S80dhcpcd restart');
                    }
                    
                    if ($_POST['adapter-ip'] == '0') {
                        // add dns to resolv.conf
                        if ($_POST['DNS1'] !== '' || $_POST['DNS2'] !== '') {
                            $orgin_data = file_get_contents('/etc/resolv.conf');
                            $new_data .= $orgin_data;
                            $new_data .= 'nameserver ' . $_POST['DNS1'] . PHP_EOL . 'nameserver ' . $_POST['DNS2'] . PHP_EOL;
                            file_put_contents('/tmp/resolv.conf', $new_data);
                            system('sudo cp /tmp/resolv.conf /etc/resolv.conf');
                        }

                        exec('sudo ifconfig eth0 down');
                        sleep(2);
                        exec('sudo ifconfig eth0 up ' . $_POST['StaticIP']);
                    }
                } elseif ($type == 'lte') {
                    exec('sudo /etc/init.d/lte restart > /dev/null');
                } elseif ($type == 'wlan0') {
                    exec('sudo dhcpcd -n wlan0');
                }

                $status->addMessage('Configuration applied.', 'success');
                exec('sudo /etc/init.d/failover restart > /dev/null');
            }
        }
    }

    $wired_interface = ['eth0'];
    $wlan0_interface = ['wlan0'];
    $lte_interface = '';
    $lte_enabled = 0;
    $wired_mac = '';
    $lte_mac = '';
    $wlan0_mac = '';

    if ($type == 'wired') {
        exec('uci get network.wan.mac', $mac_conf);
        if ($mac_conf[0] != '') {
            $wired_mac = $mac_conf[0];
        } else {
            $wired_mac = file_get_contents('/sys/class/net/eth0/address');
        }
    } elseif ($type == 'lte') {
        exec('ls /sys/class/net | grep -v lo', $interfaces);
        exec("uci get network.swan.ifname", $cur_interface);
        foreach( $interfaces as $k=>$v) {
            if($v == $cur_interface[0]) {
                $lte_enabled = 1;
                $lte_interface = [$cur_interface[0]];
            }
        }
        $lte_mac = file_get_contents("/sys/class/net/$cur_interface[0]/address");
    } else if ($type == 'wlan0') {
        exec('uci get network.wifi.mac', $mac_conf);
        if ($mac_conf[0] != '') {
            $wlan0_mac = $mac_conf[0];
        } else {
            $wlan0_mac = file_get_contents('/sys/class/net/wlan0/address');
        }
    }

    // $routeInfo = getRouteInfo(true);
    echo renderTemplate('networking', compact(
        'status',
        'type',
        'wired_interface',
        'lte_interface',
        'wlan0_interface',
        //'routeInfo',
        'lte_enabled',
        'wired_mac',
        'lte_mac',
        'wlan0_mac'
    ));
}

/**
 * Saves a static ip configuration
 *
 * @return object $status
 */
function saveStaticConfig($status)
{
    $iface0 = $_POST['interface0'];
    $return = 1;

    if ($iface0 == 'eth0') {
        exec('sudo uci set network.wan.device=eth0');
        if ($_POST['Metric'] !== '') {
            exec('sudo /usr/local/bin/uci set network.wan.metric=' . $_POST['Metric']);
        } else {
            exec('sudo /usr/local/bin/uci set network.wan.metric=202');
        }

        if (filter_var($_POST['wired_mac'], FILTER_VALIDATE_MAC)) {
            exec('sudo /usr/local/bin/uci set network.wan.mac=' . $_POST['wired_mac']);
        }

        if ($_POST['wan-multi'] == '1') {
            exec('sudo uci set network.wan.wan_multi=1');
        } else {
            exec('sudo uci set network.wan.wan_multi=0');
        }

        // handle disable dhcp option
        if ($_POST['adapter-ip'] == '1') {
            exec('sudo uci set network.wan.proto=dhcp');
            updateDHCPConfigMetric($iface0, '', $status);
        } else {
            //$status->addMessage('updateDHCPConfig');
            exec('sudo uci set network.wan.proto=static');
            $errors = validateDHCPInputNetwork($iface0, '');
            if (empty($errors)) {
                $return = updateDHCPConfigNetwork($iface0, '', $status);
            } else {
                $status->addMessage($errors, 'danger');
            }
            return true;
        }
    }
}

function saveLteConfig($status)
{
    $iface = $_POST['interface'];
    $return = 1;

    if ($iface == 'wwan0' || $iface == 'usb0' || strpos($iface, 'eth') !== false) {
        $auth_type = $_POST['auth_type'];
        $error = array();

        if ($auth_type != 'none') {
            $errors = validateLteInputNetwork();
        }

        if (empty($errors)) {
            $return = updateLteConfigNetwork($iface, $status);
        } else {
            $status->addMessage($errors, 'danger');
        }

        return true;
    }
}

function saveWlan0Config($status)
{
    $iface = $_POST['wlan0_interface'];
    $return = 1;
    if ($iface == 'wlan0') {
        exec('sudo uci set network.wifi.device=wlan0');;
        if ($_POST['wlan0_Metric'] !== '') {
            exec('sudo /usr/local/bin/uci set network.wifi.metric=' . $_POST['wlan0_Metric']);
        } else {
            exec('sudo /usr/local/bin/uci set network.wifi.metric=203');
        }

        if (filter_var($_POST['wlan0_mac'], FILTER_VALIDATE_MAC)) {
            exec('sudo /usr/local/bin/uci set network.wifi.mac=' . $_POST['wlan0_mac']);
        }

        // handle disable dhcp option
        if ($_POST['wlan0_adapter-ip'] == '1') {
            exec('sudo uci set network.wifi.proto=dhcp');
            updateDHCPConfigMetric($iface, 'wlan0_', $status);
        } else {
            //$status->addMessage('updateDHCPConfig');
            exec('sudo uci set network.wifi.proto=static');
            $errors = validateDHCPInputNetwork($iface, 'wlan0_');
            if (empty($errors)) {
                $return = updateDHCPConfigNetwork($iface, 'wlan0_', $status);
            } else {
                $status->addMessage($errors, 'danger');
            }
            return true;
        }
    }
}

/**
 * Validates DHCP user input from the $_POST object
 *
 * @return string $errors
 */
function validateDHCPInputNetwork($iface0, $head)
{
    define('IFNAMSIZ', 16);
    if (!preg_match('/^[a-zA-Z0-9]+$/', $iface0)
        || strlen($iface0) >= IFNAMSIZ
    ) {
        $errors .= _('Invalid interface name.').'<br />'.PHP_EOL;
    }
    if (!filter_var($_POST[$head.'StaticIP'], FILTER_VALIDATE_IP) && !empty($_POST[$head.'StaticIP'])) {
        $errors .= _('Invalid static IP address.').'<br />'.PHP_EOL;
    }
    if (!filter_var($_POST[$head.'SubnetMask'], FILTER_VALIDATE_IP) && !empty($_POST[$head.'SubnetMask'])) {
        $errors .= _('Invalid subnet mask.').'<br />'.PHP_EOL;
    }
    if (!filter_var($_POST[$head.'DefaultGateway'], FILTER_VALIDATE_IP) && !empty($_POST[$head.'DefaultGateway'])) {
        $errors .= _('Invalid default gateway.').'<br />'.PHP_EOL;
        var_dump($_POST[$head.'DefaultGateway']);
        die();
    }

    return $errors;
}

function validateLteInputNetwork()
{
    define('IFNAMSIZ', 16);
    $iface = $_POST['interface'];
    if (!preg_match('/^[a-zA-Z0-9]+$/', $iface)
        || strlen($iface) >= IFNAMSIZ
    ) {
        $errors .= _('Invalid interface name.').'<br />'.PHP_EOL;
    }
    if (empty($_POST['username'])) {
        $errors .= _('Invalid PAP/CHAP username.').'<br />'.PHP_EOL;
    }
    if (empty($_POST['password'])) {
        $errors .= _('Invalid PAP/CHAP password.').'<br />'.PHP_EOL;
    }

    return $errors;
}

/**
 * Updates a dhcp configuration
 *
 * @param string $iface0
 * @param object $status
 * @return boolean $result
 */
function updateDHCPConfigNetwork($iface0, $head, $status)
{
    $keyword = 'ElastPro';
    $orgin_str = file_get_contents(RASPI_DHCPCD_CONFIG);
    if (strpos($orgin_str, $keyword) == false) {
        $keyword = 'RaspAP';
    }

    $cfg[] = "# $keyword ".$iface0.' configuration';
    $cfg[] = 'interface '.$iface0;

    if (isset($_POST[$head.'StaticIP'])) {
        $mask = ($_POST[$head.'SubnetMask'] !== '' && $_POST[$head.'SubnetMask'] !== '0.0.0.0') ? '/'.mask2cidr($_POST[$head.'SubnetMask']) : null;
        $cfg[] = 'static ip_address='.$_POST[$head.'StaticIP'].$mask;
    }
    if (isset($_POST[$head.'DefaultGateway'])) {
      $cfg[] = 'static routers='.$_POST[$head.'DefaultGateway'];
    }
    if (isset($_POST[$head.'DNS1']) && ($_POST[$head.'DNS1'] !== '' || $_POST[$head.'DNS2'] !== '')) {
        $cfg[] = 'static domain_name_server='.$_POST[$head.'DNS1'].' '.$_POST[$head.'DNS2'];
    }
    if ($_POST[$head.'Metric'] !== '') {
      $cfg[] = 'metric '.$_POST[$head.'Metric'];
    }
    
    if ($_POST[$head.'Fallback'] == 1) {
        $cfg[] = 'profile static_'.$iface0;
        $cfg[] = 'fallback static_'.$iface0;
    }

    // $cfg[] = $_POST[$head.'DefaultRoute'] == '1' ? 'gateway' : 'nogateway';
    if (strlen($head) == 0) {
        exec("sudo /usr/local/bin/uci get wifi.wifi_client.enabled", $tmp);
        $enablewificlient = $tmp[0];
        if ($_POST['wan-multi'] == '1') {
            if ($enablewificlient == '1') {
                $new_deny = 'denyinterfaces eth1 eth0';
            } else {
                $new_deny = 'denyinterfaces eth1 wlan0 eth0';
            }
        } else {
            if ($enablewificlient == '1') {
                $new_deny = 'denyinterfaces eth1';
            } else {
                $new_deny = 'denyinterfaces eth1 wlan0';
            }
        }

        if (preg_match('/^denyinterfaces.*$/m', $orgin_str)) {
            $dhcp_cfg = preg_replace('/^denyinterfaces.*$/m', $new_deny, $orgin_str, 1);
        } else {
            $dhcp_cfg = rtrim($orgin_str) . PHP_EOL . $new_deny . PHP_EOL;
        }
    } else {
        $dhcp_cfg = rtrim($orgin_str) . PHP_EOL;
    }
    

    if (!preg_match('/^interface\s'.$iface0.'$/m', $dhcp_cfg)) {
        $cfg = join(PHP_EOL, $cfg) . PHP_EOL;
        $dhcp_cfg .= $cfg;
        $status->addMessage('Configuration added.', 'success');
    } else {
        $cfg = join(PHP_EOL, $cfg) . PHP_EOL;
        $pattern = "/^#\s$keyword\s" . preg_quote($iface0, '/') . "\sconfiguration.*?(?=^#\s$keyword\s|\z)/ms";
        if (preg_match($pattern, $dhcp_cfg)) {
            $dhcp_cfg = preg_replace($pattern, $cfg, $dhcp_cfg, 1);
        } 
    }

    file_put_contents('/tmp/dhcpddata', $dhcp_cfg);
    system('sudo cp /tmp/dhcpddata '.RASPI_DHCPCD_CONFIG, $result);

    return $result;
}

function updateDHCPConfigMetric($iface0, $head, $status)
{
    $keyword = 'ElastPro';
    $orgin_str = file_get_contents(RASPI_DHCPCD_CONFIG);
    if (strpos($orgin_str, $keyword) == false) {
        $keyword = 'RaspAP';
    }
    
    $cfg[] = "# $keyword ".$iface0.' configuration';
    $cfg[] = 'interface '.$iface0;

    if ($_POST[$head.'Metric'] !== '') {
      $cfg[] = 'metric '.$_POST[$head.'Metric'];
    }

    // $cfg[] = $_POST['DefaultRoute'] == '1' ? 'gateway' : 'nogateway';
    if (strlen($head) == 0) {
        exec("sudo /usr/local/bin/uci get wifi.wifi_client.enabled", $tmp);
        $enablewificlient = $tmp[0];
        if ($_POST['wan-multi'] == '1') {
            if ($enablewificlient == '1') {
                $new_deny = 'denyinterfaces eth1 eth0';
            } else {
                $new_deny = 'denyinterfaces eth1 wlan0 eth0';
            }
        } else {
            if ($enablewificlient == '1') {
                $new_deny = 'denyinterfaces eth1';
            } else {
                $new_deny = 'denyinterfaces eth1 wlan0';
            }
        }

        if (preg_match('/^denyinterfaces.*$/m', $orgin_str)) {
            $dhcp_cfg = preg_replace('/^denyinterfaces.*$/m', $new_deny, $orgin_str, 1);
        } else {
            $dhcp_cfg = rtrim($orgin_str) . PHP_EOL . $new_deny . PHP_EOL;
        }
    } else {
        $dhcp_cfg = rtrim($orgin_str) . PHP_EOL;
    }
    
    if (!preg_match('/^interface\s'.$iface0.'$/m', $dhcp_cfg)) {
        $cfg = join(PHP_EOL, $cfg) . PHP_EOL;
        $dhcp_cfg .= $cfg;
        $status->addMessage('Configuration added.', 'success');
    } else {
        $cfg = join(PHP_EOL, $cfg) . PHP_EOL;
        $pattern = "/^#\s$keyword\s" . preg_quote($iface0, '/') . "\sconfiguration.*?(?=^#\s$keyword\s|\z)/ms";
        if (preg_match($pattern, $dhcp_cfg)) {
            $dhcp_cfg = preg_replace($pattern, $cfg, $dhcp_cfg, 1);
        }
    }
    file_put_contents('/tmp/dhcpddata', $dhcp_cfg);
    system('sudo cp /tmp/dhcpddata '.RASPI_DHCPCD_CONFIG, $result);

    return $result;
}

function updateLteMetric($iface0,$status)
{
    $keyword = 'ElastPro';
    $orgin_str = file_get_contents(RASPI_DHCPCD_CONFIG);
    if (strpos($orgin_str, $keyword) == false) {
        $keyword = 'RaspAP';
    }
    
    $cfg[] = "# $keyword ".$iface0.' configuration';
    $cfg[] = 'interface '.$iface0;

    if ($_POST['lte_metric'] !== '') {
      $cfg[] = 'metric '.$_POST['lte_metric'];
    }

    $dhcp_cfg = rtrim($orgin_str) . PHP_EOL;
    if (!preg_match('/^interface\s'.$iface0.'$/m', $dhcp_cfg)) {
        $cfg = join(PHP_EOL, $cfg) . PHP_EOL;
        $dhcp_cfg .= $cfg;
        $status->addMessage('Configuration added.', 'success');
    } else {
        $cfg = join(PHP_EOL, $cfg) . PHP_EOL;
        $pattern = "/^#\s$keyword\s" . preg_quote($iface0, '/') . "\sconfiguration.*?(?=^#\s$keyword\s|\z)/ms";
        if (preg_match($pattern, $dhcp_cfg)) {
            $dhcp_cfg = preg_replace($pattern, $cfg, $dhcp_cfg, 1);
        }
    }
    file_put_contents('/tmp/dhcpddata', $dhcp_cfg);
    system('sudo cp /tmp/dhcpddata '.RASPI_DHCPCD_CONFIG, $result);

    return $result;
}

function updateLteConfigNetwork($iface0, $status)
{
    if ($_POST['lte_metric'] != '') {
        exec('sudo /usr/local/bin/uci set network.swan.metric=' .$_POST['lte_metric']);
    } else {
        exec('sudo /usr/local/bin/uci set network.swan.metric=207');
    }

    exec('sudo /usr/local/bin/uci set network.swan.apn=' .$_POST['apn']);
    exec('sudo /usr/local/bin/uci set network.swan.pincode=' .$_POST['pin']);
    exec('sudo /usr/local/bin/uci set network.swan.auth=' .$_POST['auth_type']);
    if ($_POST['auth_type'] == 'none') {
        exec('sudo /usr/local/bin/uci delete network.swan.username');
        exec('sudo /usr/local/bin/uci delete network.swan.password');
    } else {
        exec('sudo /usr/local/bin/uci set network.swan.username=' .$_POST['username']);
        exec('sudo /usr/local/bin/uci set network.swan.password=' .$_POST['password']);
    }
    exec('sudo /usr/local/bin/uci set network.swan.data_saving_mode=' .$_POST['data_saving_mode']);

    updateLteMetric($iface0, $status);

    return $result;
}
