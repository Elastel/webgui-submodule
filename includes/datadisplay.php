<?php

require_once 'includes/config.php';
require_once 'includes/functions.php';

/**
 * Displays info about the RaspAP project
 */
function dataDisplay()
{
    $select = ['all'];
    $prefixes = [];

    $jsonFile = '/tmp/factor_list';
    if (file_exists($jsonFile)) {
        $json = file_get_contents($jsonFile);
        $data = json_decode($json, true);

        if ($data !== null) {
            foreach ($data as $item) {
                $parts = explode('-', $item);
                if (isset($parts[0])) {
                    $prefixes[] = trim($parts[0]);
                }
            }

            $unique_prefixes = array_unique($prefixes);
            $select = array_merge($select, array_values($unique_prefixes));
        }
    }

    echo renderTemplate(
        "datadisplay", compact("select")
    );
}

