<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savebacnetsettings', 'applybacnetsettings');
  endif;
  $msg = _('Restarting BACnet Server');
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
          <?php echo _("BACnet Server"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="bacnet" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField();;
            echo '<div class="cbi-section cbi-tblsection">';
            RadioControlCustom(_('BACnet Server'), 'enabled', 'bacnet', 'enableBACnet');

            echo '<div id="page_bacnet" name="page_bacnet">';

            $proto = array('BACnet/IP', 'BACnet/MSTP');
            SelectControlCustom(_('Protocol'), 'proto', $proto, $proto[0], 'proto', null, "bacnetProtocolChange()");

            echo '<div id="page_proto_ip" name="page_proto_ip">';
            SelectControlCustom(_('Interface'), 'ifname', $interface_list, $interface[0], 'ifname');

            InputControlCustom(_('Port'), 'port', 'port', _('1~65535'));
            echo '</div>';

            echo '<div id="page_proto_mstp" name="page_proto_mstp">';
            exec("cat /etc/fw_model", $model);
            
            if ($model[0] == "EG324" || $model[0] == "EG324L") {
              $comlist = array('COM1'=>'COM1', 'COM2'=>'COM2');
            } else {
              $comlist = array('COM1'=>'COM1');
            }
            SelectControlCustom(_('Interface'), 'interface', $comlist, $comlist[0], 'interface');

            $baudrate_list = array('1200'=>'1200', '2400'=>'2400', '4800'=>'4800', '9600'=>'9600', '19200'=>'19200', '38400'=>'38400',
            '57600'=>'57600', '115200'=>'115200', '230400'=>'230400');
            SelectControlCustom(_('Baudrate'), 'baudrate', $baudrate_list, $baudrate_list['38400'], 'baudrate');
            InputControlCustom(_('Source Address'), 'mac', 'mac');
            InputControlCustom(_('Max Master'), 'max_master', 'max_master', _('1~127'));
            InputControlCustom(_('Frames'), 'frames', 'frames', _('1~127'));
            echo '</div>';

            InputControlCustom(_('Device ID'), 'device_id', 'device_id', _('1~65535'));

            InputControlCustom(_('Object Name'), 'object_name', 'object_name');
            
            echo '</div>
            </div>';

            echo $buttons; 
          ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

