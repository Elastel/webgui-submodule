<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savehmisettings', 'applyhmisettings');
  endif;
  $msg = _('Restarting HMI');
  page_progressbar($msg, _("Executing HMI start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("HMI"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="hmi" enctype="multipart/form-data" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
            <div class="cbi-section cbi-tblsection">
              <div class="cbi-value">
                <label class="cbi-value-title"><?php echo _("HMI"); ?></label>
                <input class="cbi-input-radio" id="hmi_enable" name="enabled" value="1" type="radio" <?php echo ($hmi['enabled'] == 1 ? 'checked' : ""); ?> onchange="enableHmi(true)">
                <label ><?php echo _("Enable"); ?></label>

                <input class="cbi-input-radio" id="hmi_disable" name="enabled" value="0" type="radio" <?php echo ($hmi['enabled'] != 1 ? 'checked' : ""); ?> onchange="enableHmi(false)">
                <label ><?php echo _("Disable"); ?></label>
              </div>

              <div id="page_hmi" name="page_hmi">
                <div class="cbi-value">
                  <label class="cbi-value-title"><?php echo _("HMI Mode"); ?></label>
                  <select id="hmi_mode" name="hmi_mode" class="cbi-input-select">
                    <option value="browser" selected=""><?php echo _("browser") ?></option>
                  </select>
                </div>
                <div id="page_browser" name="page_browser">
                    <div class="cbi-value">
                        <label class="cbi-value-title"><?php echo _("Browser Url"); ?></label>
                        <input type="text" class="cbi-input-text" name="browser_url" id="browser_url" 
                        value="<?php echo $hmi['browser_url']; ?>" />
                    </div>
                </div>
                <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("HMI Backlight Timeout"); ?></label>
                    <input type="text" class="cbi-input-text" name="hmi_backlight_timeout" id="hmi_backlight_timeout" 
                    value="<?php echo $hmi['hmi_backlight_timeout']; ?>" />
                    <label class="cbi-value-description"><?php echo _("minute"); ?></label>
                </div>
                <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("HMI Touchbeep"); ?></label>
                    <select id="hmi_touchbeep" name="hmi_touchbeep" class="cbi-input-select">
                      <option value="off" <?php echo $hmi['hmi_touchbeep'] == 'off' ? 'selected' : ''; ?>><?php echo _("off") ?></option>
                      <option value="on" <?php echo $hmi['hmi_touchbeep'] == 'on' ? 'selected' : ''; ?>><?php echo _("on") ?></option>
                    </select>
                </div>
                <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("HMI Brightness"); ?></label>
                    <input type="range" name="hmi_brightness" id="hmi_brightness" min="0" max="255" value="<?php echo $hmi['hmi_brightness']; ?>" style="width:12rem">
                    <span id="brightnessValue"><?php echo $hmi['hmi_brightness']; ?></span>
                </div>
                <div class="cbi-value">
                  <label class="cbi-value-title"><?php echo _("HMI Boot LOGO"); ?></label>
                  <label for="upload" class="cbi-file-lable">
                      <input type="button" class="cbi-file-btn" id="upload_btn" value="<?php echo _("Choose file"); ?>">
                      <span id="upload_text"><?php echo _("No file chosen"); ?></span>
                      <input type="file" class="cbi-file" name="upload_file" id="upload_file" onchange="fileChange()">
                  </label>
                  <input type="submit" class="btn btn-success" style="margin-left:8rem;"  value="<?php echo _("Replace"); ?>" name="replace">
                </div>
                <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("HMI Calibration"); ?></label>
                    <input type="submit" class="btn btn-success"  value="<?php echo _("Perform Calibration"); ?>" name="hmi_calibration">
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
  function enableHmi(state) {
    if (state) {
      $('#page_hmi').show();
    } else {
      $('#page_hmi').hide();
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
      var enabled = document.getElementById('hmi_enable').checked;
      enableHmi(enabled);
  });

  function fileChange() {
    $('#upload_text').html($('#upload_file')[0].files[0].name);
  }

  const slider = document.getElementById("hmi_brightness");
  const valueDisplay = document.getElementById("brightnessValue");
  const csrfToken = document.querySelector('input[name="csrf_token"]').value; // 假设隐藏字段名为csrf_token

  slider.addEventListener("input", () => {
      valueDisplay.textContent = slider.value;

      // 可选：调用后台 PHP 进行实际亮度设置
      fetch("ajax/system/set_brightness.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "value=" + slider.value + "&csrf_token=" + encodeURIComponent(csrfToken)
      });
  });
</script>

