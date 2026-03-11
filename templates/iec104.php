<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('saveiec104settings', 'applyiec104settings');
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
          <?php echo _("IEC104 Setting"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="iec104_conf" role="form">
            <input type="hidden" name="table_data" value="" id="hidTD_iec104">
            <input type="hidden" name="option_list_iec104" value="" id="option_list_iec104">
            <?php echo \ElastPro\Tokens\CSRF::hiddenField();
            $arr= array(
              array("name"=>"Order",                "style"=>"", "descr"=>""),
              array("name"=>"Device Name",          "style"=>"", "descr"=>""),
              array("name"=>"Belonged Interface",   "style"=>"", "descr"=>""),
              array("name"=>"Tag Name",             "style"=>"", "descr"=>"Multiple Tags Are Separated By Semicolon"),
              array("name"=>"Type ID - IOA",          "style"=>"", "descr"=>""),
              // array("name"=>"Start IOA",            "style"=>"", "descr"=>"0~255"),
              // array("name"=>"Common Address",       "style"=>"", "descr"=>""),
              // array("name"=>"Data Type",            "style"=>"", "descr"=>""),
            );

            $arr = dct_rules_common_add_fields($arr);
            ?>
            <div class="cbi-section cbi-tblsection" id="page_iec104" name="page_iec104">
              <?php page_table_title('iec104', $arr); ?>
              <div class="cbi-section-create">
                <input type="button" class="cbi-button-add" name="popBox" value="Add" onclick="addData('iec104')">
                <?php conf_im_ex('Iec104'); ?>
              </div>
            </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('Iec104');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("Iec104 Rules Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'iec104';
      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

      InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');

      $interface_list = get_belonged_interface(-1, TcpProtoEnum::TCP_PROTO_IEC104);
      SelectControlCustom(_('Belonged Interface'), $table_name.'.belonged_com', $interface_list, $interface_list[0], $table_name.'.belonged_com');

      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name', _('Multiple Tags Are Separated By Semicolon'));
      
      // SelectControlCustom(_('Type ID - IOA'), $table_name.'.type_id', $type_id_list, $type_id_list[0], $table_name.'.type_id');
    ?>
      <div class="cbi-value">
          <input type="hidden" name="iec104_discover_data" value="" id="iec104_discover_data">
          <label class="cbi-value-title"><?php echo _("Type ID - IOA"); ?></label>
          <input type="text" class="cbi-input-text" name="iec104.type_id" id="iec104.type_id" oninput="iec104FilterFunction()">
          <div id="typeIdList" class="dropdown-content"></div>
          <button class="btn rounded-right btn_iec104discover" type="button"><i class="fas fa-sync"></i></button>
      </div>
    <?php
      // InputControlCustom(_('Start IOA'), $table_name.'.start_addr', $table_name.'.start_addr', _('0~65535'));

      // InputControlCustom(_('Common Address'), $table_name.'.common_addr', $table_name.'.common_addr');

      // SelectControlCustom(_('Data Type'), $table_name.'.data_type', $data_type_list, $data_type_list[0], $table_name.'.data_type');

      dct_rules_common($table_name);
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('iec104')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->