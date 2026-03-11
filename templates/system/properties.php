<!-- properties tab -->  
<div role="tabpanel" class="tab-pane" id="properties">
    <h4 class="mt-3"><?php echo _("System Properties") ;?></h4>
    <div class="cbi-value">
      <label class="cbi-value-title"><?php echo _("Current time"); ?></label>
      <label id="current_time" name="current_time"><?php echo $current_time; ?></label>
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

    <input type="submit" class="btn btn-success"  name="applyProperties" value="<?php echo _("Apply settings");?>"/>
</div>

