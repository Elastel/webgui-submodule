<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('saveopcuaclisettings', 'applyopcuaclisettings');
  endif;
  $msg = _('Restarting OPCUA Rules');
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
          <?php echo _("OPCUA Rules"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="opcuacli_conf" role="form">
            <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
              <input type="hidden" name="table_data" value="" id="hidTD_opcuacli">
              <input type="hidden" name="option_list_opcuacli" value="" id="option_list_opcuacli">
              <div class="cbi-section cbi-tblsection" id="page_opcuacli" name="page_opcuacli">
                <?php
                $arr= array(
                  array("name"=>"Order",                "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Device Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Belonged Interface",   "style"=>"", "descr"=>"", "ctl"=>"select"),
                  array("name"=>"Tag Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Node Name",            "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Data Type",            "style"=>"", "descr"=>"", "ctl"=>"select"),
                );

                $arr = dct_rules_common_add_fields($arr);
                page_table_title('opcuacli', $arr);
                ?>
                <div class="cbi-section-create">
                  <input type="button" class="cbi-button-add" name="popBox" value="Add" onclick="addData('opcuacli')">
                  <?php conf_im_ex('Opcuacli'); ?>
                </div>
              </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('Opcuacli');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("OPCUA Rules Object Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'opcuacli';
      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

      InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');

      $interface_list = get_belonged_interface(-1, TcpProtoEnum::TCP_PROTO_OPCUA);
      SelectControlCustom(_('Belonged Interface'), $table_name.'.belonged_com', $interface_list, $interface_list[0], $table_name.'.belonged_com');

      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name');

      InputControlCustom(_('Node Name'), $table_name.'.node_name', $table_name.'.node_name');

      $data_type_list = ["Bool", "Byte", "Int16", "UInt16", "Int32", "UInt32", "Float", "String"];
      SelectControlCustom(_('Data Type'), $table_name.'.data_type', $data_type_list, $data_type_list[0], $table_name.'.data_type');

      dct_rules_common($table_name);
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('opcuacli')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->
