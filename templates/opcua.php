<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savesettings', 'applysettings');
  endif;
  $msg = _('Restarting OPC UA Server');
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
          <?php echo _("OPC UA Server"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form role="form" action="opcua" enctype="multipart/form-data" method="POST">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField();
            echo '<div class="cbi-section cbi-tblsection">';

            RadioControlCustom(_('OPC UA Server'), 'enabled', 'opcua', 'enableOpcua');
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
            echo '</div>
            </div>
            </div>';
            echo $buttons;
          ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

