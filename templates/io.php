<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('saveIOsettings', 'applyIOsettings');
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
          <?php echo _("IO Setting"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="io_conf" role="form">
            <?php echo \ElastPro\Tokens\CSRF::hiddenField();
              if ($adc_index_count > 0) { 
                $arrADC = array(
                  array("name"=>"Device Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"ADC Channel",          "style"=>"", "descr"=>"", "ctl"=>"select"),
                  array("name"=>"Tag Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Capture Type",         "style"=>"", "descr"=>"", "ctl"=>"select"),
                  array("name"=>"Range Down",           "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Range Up",             "style"=>"", "descr"=>"", "ctl"=>"input"),
                );

                $arrADC = dct_rules_common_add_fields($arrADC);
            ?>
              <div class="cbi-section cbi-tblsection" id="pageADC" name="pageADC">
                <input type="hidden" name="tableDataADC" value="" id="hidTD_adc">
                <input type="hidden" name="option_list_adc" value="" id="option_list_adc">
                <h4><?php echo _("ADC Setting"); ?></h4>
                <?php page_table_title('adc', $arrADC); ?>
                <div class="cbi-section-create">
                  <input type="button" class="cbi-button-add" name="btnADC" value="ADD" onclick="addDataIO(this, 'io')">
                  <?php conf_im_ex('ADC'); ?>
                </div>
              </div>
            <?php } ?>
            <?php
            if ($di_index_count > 0) { 
              $arrDI = array(
                array("name"=>"Device Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
                array("name"=>"DI Channel",           "style"=>"", "descr"=>"", "ctl"=>"select"),
                array("name"=>"Tag Name",          "style"=>"", "descr"=>"Multiple Tags Are Separated By Semicolon", "ctl"=>"input"),
                array("name"=>"Mode",                 "style"=>"", "descr"=>"", "ctl"=>"select"),
                array("name"=>"Count Method",         "style"=>"", "descr"=>"", "ctl"=>"select"),
                array("name"=>"Debounce Interval",    "style"=>"", "descr"=>"", "ctl"=>"input"),
              );

              $arrDI = dct_rules_common_add_fields($arrDI);
            ?>
              <div class="cbi-section cbi-tblsection" id="pageDI" name="pageDI">
                <input type="hidden" name="tableDataDI" value="" id="hidTD_di">
                <input type="hidden" name="option_list_di" value="" id="option_list_di">
                <h4><?php echo _("DI Setting"); ?></h4>
                <?php page_table_title('di', $arrDI);?>
                <div class="cbi-section-create">
                  <input type="button" class="cbi-button-add" name="btnDI" value="ADD" onclick="addDataIO(this, 'io')">
                  <?php conf_im_ex('DI'); ?>
                </div>
              </div>
            <?php } ?>
            <?php
            if ($do_index_count > 0) { 
              $arrDO = array(
                array("name"=>"Device Name",          "style"=>"", "descr"=>"", "ctl"=>"input"),
                array("name"=>"DO Channel",           "style"=>"", "descr"=>"", "ctl"=>"select"),
                array("name"=>"Tag Name",          "style"=>"", "descr"=>"Multiple Tags Are Separated By Semicolon", "ctl"=>"input"),
                array("name"=>"Init Status",          "style"=>"", "descr"=>"", "ctl"=>"input"),
                array("name"=>"Current Status",       "style"=>"", "descr"=>"", "ctl"=>"input"),
              );

              $arrDO = dct_rules_common_add_fields($arrDO);
            ?>
              <div class="cbi-section cbi-tblsection" id="pageDO" name="pageDO">
                <input type="hidden" name="tableDataDO" value="" id="hidTD_do">
                <input type="hidden" name="option_list_do" value="" id="option_list_do">
                <h4><?php echo _("DO Setting"); ?></h4>
                <?php page_table_title('do', $arrDO); ?>
                <div class="cbi-section-create">
                  <input type="button" class="cbi-button-add" name="btnDO" value="ADD" onclick="addDataIO(this, 'io')">
                  <?php conf_im_ex('DO'); ?>
                </div>
              </div>
            <?php } ?>
            <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('IO');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <input hidden="hidden" name="model" id="model" value="<?php echo $model ?>">
  <input hidden="hidden" name="page_name" id="page_name" value="0">
  <h4 name="popBoxTitle" id="popBoxTitle"><?php echo _("ADC Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'io';
      InputControlCustom(_('Device Name'), $table_name.'.device_name', $table_name.'.device_name');

      echo '<div name="pageIndexADC" id="pageIndexADC">';
      $adc_index = [];
      for ($i = 0; $i < $adc_index_count; $i++) {
        $adc_index["ADC$i"] = "ADC$i";
      }

      SelectControlCustom(_('ADC Channel'), $table_name.'.index.adc', $adc_index, $adc_index[0], $table_name.'.index.adc');
      echo '</div>';

      echo '<div name="pageIndexDI" id="pageIndexDI">';
      $di_index = [];
      for ($i = 0; $i < $di_index_count; $i++) {
        $di_index["DI$i"] = "DI$i";
      }

      SelectControlCustom(_('DI Channel'), $table_name.'.index.di', $di_index, $di_index[0], $table_name.'.index.di');
      echo '</div>';

      echo '<div name="pageIndexDO" id="pageIndexDO">';
      $do_index = [];
      for ($i = 0; $i < $do_index_count; $i++) {
        $do_index["DO$i"] = "DO$i";
      }

      SelectControlCustom(_('DO Channel'), $table_name.'.index.do', $do_index, $do_index[0], $table_name.'.index.do');
      echo '</div>';

      InputControlCustom(_('Tag Name'), $table_name.'.factor_name', $table_name.'.factor_name', _('Multiple Tags Are Separated By Semicolon'));

      echo '<div name="pageADCMod" id="pageADCMod">';
      $cap_type = ['4-20mA', '0-10V'];
      SelectControlCustom(_('Capture Type'), $table_name.'.cap_type', $cap_type, $cap_type[0], $table_name.'.cap_type');
      InputControlCustom(_('Range Down'), $table_name.'.range_down', $table_name.'.range_down');
      InputControlCustom(_('Range Up'), $table_name.'.range_up', $table_name.'.range_up');
      echo '</div>
      <div name="pageDIMod" id="pageDIMod">';
      $mode = ['Counting Mode', 'Status Mode'];
      SelectControlCustom(_('Mode'), $table_name.'.mode', $mode, $mode[0], $table_name.'.mode', null, 'selectMode()');
      echo '<div id="pageCount" name="pageCount">';
      $count_method = ['Rising Edge', 'Falling Edge'];
      SelectControlCustom(_('Count Method'), $table_name.'.count_method', $count_method, $count_method[0], $table_name.'.count_method');
      InputControlCustom(_('Debounce Interval'), $table_name.'.debounce_interval', $table_name.'.debounce_interval', _('ms'));
      echo '</div>
      </div>';

      echo '<div name="pageDOMod" id="pageDOMod">';
      $init_status = ['Open', 'Close'];
      SelectControlCustom(_('Init Status'), $table_name.'.init_status', $init_status, $init_status[0], $table_name.'.init_status');
      
      LabelControlCustom(_("Current Status"), $table_name.'.cur_status', $table_name.'.cur_status');
      echo '</div>';

      dct_rules_common($table_name);
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveDataIO()"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->
