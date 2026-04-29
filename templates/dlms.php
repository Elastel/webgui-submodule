<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savedlmssettings', 'applydlmssettings');
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
          DLMS <?php echo _("Setting"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="dlms_conf" role="form">
            <input type="hidden" name="table_data" value="" id="hidTD_dlms">
            <input type="hidden" name="option_list_dlms" value="" id="option_list_dlms">
            <?php echo \ElastPro\Tokens\CSRF::hiddenField();
            $arr= array(
              array("name"=>"Interface Class",      "style"=>"", "descr"=>""),
              array("name"=>"OBIS",                 "style"=>"", "descr"=>""),
              array("name"=>"Data Type",            "style"=>"", "descr"=>""),
            );

            $arr = dct_rules_common_add_fields($arr);
            ?>
            <div class="cbi-section cbi-tblsection" id="page_dlms" name="page_dlms">
              <?php page_table_title('dlms', $arr); ?>
            </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('DLMS');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4>DLMS <?php echo _("Rules Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'dlms';
      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');
      InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');
      $interface_list = get_belonged_interface(ComProtoEnum::COM_PROTO_DLMS, TcpProtoEnum::TCP_PROTO_DLMS);
      SelectControlCustom(_('Belonged Interface'), $table_name.'.belonged_com', $interface_list, $interface_list[0], $table_name.'.belonged_com');
      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name');
      $ic_list = array('None'=>'None', 
                      'Data'=>'Data', 
                      'Register'=>'Register', 
                      'Extended Register'=>'Extended Register', 
                      'Demand Register'=>'Demand Register', 
                      'Clock'=>'Clock', 
                      'SAP Assignment'=>'SAP Assignment');
      SelectControlCustom(_('Interface Class'), $table_name.'.ic', $ic_list, $ic_list[0], $table_name.'.ic');
      InputControlCustom(_('OBIS'), $table_name.'.obis', $table_name.'.obis');
      $data_type_list = array('Int', 'Float', 'String');
      SelectControlCustom(_('Data Type'), $table_name.'.data_type', $data_type_list, $data_type_list[0], $table_name.'.data_type');
      dct_rules_common($table_name);
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('dlms')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->

</br>
<div name="dlms_scan" id="dlms_scan">
  <div class="cbi-value">
    <h4><?php echo _("Tip: Scan to identify the data that needs to be collected.");?></h4>
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
    <button class="cbi-button cbi-button-positive important" id="btn_scan" onclick="dlmsScan()"><?php echo _("Scan"); ?></button>
  </div>
  <div class="cbi-value" id="dlms_result">
    <textarea id="dlms_result_area" name="dlms_result_area" rows="10" cols="150"></textarea>
  </div>
</div>