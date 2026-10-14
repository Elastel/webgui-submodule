<?php

require_once 'config.php';

/**
 * System log page backend.
 *
 * Built to work across distributions rather than assuming one layout:
 *
 *   Buildroot / OpenWrt : busybox syslogd ring buffer, read with `logread`
 *                         (frequently only present as a busybox applet)
 *   Ubuntu / Debian     : syslog file under /var/log, and/or the systemd
 *                         journal (`journalctl`)
 *   Kernel log          : `dmesg`, available on all of them
 *
 * Sources are verified by actually reading them, not merely by checking that
 * the binary exists - busybox ships `logread` even on hosts that run
 * systemd-journald, where no syslogd ring buffer exists at all. Line limiting
 * and filtering are applied in PHP so we never depend on per-tool arguments
 * that differ between busybox and util-linux builds, and so user input is
 * never interpolated into a shell command.
 *
 * Performance notes (the reason for the -n flags and the cache below):
 * source detection runs on every request, so each probe must be bounded.
 * A bare `journalctl -b` dumps the entire boot log and costs ~5 s; adding
 * -n 3 brings it down to a few tens of ms. The resolved source list is then
 * cached across requests so a page load does not re-probe at all.
 */

define('SYSLOG_FILE_CANDIDATES', array('/var/log/syslog', '/var/log/messages'));
define('SYSLOG_MAX_LINES', 50000);
// Tighter window used only when a filter is active: scanning the full
// SYSLOG_MAX_LINES into a PHP array just to filter it can peak at tens of MB,
// so filtered reads are bounded to this smaller limit instead.
define('SYSLOG_FILTER_SCAN_LINES', 5000);
define('SYSLOG_CACHE_FILE', '/tmp/syslog_sources.cache');
define('SYSLOG_CACHE_TTL', 300);

/**
 * Passwordless sudo prefix when the web user may escalate, empty otherwise.
 * Journal and /var/log files are root-owned on most distros.
 */
function syslogSudoPrefix()
{
    static $usable = null;

    if ($usable === null) {
        $output = array();
        $ret = 0;
        exec('sudo -n true 2>/dev/null', $output, $ret);
        $usable = ($ret === 0);
    }

    return $usable ? 'sudo -n ' : '';
}

/**
 * Whether busybox offers the given applet.
 */
function syslogBusyboxHas($name)
{
    static $applets = null;

    if ($applets === null) {
        $applets = array();
        $output = array();
        exec('busybox --list 2>/dev/null', $output);
        foreach ($output as $a) {
            $a = trim($a);
            if ($a !== '') {
                $applets[] = $a;
            }
        }
    }

    return in_array($name, $applets, true);
}

/**
 * Resolve how a tool is invoked: bare name, "busybox <name>", or null.
 */
function syslogResolve($name)
{
    static $cache = array();

    if (array_key_exists($name, $cache)) {
        return $cache[$name];
    }

    $output = array();
    $ret = 0;
    exec('command -v ' . escapeshellarg($name) . ' 2>/dev/null', $output, $ret);
    if ($ret === 0 && !empty($output)) {
        return $cache[$name] = $name;
    }

    if (syslogBusyboxHas($name)) {
        return $cache[$name] = 'busybox ' . $name;
    }

    return $cache[$name] = null;
}

/**
 * First existing syslog file, or null.
 */
function syslogDetectFile()
{
    foreach (SYSLOG_FILE_CANDIDATES as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }
    return null;
}

/**
 * Build the command that dumps a system log source, bounded to $limit lines.
 * Returns null when the requested source is unavailable.
 */
function syslogReadCommand($sourceKey, $limit)
{
    $sudo = syslogSudoPrefix();
    $n = ($limit > 0) ? min((int)$limit, SYSLOG_MAX_LINES) : SYSLOG_MAX_LINES;

    switch ($sourceKey) {
        case 'logread':
            $bin = syslogResolve('logread');
            if ($bin === null) {
                return null;
            }
            // busybox logread has no line limit, so bound it through tail.
            return $sudo . $bin . ' 2>/dev/null | tail -n ' . $n;

        case 'journalctl':
            $bin = syslogResolve('journalctl');
            if ($bin === null) {
                return null;
            }
            // -n is essential: without it journalctl streams the whole boot
            // log (~5 s) even when only a handful of lines are needed.
            return $sudo . $bin . ' -b --no-pager -o short -n ' . $n;

        case 'file':
            $path = syslogDetectFile();
            if ($path === null) {
                return null;
            }
            return $sudo . 'tail -n ' . $n . ' ' . escapeshellarg($path);
    }

    return null;
}

/**
 * Kernel ring buffer command, bounded to $limit lines.
 */
function syslogKernelCommand($limit)
{
    $bin = syslogResolve('dmesg');
    if ($bin === null) {
        return null;
    }

    $n = ($limit > 0) ? min((int)$limit, SYSLOG_MAX_LINES) : SYSLOG_MAX_LINES;

    return syslogSudoPrefix() . $bin . ' 2>/dev/null | tail -n ' . $n;
}

/**
 * Execute a command, trim to the newest lines.
 */
function syslogExec($cmd, $limit)
{
    if ($cmd === null) {
        return array(2, array());
    }

    $output = array();
    $ret = 0;
    exec($cmd . ' 2>&1', $output, $ret);

    if ($limit > 0 && count($output) > $limit) {
        $output = array_slice($output, -$limit);
    }

    return array($ret, $output);
}

/**
 * Whether a source can really deliver content, verified with a tiny read.
 * Binary presence alone is not sufficient (see the class comment).
 */
function syslogSourceUsable($sourceKey)
{
    static $cache = array();

    if (!array_key_exists($sourceKey, $cache)) {
        list($ret, $out) = syslogExec(syslogReadCommand($sourceKey, 3), 3);
        $cache[$sourceKey] = ($ret === 0 && !empty($out));
    }

    return $cache[$sourceKey];
}

/**
 * Rebuild a source descriptor from its key. Used when reading the cache so
 * labels are always generated in the current request's locale.
 */
function syslogBuildSource($key)
{
    switch ($key) {
        case 'file':
            $path = syslogDetectFile();
            return ($path === null)
                ? null
                : array('key' => 'file', 'label' => $path, 'path' => $path);

        case 'logread':
            return array('key' => 'logread', 'label' => _('Syslog buffer (logread)'));

        case 'journalctl':
            return array('key' => 'journalctl', 'label' => _('Journal (journalctl)'));
    }

    return null;
}

/**
 * Cached source keys, or null when absent/expired.
 */
function syslogCachedKeys()
{
    if (!file_exists(SYSLOG_CACHE_FILE)) {
        return null;
    }
    if ((time() - filemtime(SYSLOG_CACHE_FILE)) > SYSLOG_CACHE_TTL) {
        return null;
    }

    $data = @file_get_contents(SYSLOG_CACHE_FILE);
    if ($data === false) {
        return null;
    }

    $keys = json_decode($data, true);

    return is_array($keys) ? $keys : null;
}

/**
 * Persist the resolved source keys for later requests.
 */
function syslogCacheKeys($keys)
{
    @file_put_contents(SYSLOG_CACHE_FILE, json_encode($keys));
}

/**
 * Log sources that are both present and actually readable, best first.
 *
 * Order note: a plain syslog file is preferred as the default because its
 * format is stable across distros and matches what most admins expect.
 * logread is next (the native source on OpenWrt/Buildroot). journalctl is
 * kept last - offered as an option, not the default.
 */
function syslogDetectSources()
{
    static $sources = null;

    if ($sources !== null) {
        return $sources;
    }

    $sources = array();

    // Reuse the previous probe so page loads and ajax calls stay fast.
    $cachedKeys = syslogCachedKeys();
    if ($cachedKeys !== null) {
        foreach ($cachedKeys as $key) {
            $built = syslogBuildSource($key);
            if ($built !== null) {
                $sources[] = $built;
            }
        }
        return $sources;
    }

    $candidates = array();

    // plain log file - rsyslog / syslog-ng setups (Ubuntu, Debian, ...)
    $file = syslogDetectFile();
    if ($file !== null) {
        $candidates[] = array(
            'key'   => 'file',
            'label' => $file,
            'path'  => $file,
        );
    }

    // busybox syslogd ring buffer - the native source on OpenWrt / Buildroot
    if (syslogResolve('logread') !== null) {
        $candidates[] = array(
            'key'   => 'logread',
            'label' => _('Syslog buffer (logread)'),
        );
    }

    // systemd journal - offered as an alternative, not the default
    if (syslogResolve('journalctl') !== null) {
        $candidates[] = array(
            'key'   => 'journalctl',
            'label' => _('Journal (journalctl)'),
        );
    }

    foreach ($candidates as $candidate) {
        if (syslogSourceUsable($candidate['key'])) {
            $sources[] = $candidate;
        }
    }

    $keys = array();
    foreach ($sources as $source) {
        $keys[] = $source['key'];
    }
    syslogCacheKeys($keys);

    return $sources;
}

/* ---------------------------------------------------------------------------
 * Filtering
 * ------------------------------------------------------------------------ */

/**
 * Normalise raw request input into a filter descriptor.
 *
 * @return array keyword, exclude, regex, case
 */
function syslogNormalizeFilter($raw)
{
    return array(
        'keyword' => isset($raw['keyword']) ? trim((string)$raw['keyword']) : '',
        'exclude' => isset($raw['exclude']) ? trim((string)$raw['exclude']) : '',
        'regex'   => !empty($raw['regex']),
        'case'    => !empty($raw['case']),
    );
}

/**
 * True when no filtering is actually requested.
 */
function syslogFilterIsEmpty($filter)
{
    return ($filter['keyword'] === '' && $filter['exclude'] === '');
}

/**
 * Match a single line against one pattern.
 */
function syslogMatchOne($line, $pattern, $isRegex, $caseSensitive)
{
    if ($isRegex) {
        $delimiter = '/';
        $escaped = str_replace($delimiter, '\\' . $delimiter, $pattern);
        $regex = $delimiter . $escaped . $delimiter . ($caseSensitive ? '' : 'i');

        // An invalid user pattern simply matches nothing rather than erroring.
        return @preg_match($regex, $line) === 1;
    }

    if (!$caseSensitive) {
        $line = strtolower($line);
        $pattern = strtolower($pattern);
    }

    return strpos($line, $pattern) !== false;
}

/**
 * Apply include/exclude filtering over the collected lines.
 */
function syslogApplyFilter($lines, $filter)
{
    if (syslogFilterIsEmpty($filter)) {
        return $lines;
    }

    $isRegex = !empty($filter['regex']);
    $caseSensitive = !empty($filter['case']);
    $keyword = $filter['keyword'];
    $exclude = $filter['exclude'];

    $result = array();
    foreach ($lines as $line) {
        if ($keyword !== '' && !syslogMatchOne($line, $keyword, $isRegex, $caseSensitive)) {
            continue;
        }
        if ($exclude !== '' && syslogMatchOne($line, $exclude, $isRegex, $caseSensitive)) {
            continue;
        }
        $result[] = $line;
    }

    return $result;
}

/* ---------------------------------------------------------------------------
 * Reading
 * ------------------------------------------------------------------------ */

/**
 * Read a log, optionally filtered.
 *
 * @param string $type      'system' or 'kernel'
 * @param string $sourceKey source key, empty = first available
 * @param int    $limit     max lines to read before filtering, 0 = cap
 * @param array  $filter    output of syslogNormalizeFilter()
 *
 * @return array ($returnCode, $lines, $stats) where $stats holds the number
 *               of lines read and how many survived the filter.
 */
function syslogRead($type, $sourceKey, $limit, $filter = array())
{
    $filter = empty($filter) ? syslogNormalizeFilter(array()) : $filter;
    $filtered = !syslogFilterIsEmpty($filter);

    $userLimit = ($limit > 0) ? min((int)$limit, SYSLOG_MAX_LINES) : SYSLOG_MAX_LINES;

    // No filter: honour the requested window (recent-N browsing) so the Lines
    // control works as expected. With a filter: read the whole log (capped at
    // SYSLOG_MAX_LINES) so matches are not lost to a scrolling window, then the
    // Lines control caps how many *matched* rows are returned for display. This
    // keeps the Lines limit meaningful while the reported count stays stable.
    $readLimit = $filtered ? SYSLOG_FILTER_SCAN_LINES : $userLimit;

    if ($type === 'kernel') {
        list($ret, $output) = syslogExec(syslogKernelCommand($readLimit), $readLimit);
    } else {
        if ($sourceKey === '') {
            $sources = syslogDetectSources();
            if (empty($sources)) {
                return array(2, array(_('No log source available on this device.')),
                    array('total' => 0, 'matched' => 0, 'filtered' => false, 'shown' => 0, 'capped' => false));
            }
            $sourceKey = $sources[0]['key'];
        }
        list($ret, $output) = syslogExec(syslogReadCommand($sourceKey, $readLimit), $readLimit);
    }

    $total = count($output);

    if ($filtered) {
        $output = syslogApplyFilter($output, $filter);
    }

    $matched = count($output);

    // Cap the returned rows to the Lines control, keeping the newest matches.
    $shown = $matched;
    $capped = false;
    if ($filtered && $limit > 0 && $matched > $limit) {
        $output = array_slice($output, -$limit);
        $shown = (int)$limit;
        $capped = true;
    }

    $stats = array(
        'total'    => $total,
        'matched'  => $matched,
        'shown'    => $shown,
        'capped'   => $capped,
        'filtered' => $filtered,
    );

    if (empty($output)) {
        $message = $filtered
            ? _('No log entries match the filter.')
            : _('No log entries.');

        return array($ret, array($message), $stats);
    }

    return array($ret, $output, $stats);
}

/**
 * Filename used for log downloads.
 */
function syslogFileName($type)
{
    $label = ($type === 'kernel') ? 'kernel' : 'system';
    return 'syslog-' . $label . '-' . date('Ymd-His') . '.txt';
}

/**
 * Drop the cached source list (used when the operator wants a re-probe).
 */
function syslogClearSourceCache()
{
    if (file_exists(SYSLOG_CACHE_FILE)) {
        @unlink(SYSLOG_CACHE_FILE);
    }
}

/**
 * Displays the system log page.
 */
function DisplaySyslog()
{
    $status = new \ElastPro\Messages\StatusMessage;
    $sources = syslogDetectSources();
    $kernelAvailable = (syslogResolve('dmesg') !== null);

    echo renderTemplate("syslog", compact("status", "sources", "kernelAvailable"));
}

/**
 * Stream a log source to the output buffer, applying the same filter the page
 * uses, without ever collecting the whole result into a PHP array or a single
 * concatenated string. Used by the download endpoint so large or filtered logs
 * do not double-buffer in memory (array + imploded string).
 */
function syslogStreamOutput($type, $sourceKey, $limit, $filter = array())
{
    $filter = empty($filter) ? syslogNormalizeFilter(array()) : $filter;
    $filtered = !syslogFilterIsEmpty($filter);

    $readLimit = $filtered
        ? SYSLOG_FILTER_SCAN_LINES
        : (($limit > 0) ? min((int)$limit, SYSLOG_MAX_LINES) : SYSLOG_MAX_LINES);

    if ($type === 'kernel') {
        $cmd = syslogKernelCommand($readLimit);
    } else {
        if ($sourceKey === '') {
            $sources = syslogDetectSources();
            if (empty($sources)) {
                echo _('No log source available on this device.');
                return;
            }
            $sourceKey = $sources[0]['key'];
        }
        $cmd = syslogReadCommand($sourceKey, $readLimit);
    }

    if ($cmd === null) {
        return;
    }

    $isRegex = !empty($filter['regex']);
    $caseSensitive = !empty($filter['case']);
    $keyword = $filter['keyword'];
    $exclude = $filter['exclude'];

    $fh = popen($cmd . ' 2>&1', 'r');
    if ($fh === false) {
        return;
    }

    while (($line = fgets($fh)) !== false) {
        if ($filtered) {
            if ($keyword !== '' && !syslogMatchOne($line, $keyword, $isRegex, $caseSensitive)) {
                continue;
            }
            if ($exclude !== '' && syslogMatchOne($line, $exclude, $isRegex, $caseSensitive)) {
                continue;
            }
        }
        echo $line;
    }

    pclose($fh);
}
