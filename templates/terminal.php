<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('saveterminalsettings', 'applyterminalsettings');
  endif;
  $msg = _('Restarting terminal');
  page_progressbar($msg, _("Executing terminal start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("Terminal"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="terminal" enctype="multipart/form-data" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
            <h4><?php echo _("Config"); ?></h4>
            <div class="cbi-section cbi-tblsection">
              <div class="cbi-value">
                  <label class="cbi-value-title"><?php echo _("Port"); ?></label>
                  <input type="text" class="cbi-input-text" name="port" id="port" value="<?php echo $port[0]; ?>" />
                  <label class="cbi-value-description"><?php echo _("0~65535"); ?></label>
              </div>
              <?php SelectControlCustom(_('Interface'), 'interface', $interface_list, $interface[0], 'interface') ?>
            </div>
            <?php echo $buttons ?>

            <div class="cbi-value">
              <label class="cbi-value-title"><?php echo _("Upload Files"); ?></label>
              <label for="upload" class="cbi-file-lable">
                  <input type="button" class="cbi-file-btn" id="upload_btn" value="<?php echo _("Choose file"); ?>">
                  <span id="upload_text"><?php echo _("No file chosen"); ?></span>
                  <input type="file" class="cbi-file" name="upload_file" id="upload_file" onchange="fileChange()">
              </label>
              <input type="submit" class="btn btn-success" style="margin-left:12rem;"  value="<?php echo _("Upload"); ?>" name="upload">
            </div>
          </form>
          <div class="cbi-value">
            <h4><?php echo _("Terminal"); ?></h4>
            <iframe src="<?php echo 'http://' . $ip[0] . ':' . $port[0] ?>" style="width: 100%; min-height: 500px; border: 2px solid #000; border-radius: 3px; resize: vertical;"></iframe>
          </div>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<script type="text/javascript">
  function fileChange() {
    $('#upload_text').html($('#upload_file')[0].files[0].name);
  }
</script>

