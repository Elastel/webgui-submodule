<?php

require_once '../../includes/autoload.php';
require_once '../../includes/CSRF.php';
require_once '../../includes/config.php';

$type = $_GET['type'];
$host = "github.com";
$network_status = false;

if ($type == "node_online_update" || $type == "update_node") {
  $ping = exec("ping -c 3 -W 5 " . escapeshellarg($host), $output, $status);
  if ($status === 0) {
      $network_status = true;
  }
  
  $cmd_get_local_node = "cd /var/www/html; sudo git for-each-ref --format='%(objectname)' refs/heads/$(git rev-parse --abbrev-ref HEAD)";
  $cmd_get_remote_node = "cd /var/www/html; sudo git for-each-ref --format='%(objectname)' refs/remotes/origin/$(git rev-parse --abbrev-ref HEAD)";
}

if ($type == "node_online_update") {
    if ($network_status) {
        exec('cd /var/www/html; sudo git fetch origin');
    }

    exec($cmd_get_remote_node, $new_node);
    $data['new_node'] = $new_node[0];

    exec($cmd_get_local_node, $cur_node);
    $data['cur_node'] = $cur_node[0];
} else if ($type == "update_node") {
    if ($network_status) {
        exec('cd /var/www/html; sudo git fetch origin');
        exec('cd /var/www/html; sudo git reset --hard origin/$(git rev-parse --abbrev-ref HEAD)');
        exec('cd /var/www/html; sudo git pull origin $(git rev-parse --abbrev-ref HEAD) && sudo git submodule update --init --recursive Elastel/$(cat /etc/fw_model)');
        // check current node update
        exec($cmd_get_remote_node, $new_node);
        exec($cmd_get_local_node, $cur_node);
        if ($new_node[0] == $cur_node[0]) {
            exec('sudo git reset --hard HEAD; sudo /var/www/html/update 2>&1', $info);
            $data['log'] = $info[0];
        } else {
            $data['error'] = "Fail to update node";
        }  
    } else {
        $data['error'] = 'No network!';
    }
} else if ($type == "reset_configs") {
    exec('cd /var/www/html; sudo git reset --hard HEAD');
    exec('sudo /var/www/html/update reset 2>&1');
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
    echo json_encode($data);
}
