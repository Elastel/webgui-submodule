<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savemodbusroutersettings', 'applymodbusroutersettings');
  endif;
  $msg = _('Restarting Modbus Router');
  page_progressbar($msg, _("Executing Modbus Router start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("Modbus Router"); ?>
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
          <form method="POST" action="modbus_router" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField();
            echo '<div class="cbi-section cbi-tblsection">';
            RadioControlCustom(_('Modbus Router'), 'enabled', 'modbus', 'enableModbusRouter', NULL, $modbusRouterConf['enabled']);

            $enable = $modbusRouterConf['enabled'] == '1' ? '' : 'style="display: none;"';
            echo '<div id="page_modbus_router" name="page_modbus_router" '.$enable.'>';

            $mode = array('Modbus RTU To Modbus TCP', 'Modbus TCP To Modbus RTU');
            SelectControlCustom(_('Mode'), 'mode', $mode, ($modbusRouterConf['mode'] != NULL) ? $mode[$modbusRouterConf['mode']] : $mode[0], 'mode', null, 'modbusRouterModeChange()');
            echo '<h5>'._("Modbus TCP Settings").'</h5>';
            InputControlCustom(_('IP Address'), 'address', 'address', NULL, $modbusRouterConf['address']);
            InputControlCustom(_('Port'), 'port', 'port', _('1~65535'), ($modbusRouterConf['port'] != NULL) ? $modbusRouterConf['port'] : '502');

            echo '<h5>'._("Modbus RTU Settings").'</h5>';
            
            SelectControlCustom(_('COM Interface'), 'com', $comlist, ($modbusRouterConf['com'] != NULL) ? $comlist[$modbusRouterConf['com']] : $comlist[0], 'com');

            $baudrate_list = array('1200'=>'1200', '2400'=>'2400', '4800'=>'4800', '9600'=>'9600', '19200'=>'19200', '38400'=>'38400',
            '57600'=>'57600', '115200'=>'115200', '230400'=>'230400');
            SelectControlCustom(_('Baudrate'), 'baudrate', $baudrate_list, ($modbusRouterConf['baudrate'] != NULL) ? $modbusRouterConf['baudrate'] : $baudrate_list['115200'], 'baudrate');

            $databit_list = array('7'=>'7', '8'=>'8');
            SelectControlCustom(_('Databit'), 'databit', $databit_list, ($modbusRouterConf['databit'] != NULL) ? $modbusRouterConf['databit'] : $databit_list['8'], 'databit');

            $stopbit_list = array('1'=>'1', '2'=>'2');
            SelectControlCustom(_('Stopbit'), 'stopbit', $stopbit_list, ($modbusRouterConf['stopbit'] != NULL) ? $modbusRouterConf['stopbit'] : $stopbit_list['1'], 'stopbit');

            $parity_list = array('N'=>'None', 'O'=>'Odd', 'E'=>'Even');
            SelectControlCustom(_('Parity'), 'parity', $parity_list, ($modbusRouterConf['parity'] != NULL) ? $parity_list[$modbusRouterConf['parity']] : $parity_list['N'], 'parity');

            $show = $modbusRouterConf['mode'] == '0' ? '' : 'style="display: none;"';
            echo '<div id="page_rtu_to_tcp" name="page_rtu_to_tcp" '.$show.'>';
            for ($i = 0; $i < count($comlist) - 1; $i ++) {
              $num = $i + 2;
              $checked = $modbusRouterConf['enable_com' . $num] == '1' ? 'checked' : '';
              CheckboxControlCustom(_('Add COM'), 'enable_com' . $num, 'enable_com' . $num, $checked, null, "enableModbusRouterCom(this, $num)");
              $display = $modbusRouterConf['enable_com' . $num] == '1' ? '' : 'style="display: none;"';
              echo '<div id="page_modbus_router_com'. $num .'" name="ppage_modbus_router_com'. $num .'" '. $display .'>';
                SelectControlCustom(_('COM Interface'), 'com' . $num, $comlist, ($modbusRouterConf['com' . $num] != NULL) ? $comlist[$modbusRouterConf['com' . $num]] : $comlist[0], 'com' . $num);

                $baudrate_list = array('1200'=>'1200', '2400'=>'2400', '4800'=>'4800', '9600'=>'9600', '19200'=>'19200', '38400'=>'38400',
                '57600'=>'57600', '115200'=>'115200', '230400'=>'230400');
                SelectControlCustom(_('Baudrate'), 'baudrate' . $num, $baudrate_list, ($modbusRouterConf['baudrate' . $num] != NULL) ? $modbusRouterConf['baudrate' . $num] : $baudrate_list['115200'], 'baudrate' . $num);

                $databit_list = array('7'=>'7', '8'=>'8');
                SelectControlCustom(_('Databit'), 'databit' . $num, $databit_list, ($modbusRouterConf['databit' . $num] != NULL) ? $modbusRouterConf['databit' . $num] : $databit_list['8'], 'databit'. $num);

                $stopbit_list = array('1'=>'1', '2'=>'2');
                SelectControlCustom(_('Stopbit'), 'stopbit' . $num, $stopbit_list, ($modbusRouterConf['stopbit' . $num] != NULL) ? $modbusRouterConf['stopbit' . $num] : $stopbit_list['1'], 'stopbit'. $num);
                
                $parity_list = array('N'=>'None', 'O'=>'Odd', 'E'=>'Even');
                SelectControlCustom(_('Parity'), 'parity' . $num, $parity_list, ($modbusRouterConf['parity' . $num] != NULL) ? $parity_list[$modbusRouterConf['parity' . $num]] : $parity_list['N'], 'parity'. $num);
              echo '</div>';
            }

            echo '</div>';
            echo '</div>';
            echo '</div>';

            echo $buttons; 
          ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

