<?php

/*
 * Fetches details of the kernel routing table
 *
 * @param boolean $checkAccesss Perform connectivity test
 * @return string
 */
function getRouteInfo($checkAccess)
{
    $model = getModel();
    $rInfo = array();
    exec('ip route list', $routeLines);

    if (!empty($routeLines)) {
        $i = 0;
        foreach ($routeLines as $line) {
            if (preg_match('/^default via ([0-9.]+).*dev (\w+)(?:.*src ([0-9.]+))?(?:.*metric (\d+))?/i', $line, $m)) {
                $iface = $m[2];
                $srcip = isset($m[3]) ? $m[3] : '';
                $gateway = $m[1];
                $metric = '';
                if (isset($m[4]) && $m[4] !== '') {
                    $metric = $m[4];
                } else if (preg_match('/metric (\d+)/', $line, $mm)) {
                    $metric = $mm[1];
                }

                if ($model != "EG324L" && $model != "EC212") {
                    exec('ifconfig ' . $iface . ' | grep -oP "(?<=netmask )([0-9]{1,3}\.){3}[0-9]{1,3}"', $netmask);
                } else {
                    exec('ifconfig ' . $iface . ' | grep -Eo "([0-9]+[.]){3}[0-9]+" | grep "255.255"', $netmask);
                }
                exec('cat /sys/class/net/' . $iface . '/address', $mac);

                $rInfo[$i]["interface"] = $iface;
                if ($srcip == '') {
                    exec('ip addr show ' . $iface . ' | grep -oP "(?<=inet )([0-9]{1,3}\.){3}[0-9]{1,3}"', $tmpSrcIp);
                    $srcip = isset($tmpSrcIp[0]) ? $tmpSrcIp[0] : '';
                }
                $rInfo[$i]["ip-address"] = $srcip;
                $rInfo[$i]["gateway"] = $gateway;
                $rInfo[$i]["netmask"] = isset($netmask[0]) ? $netmask[0] : '';
                $rInfo[$i]["mac"] = isset($mac[0]) ? $mac[0] : '';
                $rInfo[$i]["metric"] = $metric;
                $i++;
                unset($tmpSrcIp, $netmask, $mac);
            }
        }
    }

    if (empty($rInfo)) {
        $rInfo = array("error" => "No route to the internet found");
    }
    return $rInfo;
}
