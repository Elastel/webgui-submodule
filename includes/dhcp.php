<?php

require_once 'config.php';

/**
 * Manage DHCP configuration
 */
function DisplayDHCPConfig()
{
    $model = getModel();
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savedhcpdsettings']) || isset($_POST['applydhcpdsettings'])) {
            saveDHCPConfig($status);
            
            if (isset($_POST['applydhcpdsettings'])) {
                //exec('sudo /bin/systemctl restart dnsmasq.service', $dnsmasq, $return);
                    exec('sudo ip addr flush dev br0'); // clear ip caches
                    exec('sudo /etc/raspap/hostapd/servicestart.sh --interface br0 --seconds 3', $return);
                if ($model == 'EG324L' || $model == 'EC212') {
                    exec('/etc/init.d/S80dnsmasq restart; sleep 1; /etc/init.d/S80dhcpcd restart', $return);
                }
            }
        }
    }

    // foreach ($return as $line) {
    //     $status->addMessage($line, 'info');
    // }
    exec('pidof dnsmasq | wc -l', $dnsmasq);
    $dnsmasq_state = ($dnsmasq[0] > 0);

    getWifiInterface();
    $ap_iface = "br0";
    $serviceStatus = $dnsmasq_state ? 'up' : 'down';
    // exec('cat '. RASPI_DNSMASQ_PREFIX.'raspap.conf', $return);
    // $conf = ParseConfig($return);
    exec('cat '. RASPI_DNSMASQ_PREFIX.$ap_iface.'.conf', $return);
    $conf = array_merge(ParseConfig($return));
    $hosts = array();
    $upstreamServers = array();
    if (array_key_exists('dhcp-host', $conf)) {
        $hosts = (array)$conf['dhcp-host'];
    }
    if (array_key_exists('server', $conf)) {
        $upstreamServers = (array)$conf['server'];
    }

    exec('cat ' . RASPI_DNSMASQ_LEASES, $leases);

    exec('uci get network.lan.mac', $mac_conf);
    if ($mac_conf[0] != '') {
        $lan_mac = $mac_conf[0];
    } else {
        $lan_mac = exec('cat /sys/class/net/br0/address');
    }

    $interfaces = ['br0'];
    echo renderTemplate(
        'dhcp', compact(
            'status',
            'serviceStatus',
            'dnsmasq_state',
            'ap_iface',
            'conf',
            'hosts',
            'upstreamServers',
            'interfaces',
            'leases',
            'lan_mac'
        )
    );
}

/**
 * Saves a DHCP configuration
 *
 * @return object $status
 */
function saveDHCPConfig($status)
{
    $iface = $_POST['interface'];
    $return = 1;

    // handle disable dhcp option
    if (!isset($_POST['dhcp-iface']) && file_exists(RASPI_DNSMASQ_PREFIX.$iface.'.conf')) {
        // remove dhcp + dnsmasq configs for selected interface
        //$return = removeDHCPConfig($iface,$status);
        $return = removeDnsmasqConfig($iface,$status);
    } else {
        $errors = validateDHCPInput();
        if (empty($errors)) {
            $return = updateDHCPConfig($iface,$status);
        } else {
            $status->addMessage($errors, 'danger');
        }
        if ($return == 1) {
            $status->addMessage('Dnsmasq configuration failed to be updated.', 'danger');
            return false;
        }

        if (($_POST['dhcp-iface'] == '1')) {
            $return = updateDnsmasqConfig($iface,$status);
        }
        if ($return == 0) {
            $status->addMessage('Dnsmasq configuration updated successfully.', 'success');
        } else {
            $status->addMessage('Dnsmasq configuration failed to be updated.', 'danger');
            return false;
        }
        return true;
    }
}

/**
 * Validates DHCP user input from the $_POST object
 *
 * @return string $errors
 */
function validateDHCPInput()
{
    $errors = '';
    define('IFNAMSIZ', 16);
    $iface = $_POST['interface'];
    if (!preg_match('/^[a-zA-Z0-9]+$/', $iface)
        || strlen($iface) >= IFNAMSIZ
    ) {
        $errors .= _('Invalid interface name.').'<br />'.PHP_EOL;
    }
    if (!filter_var($_POST['StaticIP'], FILTER_VALIDATE_IP) && !empty($_POST['StaticIP'])) {
        $errors .= _('Invalid static IP address.').'<br />'.PHP_EOL;
    }
    if (!filter_var($_POST['SubnetMask'], FILTER_VALIDATE_IP) && !empty($_POST['SubnetMask'])) {
        $errors .= _('Invalid subnet mask.').'<br />'.PHP_EOL;
    }
    if (!filter_var($_POST['DefaultGateway'], FILTER_VALIDATE_IP) && !empty($_POST['DefaultGateway'])) {
        $errors .= _('Invalid default gateway.').'<br />'.PHP_EOL;
        var_dump($_POST['DefaultGateway']);
        die();
    }
    if (($_POST['dhcp-iface'] == '1')) {
        if (!filter_var($_POST['RangeStart'], FILTER_VALIDATE_IP) && !empty($_POST['RangeStart'])) {
            $errors .= _('Invalid DHCP range start.').'<br />'.PHP_EOL;
        }
        if (!filter_var($_POST['RangeEnd'], FILTER_VALIDATE_IP) && !empty($_POST['RangeEnd'])) {
            $errors .= _('Invalid DHCP range end.').'<br />'.PHP_EOL;
        }
        if (!ctype_digit($_POST['RangeLeaseTime']) && $_POST['RangeLeaseTimeUnits'] !== 'infinite') {
            $errors .= _('Invalid DHCP lease time, not a number.').'<br />'.PHP_EOL;
        }
        if (!in_array($_POST['RangeLeaseTimeUnits'], array('m', 'h', 'd', 'infinite'))) {
            $errors .= _('Unknown DHCP lease time unit.').'<br />'.PHP_EOL;
        }
        // if ($_POST['Metric'] !== '' && !ctype_digit($_POST['Metric'])) {
        //     $errors .= _('Invalid metric value, not a number.').'<br />'.PHP_EOL;
        // }
    }
    return $errors;
}

/**
 * Compares to string IPs
 *
 * @param string $ip1
 * @param string $ip2
 * @return boolean $result
 */
function compareIPs($ip1, $ip2)
{
    $ipu1 = sprintf('%u', ip2long($ip1['ip'])) + 0;
    $ipu2 = sprintf('%u', ip2long($ip2['ip'])) + 0;
    return $ipu1 > $ipu2;
}

/**
 * Updates a dnsmasq configuration
 *
 * @param string $iface
 * @param object $status
 * @return boolean $result
 */
function updateDnsmasqConfig($iface,$status)
{
    $config = '# RaspAP '.$iface.' configuration'.PHP_EOL;
    $config .= 'interface='.$iface.PHP_EOL.'dhcp-range='.$_POST['RangeStart'].','.$_POST['RangeEnd'].','.$_POST['SubnetMask'].',';
    if ($_POST['RangeLeaseTimeUnits'] !== 'infinite') {
        $config .= $_POST['RangeLeaseTime'];
    }
    $config .= $_POST['RangeLeaseTimeUnits'].PHP_EOL;
    //  Static leases
    $staticLeases = array();
    if (isset($_POST["static_leases"]["mac"])) {
        for ($i=0; $i < count($_POST['static_leases']['mac']); $i++) {
            $mac = trim($_POST['static_leases']['mac'][$i]);
            $ip  = trim($_POST['static_leases']['ip'][$i]);
            $comment  = trim($_POST['static_leases']['comment'][$i]);
            if ($mac != '' && $ip != '') {
                $staticLeases[] = array('mac' => $mac, 'ip' => $ip, 'comment' => $comment);
            }
        }
    }
    
    //  Sort ascending by IPs
    usort($staticLeases, 'compareIPs');
    //  Update config
    for ($i = 0; $i < count($staticLeases); $i++) {
        $mac = $staticLeases[$i]['mac'];
        $ip = $staticLeases[$i]['ip'];
        $comment = $staticLeases[$i]['comment'];
        $config .= "dhcp-host=$mac,$ip # $comment".PHP_EOL;
    } 
    if ($_POST['no-resolv'] == '1') {
        $config .= 'no-resolv'.PHP_EOL;
    }

    if (isset($_POST['server'])) {
        foreach ($_POST['server'] as $server) {
            $config .= "server=$server".PHP_EOL;
        }
    }
    
    if ($_POST['DNS1']) {
        $config .= 'dhcp-option=6,' . $_POST['DNS1'];
        if ($_POST['DNS2']) {
            $config .= ','.$_POST['DNS2'];
        }
        $config .= PHP_EOL;
    }
    file_put_contents('/tmp/dnsmasqdata', $config);
    $msg = file_exists(RASPI_DNSMASQ_PREFIX.$iface.'.conf') ? 'updated' : 'added';
    system('sudo cp /tmp/dnsmasqdata '.RASPI_DNSMASQ_PREFIX.$iface.'.conf', $result);
    if ($result == 0) {
        $status->addMessage('Dnsmasq configuration for '.$iface.' '.$msg.'.', 'success');
    }

    // write default 090_raspap.conf
    $config = '# RaspAP default config'.PHP_EOL;
    $config .='log-facility='.RASPI_DHCPCD_LOG.PHP_EOL;
    $config .='conf-dir=/etc/dnsmasq.d'.PHP_EOL;
    // handle log option
    if ($_POST['log-dhcp'] == '1') {
        $config .= 'log-dhcp'.PHP_EOL;
    }
    if ($_POST['log-queries'] == '1') {
      $config .= 'log-queries'.PHP_EOL;
    }
    $config .= PHP_EOL;
    file_put_contents('/tmp/dnsmasqdata', $config);
    system('sudo cp /tmp/dnsmasqdata '.RASPI_DNSMASQ_PREFIX.'raspap.conf', $result);

    return $result;
}

/**
 * Updates a dhcp configuration
 *
 * @param string $iface
 * @param object $status
 * @return boolean $result
 */
function updateDHCPConfig($iface,$status)
{
    $cfg[] = '# RaspAP '.$iface.' configuration';
    $cfg[] = 'interface '.$iface;
    if (isset($_POST['StaticIP'])) {
        $mask = ($_POST['SubnetMask'] !== '' && $_POST['SubnetMask'] !== '0.0.0.0') ? '/'.mask2cidr($_POST['SubnetMask']) : null;
        $cfg[] = 'static ip_address='.$_POST['StaticIP'].$mask;
        exec('sudo /usr/local/bin/uci set network.lan.ip='.$_POST['StaticIP']);
        exec('sudo /usr/local/bin/uci set network.lan.netmask='.$_POST['SubnetMask']);
    }
    if (isset($_POST['DefaultGateway'])) {
      $cfg[] = 'static routers='.$_POST['DefaultGateway'];
    }
    if (filter_var($_POST['lan_mac'], FILTER_VALIDATE_MAC)) {
        exec('sudo /usr/local/bin/uci set network.lan.mac=' . $_POST['lan_mac']);
    }
    if ($_POST['DNS1'] !== '' || $_POST['DNS2'] !== '') {
        $cfg[] = 'static domain_name_server='.$_POST['DNS1'].' '.$_POST['DNS2'];
    }
    if ($_POST['Fallback'] == 1) {
        $cfg[] = 'profile static_'.$iface;
        $cfg[] = 'fallback static_'.$iface;
    }
    $cfg[] = 'metric 500';
    // $cfg[] = $_POST['DefaultRoute'] == '1' ? 'gateway' : 'nogateway';
    $dhcp_cfg = file_get_contents(RASPI_DHCPCD_CONFIG);
    if (!preg_match('/^interface\s'.$iface.'$/m', $dhcp_cfg)) {
        $cfg[] = PHP_EOL;
        $cfg = join(PHP_EOL, $cfg);
        $dhcp_cfg .= $cfg;
        $status->addMessage('DHCP configuration for '.$iface.' added.', 'success');
    } else {
        $cfg = join(PHP_EOL, $cfg);
        $dhcp_cfg = preg_replace('/^#\sRaspAP\s'.$iface.'\s.*?(?=\s*^\s*$)/ms', $cfg, $dhcp_cfg, 1);
        $status->addMessage('DHCP configuration for '.$iface.' updated.', 'success');
    }
    file_put_contents('/tmp/dhcpddata', $dhcp_cfg);
    system('sudo cp /tmp/dhcpddata '.RASPI_DHCPCD_CONFIG, $result);
    exec('sudo /usr/local/bin/uci commit network');
    
    return $result;
}

