<?php
require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/session.php';
require_once '../../includes/config.php';

// NOTE: includes/authenticate.php is deliberately NOT required here. It answers an
// unauthenticated request with a 302 to /login, which jQuery follows transparently,
// so the 5s poll received the login page HTML and never saw the expiry. This
// endpoint answers JSON instead and must never become a keep-alive.

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

// Check 2: the session itself is no longer valid — user removed from the auth file,
// or it belongs to an earlier boot (PHP keeps its session files in the persistent
// /var/lib/php/sessions, so without this a reboot would silently resume it).
if ($status === 'active' && RASPI_AUTH_ENABLED) {
    $auth = new \ElastPro\Auth\HTTPAuth;
    if (!$auth->isLogged()) {
        $status = 'session_expired';
    }
}

if ($status === 'session_expired') {
    $_SESSION = array();
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset(); // unset all session variables
        session_destroy(); // destroy the session
    }
    // Drop the session cookie as well, otherwise the browser keeps presenting the
    // dead session id and every following request would revive an empty session.
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            (bool) $params['secure'],
            (bool) $params['httponly']
        );
    }
}
// This poll must NOT refresh lastActivity: doing so turned the 5s heartbeat into a
// keep-alive, so an idle page never expired. The activity time is refreshed by real
// page loads (sidebar.php) and by the user-activity ping in
// ajax/session/do_touch_session.php.

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
