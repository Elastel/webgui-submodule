<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savednp3clisettings', 'applydnp3clisettings');
  endif;
  $msg = _('Restarting DNP3 Client');
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
          DNP3 <?php echo _("Setting"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="dnp3cli_conf" role="form">
            <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
              <input type="hidden" name="table_data" value="" id="hidTD_dnp3cli">
              <input type="hidden" name="option_list_dnp3cli" value="" id="option_list_dnp3cli">
              <div class="cbi-section cbi-tblsection" id="page_dnp3cli" name="page_dnp3cli">
                <?php
                $arr= array(
                  array("name"=>"Group ID",             "data-field" => "", "style"=>"", "descr"=>"", "ctl"=>"select"),
                  array("name"=>"Index Number",     "data-field" => "", "style"=>"", "descr"=>"0~100", "ctl"=>"input"),
                );

                $arr = dct_rules_common_add_fields($arr);
                page_table_title('dnp3cli', $arr);
                ?>
              </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('Dnp3cli');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4>DNP3 <?php echo _("Rules Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'dnp3cli';
      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

      InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');

      $interface_list = get_belonged_interface(ComProtoEnum::COM_PROTO_DNP3, TcpProtoEnum::TCP_PROTO_DNP3);
      SelectControlCustom(_('Belonged Interface'), $table_name.'.belonged_com', $interface_list, $interface_list[0], $table_name.'.belonged_com');

      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name');

      $group_id_list = ['BINARY_INPUT' => 'BINARY_INPUT', 'DOUBLE_INPUT' => 'DOUBLE_INPUT', 
                      'BINARY_OUTPUT' => 'BINARY_OUTPUT', 'COUNTER_INPUT' => 'COUNTER_INPUT', 
                      'ANALOG_INPUT' => 'ANALOG_INPUT', 'ANALOG_OUTPUTS' => 'ANALOG_OUTPUTS'];
      SelectControlCustom(_('Group ID'), $table_name.'.group_id', $group_id_list, $group_id_list['ANALOG_INPUT'], $table_name.'.group_id');

      InputControlCustom(_('Index Number'), $table_name.'.point_number', $table_name.'.point_number', '0~100');

      dct_rules_common($table_name);
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('dnp3cli')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->
