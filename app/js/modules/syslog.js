const SYSLOG_AJAX = 'ajax/system/get_log.php';
const SYSLOG_DOWNLOAD = 'ajax/system/download_log.php';

/** Element-id prefix for a given log type. */
function syslogPrefix(type) {
    return (type === 'kernel') ? 'syslog_kernel_' : 'syslog_';
}

function syslogShow($pre, text, isError) {
    $pre
        .toggleClass('text-danger', !!isError)
        .text(text);
}

function syslogShowStats(type, stats) {
    const $el = $('#' + syslogPrefix(type) + 'stats');
    if (!$el.length || !stats) {
        return;
    }
    // Filtering runs against the full log, so the meaningful number is how many
    // lines matched; the Lines control only caps how many of those are shown.
    if (stats.filtered) {
        let txt = stats.matched + ' matches';
        if (stats.capped) {
            txt += ' (showing last ' + stats.shown + ')';
        }
        $el.text(txt);
    } else {
        $el.text(stats.total + ' lines');
    }
}

function syslogParams(type) {
    const p = syslogPrefix(type);
    const params = { type: type, lines: $('#' + p + 'lines').val() || '500' };

    if (type !== 'kernel') {
        params.source = $('#syslog_source').val() || '';
    }

    const keyword = $('#' + p + 'keyword').val();
    const exclude = $('#' + p + 'exclude').val();

    if (keyword) {
        params.keyword = keyword;
    }
    if (exclude) {
        params.exclude = exclude;
    }
    if ($('#' + p + 'regex').is(':checked')) {
        params.regex = '1';
    }
    if ($('#' + p + 'case').is(':checked')) {
        params.case = '1';
    }

    return params;
}

function syslogLoad(type) {
    const $pre = $('#' + syslogPrefix(type) + 'result');
    if (!$pre.length) {
        return;
    }

    $pre.removeClass('text-danger').text('Loading...');

    $.get(SYSLOG_AJAX, syslogParams(type), function (data) {
        const lines = Array.isArray(data.output) ? data.output.join('\n') : String(data.output || '');
        syslogShow($pre, lines || 'No log entries.', data.return != 0);
        syslogShowStats(type, data.stats);
    }).fail(function () {
        syslogShow($pre, 'Failed to load the log.', true);
    });
}

function syslogDownload(type) {
    const p = syslogParams(type);
    const query = Object.keys(p)
        .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(p[k]))
        .join('&');

    window.location.href = SYSLOG_DOWNLOAD + '?' + query;
}

/** Wire up one log pane's controls. Does not trigger a load by itself. */
function syslogBind(type) {
    const p = syslogPrefix(type);

    $('#btn_' + p + 'refresh').on('click', function () {
        syslogLoad(type);
    });

    $('#btn_' + p + 'download').on('click', function () {
        syslogDownload(type);
    });

    $('#btn_' + p + 'clear').on('click', function () {
        $('#' + p + 'keyword, #' + p + 'exclude').val('');
        $('#' + p + 'regex, #' + p + 'case').prop('checked', false);
        syslogLoad(type);
    });

    $('#' + p + 'regex, #' + p + 'case').on('change', function () {
        syslogLoad(type);
    });

    $('#' + p + 'keyword, #' + p + 'exclude').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            syslogLoad(type);
        }
    });
}

export function initSyslog() {
    const hasSystem = $('#syslog-system').length > 0;
    const hasKernel = $('#syslog-kernel').length > 0;

    // Source / line count only exist on the system pane
    $('#syslog_source, #syslog_lines').on('change', function () {
        syslogLoad('system');
    });

    if (hasSystem) {
        syslogBind('system');
    }
    if (hasKernel) {
        syslogBind('kernel');
    }

    // Only fetch the pane that is actually visible, so opening the page costs
    // a single request instead of two.
    if (hasSystem) {
        syslogLoad('system');
    } else if (hasKernel) {
        syslogLoad('kernel');
    }

    // Kernel log is fetched lazily, the first time its tab is opened.
    if (hasSystem && hasKernel) {
        let kernelLoaded = false;
        $('a[data-toggle="tab"][href="#syslog-kernel"]').on('shown.bs.tab', function () {
            if (!kernelLoaded) {
                kernelLoaded = true;
                syslogLoad('kernel');
            }
        });
    }
}
