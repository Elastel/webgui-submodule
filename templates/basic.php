<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savebasicsettings', 'applybasicsettings');
  endif;
  $msg = _('Restarting dct');
  page_progressbar($msg, _("Executing dct start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("Basic Setting"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="basic_conf" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField();;
            echo '<div class="cbi-section cbi-tblsection">';

            RadioControlCustom(_('Data Collect'), 'enabled', 'basic', 'enableBasic');
            echo '<div id="page_basic" name="page_basic">';

            InputControlCustom(_('Collect Period'), 'collect_period', 'collect_period', _('Seconds'), '5');

            InputControlCustom(_('Report Period'), 'report_period', 'report_period', _('Seconds'), '10');

            CheckboxControlCustom(_('Batch Reporting'), 'batch_reporting', 'batch_reporting');

            CheckboxControlCustom(_('Enable Cache'), 'cache_enabled', 'cache_enabled', null, _('Cache History Data'), 'enableCache(this)');

            echo '<div id="page_cache_days" name="page_cache_days">';
            InputControlCustom(_('Cache Days'), 'cache_day', 'cache_day', _('Days'));
            echo '</div>';

            // CheckboxControlCustom(_('Send Minute Data'), 'minute_enabled', 'minute_enabled', null, null, 'enableMinuteData(this)');
            // echo '<div id="page_minute_data" name="page_minute_data">';
            // InputControlCustom(_('Minute Data Period'), 'minute_period', 'minute_period', _('Minutes'));
            // echo '</div>';
            // CheckboxControlCustom(_('Send Hour Data'), 'hour_enabled', 'hour_enabled');
            // CheckboxControlCustom(_('Send Day Data'), 'day_enabled', 'day_enabled');

            echo '</div>';
            echo '</div>';
            echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

