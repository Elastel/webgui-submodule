<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('saveiec61850clisettings', 'applyiec61850clisettings');
  endif;
  $msg = _('Restarting IEC61850 Rules');
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
          IEC61850 <?php echo _("Setting"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="iec61850cli_conf" role="form">
            <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
              <input type="hidden" name="table_data" value="" id="hidTD_iec61850cli">
              <input type="hidden" name="option_list_iec61850cli" value="" id="option_list_iec61850cli">
              <div class="cbi-section cbi-tblsection" id="page_iec61850cli" name="page_iec61850cli">
                <?php
                $arr= array(
                  array("name"=>"Functional Constraints",   "style"=>"", "descr"=>"", "ctl"=>"select"),
                  array("name"=>"Node Name",            "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Data Type",            "style"=>"", "descr"=>"", "ctl"=>"select"),
                );

                $arr = dct_rules_common_add_fields($arr);
                page_table_title('iec61850cli', $arr);
                ?>
              </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('iec61850cli');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4>IEC61850 <?php echo _("Rules Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'iec61850cli';
      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

      InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');

      $interface_list = get_belonged_interface(-1, TcpProtoEnum::TCP_PROTO_IEC61850);
      SelectControlCustom(_('Belonged Interface'), $table_name.'.belonged_com', $interface_list, $interface_list[0], $table_name.'.belonged_com');

      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name');

      $fc_list = ['ST', 'MX', 'SP', 'SV', 'CF', 'DC', 'SG', 'SE', 'SR', 'OR', 'BL', 'EX', 'CO', 'US', 'MS', 'RP', 'BR', 'LG', 'GO'];
      SelectControlCustom(_('Functional Constraints'), $table_name.'.fc', $fc_list, $fc_list[0], $table_name.'.fc');

      InputControlCustom(_('Node Name'), $table_name.'.node_name', $table_name.'.node_name');

      $data_type_list = ['Bool', 'Int', 'Float', 'String'];
      SelectControlCustom(_('Data Type'), $table_name.'.data_type', $data_type_list, $data_type_list[0], $table_name.'.data_type');

      dct_rules_common($table_name);
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('iec61850cli')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->

</br>
<div name="iec61850cli_scan" id="iec61850cli_scan">
  <div class="cbi-value">
    <h4><?php echo _("Tip: Scan to get logical device list.");?></h4>
  </div>
  <div class="cbi-value">
    <a><?php echo _("Interface");?>:</a>
    <select id="scan_interface" class="cbi-input-select" name="scan_interface" style="width: 100%; max-width: 15rem; min-width: 5rem;">
    <?php
      foreach ($interface_list as $key => $value) {
        echo "<option value='$key'>$value</option>";
      }
    ?>
    </select>
    &nbsp;&nbsp;&nbsp;
    <button class="cbi-button cbi-button-positive important" id="btn_scan" onclick="iec61850cliScan()"><?php echo _("Scan"); ?></button>
  </div>
  <div class="cbi-value" id="iec61850cli_result">
    <textarea id="iec61850cli_result_area" name="iec61850cli_result_area" rows="10" cols="150"></textarea>
  </div>
</div>
