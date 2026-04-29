<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savedetectionsettings', 'applydetectionsettings');
  endif;
  $msg = _('Restarting failover');
  page_progressbar($msg, _("Executing failover start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("Online Detection"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="detection_conf" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
            <div class="cbi-section cbi-tblsection">
              <div class="cbi-value">
                <label class="cbi-value-title"><?php echo _("Online Detection"); ?></label>
                <input class="cbi-input-radio" id="detection_enable" name="enabled" value="1" type="radio" <?php echo ($enabled[0] == 1 ? 'checked' : ""); ?> onchange="enableDetection(true)">
                <label ><?php echo _("Enable"); ?></label>

                <input class="cbi-input-radio" id="detection_disable" name="enabled" value="0" type="radio" <?php echo ($enabled[0] != 1 ? 'checked' : ""); ?> onchange="enableDetection(false)">
                <label ><?php echo _("Disable"); ?></label>
              </div>

              <div id="page_detection" name="page_detection">
                  <div class="cbi-value">
                      <label class="cbi-value-title"><?php echo _("Primary Detection Server"); ?></label>
                      <input type="text" class="cbi-input-text" name="primary_addr" id="primary_addr" 
                      value="<?php echo ($primary_addr[0] != null ? $primary_addr[0] : ""); ?>" />
                  </div>
                  
                  <div class="cbi-value">
                      <label class="cbi-value-title"><?php echo _("Second Detection Server"); ?></label>
                      <input type="text" class="cbi-input-text" name="secondary_addr" id="secondary_addr" 
                      value="<?php echo ($secondary_addr[0] != null ? $secondary_addr[0] : ""); ?>" />
                  </div>

                  <div class="cbi-value">
                      <label class="cbi-value-title"><?php echo _("Detection Period"); ?></label>
                      <input type="text" class="cbi-input-text" name="detect_period" id="detect_period" 
                      value="<?php echo ($detect_period[0] != null ? $detect_period[0] : ""); ?>" />
                      <label class="cbi-value-description"><?php echo _("Minutes"); ?></label>
                  </div>

                  <div class="cbi-value">
                      <label class="cbi-value-title"><?php echo _("Enable Reboot"); ?></label>
                      <input type="checkbox" class="cbi-input-checkbox" onchange="enableReboot(this)" name="enabled_reboot" id="enabled_reboot" 
                      value="1" <?php echo ($enabled_reboot[0] == 1 ? 'checked' : ""); ?> />
                  </div>
                  <div class="cbi-value" id="page_reboot" name="page_reboot" <?php if ($enabled_reboot[0] != 1) { ?> style="display: none;" <?php } ?> >
                      <label class="cbi-value-title"><?php echo _("Reboot After Interval"); ?></label>
                      <input type="text" class="cbi-input-text" name="reboot_inter" id="reboot_inter" 
                      value="<?php echo ($reboot_inter[0] != null ? $reboot_inter[0] : ""); ?>" />
                      <label class="cbi-value-description"><?php echo _("Minutes"); ?></label>
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
    function enableReboot(checkbox) {
        if (checkbox.checked == true) {
            $('#page_reboot').show();
        } else {
            $("#page_reboot").hide();
        }
    }

    function enableDetection(state) {
      if (state) {
        $('#page_detection').show();
      } else {
        $('#page_detection').hide();
      }
  }

  document.addEventListener('DOMContentLoaded', function() {
      var enabled = document.getElementById('detection_enable').checked;
      enableDetection(enabled);
  });
</script>

