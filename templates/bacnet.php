<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savebacnetsettings', 'applybacnetsettings');
  endif;
  $msg = _('Restarting BACnet Server');
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
          BACnet <?php echo _("Server"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="bacnet" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField();;
            echo '<div class="cbi-section cbi-tblsection">';
            /* "bacnet_enabled" (not "enabled"): the mapping table rows below use
               name="enabled" for their own checkbox, and two fields with the same
               name in one form make the browser post both values. */
            RadioControlCustom('BACnet '._('Server'), 'bacnet_enabled', 'bacnet', 'enableBACnetServer', null, $enabled);

            echo '<div id="page_bacnet" name="page_bacnet">';

            $proto = array('BACnet/IP', 'BACnet/MSTP');
            SelectControlCustom(_('Protocol'), 'proto', $proto, $proto[0], 'proto', null, "bacnetProtocolChange()");

            echo '<div id="page_proto_ip" name="page_proto_ip">';
            SelectControlCustom(_('Interface'), 'ifname', $interface_list, null, 'ifname');

            InputControlCustom(_('Port'), 'port', 'port', _('1~65535'));
            echo '</div>';

            echo '<div id="page_proto_mstp" name="page_proto_mstp">';
            
            if (model_category('four_com')) {
              $comlist = array('COM1'=>'COM1', 'COM2'=>'COM2');
            } else {
              $comlist = array('COM1'=>'COM1');
            }
            SelectControlCustom(_('Interface'), 'interface', $comlist, $comlist[0], 'interface');

            $baudrate_list = array('1200'=>'1200', '2400'=>'2400', '4800'=>'4800', '9600'=>'9600', '19200'=>'19200', '38400'=>'38400',
            '57600'=>'57600', '115200'=>'115200', '230400'=>'230400');
            SelectControlCustom(_('Baudrate'), 'baudrate', $baudrate_list, $baudrate_list['38400'], 'baudrate');
            InputControlCustom(_('Source Address'), 'mac', 'mac');
            InputControlCustom(_('Max Master'), 'max_master', 'max_master', _('1~127'));
            InputControlCustom(_('Frames'), 'frames', 'frames', _('1~127'));
            echo '</div>';

            InputControlCustom(_('Device ID'), 'device_id', 'device_id', _('1~65535'));

            InputControlCustom(_('Object Name'), 'object_name', 'object_name');
            
            echo '</div>
            </div>';
            ?>
            <input type="hidden" name="table_data" value="" id="hidTD_bacnet_point">
            <input type="hidden" name="option_list_bacnet_point" value="" id="option_list_bacnet_point">
            <div id="page_bacnet_mapping" name="page_bacnet_mapping">
              <div class="cbi-section cbi-tblsection">
                <h4><?php echo _("Mappings"); ?></h4>
                <?php
                $arr = array(
                  array("name"=>"Source Object",   "data-field"=>"factor_name", "style"=>"", "descr"=>"", "ctl"=>"select"),
                  array("name"=>"Object Type",     "data-field"=>"object_type", "style"=>"", "descr"=>"", "ctl"=>"select"),
                  array("name"=>"Instance Number", "data-field"=>"object_instance", "style"=>"", "descr"=>"Unique, 0~4194303", "ctl"=>"input"),
                  array("name"=>"Object Name",     "data-field"=>"object_name", "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Description",     "data-field"=>"description", "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"COV Increment",   "data-field"=>"cov_increment", "style"=>"", "descr"=>"", "ctl"=>"input"),
                  array("name"=>"Enable",          "data-field"=>"enabled", "style"=>"", "descr"=>"", "ctl"=>"check"),
                );
                page_table_title('bacnet_point', $arr);
                ?>
                <div class="cbi-section-create">
                  <input type="button" class="cbi-button-add" name="popBox" value="<?=_('Add')?>" onclick="addData('bacnet_point')">
                  <?php conf_im_ex('Bacnet_point'); ?>
                </div>
              </div>
            </div>
            <?php
            echo $buttons; 
          ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

<?php page_im_ex('Bacnet_point', 'bacnet'); ?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("BACnet Point Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'bacnet_point';

      SelectControlCustom(_('Source Object'), $table_name.'.source_object', NULL, NULL, $table_name.'.source_object');

      $object_type = array('AI'=>'AI', 'AO'=>'AO', 'AV'=>'AV', 'BI'=>'BI', 'BO'=>'BO', 'BV'=>'BV', 'MSV'=>'MSV');
      SelectControlCustom(_('Object Type'), $table_name.'.object_type', $object_type, $object_type['AV'], $table_name.'.object_type', null, "objectTypeChange()");

      InputControlCustom(_('Instance Number'), $table_name.'.object_instance', $table_name.'.object_instance', _('0~4194303'));

      InputControlCustom(_('Object Name'), $table_name.'.object_name', $table_name.'.object_name');

      InputControlCustom(_('Description'), $table_name.'.description', $table_name.'.description');

      echo '<div id="page_cov_increment" name="page_cov_increment">';
      InputControlCustom(_('COV Increment'), $table_name.'.cov_increment', $table_name.'.cov_increment', _('Analog only (AI/AO/AV)'));
      echo '</div>';

      CheckboxControlCustom(_('Enable'), $table_name.'.enabled', $table_name.'.enabled', 'checked');
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('bacnet_point')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->

