<body>
<div class="tab-pane" id="wired">
  <div class="cbi-section cbi-tblsection">
    <div class="cbi-value">
      <label class="cbi-value-title" for="cbxdhcpiface"><?php echo _("Interface") ;?></label>
      <?php SelectorOptions('interface0', $wired_interface, null, 'cbxdhcpiface', 'loadInterfaceDHCPSelect', null, 'cbi-input-select'); ?>
    </div>
    <?php
      InputControlCustom(_('Metric'), 'Metric', 'txtmetric');
      InputControlCustom(_('Mac'), 'wired_mac', 'wired_mac', null, $wired_mac);
      CheckboxControlCustom(_('Enable WAN port multiplexing into LAN port'), 'wan-multi', 'wan-multi');
    ?>

    <div class="cbi-value">
      <label class="cbi-value-title"><?php echo _("Adapter IP Address Settings"); ?></label>
      <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-light active" checked onclick="setStaticIP(false)">
          <input type="radio" name="adapter-ip" id="chkdhcp" autocomplete="off" value = '1'> <?php echo _("DHCP"); ?>
        </label>
        <label class="btn btn-light" onclick="setStaticIP(true)">
          <input type="radio" name="adapter-ip" id="chkstatic"  autocomplete="off" value = '0'> <?php echo _("Static IP"); ?>
        </label>
      </div>
    </div>

    <div name="static_ip" id="static_ip" value="1">
      <h5 class="mt-1"><?php echo _("Static IP options"); ?></h5>
      <?php
        InputControlCustom(_('IP Address'), 'StaticIP', 'txtipaddress');
        InputControlCustom(_('Subnet Mask'), 'SubnetMask', 'txtsubnetmask');
        InputControlCustom(_('Default gateway'), 'DefaultGateway', 'txtgateway');
        InputControlCustom(_('DNS Server') . ' 1', 'DNS1', 'txtdns1');
        InputControlCustom(_('DNS Server') . ' 2', 'DNS2', 'txtdns2');
      ?>
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
