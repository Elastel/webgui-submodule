<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savemodbusslavesettings', 'applymodbusslavesettings');
  endif;
  $msg = _('Restarting Modbus Slave');
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
          <?php echo _("Modbus Slave"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="modbus_slave" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField();
            echo '<div class="cbi-section cbi-tblsection">';
            RadioControlCustom(_('Modbus Slave'), 'modbus_slave_enabled', 'modbus_slave', 'enableModbusSlave');

            echo '<div id="page_modbus_slave" name="page_modbus_slave">';

            $proto = array('RTU'=>'RTU', 'TCP'=>'TCP');
            SelectControlCustom(_('Protocol'), 'proto', $proto, $proto['TCP'], 'proto', null, "modbusSlaveProtocolChange()");

            echo '<div id="page_proto_ip" name="page_proto_ip">';
            InputControlCustom(_('Port'), 'port', 'port', _('1~65535'), 502);
            echo '</div>';

            echo '<div id="page_proto_rtu" name="page_proto_rtu">';
            exec("cat /etc/fw_model", $model);
            
            if ($model[0] == "EG324" || $model[0] == "EG324L") {
              $comlist = array('COM1'=>'COM1', 'COM2'=>'COM2', 'COM3'=>'COM3', 'COM4'=>'COM4');
            } else {
              $comlist = array('COM1'=>'COM1', 'COM2'=>'COM2');
            }
            SelectControlCustom(_('Interface'), 'interface', $comlist, $comlist['COM1'], 'interface');

            $baudrate_list = array('1200'=>'1200', '2400'=>'2400', '4800'=>'4800', '9600'=>'9600', '19200'=>'19200', '38400'=>'38400',
            '57600'=>'57600', '115200'=>'115200', '230400'=>'230400');
            SelectControlCustom(_('Baudrate'), 'baudrate', $baudrate_list, $baudrate_list['9600'], 'baudrate');
            $databit_list = array('7'=>'7', '8'=>'8');
            SelectControlCustom(_('Databit'), 'databit', $databit_list, $databit_list['8'], 'databit');
            $stopbit_list = array('1'=>'1', '2'=>'2');
            SelectControlCustom(_('Stopbit'), 'stopbit', $stopbit_list, $stopbit_list['1'], 'stopbit');
            $parity_list = array('N'=>'None', 'O'=>'Odd', 'E'=>'Even');
            SelectControlCustom(_('Parity'), 'parity', $parity_list, $parity_list['0'], 'parity');
            echo '</div>';

            InputControlCustom(_('Slave ID'), 'slave_id', 'slave_id');
            
            ?>
                <input type="hidden" name="table_data" value="" id="hidTD_modbus_slave_point">
                <input type="hidden" name="option_list_modbus_slave_point" value="" id="option_list_modbus_slave_point">
                <div class="cbi-section cbi-tblsection" id="page_modbus_slave" name="page_modbus_slave">
                  <?php
                  $arr= array(
                    array("name"=>"Source Object",        "style"=>"", "descr"=>"", "ctl"=>"select"),
                    array("name"=>"Function Code",        "style"=>"", "descr"=>"", "ctl"=>"select"),
                    array("name"=>"Start Address",        "style"=>"", "descr"=>"", "ctl"=>"input"),
                    array("name"=>"Data Type",            "style"=>"", "descr"=>"", "ctl"=>"select"),
                    array("name"=>"Count",                "style"=>"", "descr"=>"", "ctl"=>"input"),
                    array("name"=>"Enable",               "style"=>"", "descr"=>"", "ctl"=>"check"),
                  );
                  page_table_title('modbus_slave_point', $arr);
                  ?>
                  <div class="cbi-section-create">
                    <input type="button" class="cbi-button-add" name="popBox" value="Add" onclick="addData('modbus_slave_point')">
                  </div>
                </div>
          <?php
            echo '</div>';
            echo '</div>';
            echo $buttons; 
          ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("Modbus Slave Point Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'modbus_slave_point';
      
      SelectControlCustom(_('Source Object'), $table_name.'.source_object', NULL, NULL, $table_name.'.source_object');
      
      $function_code_list = ['01' => '01', '02' => '02', 
                          '03' => '03', '04' => '04'];
      SelectControlCustom(_('Function Code'), $table_name.'.function_code', $function_code_list, $function_code_list['0x03'], $table_name.'.function_code');

      InputControlCustom(_('Start Address'), $table_name.'.start_address', $table_name.'.start_address');

      $data_type_list = ["Bit", "Unsigned 16Bits AB", "Unsigned 16Bits BA", "Signed 16Bits AB", "Signed 16Bits BA", 
                          "Unsigned 32Bits ABCD", "Unsigned 32Bits BADC", "Unsigned 32Bits CDAB", "Unsigned 32Bits DCBA", 
                          "Signed 32Bits ABCD", "Signed 32Bits BADC", "Signed 32Bits CDAB", "Signed 32Bits DCBA", 
                          "Float ABCD", "Float BADC", "Float CDAB", "Float DCBA"];
      SelectControlCustom(_('Data Type'), $table_name.'.data_type', $data_type_list, $data_type_list[1], $table_name.'.data_type', _("A highest byte"));

      InputControlCustom(_('Count'), $table_name.'.count', $table_name.'.count');

      CheckboxControlCustom(_('Enable'), $table_name.'.enabled', $table_name.'.enabled', 'checked');
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('modbus_slave_point')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->

