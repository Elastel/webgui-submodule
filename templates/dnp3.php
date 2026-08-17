<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savednp3settings', 'applydnp3settings');
  endif;
  $msg = _('Restarting DNP3 Server');
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
          DNP3 <?php echo _("Server"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="dnp3" role="form">
            <?php echo \ElastPro\Tokens\CSRF::hiddenField();
            function page_channel($num) {
              if ($num == 1)
                $active = "active";
              else
                $active = "fade";
              //echo '<div class="cbi-section cbi-tblsection">';
              // RadioControlCustom('Channel '.$num, 'dnp3_enabled'.$num, 'dnp3_server'.$num, "enableDnp3");
              echo "
              <div class=\"tab-pane $active\" id=\"channel$num\">
                <div class=\"row\">
                  <div class=\"cbi-value\">
                    <label class=\"cbi-value-title\">"; echo _("Enabled"); echo "</label>
                    <input class=\"cbi-input-radio\" id=\"dnp3_server_enable$num\" name=\"dnp3_enabled$num\" value=\"1\" type=\"radio\" checked onchange=\"enableDnp3(true, $num)\">
                    <label >"; echo _("Enable"); echo "</label>

                    <input class=\"cbi-input-radio\" id=\"dnp3_server_disable$num\" name=\"dnp3_enabled$num\" value=\"0\" type=\"radio\" onchange=\"enableDnp3(false, $num)\">
                    <label >"; echo _("Disable"); echo "</label>
                  </div>
              ";

              echo '<div id="page_dnp3'.$num.'" name="page_dnp3'.$num.'">';

              $proto = array('RTU'=>'RTU', 'TCP'=>'TCP');
              SelectControlCustom(_('Protocol'), 'proto'.$num, $proto, $proto['TCP'], 'proto'.$num, null, "dnp3ProtocolChange($num)");

              echo '<div id="page_proto_ip'.$num.'" name="page_proto_ip'.$num.'">';
              InputControlCustom(_('Port'), 'port'.$num, 'port'.$num, _('1~65535'), 20000);
              echo '</div>';

              echo '<div id="page_proto_rtu'.$num.'" name="page_proto_rtu'.$num.'">';
              
              if (model_category('four_com')) {
                $comlist = array('COM1'=>'COM1', 'COM2'=>'COM2', 'COM3'=>'COM3', 'COM4'=>'COM4');
              } else {
                $comlist = array('COM1'=>'COM1', 'COM2'=>'COM2');
              }
              SelectControlCustom(_('Interface'), 'interface'.$num, $comlist, $comlist['COM1'], 'interface'.$num);

              $baudrate_list = array('1200'=>'1200', '2400'=>'2400', '4800'=>'4800', '9600'=>'9600', '19200'=>'19200', '38400'=>'38400',
              '57600'=>'57600', '115200'=>'115200', '230400'=>'230400');
              SelectControlCustom(_('Baudrate'), 'baudrate'.$num, $baudrate_list, $baudrate_list['9600'], 'baudrate'.$num);
              $databit_list = array('7'=>'7', '8'=>'8');
              SelectControlCustom(_('Databit'), 'databit'.$num, $databit_list, $databit_list['8'], 'databit'.$num);
              $stopbit_list = array('1'=>'1', '2'=>'2');
              SelectControlCustom(_('Stopbit'), 'stopbit'.$num, $stopbit_list, $stopbit_list['1'], 'stopbit'.$num);
              $parity_list = array('0'=>'None', '1'=>'Odd', '2'=>'Even');
              SelectControlCustom(_('Parity'), 'parity'.$num, $parity_list, $parity_list['0'], 'parity'.$num);
              echo '</div>';
              InputControlCustom(_('Slave Address'), 'slave_address'.$num, 'slave_address'.$num, "0~65519");
              InputControlCustom(_('Master Address'), 'master_address'.$num, 'master_address'.$num, "0~65519");
              echo '
                  </div>
                </div>
              </div>
              ';
            }
            ?>
            <div class="cbi-section cbi-tblsection">
              <h4><?php echo _("DNP3").' '._('Server').' '._("Channel"); ?></h4>
              <ul class="nav nav-tabs">
                <li role="presentation" class="nav-item"><a class="nav-link active" href="#channel1" aria-controls="channel1" role="tab" data-toggle="tab"><?php echo _("Channel")."1 "._("Setting"); ?></a></li>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#channel2" aria-controls="channel2" role="tab" data-toggle="tab"><?php echo _("Channel")."2 "._("Setting"); ?></a></li>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#channel3" aria-controls="channel3" role="tab" data-toggle="tab"><?php echo _("Channel")."3 "._("Setting"); ?></a></li>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#channel4" aria-controls="channel4" role="tab" data-toggle="tab"><?php echo _("Channel")."4 "._("Setting"); ?></a></li>
              </ul>
              <!-- Tab panes -->
              <div class="tab-content">
                  <?php page_channel(1); ?>
                  <?php page_channel(2); ?>
                  <?php page_channel(3); ?>
                  <?php page_channel(4); ?>
              </div><!-- /.tab-content -->
            </div>
            <input type="hidden" name="table_data" value="" id="hidTD_dnp3">
            <input type="hidden" name="option_list_dnp3" value="" id="option_list_dnp3">
            <div class="cbi-section cbi-tblsection" id="page_dnp3" name="page_dnp3">
              <h4><?php echo _("Mappings"); ?></h4>
              <?php
              $arr= array(
                array("name"=>"Source Object",        "data-field" => "factor_name", "style"=>"", "descr"=>"", "ctl"=>"select"),
                array("name"=>"Destination Channel",  "data-field" => "", "style"=>"", "descr"=>"", "ctl"=>"select"),
                array("name"=>"Group ID",             "data-field" => "", "style"=>"", "descr"=>"", "ctl"=>"select"),
                array("name"=>"Index Number",         "data-field" => "", "style"=>"", "descr"=>"0~100", "ctl"=>"input"),
                array("name"=>"Ivent Class",          "data-field" => "", "style"=>"", "descr"=>"", "ctl"=>"select"),
                array("name"=>"Event Variation",      "data-field" => "", "style"=>"", "descr"=>"", "ctl"=>"select"),
                array("name"=>"Static Variation",     "data-field" => "", "style"=>"", "descr"=>"", "ctl"=>"select"),
                array("name"=>"Enable",               "data-field" => "", "style"=>"", "descr"=>"", "ctl"=>"check"),
              );
              page_table_title('dnp3', $arr);
              ?>
              <div class="cbi-section-create">
                <input type="button" class="cbi-button-add" name="popBox" value="<?=_('Add')?>" onclick="addData('dnp3')">
              </div>
            </div>
          <?php
            // echo '</div>';
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
  <h4><?php echo _("DNP3 Point Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'dnp3';
      
      SelectControlCustom(_('Source Object'), $table_name.'.source_object', NULL, NULL, $table_name.'.source_object');

      

      $dest_channel = ['channel1' => 'channel1', 'channel2' => 'channel2', 'channel3' => 'channel3', 'channel4' => 'channel4'];
      SelectControlCustom(_('Destination Channel'), $table_name.'.dest_channel', $dest_channel, $dest_channel['channel1'], $table_name.'.dest_channel');

      $group_id_list = ['BINARY_INPUT' => 'BINARY_INPUT', 'DOUBLE_INPUT' => 'DOUBLE_INPUT', 
                      'BINARY_OUTPUT' => 'BINARY_OUTPUT', 'COUNTER_INPUT' => 'COUNTER_INPUT', 
                      'ANALOG_INPUT' => 'ANALOG_INPUT', 'ANALOG_OUTPUTS' => 'ANALOG_OUTPUTS', 
                      /*'OCTECT_STRING' => 'OCTECT_STRING'*/];
      SelectControlCustom(_('Group ID'), $table_name.'.group_id', $group_id_list, $group_id_list['ANALOG_INPUT'], $table_name.'.group_id', null, "groupIdChange()");
    
      InputControlCustom(_('Index Number'), $table_name.'.point_number', $table_name.'.point_number');

      $event_class = ['none' => 'none', 'class1' => 'class1', 'class2' => 'class2', 'class3' => 'class3'];
      SelectControlCustom(_('Event Class'), $table_name.'.event_class', $event_class, $event_class['none'], $table_name.'.event_class');
      
      $event_var = ['var1' => 'var1', 'var2' => 'var2', 'var3' => 'var3', 'var4' => 'var4', 'var5' => 'var5', 'var6' => 'var6', 'var7' => 'var7', 'var8' => 'var8'];
      SelectControlCustom(_('Event Variation'), $table_name.'.event_var', $event_var, $event_var['var1'], $table_name.'.event_var');

      $static_var = ['var1' => 'var1', 'var2' => 'var2', 'var3' => 'var3', 'var4' => 'var4'];
      SelectControlCustom(_('Static Variation'), $table_name.'.static_var', $static_var, $static_var['var1'], $table_name.'.static_var');

      CheckboxControlCustom(_('Enable'), $table_name.'.enabled', $table_name.'.enabled', 'checked');
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('dnp3')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->

