<?php

require_once 'config.php';

function DisplayTimeSetting()
{
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        // Apply settings: system time, then write back to RTC
        if (isset($_POST['applyTimeSetting'])) {
            $applySuccess = true;

            // Set system time
            if (isset($_POST['system_time']) && !empty($_POST['system_time'])) {
                $new_time = trim($_POST['system_time']);
                $new_time = str_replace('T', ' ', $new_time);
                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$/', $new_time)) {
                    if (strlen($new_time) == 16) {
                        $new_time .= ':00';
                    }
                    exec("sudo date -s '$new_time' 2>&1", $output, $ret);
                    if ($ret === 0) {
                        $status->addMessage('System time updated to ' . $new_time . '.', 'success');
                    } else {
                        $applySuccess = false;
                        $status->addMessage('Failed to set system time: ' . implode("\n", $output), 'danger');
                    }
                } else {
                    $applySuccess = false;
                    $status->addMessage('Invalid time format. Please use YYYY-MM-DD HH:MM:SS.', 'danger');
                }
            }

            // Write back to RTC so the time survives reboot
            if ($applySuccess) {
                exec("sudo hwclock --systohc 2>&1", $hw_output, $hw_ret);
                if ($hw_ret === 0) {
                    $status->addMessage('Configuration applied and synchronized to RTC.', 'success');
                } else {
                    $status->addMessage('Configuration applied, but RTC sync failed: ' . implode("\n", $hw_output), 'warning');
                }
            }
        }
    }

    // Read current system time
    $system_time = getSystemTime();

    // Read RTC time
    exec("sudo hwclock -r 2>&1", $rtc_output, $rtc_ret);
    if ($rtc_ret === 0 && !empty($rtc_output)) {
        $rtc_time = trim(implode("\n", $rtc_output));
    } else {
        $rtc_time = 'RTC not available';
    }

    echo renderTemplate('time_setting', compact(
        'status',
        'system_time',
        'rtc_time'
    ));
}
