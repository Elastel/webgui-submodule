<?php

require_once 'config.php';

function save_upload_file($file) {
    define('KB', 1024);
      $tmp_destdir = '/tmp/';
      $auth_flag = 0;
  
      try {
          // If undefined or multiple files, treat as invalid
          if (!isset($file['error']) || is_array($file['error'])) {
              throw new RuntimeException('Invalid parameters');
          }
  
          $upload = \ElastPro\Uploader\FileUpload::factory('upload', $tmp_destdir);
          $upload->set_max_file_size(2048*KB);
          $upload->set_allowed_mime_types(array('text/plain', 'application/octet-stream', 'application/gzip'));
          $upload->file($file);
          $validation = new validation;
          $upload->callbacks($validation, array('check_name_length'));
          $results = $upload->upload();
  
          if (!empty($results['errors'])) {
              throw new RuntimeException($results['errors'][0]);
          }
  
          // Valid upload, get file contents
          $file_path = $results['full_path'];
          $new_file_path = '/tmp/backup.tar.gz';
          system("sudo mv $file_path $new_file_path");
          
          if (file_exists($new_file_path)) {
              return true;
          } else {
              return false;
          }
      } catch (RuntimeException $e) {
          return false;
      }
  }

function DisplayBackupRestore()
{
    // checkBox name, filename
    $checkBoxList = [
        "wan_lan"           => ["WAN&LAN", "/etc/config/network;/etc/dhcpcd.conf;/etc/dnsmasq.d/090_br0.conf", "0"],
        "wifi_ap"           => ["WiFi AP", "/etc/hostapd/hostapd.conf", "0"],
        "wifi_client"       => ["WiFi Client", "/etc/wpa_supplicant/wpa_supplicant.conf", "0"],
        "data_collect"      => ["Data Collect", "/etc/config/dct", "0"],
        "bacnet_router"     => ["BACnet Router", "/etc/config/bacnet_router", "0"],
        "modbus_router"     => ["Modbus Rouoter", "/etc/config/modbus_router", "0"]
    ];

    $paths = [];
    if (isset($_POST['saveBackupList'])) {
        foreach ($checkBoxList as $key => &$value) {
            $value[2] = $_POST[$key] == '1' ? '1' : '0';
            if ($value[2] == 1) {
                $splitPaths = explode(";", $value[1]);

                foreach ($splitPaths as $path) {
                    $paths[] = $path;
                }
            }
        }

        exec("sudo echo '' > /etc/checkbox_backup.list");
        $filePath = '/tmp/checkbox_backup.list';
        if (file_put_contents($filePath, implode(PHP_EOL, $paths) . PHP_EOL)) {
            exec("sudo mv $filePath /etc/checkbox_backup.list");
        }

        $newBackupList = $_POST['backup_list'];
        if (strlen($newBackupList) > 0) {
            $file = '/tmp/backup.list';
            $unixText = str_replace(["\r\n", "\r"], "\n", $newBackupList);
            if (file_put_contents($file, $unixText)) {
                exec("sudo mv $file /etc/backup.list");
            }
        } else {
            exec("sudo echo '' > /etc/backup.list");
        }
    }

    if ( isset($_POST['upload']) ) {
        if (strlen($_FILES['upload_file']['name']) > 0) {
            if (is_uploaded_file($_FILES['upload_file']['tmp_name'])) {
                $file = "/tmp/backup.tar.gz";

                if (file_exists($file)) {
                    unlink($file);
                }

                $ret = save_upload_file($_FILES['upload_file']);
                $upload_backup_list = '';
                if ($ret) {
                    exec("tar tzf /tmp/backup.tar.gz", $tmp);
                    $upload_backup_list = implode("\n", $tmp);
                }
            }
        }
    }

    $backupList = file_get_contents("/etc/backup.list");
    $checkboxBackupList = file_get_contents("/etc/checkbox_backup.list");

    foreach ($checkBoxList as $key => &$value) {
        $splitPaths = explode(";", $value[1]);
        if (strpos($checkboxBackupList, $splitPaths[0]) !== false) {
            $value[2] = '1';
        }
    }

    echo renderTemplate("backup_restore", compact('backupList', 'upload_backup_list', 'checkBoxList'));
}

