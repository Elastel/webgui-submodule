<?php

require_once 'includes/config.php';
require_once 'includes/functions.php';

function DisplayChirpstack()
{
    $status = new \ElastPro\Messages\StatusMessage;
    if (isset($_POST['restart'])) {
        exec('sudo systemctl restart chirpstack.service');
        exec('sudo systemctl restart chirpstack-gateway-bridge.service');
    }

    exec('chirpstack --version', $version);
    exec('pgrep chirpstack', $run_status);

    exec('sudo grep -r "event_topic_template" /etc/chirpstack-gateway-bridge/chirpstack-gateway-bridge.toml', $tmp);
    preg_match('/"([^"]*)"/', $tmp[0], $cur_region);
    $array = explode("/", $cur_region[1]);
    unset($cur_region);
    $cur_region = $array[0];

    echo renderTemplate(
        'chirpstack', compact(
            'status',
            'run_status',
            'version',
            'cur_region'
        )
    );
}

