<body>
<div class="tab-pane" id="wired">
  <h4 class="mt-3"><?php echo _("Wired settings"); ?></h4>
  <div class="row">
    <div class="form-group col-md-6">
      <label for="code"><?php echo _("Interface") ;?></label>
        <?php SelectorOptions('interface0', $wired_interface, null, 'cbxdhcpiface', 'loadInterfaceDHCPSelect', null); ?>
    </div>
  </div>
  
  <div class="row">
    <div class="form-group col-md-6">
      <label for="metric"><?php echo _("Metric") ?></label>
      <input type="text" class="form-control" id="txtmetric" name="Metric">
    </div>
  </div>

  <div class="row">
    <div class="form-group col-md-6">
      <label for="wired_mac"><?php echo _("Mac") ?></label>
      <input type="text" class="form-control" id="wired_mac" name="wired_mac" value=<?php echo $wired_mac ?>>
    </div>
  </div>

  <div class="row">
    <div class="form-group col-md-6">
      <div class="input-group">
        <div class="custom-control custom-switch">
          <input class="custom-control-input" id="wan-multi" type="checkbox" name="wan-multi" value="1">
          <label class="custom-control-label" for="wan-multi"><?php echo _("Enable WAN port multiplexing into LAN port") ?></label>
        </div>
      </div>
     </div>
  </div>

  <h5 class="mt-1"><?php echo _("Adapter IP Address Settings"); ?></h5>
  <div class="row">
    <div class="form-group col-md-6">
      <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-light active" checked onclick="setStaticIP(false)">
          <input type="radio" name="adapter-ip" id="chkdhcp" autocomplete="off" value = '1'> <?php echo _("DHCP"); ?>
        </label>
        <label class="btn btn-light" onclick="setStaticIP(true)">
          <input type="radio" name="adapter-ip" id="chkstatic"  autocomplete="off" value = '0'> <?php echo _("Static IP"); ?>
        </label>
      </div>
    </div>
  </div>

  <div name="static_ip" id="static_ip" value="1">
    <h5 class="mt-1">Static IP options</h5>
    <div class="row">
      <div class="form-group col-md-6">
        <label for="code"><?php echo _("IP Address"); ?></label>
        <input type="text" class="form-control" id="txtipaddress" name="StaticIP" />
      </div>
    </div>

    <div class="row">
      <div class="form-group col-md-6">
        <label for="code"><?php echo _("Subnet Mask"); ?></label>
        <input type="text" class="form-control" id="txtsubnetmask" name="SubnetMask" />
      </div>
    </div>

    <div class="row">
      <div class="form-group col-md-6">
        <label for="code"><?php echo _("Default gateway"); ?></label>
        <input type="text" class="form-control" id="txtgateway" name="DefaultGateway" />
      </div>
    </div>

    <div class="row">
      <div class="form-group col-md-6">
        <label for="code"><?php echo _("DNS Server"); ?> 1</label>
        <input type="text" class="form-control" id="txtdns1" name="DNS1" />
      </div>
    </div>

    <div class="row">
      <div class="form-group col-md-6">
        <label for="code"><?php echo _("DNS Server"); ?> 2</label>
        <input type="text" class="form-control" id="txtdns2" name="DNS2" />
      </div>
    </div>
  </div>
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
