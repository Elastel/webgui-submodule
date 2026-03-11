<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savesnmpclisettings', 'applysnmpclisettings');
  endif;
  $msg = _('Restarting SNMP Client');
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
          <?php echo _("SNMP Rules"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="snmpcli_conf" role="form">
              <input type="hidden" name="table_data" value="" id="hidTD_snmpcli">
              <input type="hidden" name="option_list_snmpcli" value="" id="option_list_snmpcli">
              <div class="cbi-section cbi-tblsection" id="page_snmpcli" name="page_snmpcli">
                <?php
                echo \ElastPro\Tokens\CSRF::hiddenField();
                $arr= array(
                  array("name"=>"Order",                "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Device Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Belonged Interface",   "style"=>"", "descr"=>"", "ctl"=>"select"),
                  array("name"=>"Tag Name",             "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"OID",                  "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Data Type",            "style"=>"", "descr"=>"", "ctl"=>"select"),
                );

                $arr = dct_rules_common_add_fields($arr);
                page_table_title('snmpcli', $arr);
                ?>
                <div class="cbi-section-create">
                  <input type="button" class="cbi-button-add" name="popBox" value="Add" onclick="addData('snmpcli')">
                  <?php conf_im_ex('snmpcli'); ?>
                </div>
              </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('Snmpcli');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("SNMP Rules Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'snmpcli';
      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

      InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');

      $interface_list = get_belonged_interface(-1, TcpProtoEnum::TCP_PROTO_SNMP);
      SelectControlCustom(_('Belonged Interface'), $table_name.'.belonged_com', $interface_list, $interface_list[0], $table_name.'.belonged_com');

      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name');

      InputControlCustom(_('OID'), $table_name.'.oid', $table_name.'.oid');

      $data_type_list = ["Int32", "UInt32", "Counter64", "String"];
      SelectControlCustom(_('Data Type'), $table_name.'.data_type', $data_type_list, $data_type_list[0], $table_name.'.data_type');

      dct_rules_common($table_name);
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('snmpcli')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->

</br>
<div name="snmp_scan" id="snmp_scan">
  <div class="cbi-value">
    <h4><?php echo _("Tip: Use an OID scan to identify the data that needs to be collected.");?></h4>
  </div>
  <div class="cbi-value">
    <a><?php echo _("Interface:");?></a>
    <select id="scan_interface" class="cbi-input-select" name="scan_interface" style="width: 100%; max-width: 15rem; min-width: 5rem;">
    <?php
      foreach ($interface_list as $key => $value) {
        echo "<option value='$key'>$value</option>";
      }
    ?>
    </select>
    &nbsp;&nbsp;&nbsp;
    <a><?php echo _("OID:");?></a>
    <input type="text" id="scan_oid" name="scan_oid" value="" style="width: 100%; max-width: 20rem; min-width: 8rem;" placeholder="<?php echo _("Enter OID");?>">
    <button class="cbi-button cbi-button-positive important" id="btn_scan" onclick="snmpScan()"><?php echo _("Scan"); ?></button>
  </div>
  <div class="cbi-value" id="snmp_result">
    <textarea id="snmp_result_area" name="snmp_result_area" rows="10" cols="150"></textarea>
  </div>
</div>
