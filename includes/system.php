<?php

require_once 'config.php';

function DisplaySystem()
{

    $status = new \ElastPro\Messages\StatusMessage;
    $model = getModel();

    if (isset($_POST['applyProperties'])) {
        if (isset($_POST['hostname'])) {
            $new_hostname = $_POST['hostname'];
            exec("sudo /var/www/html/installers/update_hostname.sh $new_hostname", $return);
        }

        if (isset($_POST['timezones'])) {
            $timezone = $_POST['timezones'];
            exec("sudo ln -sf /usr/share/zoneinfo/$timezone /etc/localtime");
        }

        $status->addMessage('Configuration applied.', 'success');
    }

    if (isset($_POST['SaveLanguage'])) {
        if (isset($_POST['locale'])) {
            $_SESSION['locale'] = $_POST['locale'];
            exec("sudo uci set system.system.locale=$_POST[locale]");
            exec("sudo uci commit system");
            $status->addMessage('Language setting saved', 'success');
        }
    }

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['SaveServerSettings'])) {
            $good_input = true;
            // Validate server port
            if (isset($_POST['serverPort'])) {
                if (strlen($_POST['serverPort']) > 4 || !is_numeric($_POST['serverPort'])) {
                    $status->addMessage('Invalid value for port number', 'danger');
                    $good_input = false;
                } else {
                    $serverPort = escapeshellarg($_POST['serverPort']);
               }
            }
            // Validate server bind address
            $serverBind = escapeshellarg('');
            if ($_POST['serverBind'] && $_POST['serverBind'] !== null ) {
                if (!filter_var($_POST['serverBind'], FILTER_VALIDATE_IP)) {
                    $status->addMessage('Invalid value for bind address', 'danger');
                    $good_input = false;
                } else {
                    $serverBind = escapeshellarg($_POST['serverBind']);
                }
            }
            // Save settings
            if ($good_input) {
                exec("sudo /etc/raspap/lighttpd/configport.sh $serverPort $serverBind " .RASPI_LIGHTTPD_CONFIG. " ".$_SERVER['SERVER_NAME'], $return);
                foreach ($return as $line) {
                    $status->addMessage($line, 'info');
                }
            }
        }

        if (isset($_POST['system_reboot'])) {
            $status->addMessage("System Rebooting Now!", "warning", false);
            $result = shell_exec("sudo /sbin/reboot");
        }
        if (isset($_POST['system_shutdown'])) {
            $status->addMessage("System Shutting Down Now!", "warning", false);
            $result = shell_exec("sudo /sbin/shutdown -h now");
        }
    }

    if (isset($_POST['RestartLighttpd'])) {
        $status->addMessage('Restarting lighttpd in 3 seconds...', 'info');
        exec('sudo /etc/raspap/lighttpd/configport.sh --restart');
    }
    exec('cat '. RASPI_LIGHTTPD_CONFIG, $return);
    $conf = ParseConfig($return);
    $serverPort = $conf['server.port'];
    $serverBind = str_replace('"', '',$conf['server.bind']);

    // define locales
    $arrLocales = array(
        'en_GB.UTF-8' => 'English',
        // 'cs_CZ.UTF-8' => 'Čeština',
        // 'zh_TW.UTF-8' => '正體中文 (Chinese traditional)',
        'zh_CN.UTF-8' => '简体中文 (Chinese simplified)',
        // 'da_DK.UTF-8' => 'Dansk',
        // 'de_DE.UTF-8' => 'Deutsch',
        // 'es_MX.UTF-8' => 'Español',
        // 'fi_FI.UTF-8' => 'Finnish',
        // 'fr_FR.UTF-8' => 'Français',
        // 'el_GR.UTF-8' => 'Ελληνικά',
        // 'id_ID.UTF-8' => 'Indonesian',
        // 'it_IT.UTF-8' => 'Italiano',
        // 'ja_JP.UTF-8' => '日本語 (Japanese)',
        // 'ko_KR.UTF-8' => '한국어 (Korean)',
        // 'nl_NL.UTF-8' => 'Nederlands',
        // 'pl_PL.UTF-8' => 'Polskie',
        // 'pt_BR.UTF-8' => 'Português',
        // 'ru_RU.UTF-8' => 'Русский',
        // 'ro_RO.UTF-8' => 'Română',
        // 'sv_SE.UTF-8' => 'Svenska',
        // 'tr_TR.UTF-8' => 'Türkçe',
        // 'vi_VN.UTF-8' => 'Tiếng Việt (Vietnamese)'
    );

    #fetch system status variables.
    $system = new \ElastPro\System\Sysinfo;

    // $hostname = $system->hostname();
    $uptime   = $system->uptime();
    $cores    = $system->processorCount();

    // mem used
    $memused  = $system->usedMemory();
    $memused_status = "primary";
    if ($memused > 90) {
        $memused_status = "danger";
        $memused_led = "service-status-down";
    } elseif ($memused > 75) {
        $memused_status = "warning";
        $memused_led = "service-status-warn";
    } elseif ($memused >  0) {
        $memused_status = "success";
        $memused_led = "service-status-up";
    }

    // cpu load
    $cpuload = $system->systemLoadPercentage();
    if ($cpuload > 90) {
        $cpuload_status = "danger";
    } elseif ($cpuload > 75) {
        $cpuload_status = "warning";
    } elseif ($cpuload >=  0) {
        $cpuload_status = "success";
    }

    // cpu temp
    if ($model != 'EG324L') {
        $cputemp = $system->systemTemperature();
    } else {
        $cputemp = file_get_contents("/sys/class/thermal/thermal_zone0/temp");
    }
    
    if ($cputemp > 70) {
        $cputemp_status = "danger";
        $cputemp_led = "service-status-down";
    } elseif ($cputemp > 50) {
        $cputemp_status = "warning";
        $cputemp_led = "service-status-warn";
    } else {
        $cputemp_status = "success";
        $cputemp_led = "service-status-up";
    }

    // hostapd status
    $hostapd = $system->hostapdStatus();
    if ($hostapd[0] == 1) {
        $hostapd_status = "active";
        $hostapd_led = "service-status-up";
    } else {
        $hostapd_status = "inactive";
        $hostapd_led = "service-status-down";
    }

    // properties
    $current_time = getSystemTime();

    exec("readlink /etc/localtime", $cur_timezone);
    if ($cur_timezone[0] == null) {
        exec("sudo ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime");
        $_SESSION['timezones'] = "Asia/Shanghai";
    } else {
        $substring = "zoneinfo/";
        $result = strstr($cur_timezone[0], $substring, false);
        $result = substr($result, strlen($substring));

        $_SESSION['timezones'] = $result;
    }
    
    $cur_hostname = getHostname();
    $sn = getSn();

    echo renderTemplate("system", compact(
        "arrLocales",
        "status",
        "serverPort",
        "serverBind",
        "uptime",
        "cores",
        "memused",
        "memused_status",
        "memused_led",
        "cpuload",
        "cpuload_status",
        "cputemp",
        "cputemp_status",
        "cputemp_led",
        "hostapd",
        "hostapd_status",
        "hostapd_led",
        "current_time",
        "cur_hostname",
        "sn"
    ));
}
