<?php

require_once 'includes/config.php';
require_once 'includes/functions.php';

function DisplayNodered()
{
    $status = new \ElastPro\Messages\StatusMessage;
    if (isset($_POST['restart'])) {
        exec('sudo systemctl restart nodered.service');
    }

    exec('node-red --help | grep Node-RED', $version);
    exec('pgrep node-red', $run_status);

    echo renderTemplate(
        'nodered', compact(
            'status',
            'run_status',
            'version'
        )
    );
}

