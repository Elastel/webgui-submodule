<?php
require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/session.php';
require_once '../../includes/config.php';

// Called from the frontend when the user actually interacts with the page
// (click / keydown / mousemove / scroll / touch). Only this — plus a real page
// load in sidebar.php — may extend the session. The 5s poll in
// ajax/session/do_check_session.php deliberately does not.
//
// NOTE: includes/authenticate.php is deliberately NOT required: an anonymous or
// stale request must get a JSON answer, not a 302 the frontend cannot see.

$status = 'active';
if (RASPI_AUTH_ENABLED) {
    $auth = new \ElastPro\Auth\HTTPAuth;
    // Rejects sessions without a user, of a removed user, or left over from an
    // earlier boot — refreshing those would resurrect a dead session.
    if (!$auth->isLogged()) {
        $status = 'session_expired';
    }
}

if ($status === 'active') {
    $_SESSION['lastActivity'] = time();
}

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Pragma: no-cache');

echo json_encode([
    'status' => $status,
    'last_activity' => $_SESSION['lastActivity'] ?? time()
]);
exit();
