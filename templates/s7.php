<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('saves7settings', 'applys7settings');
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
          <?php echo _("S7 Setting"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="s7_conf" role="form">
          <input type="hidden" name="table_data" value="" id="hidTD_s7">
          <input type="hidden" name="option_list_s7" value="" id="option_list_s7">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField();
          $arr= array(
            array("name"=>"Order",                "style"=>"", "descr"=>"", "ctl"=>"input"),
            array("name"=>"Device Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
            array("name"=>"Belonged Interface",   "style"=>"", "descr"=>"", "ctl"=>"select"),
            array("name"=>"Tag Name",          "style"=>"", "descr"=>"Multiple Tags Are Separated By Semicolon", "ctl"=>"input"),
            array("name"=>"Register Type",        "style"=>"", "descr"=>"", "ctl"=>"select"),
            array("name"=>"Register Address",     "style"=>"", "descr"=>"", "ctl"=>"input"),
            array("name"=>"Count",                "style"=>"", "descr"=>"1~120", "ctl"=>"input"),
            array("name"=>"Data Type",            "style"=>"", "descr"=>"", "ctl"=>"select"),
          );
          
          $arr = dct_rules_common_add_fields($arr);
          ?>       
            <div class="cbi-section cbi-tblsection" id="page_s7" name="page_s7">
              <?php page_table_title('s7', $arr); ?>
              <div class="cbi-section-create">
                <input type="button" class="cbi-button-add" name="popBox" value="Add" onclick="addData('s7')">
                <?php conf_im_ex('S7'); ?>
              </div>
            </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('S7');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("S7 Rules Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 's7';
      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

      InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');

      $interface_list = get_belonged_interface(-1, TcpProtoEnum::TCP_PROTO_S7);
      SelectControlCustom(_('Belonged Interface'), $table_name.'.belonged_com', $interface_list, $interface_list[0], $table_name.'.belonged_com');

      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name', _('Multiple Tags Are Separated By Semicolon'));
      
      $reg_type = ['I', 'Q', 'M', 'DB', 'V', 'C', 'T'];
      SelectControlCustom(_('Register Type'), $table_name.'.reg_type', $reg_type, $reg_type[0], $table_name.'.reg_type');

      InputControlCustom(_('Register Address'), $table_name.'.reg_addr', $table_name.'.reg_addr');

      InputControlCustom(_('Count'), $table_name.'.reg_count', $table_name.'.reg_count', _('1~120'));

      $word_len = ['Bit', 'Byte', 'Word', 'DWord', 'Real', 'Counter', 'Timer'];
      SelectControlCustom(_('Data Type'), $table_name.'.word_len', $word_len, $word_len[0], $table_name.'.word_len');

      dct_rules_common($table_name);
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('s7')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->