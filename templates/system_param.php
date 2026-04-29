<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savesystemparamsettings', 'applysystemparamsettings');
  endif;
  $msg = _('Restarting System Parameters Rules');
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
          <?php echo _("System Parameters"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="system_param_conf" role="form">
            <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
              <input type="hidden" name="table_data" value="" id="hidTD_system_param">
              <input type="hidden" name="option_list_system_param" value="" id="option_list_system_param">
              <div class="cbi-section cbi-tblsection" id="page_system_param" name="page_system_param">
                <?php
                $arr= array(
                  array("name"=>"Order",                "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Tag Name",             "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Parameter",            "style"=>"", "descr"=>"", "ctl"=>"select"),
                  array("name"=>"Reporting Center",     "style"=>"", "descr"=>"Multiple Servers Are Separated By Minus", "ctl"=>"input"),
                  array("name"=>"Enable",               "style"=>"", "descr"=>"", "ctl"=>"check"),
                );

                page_table_title('system_param', $arr);
                ?>
                <div class="cbi-section-create">
                  <input type="button" class="cbi-button-add" name="popBox" value=<?=_("ADD")?> onclick="addData('system_param')">
                  <?php conf_im_ex('system_param'); ?>
                </div>
              </div>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('system_param');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("System Parameter Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'system_param';
      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name');
      
      $system_list = array('model'=>'model', 'sn'=>'sn', 'time'=>'time', 'uptime'=>'uptime', 'cpu'=>'cpu', 'cur_network'=>'cur_network', 
                          'memory'=>'memory', 'disk'=>'disk', 'wan'=>'wan', 'lan'=>'lan', 'lte'=>'lte', /*'wifi'=>'wifi',*/ 'custom'=>'custom');
      SelectControlCustom(_('Parameter'), $table_name.'.param', $system_list, $system_list[0], $table_name.'.param', null, "systemParamChange('$table_name')");
      
      echo '<div id="page_cmd" name="page_cmd">';
        InputControlCustom(_('Command'), $table_name.'.command', $table_name.'.command');
      echo '</div>';

      InputControlCustom(_('Reporting Center'), $table_name.'.server_center', $table_name.'.server_center', _('Multiple Servers Are Separated By Minus'));
      CheckboxControlCustom(_('Enable'), $table_name.'.enabled', $table_name.'.enabled', 'checked');
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('system_param')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->
