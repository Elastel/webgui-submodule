<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

$dashboarddata = [];

$dashboarddata['local_time'] = getSystemTime();

$uptimeStr = trim(@file_get_contents('/proc/uptime'));

$seconds = (int) floatval(explode(' ', $uptimeStr)[0]);

$days    = floor($seconds / 86400);
$hours   = floor(($seconds % 86400) / 3600);
$minutes = floor(($seconds % 3600) / 60);

$uptime = [];

if ($days > 0) {
    $uptime[] = $days . ' day' . ($days > 1 ? 's' : '');
}
if ($hours > 0) {
    $uptime[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
}
if ($minutes > 0) {
    $uptime[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
}

$dashboarddata['uptime'] = !empty($uptime) ? implode(' ', $uptime) : '0 minute';

echo json_encode($dashboarddata);
