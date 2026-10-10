import { getCSRFToken } from "../helpers.js";

const DIAG_AJAX = 'ajax/networking/get_diagnostics.php';

// Host names, IPv4/IPv6 literals. Must not start with '-' so a value can
// never be interpreted as a command line option.
const DIAG_HOST_RE = /^[A-Za-z0-9][A-Za-z0-9._:-]{0,252}$/;

function diagSetBusy($btn, busy) {
    $btn.prop('disabled', busy);
    $btn.toggleClass('diag-busy', busy);
}

function diagShow($result, text, isError) {
    $result
        .toggleClass('text-danger', !!isError)
        .text(text);
}

function diagRun(tool, params, $result, $btn) {
    const host = (params.host || '').trim();

    if (host === '') {
        diagShow($result, 'Please enter a host.', true);
        return;
    }
    if (!DIAG_HOST_RE.test(host)) {
        diagShow($result, 'Invalid host name or address.', true);
        return;
    }

    diagSetBusy($btn, true);
    diagShow($result, 'Running, please wait...', false);

    const payload = Object.assign({}, params, { csrf_token: getCSRFToken() });

    $.post(DIAG_AJAX, payload, function (data) {
        let res = data;
        if (typeof data === 'string') {
            try {
                res = JSON.parse(data);
            } catch (e) {
                diagShow($result, data, true);
                return;
            }
        }

        const lines = Array.isArray(res.output) ? res.output.join('\n') : String(res.output || '');
        diagShow($result, lines || 'No output.', res.return != 0);
    }).fail(function (xhr) {
        let msg = 'An error occurred while running the diagnostic.';
        try {
            const parsed = JSON.parse(xhr.responseText);
            if (parsed && parsed.error) msg = parsed.error;
        } catch (e) { /* keep default message */ }
        diagShow($result, msg, true);
    }).always(function () {
        diagSetBusy($btn, false);
    });
}

export function initDiagnostics() {
    $('#btn_diag_ping').on('click', function () {
        diagRun('ping', {
            tool: 'ping',
            host: $('#diag_ping_host').val(),
            count: $('#diag_ping_count').val(),
            iface: $('#diag_ping_iface').val()
        }, $('#diag_ping_result'), $(this));
    });

    $('#btn_diag_traceroute').on('click', function () {
        diagRun('traceroute', {
            tool: 'traceroute',
            host: $('#diag_trace_host').val(),
            iface: $('#diag_trace_iface').val()
        }, $('#diag_trace_result'), $(this));
    });

    $('#btn_diag_nslookup').on('click', function () {
        diagRun('nslookup', {
            tool: 'nslookup',
            host: $('#diag_ns_host').val(),
            server: $('#diag_ns_server').val()
        }, $('#diag_ns_result'), $(this));
    });

    // Submit on Enter from the host fields
    $('#diag_ping_host').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); $('#btn_diag_ping').trigger('click'); }
    });
    $('#diag_trace_host').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); $('#btn_diag_traceroute').trigger('click'); }
    });
    $('#diag_ns_host').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); $('#btn_diag_nslookup').trigger('click'); }
    });
}
