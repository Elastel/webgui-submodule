<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';

// 判断openvpn是否在运行
exec("sudo pgrep openvpn", $pid);
if ($pid[0]) {
    exec("sudo /usr/local/bin/uci get openvpn.openvpn.role", $role);
    if ($role[0] == 'client') {
        $interface = '';
        exec("ifconfig tap0 | grep -c tap0", $count);
        if ($count[0] == 1) {
            $interface = 'tap0';
        } else {
            $interface = 'tun0';
        }

        exec("sudo ifconfig $interface | grep 'inet ' | awk '{print $2}'", $local_ip);
        if ($local_ip[0] != NULL) {
            $vpnstatus['local_ip'] = $local_ip[0];
            $vpnstatus['status'] = _('Connected');
        } else {
            $vpnstatus['status'] = _('Connecting');
        }
        
        exec("sudo ifconfig $interface | grep 'inet ' | awk '{print $4}'", $netmask);
        if ($netmask[0] != NULL)
            $vpnstatus['netmask'] = $netmask[0];

        exec("cat /etc/openvpn/client/client.conf | grep '^remote' | awk '{print $2}' | grep '[0-9]'", $remote);
        if ($remote[0] != NULL)
            $vpnstatus['remote_ip'] = $remote[0];
    } else {
        $vpnstatus['status'] = _('Disconnected');
    }
} else {
    $vpnstatus['status'] = _('Disconnected');
}

echo json_encode($vpnstatus);
