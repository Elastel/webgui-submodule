<?php

require_once 'includes/config.php';
require_once 'includes/functions.php';

function DisplayDocker()
{
    $status = new \ElastPro\Messages\StatusMessage;
    if (isset($_POST['restart'])) {
        exec('sudo systemctl restart docker.service');
    }
    
    $version = exec("docker -v | grep version | awk -F ' ' '{print $3}' | awk -F ',' '{print $1}'");
    $run_status = exec('pgrep dockerd');

    if ($run_status != null) {
        $container = exec("sudo docker ps | grep portainer | awk -F ' ' '{print $1}'");
        if ($container != null) {
            $tmp = exec("sudo ss -tlnp | grep -E '9000'");
            if ($tmp != null) {
                $port = '9000';
            } else {
                $port = '9100';
            }
        }
    }

    echo renderTemplate(
        'docker', compact(
            'status',
            'run_status',
            'version',
            'port'
        )
    );
}

