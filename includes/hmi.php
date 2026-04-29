<?php

require_once 'config.php';

function DisplayHmi()
{   
    $arrInfo = array('hmi_mode', 'browser_url', 'hmi_backlight_timeout', 'hmi_touchbeep');
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savehmisettings']) || isset($_POST['applyhmisettings'])) {
            saveHmiConfig($arrInfo, $status);  
            
            if (isset($_POST['applyhmisettings'])) {
                exec('sudo /etc/init.d/hmi restart > /dev/null');
            }
        }
    }

    if (isset($_POST['replace'])) {
        if (strlen($_FILES['upload_file']['name']) > 0) {
            if (is_uploaded_file($_FILES['upload_file']['tmp_name'])) {
                replaceLogo($status, $_FILES['upload_file']);
            } else {
                $status->addMessage('Fail to upload file', 'danger');
            }
        }
    }

    if (isset($_POST['hmi_calibration'])) {
        if (file_exists('/usr/sbin/tp_calibrate')) {
            exec("sudo /usr/sbin/tp_calibrate > /dev/null 2>&1 &");
            $status->addMessage('perform calibration successfully', 'info');
        } else {
            $status->addMessage('does not support calibration', 'danger');
        }
    }

    exec("sudo /usr/local/bin/uci get system.hmi.enabled", $tmp);
    $hmi['enabled'] = $tmp[0] ?? 0;
    if ($hmi['enabled'] == 1) {
        foreach ($arrInfo as $info) {
            unset($tmp);
            exec("sudo /usr/local/bin/uci get system.hmi.$info", $tmp);
            $hmi[$info] = $tmp[0] ?? '';
        }
    }

    $tmp = file_get_contents('/sys/class/backlight/pwm-backlight/brightness');
    $hmi['hmi_brightness'] = $tmp ?? '255';

    echo renderTemplate("hmi", compact(
        'status',
        'hmi'
    ));
}

function saveHmiConfig($arrInfo, $status)
{
    exec("sudo /usr/local/bin/uci set system.hmi.enabled=" .$_POST['enabled']);
    if ($_POST['enabled'] == "1") {
        foreach ($arrInfo as $info) {
            if ($info == 'hmi_brightness') {
                $brightness = intval($_POST["hmi_brightness"]);
                if ($brightness < 0) {
                    $brightness = 0;
                } elseif ($brightness > 255) {
                    $brightness = 255;
                }
                exec("sudo /usr/local/bin/uci set system.hmi.$info=$brightness");
            } else {
                exec("sudo /usr/local/bin/uci set system.hmi.$info=$_POST[$info]");
            }
        }
    } 
    
    exec("sudo /usr/local/bin/uci commit system");

    $status->addMessage('configuration updated ', 'success');
    return true;
}

function replaceLogo($status, $file)
{
    define('KB', 1024);
    $tmp_destdir = '/tmp/';
    $auth_flag = 0;
    $target = getTarget();

    try {
        // If undefined or multiple files, treat as invalid
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters');
        }

        $upload = \ElastPro\Uploader\FileUpload::factory('hmi', $tmp_destdir);
        $upload->set_max_file_size(10*1024*KB);
        $upload->set_allowed_mime_types(array('image/bmp', 'image/x-ms-bmp'));
        $upload->file($file);
        $validation = new validation;
        $upload->callbacks($validation, array('check_name_length'));
        $results = $upload->upload();

        if (!empty($results['errors'])) {
            throw new RuntimeException($results['errors'][0]);
        }

        // Valid upload, get file contents
        $file_path = $results['full_path'];
        $new_file_path = "/mnt/mmc/logo.bmp";

        if (strpos($target, 'EH607') !== false) {
            system("sudo mount /dev/mmcblk0p1 /mnt/mmc");
            system("sudo mv $file_path $new_file_path");
        } else {
            $status->addMessage('does not support replacing logo', 'danger');
            return $status;
        }
        
        if (file_exists($new_file_path)) {
            $status->addMessage('replace logo successfully:' . $new_file_path, 'info');
        } else {
            $status->addMessage('fail to replace logo', 'danger');
        }

        if (strpos($target, 'EH607') !== false) {
            system("sudo umount /mnt/mmc");
        }

        return $status;
    } catch (RuntimeException $e) {
        $status->addMessage($e->getMessage(), 'danger');
        return $status;
    }
}

