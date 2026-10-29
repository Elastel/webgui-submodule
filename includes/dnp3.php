<?php

require_once 'config.php';

function DisplayDnp3()
{   
    $status = new \ElastPro\Messages\StatusMessage;

    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['savednp3settings']) || isset($_POST['applydnp3settings'])) {
            $ret = saveDnp3Config($status);
            if ($ret == false) {
                $status->addMessage('Error data', 'danger');
            } else {
                if (isset($_POST['applydnp3settings'])) {
                    exec('sudo /etc/init.d/dct restart >/dev/null');
                    $status->addMessage('Configuration applied.', 'success'); 
                }
            }
        }
    }

    /* Mapping rules import: conf_im_ex writes the dnp3 sections
       directly into UCI and restarts dct by itself. */
    if ( isset($_POST['upload']) ) {
        if (strlen($_FILES['upload_file']['name']) > 0) {
            if (is_uploaded_file($_FILES['upload_file']['tmp_name'])) {
                save_import_file('dnp3', $status, $_FILES['upload_file']);
            } else {
                $status->addMessage('Fail to upload file', 'danger');
            }
        }
    }

    echo renderTemplate("dnp3", compact('status'));
}

function saveDnp3Config($status)
{
    for ($i = 1; $i <= 4; $i++) {
        exec("sudo /usr/local/bin/uci set dct.dnp3_server.enabled$i=" . $_POST['dnp3_enabled'.$i]);
        exec("sudo /usr/local/bin/uci set dct.dnp3_server.proto$i=" . $_POST['proto'.$i]);
        if ($_POST['proto'.$i] == 'RTU') {
            exec("sudo /usr/local/bin/uci set dct.dnp3_server.interface$i=" .$_POST['interface'.$i]);
            exec("sudo /usr/local/bin/uci set dct.dnp3_server.baudrate$i=" .$_POST['baudrate'.$i]);
            exec("sudo /usr/local/bin/uci set dct.dnp3_server.databit$i=" .$_POST['databit'.$i]);
            exec("sudo /usr/local/bin/uci set dct.dnp3_server.stopbit$i=" .$_POST['stopbit'.$i]);
            exec("sudo /usr/local/bin/uci set dct.dnp3_server.parity$i=" .$_POST['parity'.$i]);
        } else {
            exec("sudo /usr/local/bin/uci set dct.dnp3_server.port$i=" .$_POST['port'.$i]);
        }

        exec("sudo /usr/local/bin/uci set dct.dnp3_server.slave_address$i=" .$_POST['slave_address'.$i]);
        exec("sudo /usr/local/bin/uci set dct.dnp3_server.master_address$i=" .$_POST['master_address'.$i]);
    }

    $data = $_POST['table_data'];
    file_put_contents(ELASTEL_DCT_CONFIG_JSON, $data);
    exec('sudo /usr/sbin/set_config ' . ELASTEL_DCT_CONFIG_JSON . ' dct dnp3');

    exec('sudo uci commit dct');
    
    $status->addMessage('Configuration updated.', 'success');
    return true;
}

