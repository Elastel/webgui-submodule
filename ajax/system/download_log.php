<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/syslog.php';

/**
 * Stream the log as a plain-text attachment, applying the same filters the
 * page is currently showing so what you see is what you download.
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

$content = implode("\n", $output);
if ($content !== '') {
    $content .= "\n";
}

$filename = syslogFileName($type);

header('Content-Description: File Transfer');
header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($content));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if (ob_get_length() !== false) {
    @ob_clean();
}

echo $content;
exit;
