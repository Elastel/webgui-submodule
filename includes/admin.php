<?php
function DisplayAuthConfig($username)
{
    $status = new \ElastPro\Messages\StatusMessage;
    $auth = new \ElastPro\Auth\HTTPAuth;
    $config = $auth->getAuthConfig();
    
    foreach ($config as $key => $value) {
        if (is_array($value)) {
            if ($value['admin_user'] == $username) {
                $password = $value['admin_pass'];
                $purview = $value['purview'];
                break;
            }
        }
    }
    // $username = $config['admin_user'];
    // $password = $config['admin_pass'];

    if (isset($_POST['UpdateAdminPassword'])) {
        if (password_verify($_POST['oldpass'], $password)) {
            if (strlen($_POST['newpass']) < 1 && $_POST['newpass'] != ' ') {
                $status->addMessage('New passwords must not be empty', 'danger');
            } else if ($_POST['newpass'] !== $_POST['newpassagain']) {
                $status->addMessage('New passwords do not match', 'danger');
            } else {
                if (!file_exists(RASPI_ADMIN_DETAILS)) {
                    $tmpauth = fopen(RASPI_ADMIN_DETAILS, 'w');
                    fclose($tmpauth);
                }
                
                $content = file_get_contents(RASPI_ADMIN_DETAILS);
                if (strlen($content) > 10) {
                    $lines = explode("\n", $content);
                    $new_content = '';
                    foreach ($lines as $key => $value) {
                        $tmp = explode(":", $value);
                        if ($tmp[0] == $username) {
                            $value = $tmp[0].':'. password_hash($_POST['newpass'], PASSWORD_BCRYPT) .':'.$tmp[2];
                        }

                        $new_content .= ($value . "\n");
                    }

                    if (file_put_contents(RASPI_ADMIN_DETAILS, trim($new_content))) {
                        $status->addMessage('Password updated');
                        $auth->logout();
                    } else {
                        $status->addMessage('Failed to update password', 'danger');
                    }
                } 
            }
        } else {
            $status->addMessage('Old password does not match', 'danger');
        }
    } else if (isset($_POST['UpdateAdminSettings'])) {
        saveAuthConfig($status, $config);
        $config = $auth->getAuthConfig();
    } elseif (isset($_POST['logout'])) {
        $auth->logout();
    }

    echo renderTemplate("admin", compact("status", "username", "config"));
}

function checkPassword($config, $user, $pass)
{
    foreach ($config as $key => $value) {
        if (is_array($value)) {
            if ($value['admin_user'] == $user) {
                if ($pass == $value['admin_pass']) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
}

function saveAuthConfig($status, $config)
{
    $data = $_POST['table_data'];
    $arr = json_decode($data, true);
    
    $content = file_get_contents(RASPI_ADMIN_DETAILS);
    if (strlen($content) > 10) {
        $lines = explode("\n", $content);
        $new_content = '';
        foreach ($lines as $key => $value) {
            if (strstr($value, 'admin:') != NULL || strstr($value, 'admin:') != NULL ) {
                $new_content .= $value . "\n";
            }
        }

        foreach ($arr as $list=>$things) {
            $user = '';
            $pass = '';
            if (is_array($things)) {
                $i = 0;
                foreach ($things as $key=>$val) {
                    if ($i == 0) {
                        $str .= $val . ':';
                        $user = $val;
                    } else if ($i == 1) {
                        if (checkPassword($config, $user, $val) == false) {
                            $str .= password_hash($val, PASSWORD_BCRYPT) . ':';
                        } else {
                            $str .= $val . ':';
                        }
                    } else if ($i == 2)
                        $str .= $val;
                    $i++;
                }
                $new_content .= trim($str) . "\n";
                unset($str);
            }
        }

        if (file_put_contents(RASPI_ADMIN_DETAILS, trim($new_content))) {
            $status->addMessage('Authentication settings updated');
        } else {
            $status->addMessage('Failed to update authentication settings', 'danger');
        }

        header("Refresh:0");
    } else {
        $status->addMessage('authentication configuration error ', 'danger');
    }

    return true;
}
