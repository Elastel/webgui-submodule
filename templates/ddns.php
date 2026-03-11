<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('saveddnssettings', 'applyddnssettings');
  endif;
  $msg = _('Restarting DDNS');
  page_progressbar($msg, _("Executing DDNS start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("DDNS"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="ddns" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField();
            echo '<div class="cbi-section cbi-tblsection">';

            RadioControlCustom(_('DDNS'), 'enabled', 'ddns', 'enableDDNS');

            echo '<div id="page_ddns" name="page_ddns">';

            $interface_list = array('eth0'=>'eth0', 'wwan0'=>'wwan0');
            SelectControlCustom(_('Interface'), 'interface', $interface_list, $interface_list[0], 'interface');

            $server_type = ['noip.com'];
            SelectControlCustom(_('Server Type'), 'server_type', $server_type, $server_type[0], 'server_type');

            InputControlCustom(_('Username'), 'username', 'username');

            InputControlCustom(_('Password'), 'password', 'password');

            InputControlCustom(_('Update Interval'), 'interval', 'interval', _('Minutes, minimum is 5'));

            InputControlCustom(_('Hostname'), 'hostname', 'hostname');

            echo '</div>
            </div>';
            echo $buttons; 
          ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>
<script type="text/javascript">
  function enableDDNS(state) {
      if (state) {
        $('#page_ddns').show();
      } else {
        $('#page_ddns').hide();
      }
  }
</script>

