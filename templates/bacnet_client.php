<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savebacclisettings', 'applybacclisettings');
  endif;
  $msg = _('Restarting BACnet Rules');
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
          <?php echo _("BACnet Rules"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="baccli_conf" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
            <input type="hidden" name="table_data" value="" id="hidTD_baccli">
            <input type="hidden" name="option_list_baccli" value="" id="option_list_baccli">
            <div class="cbi-section cbi-tblsection" id="page_baccli" name="page_baccli">
              <?php
              $arr= array(
                array("name"=>"Order",                "style"=>"", "descr"=>"", "ctl"=>"input"),
                array("name"=>"Device Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
                array("name"=>"Belonged Interface",   "style"=>"", "descr"=>"", "ctl"=>"select"),
                array("name"=>"Tag Name",             "style"=>"", "descr"=>"", "ctl"=>"input"),
                array("name"=>"Object Device ID",     "style"=>"", "descr"=>"", "ctl"=>"input"),
                array("name"=>"Object Identifier",    "style"=>"", "descr"=>"", "ctl"=>"input"),
              );
              
              $arr = dct_rules_common_add_fields($arr);
              page_table_title('baccli', $arr);
              ?>
              <div class="cbi-section-create">
                <input type="button" class="cbi-button-add" name="popBox" value="Add" onclick="addData('baccli')">
                <?php conf_im_ex('Baccli'); ?>
              </div>
            </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('Baccli');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("BACnet Rules Object Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'baccli';
      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

      InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');

      $interface_list = get_belonged_interface(ComProtoEnum::COM_PROTO_BACNET, TcpProtoEnum::TCP_PROTO_BACNET);
      SelectControlCustom(_('Belonged Interface'), $table_name.'.belonged_com', $interface_list, $interface_list[0], $table_name.'.belonged_com');

      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name');
    ?>

    <div class="cbi-value">
        <input type="hidden" name="bacnet_discover_data" value="" id="bacnet_discover_data">
        <label class="cbi-value-title"><?php echo _("Object Device ID"); ?></label>
        <input type="text" class="cbi-input-text" name="baccli.object_device_id" id="baccli.object_device_id" oninput="filterFunction()">
        <div id="deviceIdList" class="dropdown-content"></div>
        <button class="btn rounded-right btn_bacdiscover" type="button"><i class="fas fa-sync"></i></button>
    </div>

    <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Object Identifier"); ?></label>
        <input type="text" class="cbi-input-text" name="baccli.object_id" id="baccli.object_id" oninput="filterFunctionObject()">
        <div id="objectIdList" class="dropdown-content"></div>
    </div>

    <?php
      //InputControlCustom(_('Object Device ID'), $table_name.'.object_device_id', $table_name.'.object_device_id');

      // InputControlCustom(_('Object Identifier'), $table_name.'.object_id', $table_name.'.object_id');

      dct_rules_common($table_name);
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('baccli')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->
<script>
</script>

