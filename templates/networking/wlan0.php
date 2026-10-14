<body>
<div class="tab-pane" id="wlan0">
  <div class="cbi-section cbi-tblsection">
    <div class="cbi-value">
      <label class="cbi-value-title" for="cbxdhcpiface"><?php echo _("Interface") ;?></label>
      <?php SelectorOptions('wlan0_interface', $wlan0_interface, null, 'cbxdhcpiface', null, null, 'cbi-input-select'); ?>
    </div>
    <?php InputControlCustom(_('Metric'), 'wlan0_Metric', 'wlan0_txtmetric'); ?>
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
