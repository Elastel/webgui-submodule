<?php

require_once 'config.php';

function DisplayServer()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['saveserversettings']) || isset($_POST['applyserversettings'])) {
            saveServerConfig($status); 
            
            if (isset($_POST['applyserversettings'])) {
                sleep(2);
                exec('sudo /etc/init.d/dct restart > /dev/null');
                $status->addMessage('Configuration applied.', 'success');
            }
        }
    }

    echo renderTemplate('server', compact('status'));
}

function SaveServerUpload($status, $file, $num)
{
    define('KB', 1024);
    $tmp_destdir = '/tmp/';
    $auth_flag = 0;

    try {
        // If undefined or multiple files, treat as invalid
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters');
        }

        $upload = \ElastPro\Uploader\FileUpload::factory('server' . $num, $tmp_destdir);
        $upload->set_max_file_size(64*KB);
        $upload->set_allowed_mime_types(array('text/plain'));
        $upload->file($file);

        $validation = new validation;
        $upload->callbacks($validation, array('check_name_length'));
        $results = $upload->upload();

        if (!empty($results['errors'])) {
            throw new RuntimeException($results['errors'][0]);
        }

        // Valid upload, get file contents
        $tmp_serverconfig = $results['full_path'];

        $path = "/etc/ssl/server" . $num;
        if (!is_dir($path)) {
            exec("sudo /bin/mkdir -p " . $path);
        }

        // Move processed file from tmp to destination
        system("sudo mv $tmp_serverconfig /etc/ssl/server" . $num . "/" . $file['name'], $return);

        // if ($return ==0) {
        //     $status->addMessage('mqtt certificate uploaded successfully', 'info');
        // } else {
        //     $status->addMessage('Unable to save mqtt certificate', 'danger');
        // }
        return $status;

    } catch (RuntimeException $e) {
        $status->addMessage($e->getMessage(), 'danger');
        return $status;
    }
}

function saveServerConfig($status)
{
    for ($i = 1; $i <= 5; $i++) {
        $data['enabled' . $i] = $_POST['enabled' . $i] ?? '0';
        if ($_POST['enabled' . $i] == '1') {
            if ($_POST['certificate_type' . $i] == '1' && $_POST['proto' . $i] == '2') {
                if (strlen($_FILES['mqtt_ca' . $i]['name']) > 0) {
                    if (is_uploaded_file($_FILES['mqtt_ca' . $i]['tmp_name'])) {
                        SaveServerUpload($status, $_FILES['mqtt_ca' . $i], $i);
                    }
                    $fileName = $_FILES['mqtt_ca' . $i]['name'];
                    $data['mqtt_ca' . $i] = $fileName;
                }     
            } else if ($_POST['certificate_type' . $i] == '2'  && $_POST['proto' . $i] == '2') {
                if (strlen($_FILES['mqtt_ca' . $i]['name']) > 0) {
                    if (is_uploaded_file($_FILES['mqtt_ca' . $i]['tmp_name'])) {
                        SaveServerUpload($status, $_FILES['mqtt_ca' . $i], $i);
                    }
                    $fileName = $_FILES['mqtt_ca' . $i]['name'];
                    $data['mqtt_ca' . $i] = $fileName;
                }

                if (strlen($_FILES['mqtt_cert' . $i]['name']) > 0) {
                    if (is_uploaded_file($_FILES['mqtt_cert' . $i]['tmp_name'])) {
                        SaveServerUpload($status, $_FILES['mqtt_cert' . $i], $i);
                    }

                    $certName = $_FILES['mqtt_cert' . $i]['name'];
                    $data['mqtt_cert' . $i] = $certName;
                }

                if (strlen($_FILES['mqtt_key' . $i]['name']) > 0) {
                    if (is_uploaded_file($_FILES['mqtt_key' . $i]['tmp_name'])) {
                        SaveServerUpload($status, $_FILES['mqtt_key' . $i], $i);
                    }

                    $keyName = $_FILES['mqtt_key' . $i]['name'];
                    $data['mqtt_key' . $i] = $keyName;
                }
            }

            $serverInfo = array("proto", "encap_type", "json_format", "server_addr", "http_url", "server_port", "cache_enabled", 
                "register_packet", "register_packet_hex", "heartbeat_packet", "heartbeat_packet_hex", "heartbeat_interval",
                "mqtt_heartbeat_interval", "mqtt_pub_topic", "mqtt_sub_topic", "mqtt_username", "mqtt_password", "sparkplug_group_id",
                "sparkplug_node_id", "sparkplug_device_id", "mqtt_client_id", "mqtt_tls_enabled", "certificate_type", "mqtt_ca", "mqtt_cert", "mqtt_key", 
                "self_define_header", "header_name1_", "header_value1_", "header_name2_", "header_value2_", "header_name3_", "header_value3_",
                "self_define_var", "var_name1_", "var_value1_", "var_name2_", "var_value2_", "var_name3_", "var_value3_", 
                "mn", "st", "pw");

            foreach ($serverInfo as $info) {
                if ($info != "mqtt_ca" && $info != "mqtt_cert" && $info != "mqtt_key") {
                    $data[$info . $i] = $_POST[$info . $i] ?? '';
                } 
            }
        }
    }

    saveCustomJsonFormat($status);

    $json_data = json_encode($data);
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $json_data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct server');

    $status->addMessage('Configuration updated.', 'success');
}

/*
 * ===================== custom-format framework validation =====================
 * These rules must stay in sync with how the backend dctd fills in the data:
 *   1. ${...} variables are replaced by placeholder values by kind (numbers are
 *      emitted without quotes, strings get quotes automatically, variables that
 *      are already quoted in the template are left alone); unknown variables
 *      are kept as-is so the JSON check fails;
 *   2. when the template contains tag variables, the tag fragment is repeated
 *      twice to emulate more than one tag;
 *   3. json_decode validates the result: '' when valid, an error text otherwise.
 */

/* Collect all ${...} variables of the template: name / offset / length */
function customJsonVars($text)
{
    $vars = array();
    $len = strlen($text);
    $i = 0;

    while ($i < $len) {
        if ($text[$i] === '$' && ($i + 1) < $len && $text[$i + 1] === '{') {
            $end = strpos($text, '}', $i);
            if ($end !== false) {
                $vars[] = array(
                    'name' => trim(substr($text, $i + 2, $end - $i - 2)),
                    'pos'  => $i,
                    'len'  => $end - $i + 1
                );
                $i = $end + 1;
                continue;
            }
        }
        $i++;
    }

    return $vars;
}

/* Whether the variable is surrounded by quotes (blanks allowed): the template then provides the quotes */
function customVarQuoted($text, $pos, $len)
{
    $l = $pos - 1;
    while ($l >= 0 && strpos(" \t\r\n", $text[$l]) !== false) {
        $l--;
    }

    $r = $pos + $len;
    $n = strlen($text);
    while ($r < $n && strpos(" \t\r\n", $text[$r]) !== false) {
        $r++;
    }

    if ($l >= 0 && $r < $n && ($text[$l] === '"' || $text[$l] === "'")
        && ($text[$r] === '"' || $text[$r] === "'")) {
        return true;
    }

    return false;
}

/* Whether the variable is written as ${$(command)}: returns the command, or null */
function customVarCommand($name)
{
    $s = trim($name);
    if (strlen($s) >= 3 && substr($s, 0, 2) === '$(' && substr($s, -1) === ')') {
        return trim(substr($s, 2, -1));
    }

    return null;
}

/* Whether the parentheses of a ${$(command)} command are balanced */
function customCmdParenOk($cmd)
{
    $depth = 0;
    $n = strlen($cmd);

    for ($i = 0; $i < $n; $i++) {
        if ($cmd[$i] === '(') {
            $depth++;
        } else if ($cmd[$i] === ')') {
            $depth--;
            if ($depth < 0) {
                return false;
            }
        }
    }

    return ($depth === 0);
}

/*
 * Whether the whole word appears in the command: the characters before and
 * after must not be alphanumeric; "_" counts as part of the word.
 * Must stay in sync with contains_word in config.c.
 */
function customCmdContainsWord($str, $word)
{
    $n = strlen($str);
    $len = strlen($word);
    $pos = 0;

    while (($pos = strpos($str, $word, $pos)) !== false) {
        $before = ($pos === 0) ? true : !ctype_alnum($str[$pos - 1]);

        $k = $pos + $len;
        $after = ($k >= $n) ? true
            : (!ctype_alnum($str[$k]) && $str[$k] !== '_');

        if ($before && $after) {
            return true;
        }

        $pos++;
    }

    return false;
}

/* Whether the command is forbidden: must stay in sync with is_danger_cmd in config.c */
function customCmdIsDanger($cmd)
{
    static $danger = array(
        'rm', 'rmdir', 'unlink', 'mv', 'cp', 'dd',
        'chmod', 'chown', 'chgrp',
        '>', '>>', 'truncate',
        'mkfs', 'mkpart', 'parted', 'fdisk',
        'echo',
        'sudo', 'su',
        'kill', 'killall',
        'reboot', 'shutdown',
        'tee',
        'tar', 'gzip', 'gunzip',
        ':(){',
        'ln'
    );

    $c = ltrim($cmd);

    foreach ($danger as $w) {
        if (customCmdContainsWord($c, $w)) {
            return true;
        }
    }

    return false;
}

/* Placeholder value of a variable */
function customVarPlaceholder($name, $quoted)
{
    if ($quoted) {
        return 'x';                 /* quotes provided by the template */
    }

    if ($name === 'ts' || $name === 'tag_value') {
        return '0';                 /* number: emitted as-is */
    }

    if ($name === 'time' || $name === 'device_name' || $name === 'tag_name') {
        return '"x"';               /* string: quotes added automatically */
    }

    if (customVarCommand($name) !== null) {
        return '0';                 /* ${$(command)}: numbers as-is, the rest quoted */
    }

    return '${' . $name . '}';      /* unknown variable: kept as-is, reported by the check */
}

/* Replace the variables of the template by their placeholder values */
function customJsonReplaceVars($text)
{
    $out = '';
    $len = strlen($text);
    $i = 0;

    while ($i < $len) {
        if ($text[$i] === '$' && ($i + 1) < $len && $text[$i + 1] === '{') {
            $end = strpos($text, '}', $i);
            if ($end !== false) {
                $varLen = $end - $i + 1;
                $name = trim(substr($text, $i + 2, $end - $i - 2));
                $out .= customVarPlaceholder($name, customVarQuoted($text, $i, $varLen));
                $i += $varLen;
                continue;
            }
        }

        $out .= $text[$i];
        $i++;
    }

    return $out;
}

/* Tag fragment: from the first tag variable to the last one; falls back to ${device_name} when there is no tag variable */
function customTagSpan($vars)
{
    $namesList = array(array('tag_name', 'tag_value'), array('device_name'));

    foreach ($namesList as $names) {
        $start = -1;
        $end = -1;

        foreach ($vars as $v) {
            if (in_array($v['name'], $names, true)) {
                if ($start < 0) {
                    $start = $v['pos'];
                }
                $end = $v['pos'] + $v['len'];
            }
        }

        if ($start >= 0) {
            return array($start, $end);
        }
    }

    return null;
}

/* Separator right before the fragment (skipping blanks and one quote): index, or -1 */
function customSepBefore($text, $pos)
{
    $i = $pos - 1;
    while ($i >= 0 && strpos(" \t\r\n", $text[$i]) !== false) {
        $i--;
    }

    if ($i >= 0 && ($text[$i] === '"' || $text[$i] === "'")) {
        $i--;
        while ($i >= 0 && strpos(" \t\r\n", $text[$i]) !== false) {
            $i--;
        }
    }

    if ($i >= 0 && ($text[$i] === ',' || $text[$i] === ';')) {
        return $i;
    }

    return -1;
}

/* Separator right after the fragment (skipping blanks and one quote): array(start, end), or null */
function customSepAfter($text, $pos)
{
    $n = strlen($text);
    $i = $pos;
    while ($i < $n && strpos(" \t\r\n", $text[$i]) !== false) {
        $i++;
    }

    if ($i < $n && ($text[$i] === '"' || $text[$i] === "'")) {
        $i++;
        while ($i < $n && strpos(" \t\r\n", $text[$i]) !== false) {
            $i++;
        }
    }

    if ($i < $n && ($text[$i] === ',' || $text[$i] === ';')) {
        return array($pos, $i + 1);
    }

    return null;
}

/*
 * Compute the range of one repeated fragment. Must stay in sync with
 * custom_span_params in config.c.
 * Returns array(fragment start, fragment end, frame head end, frame tail start, auto comma).
 */
function customSpanParams($text, $start, $end)
{
    $n = strlen($text);
    $sepBefore = customSepBefore($text, $start);
    $sepAfter = customSepAfter($text, $end);

    if ($sepBefore >= 0) {
        /* the leading separator is emitted with every repetition */
        $sepStart = $sepBefore;
        $headEnd = $sepBefore;
        $sepAfterEnd = $end;
    } else {
        $sepStart = $start;
        $headEnd = $start;

        /* without a leading separator, the quote right before the fragment is repeated too */
        if ($start > 0 && ($text[$start - 1] === '"' || $text[$start - 1] === "'")) {
            $sepStart--;
            $headEnd--;
        }

        if ($sepAfter !== null) {
            $sepAfterEnd = $sepAfter[1];    /* includes the trailing separator */
        } else {
            $sepAfterEnd = $end;

            /* without a trailing separator, the quote right after the fragment is repeated too */
            if ($end < $n && ($text[$end] === '"' || $text[$end] === "'")) {
                $sepAfterEnd++;
            }
        }
    }

    $tailStart = $sepAfterEnd;
    $autoComma = ($sepBefore < 0 && $sepAfter === null);

    return array($sepStart, $sepAfterEnd, $headEnd, $tailStart, $autoComma);
}

/* Whether the fragment itself spans a separator (one tag covers several JSON members) */
function customSpanHasInnerSep($text, $start, $end)
{
    for ($i = $start; $i < $end; $i++) {
        if ($text[$i] === ',' || $text[$i] === ';') {
            return true;
        }
    }

    return false;
}

/* Whether the object at start is the value of a member ("data": { ... }) */
function customIsMemberValue($text, $start)
{
    $p = $start - 1;
    while ($p >= 0 && strpos(" \t\r\n", $text[$p]) !== false) {
        $p--;
    }

    return ($p >= 0 && $text[$p] === ':');
}

/* Whether the character is allowed inside a variable name */
function customVarChar($c)
{
    return ($c >= 'a' && $c <= 'z') || ($c >= 'A' && $c <= 'Z')
        || ($c >= '0' && $c <= '9') || $c === '_';
}

/*
 * Find the {...} level enclosing $pos: array(start, end), or null.
 * That level must itself be nested in another container, otherwise repeating
 * it would produce more than one root object.
 */
function customFindFrame($text, $pos)
{
    $n = strlen($text);
    $p = $pos - 1;

    while ($p >= 0) {
        if ($text[$p] === '{') {
            break;
        }

        if ($text[$p] === '}') {
            $k = $p - 1;
            /* the braces of a ${...} variable are not JSON separators: skip the whole variable */
            while ($k >= 0 && customVarChar($text[$k])) {
                $k--;
            }
            if ($k > 0 && $text[$k] === '{' && $text[$k - 1] === '$') {
                $p = $k - 1;
                continue;
            }

            return null;
        }

        if ($text[$p] === '[' || $text[$p] === ']') {
            return null;
        }

        $p--;
    }

    if ($p < 0) {
        return null;
    }

    /* there must be something before it (an array item, etc.), otherwise repeating produces several root objects */
    $k = $p - 1;
    while ($k >= 0 && strpos(" \t\r\n", $text[$k]) !== false) {
        $k--;
    }
    if ($k < 0) {
        return null;
    }

    $depth = 0;
    for ($i = $p; $i < $n; $i++) {
        if ($text[$i] === '$' && ($i + 1) < $n && $text[$i + 1] === '{') {
            $k = $i + 2;
            while ($k < $n && $text[$k] !== '}') {
                $k++;
            }
            $i = $k;
            continue;
        }

        if ($text[$i] === '{') {
            $depth++;
        } else if ($text[$i] === '}') {
            $depth--;
            if ($depth === 0) {
                return array($p, $i + 1);
            }
        }
    }

    return null;
}

/*
 * Check whether the custom JSON framework is valid: '' when valid,
 * an error description otherwise.
 */
function checkCustomJsonFormat($text)
{
    $text = trim($text);
    if ($text === '') {
        return '';
    }

    $vars = customJsonVars($text);

    /*
     * ${$(command)}: report an error directly when the command is empty, its
     * parentheses are unbalanced or it is rejected by the security policy;
     * otherwise the command output takes part in the JSON check below.
     */
    foreach ($vars as $v) {
        $cmd = customVarCommand($v['name']);
        if ($cmd === null) {
            continue;
        }

        if ($cmd === '') {
            return 'empty shell command in ${$()}';
        }

        if (!customCmdParenOk($cmd)) {
            return 'unbalanced parenthesis in ${$(' . $cmd . ')}';
        }

        if (customCmdIsDanger($cmd)) {
            return 'command is not allowed: ' . $cmd;
        }
    }

    $span = customTagSpan($vars);

    if ($span === null) {
        /* no tag variable: check the framework itself */
        $sample = customJsonReplaceVars($text);
    } else {
        list($start, $end) = $span;
        list($sepStart, $sepAfterEnd, $headEnd, $tailStart, $autoComma)
            = customSpanParams($text, $start, $end);

        /*
         * When one tag covers several JSON members ("name":${tag_name},"value":${tag_value}),
         * the fragment itself spans a separator, so what has to be repeated is
         * the object enclosing it.
         * When that object is itself the value of a member ("data": {"name":..,"value":..}),
         * repeating it is still invalid: the object one level above has to be
         * repeated together with it.
         */
        if ($autoComma && customSpanHasInnerSep($text, $start, $end)) {
            $frame = customFindFrame($text, $start);
            if ($frame !== null) {
                list($start, $end) = $frame;
                if (customIsMemberValue($text, $start)) {
                    $outer = customFindFrame($text, $start);
                    if ($outer !== null) {
                        list($start, $end) = $outer;
                    }
                }
                list($sepStart, $sepAfterEnd, $headEnd, $tailStart, $autoComma)
                    = customSpanParams($text, $start, $end);
            }
        }

        $head = substr($text, 0, $headEnd);
        $unit = substr($text, $sepStart, $sepAfterEnd - $sepStart);
        $tail = substr($text, $tailStart);

        if ($autoComma) {
            /* no separator around the fragment: add a comma between repetitions */
            $sample = $head . $unit . ',' . $unit . $tail;
        } else {
            $unitLast = ($sepAfterEnd > $end) ? substr($unit, 0, -1) : $unit;
            $sample = $head . $unit . $unitLast . $tail;
        }

        $sample = customJsonReplaceVars($sample);
    }

    json_decode($sample);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return 'invalid JSON (' . json_last_error_msg() . ')';
    }

    return '';
}

/*
 * When JSON Format = custom-format, save the JSON framework text typed by the
 * user to /etc/config/custom_json_format (a plain text file, not a uci option).
 * The file is shared globally: the content of the first reporting center that
 * selects custom-format is used.
 * The text is validated before saving; invalid text is not saved.
 */
function saveCustomJsonFormat($status)
{
    $customText = null;

    for ($i = 1; $i <= 5; $i++) {
        if (($_POST['json_format' . $i] ?? '') == '4') {
            $customText = $_POST['custom_json_format' . $i] ?? '';
            break;
        }
    }

    if ($customText === null) {
        return; // no reporting center uses custom-format: keep the file unchanged
    }

    $err = checkCustomJsonFormat($customText);
    if ($err !== '') {
        $status->addMessage('Custom JSON Format: ' . $err, 'danger');
        return;
    }

    if (file_put_contents(ELASTEL_CUSTOM_JSON_FORMAT_TMP, $customText) === false) {
        $status->addMessage('Unable to write custom json format.', 'danger');
        return;
    }

    exec('sudo cp -f ' . ELASTEL_CUSTOM_JSON_FORMAT_TMP . ' ' . ELASTEL_CUSTOM_JSON_FORMAT, $output, $result);
    exec('sudo chmod 0644 ' . ELASTEL_CUSTOM_JSON_FORMAT);
    exec('sudo chown root:root ' . ELASTEL_CUSTOM_JSON_FORMAT);
    @unlink(ELASTEL_CUSTOM_JSON_FORMAT_TMP);

    if ($result !== 0) {
        $status->addMessage('Unable to save custom json format.', 'danger');
        return;
    }

    $status->addMessage('Custom json format saved.', 'success');
}
