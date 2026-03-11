<?php

require_once 'config.php';

function DisplayThingsWing()
{   
    $status = new \ElastPro\Messages\StatusMessage;
    $model = getModel();

    $enable = file_exists("/usr/local/baseagent/baseagent");
    
    if ((isset($_POST['restart']) || isset($_POST['enable']))&& $enable) {
        if ($model == 'EG324L' || $model == 'EC212') {
            if (isset($_POST['enable'])) {
                exec('chmod +x /etc/init.d/baseagent \
                /etc/init.d/newficus \
                /etc/init.d/device_console \
                /etc/init.d/plc-rel \
                /etc/init.d/remote-serial-server');
            }
            exec("/etc/init.d/baseagent restart;
                /etc/init.d/newficus restart;
                /etc/init.d/device_console restart;
                /etc/init.d/plc-rel restart;
                /etc/init.d/remote-serial-server restart");
        } else {
            exec('sudo systemctl restart baseagent;
                sudo systemctl restart plc-rel;
                sudo systemctl restart device_console;
                sudo systemctl restart remote-serial-server;
                sudo systemctl restart newficus');
            if (isset($_POST['enable'])) {
                exec('sudo systemctl enable baseagent;
                sudo systemctl enable plc-rel;
                sudo systemctl enable device_console;
                sudo systemctl enable remote-serial-server;
                sudo systemctl enable newficus;
                sudo systemctl daemon-reload');
            }
        }
    } else if ((isset($_POST['stop']) || isset($_POST['disable'])) && $enable) {
        if ($model == 'EG324L' || $model == 'EC212') {
            exec("/etc/init.d/baseagent stop;
                /etc/init.d/newficus stop;
                /etc/init.d/device_console stop;
                /etc/init.d/plc-rel stop;
                /etc/init.d/remote-serial-server stop");
            if (isset($_POST['disable'])) {
                exec('chmod 444 /etc/init.d/baseagent \
                /etc/init.d/newficus \
                /etc/init.d/device_console \
                /etc/init.d/plc-rel \
                /etc/init.d/remote-serial-server');
            }
        } else {
            exec('sudo systemctl stop baseagent;
                sudo systemctl stop plc-rel;
                sudo systemctl stop device_console;
                sudo systemctl stop remote-serial-server;
                sudo systemctl stop newficus');
            if (isset($_POST['disable'])) {
                exec('sudo systemctl disable baseagent;
                sudo systemctl disable plc-rel;
                sudo systemctl disable device_console;
                sudo systemctl disable remote-serial-server;
                sudo systemctl disable newficus;
                sudo systemctl daemon-reload');
            }
        }
    } else if (isset($_POST['install'])) {
        if ($model == 'EG500' || $model == 'ElastBox400' || $model == 'EG410' || $model == 'EG510')
            exec('curl -L https://storage.thingswing.com/package/install_eg500.sh | sudo bash -s', $return);
        else if ($model == 'EG324')
            exec('curl -L https://storage.thingswing.com/package/install_eg324.sh | sudo bash -s', $return);
        else if ($model == 'EG324L')
            exec('curl -L https://storage.thingswing.com/package/install_eg324l_merge.sh | sudo bash -s', $return);

        if (strstr(end($return), "success")) {
            $status->addMessage("ThingsWing installed successfully", 'info');
            $enable = true;
        }  else {
            $status->addMessage("ThingsWing installation failed", 'danger');
        }
    }

    $version = '-';
    $use_sn = '-';
    $auth_code = '-';
    $run_status = '-';
    $start_enable = false;

    if ($enable) {
        if ($model == 'EG324L' || $model == 'EC212') {
            exec('test -x /etc/init.d/baseagent && echo 1 || echo 0', $tmp);
            $start_enable = $tmp[0] == '1' ? true : false;
            unset($tmp);
        } else {
            exec("systemctl is-enabled baseagent", $tmp);
            $start_enable = $tmp[0] == 'enabled' ? true : false;
            unset($tmp);
        }
    }

    if ($enable) {
        exec('/usr/local/baseagent/baseagent -v', $tmp);
        $version = $tmp[0];
        unset($tmp);
        exec('pgrep baseagent', $run_status);
        exec('/usr/sbin/authkeygen', $tmp);
        $str = explode(":", $tmp[0]);
        if (isset($str[1])) {
            $use_sn = $str[1];
        }
        unset($str);
        $str = explode(":", $tmp[1]);
        if (isset($str[1])) {
            $auth_code = $str[1];
        }
    }

    echo renderTemplate(
        'things_wing', compact(
            'status',
            'enable',
            'run_status',
            'version',
            'use_sn',
            'auth_code',
            'start_enable'
        )
    );
}

