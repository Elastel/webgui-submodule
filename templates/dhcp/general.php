<div class="tab-pane active" id="server-settings">
  <div class="cbi-section cbi-tblsection">
    <div class="cbi-value">
      <label class="cbi-value-title" for="cbxdhcpiface"><?php echo _("Interface"); ?></label>
      <?php SelectorOptions('interface', $interfaces, $ap_iface, 'cbxdhcpiface', 'loadInterfaceDHCPSelect', null, 'cbi-input-select'); ?>
    </div>
    <?php
      InputControlCustom(_('IP Address'), 'StaticIP', 'txtipaddress');
      InputControlCustom(_('Subnet Mask'), 'SubnetMask', 'txtsubnetmask');
      InputControlCustom(_('Mac'), 'lan_mac', 'lan_mac', null, $lan_mac);
      CheckboxControlCustom(
        _('Enable DHCP for this interface'),
        'dhcp-iface',
        'dhcp-iface',
        null,
        _('Enable this option if you want to assign IP addresses to clients on the selected interface. A static IP address is required for this option.')
      );
      InputControlCustom(_('Starting IP Address'), 'RangeStart', 'txtrangestart');
      InputControlCustom(_('Ending IP Address'), 'RangeEnd', 'txtrangeend');
    ?>

    <div class="cbi-value">
      <label class="cbi-value-title"><?php echo _("Lease Time"); ?></label>
      <input type="text" class="cbi-input-text" id="txtrangeleasetime" name="RangeLeaseTime" style="width:8rem; max-width:8rem;" />
      <label class="cbi-value-title" style="width:auto; min-width:0; max-width:none; margin-left:1.5rem;"><?php echo _("Interval"); ?></label>
      <select id="cbxrangeleasetimeunits" name="RangeLeaseTimeUnits" class="cbi-input-select" style="width:10rem; max-width:10rem;">
        <option value="m"><?php echo _("Minute(s)"); ?></option>
        <option value="h"><?php echo _("Hour(s)"); ?></option>
        <option value="d"><?php echo _("Day(s)"); ?></option>
        <option value="infinite"><?php echo _("Infinite"); ?></option>
      </select>
    </div>
    <?php
      InputControlCustom(_('DNS Server') . ' 1', 'DNS1', 'txtdns1');
      InputControlCustom(_('DNS Server') . ' 2', 'DNS2', 'txtdns2');
    ?>
  </div>

  <!-- <div class="row">
    <div class="form-group col-md-6">
      <label for="<metric"><?php echo _("Metric") ?></label>
      <input type="text" class="form-control" id="txtmetric" name="Metric">
    </div>
  </div> -->

</div><!-- /.tab-pane -->
