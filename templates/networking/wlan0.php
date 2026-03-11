<body>
<div class="tab-pane" id="wlan0">
  <h4 class="mt-3"><?php echo _("WiFi Client settings"); ?></h4>
  <div class="row">
    <div class="form-group col-md-6">
      <label for="code"><?php echo _("Interface") ;?></label>
        <?php SelectorOptions('wlan0_interface', $wlan0_interface, null, 'cbxdhcpiface'); ?>
    </div>
  </div>
  
  <div class="row">
    <div class="form-group col-md-6">
      <label for="code"><?php echo _("Metric") ?></label>
      <input type="text" class="form-control" id="wlan0_txtmetric" name="wlan0_Metric">
    </div>
  </div>
<!--
  <div class="row">
    <div class="form-group col-md-6">
      <label for="code"><?php echo _("Mac") ?></label>
      <input type="text" class="form-control" id="wlan0_mac" name="wlan0_mac" value=<?php echo $wlan0_mac ?>>
    </div>
  </div>
 
  <h5 class="mt-1"><?php echo _("Adapter IP Address Settings"); ?></h5>
  <div class="row">
    <div class="form-group col-md-6">
      <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-light active" checked onclick="setStaticIP(false)">
          <input type="radio" name="wlan0_adapter-ip" id="wlan0_chkdhcp" autocomplete="off" value = '1'> <?php echo _("DHCP"); ?>
        </label>
        <label class="btn btn-light" onclick="setStaticIP(true)">
          <input type="radio" name="wlan0_adapter-ip" id="wlan0_chkstatic"  autocomplete="off" value = '0'> <?php echo _("Static IP"); ?>
        </label>
      </div>
    </div>
  </div>

  <div name="static_ip" id="static_ip" value="1">
    <h5 class="mt-1">Static IP options</h5>
    <div class="row">
      <div class="form-group col-md-6">
        <label for="code"><?php echo _("IP Address"); ?></label>
        <input type="text" class="form-control" id="wlan0_txtipaddress" name="wlan0_StaticIP" />
      </div>
    </div>

    <div class="row">
      <div class="form-group col-md-6">
        <label for="code"><?php echo _("Subnet Mask"); ?></label>
        <input type="text" class="form-control" id="wlan0_txtsubnetmask" name="wlan0_SubnetMask" />
      </div>
    </div>

    <div class="row">
      <div class="form-group col-md-6">
        <label for="code"><?php echo _("Default gateway"); ?></label>
        <input type="text" class="form-control" id="wlan0_txtgateway" name="wlan0_DefaultGateway" />
      </div>
    </div>

    <div class="row">
      <div class="form-group col-md-6">
        <label for="code"><?php echo _("DNS Server"); ?> 1</label>
        <input type="text" class="form-control" id="wlan0_txtdns1" name="wlan0_DNS1" />
      </div>
    </div>

    <div class="row">
      <div class="form-group col-md-6">
        <label for="code"><?php echo _("DNS Server"); ?> 2</label>
        <input type="text" class="form-control" id="wlan0_txtdns2" name="wlan0_DNS2" />
      </div>
    </div>
  </div> -->
</div><!-- /.tab-pane | advanded tab -->
</body>
<script type="text/javascript">
  function setStaticIP(state) {
      if (state) {
        $('#static_ip').show(); 
      } else {
        $('#static_ip').hide();
      }
  }
</script>
