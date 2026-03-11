<?php

require_once 'includes/config.php';
require_once 'includes/wifi_functions.php';

getWifiInterface();

/**
 * Manage OpenVPN configuration
 */
function DisplayOpenVPNConfig()
{
    $model = getModel();
    $status = new \ElastPro\Messages\StatusMessage;

    $cipher=array('BF-CBC', 'DES-EDE-CBC', 'DES-EDE3-CBC', 'AES-128-CBC', 'AES-192-CBC',
                'AES-256-CBC');

    $auth=array('MD4', 'RSA-MD4', 'MD5', 'RSA-MD5', 'SHA1', 'RSA-SHA1', 'RSA-SHA1-2', 'DSA-SHA1',
            'DSA', 'RIPEMD160', 'RSA-RIPEMD160', 'RSA-SHA224', 'RSA-SHA256', 'RSA-SHA384',
            'RSA-SHA512', 'SHA224', 'SHA256', 'SHA384', 'SHA512');

    if (isset($_POST['SaveOpenVPNSettings']) || isset($_POST['ApplyOpenVpnSettings'])) {
        // check path
        $clientPath = "/etc/openvpn/client";
        if (!is_dir($clientPath)) {
            mkdir($clientPath, 0777, true);
        }

        $serverPath = "/etc/openvpn/server";
        if (!is_dir($serverPath)) {
            mkdir($serverPath, 0777, true);
        }

        saveOpenVpnConfig($status);
        if (isset($_POST['ApplyOpenVpnSettings'])) {
            $status->addMessage('Attempting to stop OpenVPN', 'info');
            
            if (isset($_POST['role'])) { 
                $role = $_POST['role'];
            }

            if (isset($_POST['type'])) {
                $type = $_POST['type'];
            }

            if ($model != "EG324L" && $model != "EC212" && $model != "EC211") {
                exec('sudo /bin/systemctl stop openvpn-client@client', $return);
                exec('sudo /bin/systemctl disable openvpn-client@client', $return);
                exec('sudo /bin/systemctl stop openvpn-server@server', $return);
                exec('sudo /bin/systemctl disable openvpn-server@server', $return);
            } else {
                exec('sudo /etc/init.d/S60openvpn stop');
            }
            
            sleep(1);
            if ($type != 'off') {
                $status->addMessage('Attempting to start OpenVPN', 'info');
                if ($model != "EG324L" && $model != "EC212" && $model != "EC211") {
                    if ($role == 'client') {
                        exec('sudo /bin/systemctl enable openvpn-client@client', $return);
                        exec('sudo /bin/systemctl start openvpn-client@client', $return);
                        exec("sudo /etc/raspap/openvpn/configauth.sh $tmp_ovpn $auth_flag " .$_SESSION['ap_interface'], $return);
                    } else {
                        exec('sudo /bin/systemctl enable openvpn-server@server', $return);
                        exec('sudo /bin/systemctl start openvpn-server@server', $return);
                    }
                } else {
                    exec('sudo /etc/init.d/S60openvpn restart');
                }
            } else {
                system('sudo rm /etc/openvpn/client/client.conf', $return);
                system('sudo rm /etc/openvpn/server/server.conf', $return);
            }

            foreach ($return as $line) {
                $status->addMessage($line, 'info');
            }
        }

        exec('sudo /usr/local/bin/uci commit openvpn');
    }

    exec('pidof openvpn | wc -l', $openvpnstatus);
    $serviceStatus = $openvpnstatus[0] == 0 ? 'down' : 'up';

    echo renderTemplate(
        'openvpn', compact(
            'status',
            'serviceStatus',
            'cipher',
            'auth'
        )
    );
}

function saveOpenVpnConfig($status)
{
    if (isset($_POST['type'])) {
        exec('sudo /usr/local/bin/uci set openvpn.openvpn.type=' . $_POST['type']);
        if ($_POST['type'] == 'config') {
            $role = $_POST['role'];
            exec('sudo /usr/local/bin/uci set openvpn.openvpn.role=' . $role);

            saveConfigs($status, $role);

            if (strlen($_FILES['ca']['name']) > 0) {
                if (is_uploaded_file($_FILES['ca']['tmp_name'])) {
                    SaveOpenvpnUpload($status, $_FILES['ca'], $role);
                }
            }

            if (strlen($_FILES['dh']['name']) > 0) {
                if (is_uploaded_file($_FILES['dh']['tmp_name'])) {
                    SaveOpenvpnUpload($status, $_FILES['dh'], $role);
                }
            }

            if (strlen($_FILES['ta']['name']) > 0) {
                if (is_uploaded_file($_FILES['ta']['tmp_name'])) {
                    SaveOpenvpnUpload($status, $_FILES['ta'], $role);
                }
            }  

            if (strlen($_FILES['cert']['name']) > 0) {
                if (is_uploaded_file($_FILES['cert']['tmp_name'])) {
                    SaveOpenvpnUpload($status, $_FILES['cert'], $role);
                }
            }

            if (strlen($_FILES['key']['name']) > 0) {
                if (is_uploaded_file($_FILES['key']['tmp_name'])) {
                    SaveOpenvpnUpload($status, $_FILES['key'], $role);
                }
            }
        } else if ($_POST['type'] == 'ovpn') {
            $role = $_POST['role'];
            exec('sudo /usr/local/bin/uci set openvpn.openvpn.role=' . $role);
            if (strlen($_FILES['ovpn']['name']) > 0) {
                if (is_uploaded_file($_FILES['ovpn']['tmp_name'])) {
                    SaveOVPNConfig($status, $_FILES['ovpn'], $role);
                    exec('sudo /usr/local/bin/uci set openvpn.openvpn.ovpn_file=' . $_FILES['ovpn']['name']);
                }
            }

            if (strlen($_POST['text_user_pwd']) > 5) {
                exec('sudo /usr/local/bin/uci get openvpn.openvpn.ovpn_file', $ovpn_file);
                $ovpn_path = "/etc/openvpn/$role/" . $ovpn_file[0];
                if ($role == 'client') {
                    exec("sudo sed -i 's/^auth-user-pass.*/auth-user-pass \/etc\/openvpn\/client\/login.conf/g' $ovpn_path");
                } else {
                    exec("sudo sed -i 's/^auth-user-pass-verify.*/auth-user-pass-verify \/etc\/openvpn\/server\/checkpsw.sh via-env/g' $ovpn_path");
                }
            }
        }

        saveUserPass($status, $role);
    }
}

function isFileExist($role, $key)
{
    $conf = file_get_contents('/etc/openvpn/' . $role . '/' . $role . '.conf');
    unset($tmp);
    unset($file_path);
    if ($key != 'tls-auth') {
        preg_match("/$key\s\/etc(.*)/", $conf, $tmp);
        if ($tmp[1] != NULL)
            $file_path = '/etc' . $tmp[1];
    } else {
        if ($role == 'client')
            preg_match("/$key\s(.*)\s1/", $conf, $tmp);
        else
            preg_match("/$key\s(.*)\s0/", $conf, $tmp);

        if ($tmp[1] != NULL)
            $file_path = '/etc/openvpn/' . $role . '/' . $tmp[1];
    }
    
    if(file_exists($file_path)) {
        if ($key != 'tls-auth')
            return $file_path;
        else
            return $tmp[1];
    } else {
        return false;
    }
}

function saveConfigs($status, $role)
{
    if ($role == "client") {
        $cfg[] = 'client' . PHP_EOL;
        if (isset($_POST['vpn_server'])) {
            $cfg[] = 'remote '.$_POST['vpn_server'] . PHP_EOL;
        }
        $cfg[] = 'nobind' . PHP_EOL;

        $tls_auth_num = '1';
    } else if ($role == "server") {
        $cfg[] = 'client-to-client' . PHP_EOL;
        if (isset($_POST['tunnel_subnet']) && isset($_POST['tunnel_mask'])) {
            $cfg[] = 'server ' .$_POST['tunnel_subnet'] . ' ' .$_POST['tunnel_mask'] . PHP_EOL;
        }

        $cfg[] = 'push "' . $_POST['tunnel_subnet'] . ' ' .$_POST['tunnel_mask'] . '"' . PHP_EOL;

        if (isset($_POST['keepalive'])) {
            $cfg[] = 'keepalive '.$_POST['keepalive'] . PHP_EOL;
        }

        $tls_auth_num = '0';
    }

    $cfg[] = 'resolv-retry infinite' . PHP_EOL;
    $cfg[] = 'persist-key' . PHP_EOL;
    $cfg[] = 'persist-tun' . PHP_EOL;
    $cfg[] = 'verb 3' . PHP_EOL;
    $cfg[] = 'log /var/log/openvpn/openvpn.log' . PHP_EOL;
    $cfg[] = 'status /var/log/openvpn/openvpn-status.log' . PHP_EOL;

    if (isset($_POST['proto'])) {
        $cfg[] = 'proto '.$_POST['proto'] . PHP_EOL;
    }
    if (isset($_POST['port'])) {
      $cfg[] = 'port '.$_POST['port'] . PHP_EOL;
    }
    if (isset($_POST['dev'])) {
        $cfg[] = 'dev '.$_POST['dev'] . PHP_EOL;
    }
    if (isset($_POST['cipher'])) {
        $cfg[] = 'cipher '.$_POST['cipher'] . PHP_EOL;
    }
    if (isset($_POST['comp_lzo'])) {
        if ($_POST['comp_lzo'] == '1') {
            $cfg[] = 'comp-lzo '.$_POST['comp-lzo'] . PHP_EOL;
        }  
    }
    if (isset($_POST['auth'])) {
        if ($_POST['auth'] != 'ignore') {
            $cfg[] = 'auth '.$_POST['auth'] . PHP_EOL;
        }
    }
    if (strlen($_FILES['ca']['name']) > 0) {
        $cfg[] = 'ca /etc/openvpn/' .$role . '/' .$_FILES['ca']['name'] . PHP_EOL;
    } else if ($ca_path = isFileExist($role, 'ca')) {
        $cfg[] = 'ca ' .$ca_path . PHP_EOL;
    }

    if (strlen($_FILES['ta']['name']) > 0) {
        $cfg[] = 'tls-auth ' .$_FILES['ta']['name'] . ' ' . $tls_auth_num . PHP_EOL;
    } else if ($ta_path = isFileExist($role, 'tls-auth')) {
        $cfg[] = 'tls-auth ' .$ta_path . ' ' . $tls_auth_num . PHP_EOL;
    }

    if (strlen($_FILES['dh']['name']) > 0) {
        $cfg[] = 'dh /etc/openvpn/' .$role . '/' .$_FILES['dh']['name'] . PHP_EOL;
    } else if ($dh_path = isFileExist($role, 'dh')) {
        $cfg[] = 'dh ' .$dh_path . PHP_EOL;
    }

    if (strlen($_FILES['cert']['name']) > 0) {
        $cfg[] = 'cert /etc/openvpn/' .$role . '/' .$_FILES['cert']['name'] . PHP_EOL;
    } else if ($cert_path = isFileExist($role, 'cert')) {
        $cfg[] = 'cert ' .$cert_path . PHP_EOL;
    }
    if (strlen($_FILES['key']['name']) > 0) {
        $cfg[] = 'key /etc/openvpn/' .$role . '/' .$_FILES['key']['name'] . PHP_EOL;
    } else if ($key_path = isFileExist($role, 'key')) {
        $cfg[] = 'key ' .$key_path . PHP_EOL;
    }

    if ($_POST['proto'] == 'udp') {
        $cfg[] = 'explicit-exit-notify 1' . PHP_EOL;
    }
    
    if (strlen($_POST['text_user_pwd']) > 5) {
        if ($role == 'client') {
            $cfg[] = 'auth-user-pass /etc/openvpn/client/login.conf' . PHP_EOL;
        } else {
            if (strlen($_FILES['cert']['name']) > 0) {
                $cfg[] = 'cert /etc/openvpn/' .$role . '/' .$_FILES['cert']['name'] . PHP_EOL;
            } else if ($cert_path = isFileExist($role, 'cert')) {
                $cfg[] = 'cert ' .$cert_path . PHP_EOL;
            }
            if (strlen($_FILES['key']['name']) > 0) {
                $cfg[] = 'key /etc/openvpn/' .$role . '/' .$_FILES['key']['name'] . PHP_EOL;
            } else if ($key_path = isFileExist($role, 'key')) {
                $cfg[] = 'key ' .$key_path . PHP_EOL;
            }
            $cfg[] = 'script-security 3' . PHP_EOL;
            $cfg[] = 'username-as-common-name' . PHP_EOL;
            //     $cfg[] = 'verify-client-cert none' . PHP_EOL;
            $cfg[] = 'auth-user-pass-verify /etc/openvpn/server/checkpsw.sh via-env' . PHP_EOL;
        }
    }
    

    $tmp_path = '/tmp/openvpn.conf';
    file_put_contents($tmp_path, $cfg);
    chmod($tmp_path, 0755);
    if ($role == "client") {
        system("sudo mv $tmp_path /etc/openvpn/client/client.conf", $return);
    } else if ($role == "server") {
        system("sudo mv $tmp_path /etc/openvpn/server/server.conf", $return);
    }

    return $status;
}

function saveUserPass($status, $role)
{   
    // check checkpsw.sh
    if (!file_exists("/etc/openvpn/server/checkpsw.sh")) {
        exec("sudo cp /var/www/html/installers/checkpsw.sh /etc/openvpn/server/checkpsw.sh");
    }

    if (strlen($_POST['text_user_pwd']) > 5) {
        $authUserPwd = strtr(strip_tags(trim($_POST['text_user_pwd'])), ["\r\n" => "\n"]);
    } else {
        if ($role == 'client') {
            exec("sudo rm /etc/openvpn/client/login.conf");
        } else {
            exec("sudo rm /etc/openvpn/server/psw-file");
        }
        return;
    }

    if (!empty($authUserPwd)) {
        $tmp_authdata = '/tmp/vpnAuthData';
        if ($role == 'client') {
            $parts = explode(' ', $authUserPwd);
            $auth = $parts[0] . PHP_EOL . $parts[1] . PHP_EOL;
            file_put_contents($tmp_authdata, $auth);
            chmod($tmp_authdata, 0644);
            $login_path = '/etc/openvpn/'. $role . '/login.conf';
            system("sudo mv $tmp_authdata $login_path", $return);
        } else {
            $auth = $authUserPwd .PHP_EOL;
            file_put_contents($tmp_authdata, $auth);
            chmod($tmp_authdata, 0644);
            $login_path = '/etc/openvpn/'. $role . '/psw-file';
            system("sudo mv $tmp_authdata $login_path", $return);
        }
        
        if ($return !=0) {
            $status->addMessage('Unable to save client auth credentials', 'danger');
        }
    }
}

function SaveOpenvpnUpload($status, $file, $role)
{
    define('KB', 1024);
    $tmp_destdir = '/tmp/';
    $auth_flag = 0;

    try {
        // If undefined or multiple files, treat as invalid
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters');
        }

        $upload = \ElastPro\Uploader\FileUpload::factory('vpn' . $num, $tmp_destdir);
        $upload->set_max_file_size(64*KB);
        $upload->set_allowed_mime_types(array('text/plain', 'application/octet-stream'));
        $upload->file($file);

        $validation = new validation;
        $upload->callbacks($validation, array('check_name_length'));
        $results = $upload->upload();

        if (!empty($results['errors'])) {
            throw new RuntimeException($results['errors'][0]);
        }

        // Valid upload, get file contents
        $tmp_serverconfig = $results['full_path'];

        $path = "/etc/openvpn/" . $role;
        if (!is_dir($path)) {
            exec("sudo /bin/mkdir -p " . $path);
        }

        // Move processed file from tmp to destination
        system("sudo mv $tmp_serverconfig " . $path . "/" . $file['name'], $return);

        return $status;

    } catch (RuntimeException $e) {
        $status->addMessage($e->getMessage(), 'danger');
        return $status;
    }
}

/**
 * Validates uploaded .ovpn file, adds auth-user-pass and
 * stores auth credentials in login.conf. Copies files from
 * tmp to OpenVPN
 *
 * @param  object $status
 * @param  object $file
 * @param  string $authUser
 * @param  string $authPassword
 * @return object $status
 */
function SaveOVPNConfig($status, $file, $role)
{
    define('KB', 1024);
    $tmp_destdir = '/tmp/';
    $auth_flag = 0;

    try {
        // If undefined or multiple files, treat as invalid
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters');
        }

        $upload = \ElastPro\Uploader\FileUpload::factory('ovpn',$tmp_destdir);
        $upload->set_max_file_size(64*KB);
        $upload->set_allowed_mime_types(array('ovpn' => 'text/plain'));
        $upload->file($file);

        $validation = new validation;
        $upload->callbacks($validation, array('check_name_length'));
        $results = $upload->upload();

        if (!empty($results['errors'])) {
            throw new RuntimeException($results['errors'][0]);
        }

        // Set iptables rules and, optionally, auth-user-pass
        $tmp_ovpn = $results['full_path'];

        // Move uploaded ovpn config from /tmp and create symlink
        $ovpn_path = "/etc/openvpn/$role/" .$file['name'];
        $ovpn_conf = "/etc/openvpn/$role/$role.conf";
        chmod($tmp_ovpn, 0644);
        exec("sudo rm /etc/openvpn/$role/*.ovpn");
        system("sudo mv $tmp_ovpn $ovpn_path", $return);
        system("sudo rm $ovpn_conf", $return);
        system("sudo ln -s $ovpn_path $ovpn_conf", $return);

        if ($return == 0) {
            $status->addMessage("OpenVPN $role uploaded successfully", 'info');
        } else {
            $status->addMessage("Unable to save OpenVPN $role config", 'danger');
        }

        return $status;
    } catch (RuntimeException $e) {
        $status->addMessage($e->getMessage(), 'danger');
        return $status;
    }
}

