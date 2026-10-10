<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/syslog.php';

/**
 * Fetch log contents as JSON for the System Log page.
 *
 * Accepts:
 *   type    system | kernel
 *   source  log source key (file / logread / journalctl); empty = default
 *   lines   max number of lines to read, 0 = all (capped)
 *   keyword include filter
 *   exclude exclude filter
 *   regex   treat keyword/exclude as regular expressions
 *   case    case-sensitive matching
 */

$type   = $_GET['type'] ?? 'system';
$source = $_GET['source'] ?? '';
$lines  = isset($_GET['lines']) ? (int)$_GET['lines'] : 500;

if ($lines < 0) {
    $lines = 0;
}

if ($type !== 'kernel') {
    $type = 'system';
}

$filter = syslogNormalizeFilter($_GET);

list($ret, $output, $stats) = syslogRead($type, $source, $lines, $filter);

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

echo json_encode(array('return' => $ret, 'output' => $output, 'stats' => $stats));
