<?php

/*
 * Fetches details of the kernel routing table
 *
 * @param boolean $checkAccesss Perform connectivity test
 * @return string
 */

function getRouteInfo($checkAccess)
{
    $rInfo = [];

    exec('ip -o route list', $routeLines);
    exec('ip -o -f inet addr show', $addrLines);

    $ifaceMap = [];
    foreach ($addrLines as $line) {
        if (preg_match('/\d+:\s+(\w+)\s+inet\s+([0-9.]+)\/(\d+)/', $line, $m)) {
            $iface = $m[1];
            $ip = $m[2];
            $cidr = $m[3];

            $ifaceMap[$iface] = [
                'ip' => $ip,
                'netmask' => cidr2mask($cidr)
            ];
        }
    }

    foreach ($routeLines as $line) {

        if (preg_match('/^default via ([0-9.]+).* dev (\w+)(?:.* src ([0-9.]+))?(?:.* metric (\d+))?/', $line, $m)) {

            $iface = $m[2];
            $gateway = $m[1];
            $srcip = $m[3] ?? '';
            $metric = $m[4] ?? '';

            if ($srcip === '' && isset($ifaceMap[$iface])) {
                $srcip = $ifaceMap[$iface]['ip'];
            }

            $mac = trim(@file_get_contents("/sys/class/net/$iface/address"));

            $rInfo[] = [
                "interface"  => $iface,
                "ip-address" => $srcip,
                "gateway"    => $gateway,
                "netmask"    => $ifaceMap[$iface]['netmask'] ?? '',
                "mac"        => $mac ?: '',
                "metric"     => $metric
            ];
        }
    }

    if (empty($rInfo)) {
        return ["error" => "No route to the internet found"];
    }

    return $rInfo;
}

