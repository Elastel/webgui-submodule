<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('saveiec1107settings', 'applyiec1107settings');
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
          IEC62056-21 <?php echo _("Setting"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="iec1107_conf" role="form">
            <input type="hidden" name="table_data" value="" id="hidTD_iec1107">
            <input type="hidden" name="option_list_iec1107" value="" id="option_list_iec1107">
            <?php echo \ElastPro\Tokens\CSRF::hiddenField();
            $arr= array(
              array("name"=>"OBIS",                 "style"=>"", "descr"=>""),
              array("name"=>"Data Type",            "style"=>"", "descr"=>""),
            );

            $arr = dct_rules_common_add_fields($arr);
            ?>
            <div class="cbi-section cbi-tblsection" id="page_iec1107" name="page_iec1107">
              <?php page_table_title('iec1107', $arr); ?>
            </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('Iec1107');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4>IEC62056-21 <?php echo _("Rules Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'iec1107';
      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

      InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');

      $interface_list = get_belonged_interface(ComProtoEnum::COM_PROTO_IEC1107, -1);
      SelectControlCustom(_('Belonged Interface'), $table_name.'.belonged_com', $interface_list, $interface_list[0], $table_name.'.belonged_com');

      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name');
    ?>
      <div class="cbi-value">
          <input type="hidden" name="iec1107_discover_data" value="" id="iec1107_discover_data">
          <label class="cbi-value-title"><?php echo _("OBIS"); ?></label>
          <input type="text" class="cbi-input-text" name="iec1107.obis" id="iec1107.obis" oninput="iec1107FilterFunction()">
          <div id="obisList" class="dropdown-content"></div>
          <button class="btn rounded-right btn_iec1107discover" type="button"><i class="fas fa-sync"></i></button>
      </div>
    <?php
      SelectControlCustom(_('Data Type'), $table_name.'.data_type', $data_type_list, $data_type_list[0], $table_name.'.data_type');
      dct_rules_common($table_name);
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('iec1107')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->