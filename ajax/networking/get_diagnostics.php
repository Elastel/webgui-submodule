<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/diagnostics.php';

/**
 * Network diagnostics backend (ping / traceroute / nslookup).
 *
 * Tool resolution goes through diagResolveTool(), which falls back to the
 * busybox applet when the standalone binary is missing. Privilege escalation
 * is handled by diagSudoPrefix(): traceroute needs a raw socket and therefore
 * root, which the web user reaches through passwordless sudo.
 *
 * Everything reaching the shell is validated against a strict whitelist: hosts
 * must start with an alphanumeric character (so they can never be parsed as an
 * option) and are additionally passed through escapeshellarg().
 */

define('DIAG_HOST_REGEX', '/^[A-Za-z0-9][A-Za-z0-9._:\-]{0,252}$/');

function diag_json($return, $output)
{
    echo json_encode(array('return' => $return, 'output' => $output));
    exit;
}

/**
 * Validate a host / DNS-server string. Returns the cleaned value or null.
 */
function diag_valid_host($value)
{
    $value = trim($value ?? '');
    if ($value === '' || strlen($value) > 253) {
        return null;
    }
    // Reject a leading '-' so the value can never be treated as an option.
    if (!preg_match(DIAG_HOST_REGEX, $value)) {
        return null;
    }
    return $value;
}

/**
 * Build a "<flag> <iface>" fragment. Only interfaces that actually exist are
 * accepted; anything else is silently ignored rather than passed through.
 */
function diag_iface_fragment($flag, $iface)
{
    $iface = trim($iface ?? '');
    if ($iface === '') {
        return '';
    }
    $known = array();
    exec('ls /sys/class/net 2>/dev/null', $known);
    $known = array_map('trim', $known);
    if (!in_array($iface, $known, true)) {
        return '';
    }
    return sprintf(' %s %s', $flag, escapeshellarg($iface));
}

function diag_run($command)
{
    $output = array();
    $ret = 0;
    exec($command . ' 2>&1', $output, $ret);

    if ($ret === 127) {
        return array(127, array(_('Command not available on this device.')));
    }
    if (empty($output)) {
        return array($ret, array(_('No output.')));
    }

    // Prepend a clearer hint when the failure was caused by missing privileges
    // (e.g. no passwordless sudo available for the web user), keeping the raw
    // tool output below it for reference.
    $joined = strtolower(implode("\n", $output));
    if (strpos($joined, 'operation not permitted') !== false ||
        strpos($joined, 'permission denied') !== false) {
        array_unshift($output, _('Permission denied: this diagnostic requires elevated privileges.'), '');
    }

    return array($ret, $output);
}

$tool = $_POST['tool'] ?? '';
$sudo = diagSudoPrefix();

switch ($tool) {
    case 'ping':
        $host = diag_valid_host($_POST['host'] ?? '');
        if ($host === null) {
            diag_json(2, array(_('Invalid host.')));
        }
        if (diagResolveTool('ping') === null) {
            diag_json(127, array(_('Command not available on this device.')));
        }
        $count = filter_var($_POST['count'] ?? 4, FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1, 'max_range' => 10, 'default' => 4)
        ));
        if ($count === false) {
            $count = 4;
        }

        $iface = diag_iface_fragment('-I', $_POST['iface'] ?? '');
        $cmd = $sudo . diagTimeoutPrefix(15) . diagToolPrefix('ping') . 'ping -c ' . $count . ' -W 2' . $iface . ' ' . escapeshellarg($host);
        list($ret, $out) = diag_run($cmd);
        diag_json($ret, $out);
        break;

    case 'traceroute':
        $host = diag_valid_host($_POST['host'] ?? '');
        if ($host === null) {
            diag_json(2, array(_('Invalid host.')));
        }
        if (diagResolveTool('traceroute') === null) {
            diag_json(127, array(_('Command not available on this device.')));
        }

        $iface = diag_iface_fragment('-i', $_POST['iface'] ?? '');
        $cmd = $sudo . diagTimeoutPrefix(30) . diagToolPrefix('traceroute') . 'traceroute -w 2 -m 15 -q 1' . $iface . ' ' . escapeshellarg($host);
        list($ret, $out) = diag_run($cmd);
        diag_json($ret, $out);
        break;

    case 'nslookup':
        $host = diag_valid_host($_POST['host'] ?? '');
        if ($host === null) {
            diag_json(2, array(_('Invalid host.')));
        }
        if (diagResolveTool('nslookup') === null) {
            diag_json(127, array(_('Command not available on this device.')));
        }

        $cmd = $sudo . diagTimeoutPrefix(10) . diagToolPrefix('nslookup') . 'nslookup ' . escapeshellarg($host);
        $server = diag_valid_host($_POST['server'] ?? '');
        if ($server !== null) {
            $cmd .= ' ' . escapeshellarg($server);
        }
        list($ret, $out) = diag_run($cmd);
        diag_json($ret, $out);
        break;

    default:
        diag_json(2, array(_('Unknown diagnostic tool.')));
}
