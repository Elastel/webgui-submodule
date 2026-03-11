<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savemcsettings', 'applymcsettings');
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
          <?php echo _("MC Setting ( Qna-3E & ASCII)"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="mc_conf" role="form">
          <input type="hidden" name="table_data" value="" id="hidTD_mc">
          <input type="hidden" name="option_list_mc" value="" id="option_list_mc">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField();
          $arr= array(
            array("name"=>"Order",                "style"=>"", "descr"=>"", "ctl"=>"input"),
            array("name"=>"Device Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
            array("name"=>"Belonged Interface",   "style"=>"", "descr"=>"", "ctl"=>"select"),
            array("name"=>"Tag Name",             "style"=>"", "descr"=>"Multiple Tags Are Separated By Semicolon", "ctl"=>"input"),
            array("name"=>"Register Type",        "style"=>"", "descr"=>"", "ctl"=>"select"),
            array("name"=>"Start Address",        "style"=>"", "descr"=>"000000~00FFFF", "ctl"=>"input"),
            array("name"=>"Count",                "style"=>"", "descr"=>"0001~0120", "ctl"=>"input"),
            array("name"=>"Data Type",            "style"=>"", "descr"=>"", "ctl"=>"select"),
          );

          $arr = dct_rules_common_add_fields($arr);
          ?>       
            <div class="cbi-section cbi-tblsection" id="page_mc" name="page_mc">
              <?php page_table_title('mc', $arr); ?>
              <div class="cbi-section-create">
                <input type="button" class="cbi-button-add" name="popBox" value="Add" onclick="addData('mc')">
                <?php conf_im_ex('MC'); ?>
              </div>
            </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('MC');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("Mc Rules Setting"); ?></h4>
  <div class="cbi-section">
  <?php
    $table_name = 'mc';
    InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

    InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');

    $interface_list = get_belonged_interface(ComProtoEnum::COM_PROTO_MC, TcpProtoEnum::TCP_PROTO_MC);
    SelectControlCustom(_('Belonged Interface'), $table_name.'.belonged_com', $interface_list, $interface_list[0], $table_name.'.belonged_com');

    InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name', _('Multiple Tags Are Separated By Semicolon'));
    
    $data_area = array("X*"=>"X*", "Y*"=>"Y*", "M*"=>"M*", "L*"=>"L*", "F*"=>"F*", "V*"=>"V*", 
                        "B*"=>"B*", "D*"=>"D*", "W*"=>"W*", "TN"=>"TN", "SN"=>"SN", "CN"=>"CN");
    SelectControlCustom(_('Register Type'), $table_name.'.data_area', $data_area, $data_area[0], $table_name.'.data_area');

    InputControlCustom(_('Start Address'), $table_name.'.start_addr', $table_name.'.start_addr', _('000000~00FFFF'));

    InputControlCustom(_('Count'), $table_name.'.reg_count', $table_name.'.reg_count', _('0001~0120'));

    $data_type = ['Bit', 'Int', 'Float'];
    SelectControlCustom(_('Data Type'), $table_name.'.data_type', $data_type, $data_type[0], $table_name.'.data_type');

    dct_rules_common($table_name);
  ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('mc')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->