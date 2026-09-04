<?php
require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/session.php';
require_once '../../includes/config.php';
require_once '../../includes/authenticate.php';

$lastActivity = $_SESSION['lastActivity'] ?? time();
$sessionLifetime = time() - $lastActivity;

// If the device clock has been adjusted (e.g. via the time settings page),
// time() jumps and may cause a false expiry; protect against a negative delta (clock moved back).
if ($sessionLifetime < 0) {
    $_SESSION['lastActivity'] = time();
    $lastActivity = $_SESSION['lastActivity'];
    $sessionLifetime = 0;
}

// Check 1: timeout
$status = $sessionLifetime >= RASPI_SESSION_TIMEOUT ? 'session_expired' : 'active';

// Check 2: user was deleted from auth file — must invalidate immediately
if ($status === 'active' && RASPI_AUTH_ENABLED && isset($_SESSION['user_id'])) {
    $auth = new \ElastPro\Auth\HTTPAuth;
    if (!$auth->userExists($_SESSION['user_id'])) {
        $status = 'session_expired';
    }
}

if ($status === 'session_expired') {
    session_unset(); // unset all session variables
    session_destroy(); // destroy the session
} else {
    // Sliding expiration: while the page is open (frontend polls every 5 seconds),
    // keep refreshing the activity time so active users are never logged out.
    $_SESSION['lastActivity'] = time();
}

// send response
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Pragma: no-cache');

$response = [
    'status' => $status,
    'last_activity' => $lastActivity,
    'session_lifetime' => $sessionLifetime
];

echo json_encode($response);
exit();
