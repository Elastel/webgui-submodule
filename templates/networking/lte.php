<div class="tab-pane" id="lte">
  <h4 class="mt-3"><?php echo _("LTE settings") ;?></h4>
  <div class="row">
    <div class="form-group col-md-6">
      <label for="code"><?php echo _("Interface") ;?></label>
      <?php
        SelectorOptions('interface', $lte_interface, null, 'cbxdhcpiface');
      ?>
    </div>
  </div>
  <div class="row">
    <div class="form-group col-md-6">
      <label for="<metric"><?php echo _("Metric") ?></label>
      <input type="text" class="form-control" id="lte_metric" name="lte_metric">
    </div>
  </div>
  <div class="row">
    <div class="form-group col-md-6">
      <label for="txtapn"><?php echo _("APN"); ?></label>
      <input type="text" id="txtapn" class="form-control" name="apn" value="" />
    </div>
  </div>

  <div class="row">
    <div class="form-group col-md-6">
      <label for="txtpin"><?php echo _("PIN"); ?></label>
      <input type="text" id="txtpin" class="form-control" name="pin" value="" />
    </div>
  </div>
  <div class="row">
    <div class="form-group col-md-6">
      <label for="cbxtype"><?php echo _("Authentication Type"); ?></label>
      <select type="select" id="auth_type" class="form-control" name="auth_type" onchange="authTypeChange()">
        <option value="pap/chap">PAP/CHAP</option>
        <option value="pap">PAP</option>
        <option value="chap">CHAP</option>
        <option value="none" selected>NONE</option>
      </select>
    </div>
  </div>
  <div class="row">
    <div id="username" class="form-group col-md-6">
      <label for="txtusername"><?php echo _("PAP/CHAP username"); ?></label>
      <input type="text" id="txtusername" class="form-control" name="username" value="" />
    </div>
  </div>

  <div class="row">
    <div id="password" class="form-group col-md-6">
      <label for="txtpassword"><?php echo _("PAP/CHAP password"); ?></label>
      <input type="text" id="txtpassword" class="form-control" name="password" value="" />
    </div>
  </div>
  <div class="row">
    <div class="form-group col-md-6">
      <div class="custom-control custom-switch">
        <input class="custom-control-input" id="data_saving_mode" type="checkbox" name="data_saving_mode" value="1">
        <label class="custom-control-label" for="data_saving_mode"><?php echo _("Enable data saving mode") ?></label>
      </div>
    </div>
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
        document.getElementById("username").style.display="inline";
        document.getElementById("password").style.display="inline";
      }
    }
</script>
