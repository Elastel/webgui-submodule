<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savescheduledsettings', 'applyscheduledsettings');
  endif;
  $msg = _('Restarting Scheduled');
  page_progressbar($msg, _("Executing Scheduled start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("Scheduled Tasks"); ?>
          </div>
          <div class="col">
            <button class="btn btn-light btn-icon-split btn-sm service-status float-right">
              <span class="icon"><i class="fas fa-circle service-status-<?php echo $statusIcon ?>"></i></span>
              <span class="text service-status"><?php echo _($routerStatus) ?></span>
            </button>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="scheduled" enctype="multipart/form-data" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
            <div class="cbi-section cbi-tblsection">
              <div class="cbi-value">
                <label class="cbi-value-title"><?php echo _("Scheduled"); ?></label>
                <input class="cbi-input-radio" id="scheduled_enable" name="enabled" value="1" type="radio" <?php echo ($scheduled['enabled'] == 1 ? 'checked' : ""); ?> onchange="enableScheduled(true)">
                <label ><?php echo _("Enable"); ?></label>

                <input class="cbi-input-radio" id="scheduled_disable" name="enabled" value="0" type="radio" <?php echo ($scheduled['enabled'] != 1 ? 'checked' : ""); ?> onchange="enableScheduled(false)">
                <label ><?php echo _("Disable"); ?></label>
              </div>

              <div id="page_scheduled" name="page_scheduled">
                <?php 
                    $mode_list = array('0'=>_('Every Day'), '1'=>_('Every Week'), '2'=>_('Every Month'));
                    SelectControlCustom(_('Scheduled Mode'), 'mode', $mode_list, $mode_list[$scheduled['mode']], 'mode', null, 'changeScheduledMode()');
                ?>
                <div id="page_week" name="page_week">
                <?php 
                    $weekday_list = array( '0'=>_('Sunday'), '1'=>_('Monday'), '2'=>_('Tuesday'), '3'=>_('Wednesday'), '4'=>_('Thursday'), '5'=>_('Friday'), '6'=>_('Saturday'));
                    SelectControlCustom(_('Weekday'), 'weekday', $weekday_list, $weekday_list[$scheduled['weekday']], 'weekday');
                ?>
                </div>
                <div id="page_day" name="page_day">
                  <div class="cbi-value">
                      <label class="cbi-value-title"><?php echo _("Monthday"); ?></label>
                      <input type="text" class="cbi-input-text" name="monthday" id="monthday" 
                      value="<?php echo $scheduled['monthday']; ?>" />
                      <label class="cbi-value-description">1~31</label>
                  </div>
                </div>
                <div class="cbi-value">
                    <label class="cbi-value-title"><?php echo _("Time"); ?></label>
                    <input type="text" class="cbi-input-text" name="time" id="time" 
                    value="<?php echo $scheduled['time']; ?>" />
                    <label class="cbi-value-description">eg:16:30:30</label>
                </div>
                <?php 
                    $task_list = array('reboot'=>_('reboot'), 'custom'=>_('custom'));
                    SelectControlCustom(_('Task'), 'task', $task_list, $task_list[$scheduled['task']], 'task', null, 'changeTask()');
                ?>
                <div id="page_custom" name="page_custom">
                  <div class="cbi-value">
                      <label class="cbi-value-title"><?php echo _("Custom Command"); ?></label>
                      <input type="text" class="cbi-input-text" name="custom_command" id="custom_command" 
                      value="<?php echo $scheduled['custom_command']; ?>" />
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
  function enableScheduled(state) {
    if (state) {
      $('#page_scheduled').show();
    } else {
      $('#page_scheduled').hide();
    }
  }

  function changeScheduledMode() {
    var mode = document.getElementById('mode').value;
    if (mode == '0') {
      $('#page_week').hide();
      $('#page_day').hide();
    } else if (mode == '1') {
      $('#page_week').show();
      $('#page_day').hide();
    } else {
      $('#page_week').hide();
      $('#page_day').show();
    }
  }

  function changeTask() {
    var task = document.getElementById('task').value;
    if (task == 'custom') {
      $('#page_custom').show();
    } else {
      $('#page_custom').hide();
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
      var enabled = document.getElementById('scheduled_enable').checked;
      enableScheduled(enabled);
      changeScheduledMode();
      changeTask();
  });
</script>

