<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

/**
 * Return the current system time as JSON.
 *
 * Used by the System Properties tab so its Refresh button can update the
 * clock in place instead of reloading the whole page.
 */

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

echo json_encode(array('time' => getSystemTime()));
