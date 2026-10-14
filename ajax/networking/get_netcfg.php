<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

if (isset($_GET['iface'])) {
    echo json_encode(getNetConfigData($_GET['iface']));
}
