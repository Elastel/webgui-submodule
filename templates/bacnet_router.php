<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savebacnetroutersettings', 'applybacnetroutersettings');
  endif;
  $msg = _('Restarting BACnet Router');
  page_progressbar($msg, _("Executing BACnet Router start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("BACnet Router"); ?>
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
          <form method="POST" action="bacnet_router" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField();;
            echo '<div class="cbi-section cbi-tblsection">';
            RadioControlCustom(_('BACnet Router'), 'enabled', 'bacnet', 'enableBACnet');

            echo '<div id="page_bacnet" name="page_bacnet">';

            // $mode = array('BACnet/IP To BACnet/MSTP', 'BACnet/MSTP To BACnet/IP');
            // SelectControlCustom(_('Mode'), 'mode', $mode, $mode[0], 'mode');
            echo '<h5>'._("BACnet/IP Settings").'</h5>';
            SelectControlCustom(_('IP Interface'), 'ifname', $interface_list, $interface[0], 'ifname');
            InputControlCustom(_('Port'), 'port', 'port', _('1~65535'));

            echo '<h5>'._("BACnet/MSTP Settings").'</h5>';
            exec("cat /etc/fw_model", $model);
            
            if ($model[0] == "EG324") {
              $comlist = array('/dev/ttyAMA0'=>'COM1', '/dev/ttyAMA1'=>'COM2');
            } else if ($model[0] == "EG324L") {
              $comlist = array('/dev/ttyS1'=>'COM1', '/dev/ttyS2'=>'COM2');
            } else if ($model[0] == "EG510") {
              $comlist = array('/dev/ttyCH9344USB0'=>'COM1');
            } else {
              $comlist = array('/dev/ttyACM0'=>'COM1');
            }
            SelectControlCustom(_('COM Interface'), 'interface', $comlist, $comlist[0], 'interface');

            $baudrate_list = array('1200'=>'1200', '2400'=>'2400', '4800'=>'4800', '9600'=>'9600', '19200'=>'19200', '38400'=>'38400',
            '57600'=>'57600', '115200'=>'115200', '230400'=>'230400');
            SelectControlCustom(_('Baudrate'), 'baudrate', $baudrate_list, $baudrate_list['38400'], 'baudrate');
            InputControlCustom(_('Source Address'), 'mac', 'mac');
            InputControlCustom(_('Max Master'), 'max_master', 'max_master', _('1~127'));
            InputControlCustom(_('Frames'), 'frames', 'frames', _('1~127'));
            echo '</div>';
            echo '</div>';

            echo $buttons; 
          ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

