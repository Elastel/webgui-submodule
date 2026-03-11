<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savegpssettings', 'applygpssettings');
  endif;
  $msg = _('Restarting gps');
  page_progressbar($msg, _("Executing gps start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("GPS Location"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="gps" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
            <div class="cbi-section cbi-tblsection">
              <div class="cbi-value">
                <label class="cbi-value-title"><?php echo _("GPS"); ?></label>
                <input class="cbi-input-radio" id="gps_enable" name="enabled" value="1" type="radio" checked onchange="enableGps(true)">
                <label ><?php echo _("Enable"); ?></label>

                <input class="cbi-input-radio" id="gps_disable" name="enabled" value="0" type="radio" onchange="enableGps(false)">
                <label ><?php echo _("Disable"); ?></label>
              </div>

              <div id="page_gps" name="page_gps">
                <div class="cbi-value">
                  <label class="cbi-value-title"><?php echo _("Output Mode"); ?></label>
                  <select id="output_mode" name="output_mode" class="cbi-input-select"  onchange="outputModeChange()">
                    <option value="network"><?php echo _("Output To Network") ?></option>
                    <option value="serial"><?php echo _("Output To Serial") ?></option>
                    <option value="none" selected=""><?php echo _("None") ?></option>
                  </select>
                </div>
                <div id="gps_network" name="gps_network">
                  <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("Server Address"); ?></label>
                    <input type="text" class="cbi-input-text" name="server_addr" id="server_addr"/>
                  </div> 

                  <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("Server Port"); ?></label>
                    <input type="text" class="cbi-input-text" name="server_port" id="server_port"/>
                    <label class="cbi-value-description"><?php echo _("0~65535"); ?></label>
                  </div>

                  <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("Report Mode"); ?></label>
                    <select id="report_mode" name="report_mode" class="cbi-input-select">
                      <option value="0" selected=""><?php echo _("Pure TCP") ?></option>
                      <option value="1"><?php echo _("Pure UDP") ?></option>
                    </select>
                  </div>

                  <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("User Defined Register Packet"); ?></label>
                    <input type="text" class="cbi-input-text" name="register_packet" id="register_packet"/>
                    <label class="cbi-value-description"><?php echo _("Max 128 Bytes ASCII"); ?></label>
                  </div>

                  <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("User Defined Heartbeat Packet"); ?></label>
                    <input type="text" class="cbi-input-text" name="heartbeat_packet" id="heartbeat_packet"/>
                    <label class="cbi-value-description"><?php echo _("Max 128 Bytes ASCII"); ?></label>
                  </div>

                  <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("Heartbeat Interval"); ?></label>
                    <input type="text" class="cbi-input-text" name="heartbeat_interval" id="heartbeat_interval" value="5"/>
                    <label class="cbi-value-description"><?php echo _("Seconds"); ?></label>
                  </div>
                </div>
                
                <div id="gps_serial" name="gps_serial">
                  <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("Baudrate"); ?></label>
                    <select id="baudrate" name="baudrate" class="cbi-input-select">
                      <option value="9600"><?php echo _("9600") ?></option>
                      <option value="19200"><?php echo _("19200") ?></option>
                      <option value="38400"><?php echo _("38400") ?></option>
                      <option value="57600"><?php echo _("57600") ?></option>
                      <option value="115200" selected=""><?php echo _("115200") ?></option>
                      <option value="230400"><?php echo _("230400") ?></option>
                    </select>
                  </div>

                  <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("Databit"); ?></label>
                    <select id="databit" name="databit" class="cbi-input-select">
                      <option value="8" selected=""><?php echo _("8") ?></option>
                      <option value="7"><?php echo _("7") ?></option>
                    </select>
                  </div>

                  <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("Stopbit"); ?></label>
                    <select id="stopbit" name="stopbit" class="cbi-input-select">
                      <option value="1" selected=""><?php echo _("1") ?></option>
                      <option value="2"><?php echo _("2") ?></option>
                    </select>
                  </div>

                  <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("Parity"); ?></label>
                    <select id="parity" name="parity" class="cbi-input-select">
                      <option value="n" selected=""><?php echo _("None") ?></option>
                      <option value="o"><?php echo _("Odd") ?></option>
                      <option value="e"><?php echo _("Even") ?></option>
                    </select>
                  </div>
                </div>

                <div id="gps_report" name="gps_report">
                  <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("Report Interval"); ?></label>
                    <input type="text" class="cbi-input-text" name="report_interval" id="report_interval" value="10"/>
                    <label class="cbi-value-description"><?php echo _("Seconds"); ?></label>
                  </div>
                </div>
                <?php
                $accuracy_list = ['0', '1', '2', '3', '4', '5', '6'];
                SelectControlCustom(_('GPS Info Accuracy'), 'accuracy', $accuracy_list, $accuracy_list[0], 'accuracy', _('0~6'));
                ?>
                <div class="cbi-value">
                  <label class="cbi-value-title"><?php echo _("GPS Info"); ?></label>
                  <?php 
                      exec("uci -P /var/state get gps.conf.latitude", $lat);
                      exec("uci -P /var/state get gps.conf.longitude", $long);
                  ?>
                  <label id="gps_info" name="gps_info"><?php echo _(($lat[0] != null) ? ("lat:$lat[0],long:$long[0]") : "-"); ?></label>
                </div>

                <div id="tcp_status" name="tcp_status">
                  <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("Connection Status"); ?></label>
                    <?php 
                        exec("uci -P /var/state get gps.conf.status", $status);
                    ?>
                    <label id="conn_status" name="conn_status"><?php echo _(($status[0] != null) ? $status[0] : "-"); ?></label>
                  </div>
                </div>
              </div>
            </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>
<script type="text/javascript">
  function enableGps(state) {
      if (state) {
        $('#page_gps').show();
      } else {
        $('#page_gps').hide();
      }

      outputModeChange();
  }

  function outputModeChange() {
    var output_mode = document.getElementById("output_mode").value;
    if (output_mode == "network") {
      $('#gps_network').show();
      $('#tcp_status').show();
      $('#gps_report').show();
      $('#gps_serial').hide(); 
    } else if (output_mode == "serial") {
      $('#gps_serial').show();
      $('#gps_report').show();
      $('#gps_network').hide(); 
      $('#tcp_status').hide();
    } else {
      $('#gps_serial').hide();
      $('#gps_network').hide(); 
      $('#tcp_status').hide();
      $('#gps_report').hide();
    }
  }

</script>

