<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savesettings', 'applysettings');
  endif;
  $msg = _('Restarting Macchina');
  page_progressbar($msg, _("Executing Macchina start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("Macchina"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form role="form" action="macchina" enctype="multipart/form-data" method="POST">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
            <div class="cbi-section cbi-tblsection">
                <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("Macchina"); ?></label>
                    <input class="cbi-input-radio" id="macchina_enable" name="enabled" value="1" type="radio" 
                    <?php if ($enabled[0] == 1) { ?> checked <?php } ?> onchange="enableMacchina(true)">
                    <label ><?php echo _("Enable"); ?></label>

                    <input class="cbi-input-radio" id="macchina_disable" name="enabled" value="0" type="radio" 
                    <?php if ($enabled[0] == 0) { ?> checked <?php } ?> onchange="enableMacchina(false)">
                    <label ><?php echo _("Disable"); ?></label>
                </div>
                <div id="page_macchina" name="page_macchina" <?php if ($enabled[0] != 1) { ?> style="display: none;" <?php } ?>>
                    <div class="cbi-value">
                        <label class="cbi-value-title"><?php echo _("Domain"); ?></label>
                        <input type="text" class="cbi-input-text" name="domain" id="domain" value="<?php echo _(($domain[0] != null) ? $domain[0] : ""); ?>"/>
                    </div>

                    <div class="cbi-value">
                        <label class="cbi-value-title"><?php echo _("Device ID"); ?></label>
                        <input type="text" class="cbi-input-text" name="device_id" id="device_id" value="<?php echo _(($device_id[0] != null) ? $device_id[0] : ""); ?>"/>
                    </div>

                    <div class="cbi-value">
                        <label class="cbi-value-title"><?php echo _("URL"); ?></label>
                        <input type="text" class="cbi-input-text" name="url" id="url" value="<?php echo _(($url[0] != null) ? $url[0] : ""); ?>"/>
                        <a class="cbi-value-description" href="https://remote.macchina.io" target="_blank"><?php echo _("remote.macchina.io"); ?></a>
                    </div>

                    <div class="cbi-value">
                      <label class="cbi-value-title"><?php echo _("Enabled http"); ?></label>
                      <input type="checkbox" class="cbi-input-checkbox" name="enabled_http" id="enabled_http" value="1" <?php echo _(($enabled_http[0] == 1) ? "checked" : ""); ?>/>
                    </div>

                    <div class="cbi-value">
                      <label class="cbi-value-title"><?php echo _("Enabled SSH"); ?></label>
                      <input type="checkbox" class="cbi-input-checkbox" name="enabled_ssh" id="enabled_ssh" value="1" <?php echo _(($enabled_ssh[0] == 1) ? "checked" : ""); ?>/>
                    </div>

                    <div class="cbi-value">
                        <label class="cbi-value-title"><?php echo _("Status:"); ?></label>
                        <label class="info-label" id="run_status" name="run_status">
                          <?php echo _(($run_status[0] != null) ? "<font color=\"green\">Running</font>" : "<font color=\"red\">Stop</font>"); ?>
                        </label>
                    </div>

                    <!-- <div class="cbi-value">
                        <label class="cbi-value-title"><?php echo _("Account Login:"); ?></label>
                        <a href="https://reflector.remote.macchina.io/my-devices/login" target="_blank"><?php echo _("macchina.io"); ?></a>
                        &nbsp&nbsp&nbsp&nbsp
                        <label class="info-label"><?php echo _("Configuration file can be downloaded by the url."); ?></label>
                    </div> -->
                </div>
            </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>
<script type="text/javascript">
  function enableMacchina(state) {
      if (state) {
        $('#page_macchina').show();
      } else {
        $('#page_macchina').hide();
      }
  }
</script>

