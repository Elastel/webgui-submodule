import { getCSRFToken } from "../helpers.js";

// Minimum gap between activity pings (ms).
const ACTIVITY_PING_INTERVAL = 60000;
let lastActivityPing = 0;

// Only real user interaction may extend the session. The 5s poll below must
// not do it, otherwise an idle page would keep the session alive forever.
function pingActivity() {
    const now = Date.now();
    if (now - lastActivityPing < ACTIVITY_PING_INTERVAL) return;
    lastActivityPing = now;

    var csrfToken = getCSRFToken();
    $.post('ajax/session/do_touch_session.php', { 'csrf_token': csrfToken });
}

function initActivityTracking() {
    ['click', 'keydown', 'mousemove', 'scroll', 'touchstart'].forEach(function (evt) {
        document.addEventListener(evt, pingActivity, { passive: true });
    });
}

export function showSessionExpiredModal() {
    $('#sessionTimeoutModal').modal('show');
}

export function initSession() {
    initActivityTracking();
    let sessionCheckInterval = setInterval(checkSession, 5000);

    function checkSession() {
        // skip session check if on login page
        if (window.location.pathname === '/login') {
            return;
        }
        var csrfToken = getCSRFToken();
        $.post('ajax/session/do_check_session.php',{'csrf_token': csrfToken},function (data) {
            if (data.status === 'session_expired') {
                clearInterval(sessionCheckInterval);
                showSessionExpiredModal();
            }
        }).fail(function (jqXHR, status, err) {
            console.error("Error checking session status:", status, err);
        });
    }
}