<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savemodbussettings', 'applymodbussettings');
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
          <?php echo _("Modbus Setting"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="modbus_conf" role="form">
            <input type="hidden" name="table_data" value="" id="hidTD_modbus">
            <input type="hidden" name="option_list_modbus" value="" id="option_list_modbus">
            <?php echo \ElastPro\Tokens\CSRF::hiddenField();
            $arr= array(
              array("name"=>"Order",                "style"=>"", "descr"=>"", "ctl"=>"input"),
              array("name"=>"Device Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
              array("name"=>"Belonged Interface",   "style"=>"", "descr"=>"", "ctl"=>"select"),
              array("name"=>"Tag Name",          "style"=>"", "descr"=>"Multiple Tags Are Separated By Semicolon", "ctl"=>"input"),
              array("name"=>"Device ID",            "style"=>"", "descr"=>"0~255", "ctl"=>"input"),
              array("name"=>"Function Code",        "style"=>"", "descr"=>"0~255", "ctl"=>"input"),
              array("name"=>"Start Address",        "style"=>"", "descr"=>"0~65535", "ctl"=>"input"),
              array("name"=>"Count",                "style"=>"", "descr"=>"1~120", "ctl"=>"input"),
              array("name"=>"Data Type",            "style"=>"", "descr"=>"A highest byte", "ctl"=>"select"),
            );
            
            $arr = dct_rules_common_add_fields($arr);
            ?>
            <div class="cbi-section cbi-tblsection" id="page_modbus" name="page_modbus">
              <?php page_table_title('modbus', $arr); ?>
              <div class="cbi-section-create">
                <input type="button" class="cbi-button-add" name="popBox" value="Add" onclick="addData('modbus')">
                <?php conf_im_ex('Modbus'); ?>
              </div>
            </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('Modbus');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("Modbus Rules Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'modbus';
      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

      InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');

      $interface_list = get_belonged_interface(ComProtoEnum::COM_PROTO_MODBUS, TcpProtoEnum::TCP_PROTO_MODBUS);
      SelectControlCustom(_('Belonged Interface'), $table_name.'.belonged_com', $interface_list, $interface_list[0], $table_name.'.belonged_com');

      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name', _('Multiple Tags Are Separated By Semicolon'));

      InputControlCustom(_('Device ID'), $table_name.'.device_id', $table_name.'.device_id', _('0~255'));

      InputControlCustom(_('Function Code'), $table_name.'.function_code', $table_name.'.function_code', _('0~255'));

      InputControlCustom(_('Start Address'), $table_name.'.reg_addr', $table_name.'.reg_addr', _('0~65535'));

      InputControlCustom(_('Count'), $table_name.'.reg_count', $table_name.'.reg_count', _('1~120'));

      $data_type_list = ["Bit", "Unsigned 16Bits AB", "Unsigned 16Bits BA", "Signed 16Bits AB", "Signed 16Bits BA", 
                          "Unsigned 32Bits ABCD", "Unsigned 32Bits BADC", "Unsigned 32Bits CDAB", "Unsigned 32Bits DCBA", 
                          "Signed 32Bits ABCD", "Signed 32Bits BADC", "Signed 32Bits CDAB", "Signed 32Bits DCBA", 
                          "Float ABCD", "Float BADC", "Float CDAB", "Float DCBA"];
      SelectControlCustom(_('Data Type'), $table_name.'.data_type', $data_type_list, $data_type_list[1], $table_name.'.data_type', _("A highest byte"));

      dct_rules_common($table_name);
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('modbus')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->