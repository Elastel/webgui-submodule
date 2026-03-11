<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';

exec("ls /sys/class/net | grep -v lo", $interfaces);
echo json_encode($interfaces);
