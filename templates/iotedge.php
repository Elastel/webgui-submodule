<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('saveiotedgesettings', 'applyiotedgesettings');
  endif;
  $msg = _('Restarting iotedge Server');
  page_progressbar($msg, _("Executing iotedge start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("Azure IoT Edge"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form role="form" action="iotedge" enctype="multipart/form-data" method="POST">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField();
            echo '<div class="cbi-section cbi-tblsection">';
              RadioControlCustom(_('Azure IoT Edge'), 'enabled', 'iotedge', 'enableIotedge');

              echo '<div id="page_iotedge" name="page_iotedge">';
                $source_list = array('manual'=>'manual', 'dps'=>'dps');
                SelectControlCustom(_('Source'), 'source', $source_list, $source_list['manual'], 'source', null, "iotedgeSourceChange()");
                SelectControlCustom(_('Attestaion Method'), 'attestion_method', $method_list, $method_list[0], 'attestion_method', null, "iotedgeMethodChange()");

                echo '<div id="page_source_manual" name="page_source_manual">';
                  echo '<div id="page_source_manual_connection_string" name="page_source_manual_connection_string">';
                    InputControlCustom(_('Device Connection String'), 'connection_string', 'connection_string');
                  echo '</div>';
                  echo '<div id="page_source_manual_key_x509" name="page_source_manual_key_x509">';
                    InputControlCustom(_('Iothub Hostname'), 'iothub_hostname', 'iothub_hostname');
                    InputControlCustom(_('Device Id'), 'device_id', 'device_id');
                  echo '</div>';
                echo '</div>';

                echo '<div id="page_source_dps" name="page_source_dps">';
                  InputControlCustom(_('Global Endpoint'), 'global_endpoint', 'global_endpoint');
                  InputControlCustom(_('ID Scope'), 'id_scope', 'id_scope');
                  InputControlCustom(_('Registration ID'), 'registration_id', 'registration_id');
                echo '</div>';

                echo '<div id="page_method_symmetric" name="page_method_symmetric">';
                  InputControlCustom(_('Symmetric Key'), 'symmetric_key', 'symmetric_key');
                echo '</div>';
                echo '<div id="page_method_x509" name="page_method_x509">';
                  UploadFileControlCustom(_('Certificate'), 'cert_btn', 'cert_text', 'certificate', 'certificate', "certChangeX509()");
                  UploadFileControlCustom(_('Private Key'), 'key_btn', 'key_text', 'private_key', 'private_key', "keyChangeX509()");
                echo '</div>';
              echo '</div>';
              LabelControlCustom(_("Version"), 'version', 'status', $version);
              LabelControlCustom(_("Status"), 'status', 'status', $run_status != '0' ? "<font color=\"green\">Running</font>" : "<font color=\"red\">Stop</font>");
              ?>
                  <div class="cbi-section cbi-tblsection">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs mb-3">
                      <li class="nav-item"><a class="nav-link active" href="#module-list" data-toggle="tab"><?php echo _("Module List"); ?></a></li>
                      <!-- <li class="nav-item"><a class="nav-link active" href="#device-management" data-toggle="tab"><?php echo _("Device Management"); ?></a></li> -->
                    </ul>
  
                    <!-- Tab panes -->
                    <div class="tab-content">
                      <?php echo renderTemplate("iotedge/module", $__template_data) ?>
                      <!-- <?php echo renderTemplate("iotedge/device", $__template_data) ?> -->
                    </div><!-- /.tab-content -->
                  </div>
              <?php
            echo '</div>';
            echo $buttons; 
          ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

