<div class="tab-pane" id="lte">
  <div class="cbi-section cbi-tblsection">
    <div class="cbi-value">
      <label class="cbi-value-title" for="cbxdhcpiface"><?php echo _("Interface") ;?></label>
      <?php SelectorOptions('interface', $lte_interface, null, 'cbxdhcpiface', null, null, 'cbi-input-select'); ?>
    </div>
    <?php
      InputControlCustom(_('Metric'), 'lte_metric', 'lte_metric');
      InputControlCustom(_('APN'), 'apn', 'txtapn');
      InputControlCustom(_('PIN'), 'pin', 'txtpin');
    ?>
    <div class="cbi-value">
      <label class="cbi-value-title" for="auth_type"><?php echo _("Authentication Type"); ?></label>
      <select type="select" id="auth_type" class="cbi-input-select" name="auth_type" onchange="authTypeChange()">
        <option value="pap/chap">PAP/CHAP</option>
        <option value="pap">PAP</option>
        <option value="chap">CHAP</option>
        <option value="none" selected>NONE</option>
      </select>
    </div>
    <div class="cbi-value" id="username">
      <label class="cbi-value-title" for="txtusername"><?php echo _("PAP/CHAP username"); ?></label>
      <input type="text" id="txtusername" class="cbi-input-text" name="username" value="" />
    </div>
    <div class="cbi-value" id="password">
      <label class="cbi-value-title" for="txtpassword"><?php echo _("PAP/CHAP password"); ?></label>
      <input type="text" id="txtpassword" class="cbi-input-text" name="password" value="" />
    </div>
    <?php if (exec("uci get dct.basic.enabled") == "1") { ?>
      <?php CheckboxControlCustom(_('Enable data saving mode'), 'data_saving_mode', 'data_saving_mode'); ?>
    <?php } ?>
  </div>
</div><!-- /.tab-pane | basic tab -->
<script type="text/javascript">
    function authTypeChange(){
      var objS = document.getElementById("auth_type");
      var value = objS.value;
      if (value == "none") {
        document.getElementById("username").style.display="none";
        document.getElementById("password").style.display="none";
      } else {
        document.getElementById("username").style.display="inline-block";
        document.getElementById("password").style.display="inline-block";
      }
    }
</script>
