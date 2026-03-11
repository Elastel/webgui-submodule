<?php

require_once 'includes/config.php';
require_once 'includes/functions.php';

function DisplayDocker()
{
    $status = new \ElastPro\Messages\StatusMessage;
    if (isset($_POST['restart'])) {
        exec('sudo systemctl restart docker.service');
    }
    
    exec("docker -v | grep version | awk -F ' ' '{print $3}' | awk -F ',' '{print $1}'", $version);
    exec('pgrep dockerd', $run_status);

    if ($run_status[0] != null) {
        exec("sudo docker ps | grep portainer | awk -F ' ' '{print $1}'", $container);
        if ($container[0] != null) {
            exec("sudo docker port " . $container[0] . "| grep 0.0.0.0 | grep 9000 | awk -F ':' '{print $2}'", $port);
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

