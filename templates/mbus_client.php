<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savembusclisettings', 'applymbusclisettings');
  endif;
  $msg = _('Restarting Mbus Client');
  page_progressbar($msg, _("Executing dct start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>

<style>
  #output {
    font-family: Arial, sans-serif;
  }

  #output table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 10px;
  }

  #output th, 
  #output td {
    border: 1px solid #ccc;
    padding: 6px 10px;
    text-align: left;
  }

  #output th {
    background: #f4f4f4;
  }

  #output .section {
    margin-bottom: 20px;
  }

  #output .title {
    font-weight: bold;
    margin-bottom: 8px;
}
</style>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("Mbus Rules"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="mbuscli_conf" role="form">
              <input type="hidden" name="table_data" value="" id="hidTD_mbuscli">
              <input type="hidden" name="option_list_mbuscli" value="" id="option_list_mbuscli">
              <div class="cbi-section cbi-tblsection" id="page_mbuscli" name="page_mbuscli">
                <?php
                echo \ElastPro\Tokens\CSRF::hiddenField();
                $arr= array(
                  array("name"=>"Order",                "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Device Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Belonged Interface",   "style"=>"", "descr"=>"", "ctl"=>"select"),
                  array("name"=>"Tag Name",             "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Address",              "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"ID",                   "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Data Type",            "style"=>"", "descr"=>"", "ctl"=>"select"),
                );

                $arr = dct_rules_common_add_fields($arr);
                page_table_title('mbuscli', $arr);
                ?>
                <div class="cbi-section-create">
                  <input type="button" class="cbi-button-add" name="popBox" value="Add" onclick="addData('mbuscli')">
                  <?php conf_im_ex('mbuscli'); ?>
                </div>
              </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('Mbuscli');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("Mbus Rules Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'mbuscli';
      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

      InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');

      $interface_list = get_belonged_interface(ComProtoEnum::COM_PROTO_MBUS, -1);
      SelectControlCustom(_('Belonged Interface'), $table_name.'.belonged_com', $interface_list, $interface_list[0], $table_name.'.belonged_com');

      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name');

      InputControlCustom(_('Address'), $table_name.'.address', $table_name.'.address');

      InputControlCustom(_('ID'), $table_name.'.id', $table_name.'.id');

      $data_type_list = ["Double", "String"];
      SelectControlCustom(_('Data Type'), $table_name.'.data_type', $data_type_list, $data_type_list[0], $table_name.'.data_type');

      dct_rules_common($table_name);
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('mbuscli')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->
</br>
<div name="mbus_scan" id="mbus_scan">
  <div class="cbi-value">
    <h4><?php echo _("Tip: Use an Mbus address scan to identify the data that needs to be collected.");?></h4>
  </div>
  <div class="cbi-value">
    <a><?php echo _("Interface:");?></a>
    <select id="scan_interface" class="cbi-input-select" name="scan_interface" style="width: 100%; max-width: 10rem; min-width: 5rem;">
    <?php
      foreach ($interface_list as $key => $value) {
        echo "<option value='$key'>$value</option>";
      }
    ?>
    </select>
    &nbsp;&nbsp;&nbsp;
    <a><?php echo _("Address:");?></a>
    <input type="text" class="cbi-input-text" id="scan_address" name="scan_address" value="" style="width: 100%; max-width: 10rem; min-width: 5rem;" placeholder="<?php echo _("Enter address");?>">
    <button class="cbi-button cbi-button-positive important" id="btn_scan" onclick="mbusScan()"><?php echo _("Scan"); ?></button>
  </div>
  <div class="cbi-value" id="output"></div>
</div>