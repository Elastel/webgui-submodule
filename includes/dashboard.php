<?php

require_once 'includes/config.php';
require_once 'includes/wifi_functions.php';
require_once 'includes/functions.php';

function timeCalculation($seconds)
{
    $day = $seconds > 86400 ? floor($seconds / 86400) : 0;
    $seconds -= $day * 86400;
    $hour = $seconds > 3600 ? floor($seconds / 3600) : 0;
    $seconds -= $hour * 3600;
    $minute = $seconds > 60 ? floor($seconds / 60) : 0;
    $seconds -= $minute * 60;
    $second = $seconds;
 
    $dayText = $day ? $day . ' day ' : '';
    $hourText = $hour ? $hour . ' hours ' : '';
    $minuteText = $minute ? $minute . ' minutes ' : '';
    // $date = $dayText . $hourText . $minuteText . $second . 's';
    $date = $dayText . $hourText . $minuteText;

    $date = $date ?? '-';

    return $date;
}

function get_revison()
{
    $dev_model = getModel();
    // Lookup table from http://www.raspberrypi-spy.co.uk/2012/09/checking-your-raspberry-pi-board-version/
    if ($dev_model != "EG324" && $dev_model != "EC212") {
        $revisions = array(
        '0002' => 'Model B Revision 1.0',
        '0003' => 'Model B Revision 1.0 + ECN0001',
        '0004' => 'Model B Revision 2.0 (256 MB)',
        '0005' => 'Model B Revision 2.0 (256 MB)',
        '0006' => 'Model B Revision 2.0 (256 MB)',
        '0007' => 'Model A',
        '0008' => 'Model A',
        '0009' => 'Model A',
        '000d' => 'Model B Revision 2.0 (512 MB)',
        '000e' => 'Model B Revision 2.0 (512 MB)',
        '000f' => 'Model B Revision 2.0 (512 MB)',
        '0010' => 'Model B+',
        '0013' => 'Model B+',
        '0011' => 'Compute Module',
        '0012' => 'Model A+',
        'a01041' => 'a01041',
        'a21041' => 'a21041',
        '900092' => 'PiZero 1.2',
        '900093' => 'PiZero 1.3',
        '9000c1' => 'PiZero W',
        'a02082' => 'Pi 3 Model B',
        'a22082' => 'Pi 3 Model B',
        'a32082' => 'Pi 3 Model B',
        'a52082' => 'Pi 3 Model B',
        'a020d3' => 'Pi 3 Model B+',
        'a220a0' => 'Compute Module 3',
        'a020a0' => 'Compute Module 3',
        'a02100' => 'Compute Module 3+',
        'a03111' => 'Model 4B Revision 1.1 (1 GB)',
        'b03111' => 'Model 4B Revision 1.1 (2 GB)',
        'c03111' => 'Model 4B Revision 1.1 (4 GB)'
        );

        $cpuinfo_array = '';
        exec('cat /proc/cpuinfo', $cpuinfo_array);
        $rev = trim(array_pop(explode(':', array_pop(preg_grep("/^Revision/", $cpuinfo_array)))));
        if (array_key_exists($rev, $revisions)) {
            return $revisions[$rev];
        } else {
            exec('cat /proc/device-tree/model', $model);
            if (isset($model[0])) {
                return $model[0];
            } else {
                return 'Unknown Device';
            }
        }
    } else {
        exec('cat /proc/cpuinfo', $cpuinfo_array);
        $rev = trim(array_pop(explode(':', array_pop(preg_grep("/^model name/", $cpuinfo_array)))));
        if ($rev == null || $rev == '') {
            $rev = trim(array_pop(explode(':', array_pop(preg_grep("/^Processor/", $cpuinfo_array)))));
        }
        return $rev;
    }
}

function getInterfaceMetric($iface) {
    $routes = shell_exec("ip route");

    foreach (explode("\n", $routes) as $line) {
        if (strpos($line, 'default') !== false && strpos($line, "dev $iface") !== false) {
            if (preg_match('/metric (\d+)/', $line, $matches)) {
                return $matches[1];
            } else {
                return '0';
            }
        }
    }

    return null;
}

/**
 * Show dashboard page.
 */
function DisplayDashboard(&$extraFooterScripts)
{
    getWifiInterface();
    $status = new \ElastPro\Messages\StatusMessage;
    // Need this check interface name for proper shell execution.
    if (!preg_match('/^([a-zA-Z0-9]+)$/', $_SESSION['wifi_client_interface'])) {
        $status->addMessage(_('Interface name invalid.'), 'danger');
        $status->showMessages();
        return;
    }

    if (!function_exists('exec')) {
        $status->addMessage(_('Required exec function is disabled. Check if exec is not added to php disable_functions.'), 'danger');
        $status->showMessages();
        return;
    }
    exec('ip a show '.$_SESSION['ap_interface'], $stdoutIp);
    $stdoutIpAllLinesGlued = implode(" ", $stdoutIp);
    $stdoutIpWRepeatedSpaces = preg_replace('/\s\s+/', ' ', $stdoutIpAllLinesGlued);

    preg_match('/state (UP|DOWN)/i', $stdoutIpWRepeatedSpaces, $matchesState) || $matchesState[1] = 'unknown';
    $interfaceState = $matchesState[1];

    // brought in from template
    $clients = array();
    $moreLink = array();
    $apInterface = array();
    if (file_exists(RASPI_CONFIG.'/hostapd.ini')) {
        $arrHostapdConf = parse_ini_file(RASPI_CONFIG.'/hostapd.ini');
        $bridgedEnable = $arrHostapdConf['BridgedEnable'];
        $clientInterface = $_SESSION['wifi_client_interface'];
        $apInterface = $_SESSION['ap_interface'];
        $MACPattern = '"([[:xdigit:]]{2}:){5}[[:xdigit:]]{2}"';

        $moreLink = "dhcpd_conf";
        // exec('cat ' . RASPI_DNSMASQ_LEASES . '| grep -E $(iw dev ' . $apInterface . ' station dump | grep -oE ' . $MACPattern . ' | paste -sd "|")', $clients);
    }

    exec('uci get network.swan.ifname', $lte_ifname);
    exec("sudo uci get -P /var/state/ network.wan.link", $cur_interface);
    if ($cur_interface[0] == "eth0") {
        $ifaceStatus = "Wired";
        $statusIcon = "up";
    } else if ($cur_interface[0] == "wlan0") {
        $ifaceStatus = "WIFI";
        $statusIcon = "up";
    } else if ($cur_interface[0] == $lte_ifname[0]) {
        $ifaceStatus = "LTE";
        $statusIcon = "up";
    } else {
        $ifaceStatus = "No network";
        $statusIcon = "down";
    }
    
    $leases = array();
    exec('cat ' . RASPI_DNSMASQ_LEASES, $leases);
    // fetch dhcpcd.conf settings for interface
    $conf = file_get_contents(RASPI_DHCPCD_CONFIG);
    preg_match('/^#\sRaspAP\seth0\s.*?(?=\s*+$)/ms', $conf, $matched);
    $data = getBetweenStrings($data, "RaspAP");
    preg_match('/metric\s(\d*)/', $data, $metric);
    preg_match('/static\sip_address=(.*)/', $data, $static_ip);
    preg_match('/static\srouters=(.*)/', $data, $static_routers);
    preg_match('/static\sdomain_name_server=(.*)/', $data, $static_dns);
    // preg_match('/fallback\sstatic_'.$interface.'/', $data, $fallback);
    preg_match('/(?:no)?gateway/', $data, $gateway);
    $dhcpdata['Metric'] = $metric[1];
    $dhcpdata['StaticIP'] = strpos($static_ip[1],'/') ?  substr($static_ip[1], 0, strpos($static_ip[1],'/')) : $static_ip[1];
    $dhcpdata['SubnetMask'] = cidr2mask($static_ip[1]);
    $dhcpdata['StaticRouters'] = $static_routers[1];
    $dhcpdata['StaticDNS'] = $static_dns[1];
    if (isset($dhcpdata['StaticDNS'])) {
        $arrStaticDns = explode(" ", $dhcpdata['StaticDNS']);
        if (count($arrStaticDns) == 1) {
            $dhcpdata['StaticDNS1'] = $arrStaticDns[0];
        } else if (count($arrStaticDns) >= 2) {
            $dhcpdata['StaticDNS1'] = $arrStaticDns[0];
            $dhcpdata['StaticDNS2'] = $arrStaticDns[1];
        }
    }

    $routeInfo = getRouteInfo(true);

    exec('ip route | grep "default"  | grep -c "'. $lte_ifname[0] .'"', $enabled);
    $lteInfo = array();
    $lteInfo["enabled"] = $enabled[0];
    if (file_exists("/dev/ttyUSB2")) {
        exec('ifconfig '. $lte_ifname[0] .' | grep -Eo "([0-9]+[.]){3}[0-9]+" | grep -v "255.255."', $ip_address);
        exec('ifconfig '. $lte_ifname[0] .' | grep -Eo "([0-9]+[.]){3}[0-9]+" | grep "255.255."', $netmask);
        exec('uci -P /var/state/ get dangle.dev.signal', $signal);
        exec('uci -P /var/state/ get dangle.dev.service', $operator);
        exec('uci -P /var/state/ get dangle.dev.iccid', $iccid);
        exec('uci -P /var/state/ get dangle.dev.imei', $imei);
		exec('uci -P /var/state/ get dangle.dev.sim', $sim);
        exec('uci -P /var/state/ get dangle.dev.connect', $lte_status);
        exec('uci -P /var/state/ get dangle.dev.uptime', $uptime);
        if ($enabled[0] == '0') {
            $lte_status[0] = "DISCONNECTED";
        }

        $lteInfo["interface"] = $lte_ifname[0];
        $lteInfo["ip_address"] = $ip_address[0] ?? '-';
        $lteInfo["netmask"] = $netmask[0] ?? '-';
        $lteInfo["signal"] = $signal[0] ?? '-';
        $lteInfo["operator"] = $operator[0] ?? '-';
        $lteInfo["iccid"] = $iccid[0] ?? '-';
        $lteInfo["imei"] = $imei[0] ?? '-';
        $lteInfo["lte_status"] = $lte_status[0]  ?? "DISCONNECTED";
		$lteInfo["sim"] = $sim[0] ?? '-';
        $lteInfo["uptime"] = $uptime[0] ? timeCalculation($uptime[0]) : '-';
        $lteInfo["metric"] = getInterfaceMetric($lte_ifname[0]) ?? '-';
    }

    exec("cat /proc/sys/kernel/hostname", $tmp);
    $cur_hostname = $tmp[0];

    $model = getModel();
    unset($tmp);
    exec("cat /etc/fw_date", $tmp);
    $fw_date = $tmp[0];

    unset($tmp);
    exec("uname -r", $tmp);
    $kernel_version = $tmp[0];

    unset($tmp);
    exec("uname -r", $tmp);
    $kernel_version = $tmp[0];

    unset($tmp);
    exec("date '+%Y-%m-%d %H:%M:%S'", $tmp);
    $local_time = $tmp[0];

    unset($tmp);
    if ($model == 'EG324' || $model == 'EG324L' || $model == 'EC212') {
        exec("cat /etc/sn", $tmp);
        $sn = $tmp[0];
    } else {
        exec("cat /proc/cpuinfo | grep Serial | awk -F ':' '{print $2}'", $tmp);
        $sn = $tmp[0];
    }

    $system = new \ElastPro\System\Sysinfo;
    $uptime   = $system->uptime();
    $cores    = $system->processorCount();

    // mem used
    $memused  = $system->usedMemory();
    $memused_status = "primary";
    if ($memused > 90) {
        $memused_status = "danger";
        $memused_led = "service-status-down";
    } elseif ($memused > 75) {
        $memused_status = "warning";
        $memused_led = "service-status-warn";
    } elseif ($memused >  0) {
        $memused_status = "success";
        $memused_led = "service-status-up";
    }

    // cpu load
    $cpuload = $system->systemLoadPercentage();
    if ($cpuload > 90) {
        $cpuload_status = "danger";
    } elseif ($cpuload > 75) {
        $cpuload_status = "warning";
    } elseif ($cpuload >=  0) {
        $cpuload_status = "success";
    }

    // cpu temp
    if ($model != 'EG324L') {
        $cputemp = $system->systemTemperature();
    } else {
        $cputemp = file_get_contents("/sys/class/thermal/thermal_zone0/temp");
    }
    
    if ($cputemp > 70) {
        $cputemp_status = "danger";
        $cputemp_led = "service-status-down";
    } elseif ($cputemp > 50) {
        $cputemp_status = "warning";
        $cputemp_led = "service-status-warn";
    } else {
        $cputemp_status = "success";
        $cputemp_led = "service-status-up";
    }

    echo renderTemplate(
        "dashboard", compact(
            // "clients",
            "moreLink",
            "ifaceStatus",
            "status",
            "leases",
            "routeInfo",
            "lteInfo",
            "statusIcon",
            'cur_hostname',
            'model',
            'kernel_version',
            'sn',
            'local_time',
            'uptime',
            "memused",
            "memused_status",
            "memused_led",
            "cpuload",
            "cpuload_status",
            "cputemp",
            "cputemp_status",
            "cputemp_led",
            'fw_date'
        )
    );
}

