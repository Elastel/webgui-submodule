<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('saveopcuasettings', 'applyopcuasettings');
  endif;
  $msg = _('Restarting OPCUA Server');
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
          OPCUA <?php echo _("Server"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form method="POST" action="opcua" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField();
            echo '<div class="cbi-section cbi-tblsection">';

            RadioControlCustom('OPCUA '._('Server'), 'opcua_enabled', 'opcua', 'enableOpcua');
            echo '<div id="page_opcua" name="page_opcua">';

            InputControlCustom(_('Port'), 'port', 'port', _('1~65535'));

            CheckboxControlCustom(_('Anonymous'), 'anonymous', 'anonymous', null, null, 'anonymousCheck(this)');

            echo '<div id="page_anonymous" name="page_anonymous">';
            InputControlCustom(_('Username'), 'username', 'username');
            InputControlCustom(_('Password'), 'password', 'password');
            echo '</div>';

            InputControlCustom(_('Maximum Historical Value'), 'max_value', 'max_value', _('Minimum is 1'));

            CheckboxControlCustom(_('Enable Database'), 'enable_database', 'enable_database');

            $policy_list = [_('None'), 'basic128', 'basic256', 'basic256sha256'];
            SelectControlCustom(_('Security Policy'), 'security_policy', $policy_list, $policy_list[0], 'security_policy', null, 'securityChange(this)');

            echo '<div id="page_security" name="page_security">';
            InputControlCustom(_('URI'), 'uri', 'uri', _('If left blank, it will be automatically filled in'));

            UploadFileControlCustom(_('Certificate'), 'cert_btn', 'cert_text', 'certificate', 'certificate', 'certChange()');

            UploadFileControlCustom(_('Private Key'), 'key_btn', 'key_text', 'private_key', 'private_key', 'keyChange()');

            UploadFileMultipleControlCustom(_('Trust Client Certificate'), 'trust_btn', 'trust_text', 'trust_crt[]', 'trust_crt', 'trustChange()');
            echo '</div>'
          ?>
          <?php if(getTarget() == '4logit') {?>
          </br></br>
          <h4><?php echo _("Custom nodes"); ?></h4>
          <input type="hidden" name="table_data" value="" id="hidTD_opcuaserv">
          <input type="hidden" name="option_list_opcuaserv" value="" id="option_list_opcuaserv">
          <div class="cbi-section cbi-tblsection" id="page_opcua_server" name="page_opcua_server">
            <table class="table cbi-section-table" name="table_opcua_server" id="table_opcua_server">
              <?php
              $arr= array(
                array("name"=>"Order",        "data-field" => "order", "style"=>"", "descr"=>"", "ctl"=>"input"),
                array("name"=>"Tag Name",        "data-field" => "factor_name", "style"=>"", "descr"=>"", "ctl"=>"input"),
                array("name"=>"Data Type",        "data-field" => "data_type", "style"=>"", "descr"=>"", "ctl"=>"select"),
                array("name"=>"Enable",               "data-field" => "enable", "style"=>"", "descr"=>"", "ctl"=>"check"),
              );
              page_table_title('opcuaserv', $arr);
              ?>
            </table>
          </div>
          <?php
          }
          echo '</div>
          </div>';
          echo $buttons;
          ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>
<?php if(getTarget() == '4logit') {?>
<?php page_im_ex('opcuaserv');?>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto; height:50%">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("Custom Nodes Setting"); ?></h4>
  <div class="cbi-section">
    <?php
      $table_name = 'opcuaserv';

      InputControlCustom(_('Order'), $table_name.'.order', $table_name.'.order');

      InputControlCustom(_('Factor Name'), $table_name.'.factor_name', $table_name.'.factor_name');
      
      $datatype_list = ['bool' => 'bool', 'int' => 'int', 
                          'float' => 'float', 'string' => 'string'];
      SelectControlCustom(_('Data Type'), $table_name.'.opcuaserv_data_type', $datatype_list, $datatype_list['float'], $table_name.'.opcuaserv_data_type');

      CheckboxControlCustom(_('Enable'), $table_name.'.enabled', $table_name.'.enabled', 'checked');
    ?>
  </div>

  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveData('opcuaserv')"><?php echo _("Save"); ?></button>
  </div>
</div><!-- popBox -->
<?php }?>

