<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['value'])) {
    $brightness = intval($_POST['value']);
    if ($brightness < 0) $brightness = 0;
    if ($brightness > 255) $brightness = 255;


    exec("sudo echo $brightness | sudo tee /sys/class/backlight/pwm-backlight/brightness");
}