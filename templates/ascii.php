<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('saveasciisettings', 'applyasciisettings');
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
          <?php echo _("ASCII Setting"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="ascii_conf" role="form">
            <input type="hidden" name="table_data" value="" id="hidTD_ascii">
            <input type="hidden" name="option_list_ascii" value="" id="option_list_ascii">
            <?php echo \ElastPro\Tokens\CSRF::hiddenField();;
            $arr= array(
              array("name"=>"Order",                "style"=>"", "descr"=>"", "ctl"=>"input"),
              array("name"=>"Device Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
              array("name"=>"Belonged Interface",   "style"=>"", "descr"=>"", "ctl"=>"select"),
              array("name"=>"Tag Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
              array("name"=>"Tx Command",            "style"=>"", "descr"=>"", "ctl"=>"input"),
              array("name"=>"Command Format",        "style"=>"", "descr"=>"", "ctl"=>"select"),
              array("name"=>"Reporting Center",     "style"=>"", "descr"=>"Multiple Servers Are Separated By Minus", "ctl"=>"input"),
              array("name"=>"Enable",               "style"=>"", "descr"=>"", "ctl"=>"check"),
            );?>       
              <div class="cbi-section cbi-tblsection" id="page_ascii" name="page_ascii">
                <?php page_table_title('ascii', $arr); ?>
              <div class="cbi-section-create">
                <input type="button" class="cbi-button-add" name="popBox" value="Add" onclick="addData('ascii')">
                <?php conf_im_ex('Ascii'); ?>
              </div>
            </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('Ascii');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("ASCII Rules Setting"); ?></h4>
  <div class="cbi-section">
  <?php
      $table_name = 'ascii';
      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

      InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');

      $interface_list = get_belonged_interface(ComProtoEnum::COM_PROTO_ASCII, TcpProtoEnum::TCP_PROTO_ASCII);
      SelectControlCustom(_('Belonged Interface'), $table_name.'.belonged_com', $interface_list, $interface_list[0], $table_name.'.belonged_com');

      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name');

      InputControlCustom(_('Tx Command'), $table_name.'.tx_cmd', $table_name.'.tx_cmd');

      $cmd_format = array('hex'=>'HEX', 'ascii'=>'ASCII');
      SelectControlCustom(_('Command Format'), $table_name.'.cmd_format', $cmd_format, $cmd_format[0], $table_name.'.cmd_format');

      InputControlCustom(_('Reporting Center'), $table_name.'.server_center', $table_name.'.server_center', _('Multiple Servers Are Separated By Minus'));

      CheckboxControlCustom(_('Enable'), $table_name.'.enabled', $table_name.'.enabled', 'checked');
  ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('ascii')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->