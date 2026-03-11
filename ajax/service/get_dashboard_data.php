<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';

exec("date '+%Y-%m-%d %H:%M:%S'", $tmp);
$local_time = $tmp[0];
$dashboarddata['local_time'] = $local_time;

$uparray = explode(" ", exec("cat /proc/uptime"));
$seconds = round($uparray[0], 0);
$minutes = $seconds / 60;
$hours   = $minutes / 60;
$days    = floor($hours / 24);
$hours   = floor($hours   - ($days * 24));
$minutes = floor($minutes - ($days * 24 * 60) - ($hours * 60));
$uptime= '';
if ($days    != 0) {
    $uptime .= $days . ' day' . (($days    > 1)? 's ':' ');
}
if ($hours   != 0) {
    $uptime .= $hours . ' hour' . (($hours   > 1)? 's ':' ');
}
if ($minutes != 0) {
    $uptime .= $minutes . ' minute' . (($minutes > 1)? 's ':' ');
}
$dashboarddata['uptime'] = $uptime;

echo json_encode($dashboarddata);
