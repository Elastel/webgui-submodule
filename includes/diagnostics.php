<?php

require_once 'config.php';

/**
 * Return the list of usable network interfaces for the diagnostics page.
 * Loopback is excluded since it is never a meaningful egress interface.
 */
function diagInterfaceList()
{
    $list = array();
    $output = array();
    exec("ls /sys/class/net 2>/dev/null", $output);

    foreach ($output as $if) {
        $if = trim($if);
        if ($if === '' || $if === 'lo') {
            continue;
        }
        $list[] = $if;
    }

    return $list;
}

/**
 * Check whether busybox provides the given applet.
 * The applet list is read once and kept for the duration of the request.
 */
function diagHasBusyboxApplet($name)
{
    static $applets = null;

    if ($applets === null) {
        $applets = array();
        $output = array();
        exec('busybox --list 2>/dev/null', $output);
        foreach ($output as $a) {
            $a = trim($a);
            if ($a !== '') {
                $applets[] = $a;
            }
        }
    }

    return in_array($name, $applets, true);
}

/**
 * Resolve how a diagnostic tool should be invoked.
 *
 * @return string 'system'  the tool is directly callable (may itself be a
 *                          busybox symlink, e.g. /bin/ping on OpenWrt)
 *                'busybox' only the busybox applet is available
 *                null      the tool is unavailable and must not be offered
 */
function diagResolveTool($name)
{
    static $cache = array();

    if (array_key_exists($name, $cache)) {
        return $cache[$name];
    }

    $output = array();
    $ret = 0;
    exec('command -v ' . escapeshellarg($name) . ' 2>/dev/null', $output, $ret);

    if ($ret === 0 && !empty($output)) {
        return $cache[$name] = 'system';
    }

    if (diagHasBusyboxApplet($name)) {
        return $cache[$name] = 'busybox';
    }

    return $cache[$name] = null;
}

/**
 * Command prefix needed to invoke a tool ("busybox " when only the applet
 * exists, empty string otherwise).
 */
function diagToolPrefix($name)
{
    return (diagResolveTool($name) === 'busybox') ? 'busybox ' : '';
}

/**
 * Prefix guarding a command with an execution deadline. Returns an empty
 * string on devices that lack the timeout utility, so the diagnostic still
 * runs (bounded by the tool's own -W/-w options) instead of failing outright.
 */
function diagTimeoutPrefix($seconds)
{
    static $hasTimeout = null;

    if ($hasTimeout === null) {
        $output = array();
        $ret = 0;
        exec('command -v timeout 2>/dev/null', $output, $ret);
        $hasTimeout = ($ret === 0 && !empty($output));
    }

    return $hasTimeout ? 'timeout ' . (int)$seconds . ' ' : '';
}

/**
 * Prefix a command with sudo when the web user is allowed to escalate.
 *
 * Some diagnostics need raw socket access (CAP_NET_RAW), which an unprivileged
 * user does not have - traceroute fails with "socket: Operation not permitted"
 * without it, while ping still works because it uses an ICMP datagram socket.
 *
 * The capability is probed once per request. When the web user has no
 * passwordless sudo the prefix is empty and the command simply runs
 * unprivileged, exactly as it did before.
 */
function diagSudoPrefix()
{
    static $usable = null;

    if ($usable === null) {
        $output = array();
        $ret = 0;
        exec('sudo -n true 2>/dev/null', $output, $ret);
        $usable = ($ret === 0);
    }

    return $usable ? 'sudo -n ' : '';
}

/**
 * Availability map consumed by the template, so unavailable tools can be
 * hidden from the UI entirely.
 */
function diagToolAvailability()
{
    return array(
        'ping'       => diagResolveTool('ping'),
        'traceroute' => diagResolveTool('traceroute'),
        'nslookup'   => diagResolveTool('nslookup'),
    );
}

/**
 * Displays the network diagnostics page (ping / traceroute / nslookup),
 * modelled after the OpenWrt LuCI "Diagnostics" page.
 */
function DisplayDiagnostics()
{
    $status = new \ElastPro\Messages\StatusMessage;
    $interfaces = diagInterfaceList();
    $tools = diagToolAvailability();

    echo renderTemplate("diagnostics", compact("status", "interfaces", "tools"));
}
