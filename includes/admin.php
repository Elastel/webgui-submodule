<?php
function DisplayAuthConfig($username)
{
    $status = new \ElastPro\Messages\StatusMessage;
    $auth = new \ElastPro\Auth\HTTPAuth;
    $config = $auth->getAuthConfig();
    $username = $config['admin_user'];
    $password = $config['admin_pass'];

    if (isset($_POST['UpdateAdminPassword'])) {
        if (password_verify($_POST['oldpass'], $password)) {
            $new_username=trim($_POST['username']);
            if ($_POST['newpass'] !== $_POST['newpassagain']) {
                $status->addMessage('New passwords do not match', 'danger');
            } elseif ($new_username == '') {
                $status->addMessage('Username must not be empty', 'danger');
            } else {
                $data = sprintf(
                    "%s\n%s\n",
                    $new_username,
                    password_hash($_POST['newpass'], PASSWORD_BCRYPT)
                );
                $tempFile = RASPI_ADMIN_DETAILS . '.tmp';
                if (file_put_contents($tempFile, $data) === false) {
                    $status->addMessage('Failed to update admin password', 'danger');
                } else {
                    rename($tempFile, RASPI_ADMIN_DETAILS);
                    $_SESSION['user_id'] = $new_username;
                    $status->addMessage('Admin password updated', 'success');
                    $auth->logout();
                }
            }
        } else {
            $status->addMessage('Old password does not match', 'danger');
        }
    } elseif (isset($_POST['logout'])) {
        $auth->logout();
    }

    echo renderTemplate("admin", compact("status", "username"));
}
