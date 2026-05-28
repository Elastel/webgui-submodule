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

function myCidr2mask($cidr)
{
    $cidr = intval($cidr);

    if ($cidr < 0 || $cidr > 32) {
        return '0.0.0.0';
    }

    return long2ip(-1 << (32 - $cidr));
}

function getLteInfo($iface)
{
    $lteInfo = [
        "enabled"    => 0,
        "interface"  => $iface,
        "ip_address" => '-',
        "netmask"    => '-',
        "signal"     => '-',
        "operator"   => '-',
        "iccid"      => '-',
        "imei"       => '-',
        "sim"        => '-',
        "lte_status" => 'DISCONNECTED',
        "uptime"     => '-',
        "metric"     => '-'
    ];

    if (empty($iface)) {
        return $lteInfo;
    }

    $ifaceSafe = escapeshellarg($iface);

    exec("ip -o route show default", $routes);

    foreach ($routes as $r) {
        if (strpos($r, $iface) !== false) {
            $lteInfo["enabled"] = 1;
            break;
        }
    }

    if (!file_exists("/dev/ttyUSB2")) {
        return $lteInfo;
    }

    exec("ip -o -f inet addr show dev {$ifaceSafe}", $addr);

    if (!empty($addr) && preg_match('/inet ([0-9.]+)\/(\d+)/', $addr[0], $m)) {
        $lteInfo["ip_address"] = $m[1];
        $lteInfo["netmask"] = myCidr2mask((int)$m[2]);
    }

    exec("uci -P /var/state show dangle.dev", $uciLines);

    $uciMap = [];
    foreach ($uciLines as $line) {
        if (preg_match('/dangle\.dev\.(\w+)=(.*)/', $line, $m)) {
            $uciMap[$m[1]] = trim($m[2], "'");
        }
    }

    if (!empty($uciMap)) {
        $lteInfo["signal"]   = $uciMap['signal']   ?? '-';
        $lteInfo["operator"] = $uciMap['service']  ?? '-';
        $lteInfo["iccid"]    = $uciMap['iccid']    ?? '-';
        $lteInfo["imei"]     = $uciMap['imei']     ?? '-';
        $lteInfo["sim"]      = $uciMap['sim']      ?? '-';

        $lte_status = $uciMap['connect'] ?? 'DISCONNECTED';

        if ($lteInfo["enabled"] == 0) {
            $lte_status = "DISCONNECTED";
        }

        $lteInfo["lte_status"] = $lte_status;

        if (!empty($uciMap['uptime'])) {
            $lteInfo["uptime"] = timeCalculation($uciMap['uptime']);
        }
    }

    if (function_exists('getInterfaceMetric')) {
        $lteInfo["metric"] = getInterfaceMetric($iface) ?? '-';
    }

    return $lteInfo;
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

    if (file_exists(RASPI_DNSMASQ_LEASES)) {
        $leases = file(RASPI_DNSMASQ_LEASES, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    $routeInfo = getRouteInfo(true);

    $lteInfo = getLteInfo($lte_ifname[0]);

    $cur_hostname = getHostname();

    $model = getModel();
    
    $fw_date = trim(file_get_contents('/etc/fw_date'));

    $kernel_version = trim(file_get_contents('/proc/sys/kernel/osrelease'));

    $local_time = getSystemTime();

    $sn = getSn();

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

