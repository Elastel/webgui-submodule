<?php
require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/session.php';
require_once '../../includes/config.php';
require_once '../../includes/authenticate.php';

// Called from the frontend when the user actually interacts with the page
// (click / keydown / mousemove / scroll / touch). Only this — plus a real page
// load in sidebar.php — may extend the session. The 5s poll in
// ajax/session/do_check_session.php deliberately does not.

$_SESSION['lastActivity'] = time();

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Pragma: no-cache');

echo json_encode([
    'status' => 'active',
    'last_activity' => $_SESSION['lastActivity']
]);
exit();
