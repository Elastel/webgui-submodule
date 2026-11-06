<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';
$host = "github.com";
$network_status = false;
$data = [];

function check_network_reachability($host = "github.com") {
    // 1. Instant check: If kernel routing table has no default gateway, we are 100% offline (0ms cost)
    $has_default_route = exec("ip route | grep -m 1 '^default'");
    if (empty($has_default_route)) {
        return false;
    }

    // 2. Fast probe: Short timeout curl to avoid hanging on DNS or dead TCP connections (max 2.5s)
    $cmd_curl = "curl -I -s --connect-timeout 2 -m 2.5 https://" . escapeshellarg($host) . " 2>&1";
    exec($cmd_curl, $curl_output, $curl_status);
    return ($curl_status === 0);
}

function cleanup_git_lock_files() {
    exec("sudo rm -f /var/www/html/.git/index.lock 2>/dev/null");
    if (is_dir('/var/www/html/.git/modules')) {
        exec("sudo find /var/www/html/.git/modules -name '*lock' -delete 2>/dev/null");
    }
}

if ($type == "node_online_update" || $type == "update_node") {
    cleanup_git_lock_files();
    $network_status = check_network_reachability($host);
    
    $cmd_get_local_node = "cd /var/www/html; sudo git for-each-ref --format='%(objectname)' refs/heads/$(git rev-parse --abbrev-ref HEAD)";
    $cmd_get_remote_node = "cd /var/www/html; sudo git for-each-ref --format='%(objectname)' refs/remotes/origin/$(git rev-parse --abbrev-ref HEAD)";
}

if ($type == "node_online_update") {
    if ($network_status) {
        // Apply mirror if configured
        if (file_exists('/etc/github_mirror')) {
            $mirror = trim(file_get_contents('/etc/github_mirror'));
            if (!empty($mirror)) {
                exec("cd /var/www/html; sudo git config url." . escapeshellarg($mirror) . ".insteadOf 'https://github.com/'");
            }
        }
        exec('cd /var/www/html; sudo git fetch origin');
    }

    exec($cmd_get_remote_node, $new_node);
    $data['new_node'] = isset($new_node[0]) ? $new_node[0] : "";

    exec($cmd_get_local_node, $cur_node);
    $data['cur_node'] = isset($cur_node[0]) ? $cur_node[0] : "";
    $data['network'] = $network_status;
    if (!$network_status) {
        $data['warning'] = "Network unavailable";
    }

} else if ($type == "update_node") {
    if ($network_status) {
        // Apply mirror if configured
        if (file_exists('/etc/github_mirror')) {
            $mirror = trim(file_get_contents('/etc/github_mirror'));
            if (!empty($mirror)) {
                exec("cd /var/www/html; sudo git config url." . escapeshellarg($mirror) . ".insteadOf 'https://github.com/'");
            }
        }
        exec('cd /var/www/html; sudo git fetch origin');
        exec('cd /var/www/html; sudo git reset --hard origin/$(git rev-parse --abbrev-ref HEAD)');
        exec('cd /var/www/html; sudo git pull origin $(git rev-parse --abbrev-ref HEAD) && sudo git submodule update --init --recursive Elastel/$(cat /etc/fw_model)');
        // check current node update
        exec($cmd_get_remote_node, $new_node);
        exec($cmd_get_local_node, $cur_node);
        if (isset($new_node[0]) && isset($cur_node[0]) && $new_node[0] == $cur_node[0]) {
            exec('sudo git reset --hard HEAD; sudo /var/www/html/update 2>&1', $info);
            $data['log'] = isset($info[0]) ? $info[0] : "";
        } else {
            $data['error'] = "Fail to update node";
        }
    } else {
        $data['error'] = 'No network!';
    }

} else if ($type == "reset_configs") {
    exec('cd /var/www/html; sudo git reset --hard HEAD');
    exec('sudo /var/www/html/update reset 2>&1');
    $data['status'] = 'success';

} else if ($type == "download_backup") {
    exec('sudo rm -f /tmp/backup.tar.gz');
    exec('sudo /var/www/html/installers/backup.sh');
    $file_path = '/tmp/backup.tar.gz';

    if (file_exists($file_path)) {
        $file_name = basename($file_path);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        ob_clean();
        flush();
        readfile($file_path);
        exit;
    }

} else if ($type == "action_backup") {
    $file = "/tmp/backup.tar.gz";
    if (file_exists($file)) {
        exec("sudo tar -xzvf /tmp/backup.tar.gz -C /; sudo sync");
        $data = ['success' => true, 'message' => 'Configuration restored successfully'];
        sleep(5);
        exec("sudo reboot");
    } else {
        $data = ['success' => false, 'message' => 'File does not exist'];
    }
}

if ($type != "download_backup") {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
}
