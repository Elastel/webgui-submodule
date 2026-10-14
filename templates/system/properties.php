<!-- properties tab -->
<div role="tabpanel" class="tab-pane" id="system-properties">
    <h4 class="mt-3"><?php echo _("System Properties") ;?></h4>
    <div class="cbi-section cbi-tblsection">
      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Current time"); ?></label>
        <label id="current_time" name="current_time"><?php echo $current_time; ?></label>
        <button type="button" class="btn btn-light btn-sm" id="refresh_system_time"><i class="fas fa-sync-alt"></i> <?php echo _("Refresh") ?></button>
      </div>
      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("HostName"); ?></label>
        <input type="text" class="cbi-input-text" name="hostname" id="hostname" value="<?php echo $cur_hostname; ?>" />
      </div>
      <?php 
      $tmp = timezone_identifiers_list();
      $timezoneList = array();
      foreach ($tmp as $value) {
        $timezoneList["$value"] = $value;
      }
      SelectControlCustom(_('Time zone'), 'timezones', $timezoneList, $_SESSION['timezones']); 
      ?>
    </div>
    <div class="cbi-page-actions">
      <input type="submit" class="btn btn-outline btn-primary" name="applyProperties" value="<?php echo _("Apply settings");?>" />
    </div>
</div>
