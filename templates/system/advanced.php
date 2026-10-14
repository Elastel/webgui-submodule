<!-- advanced tab -->
<div role="tabpanel" class="tab-pane" id="system-advanced">
  <h4 class="mt-3"><?php echo _("Advanced settings") ;?></h4>
    <?php if (!RASPI_MONITOR_ENABLED) : ?>
      <div class="cbi-section cbi-tblsection">
        <?php
          InputControlCustom(_('Web server port'), 'serverPort', 'serverPort', null, $serverPort);
          InputControlCustom(_('Web server bind address'), 'serverBind', 'serverBind', null, $serverBind);
        ?>
      </div>
      <div class="cbi-page-actions">
        <input type="submit" class="btn btn-outline btn-primary" name="SaveServerSettings" value="<?php echo _("Save settings"); ?>" />
        <input type="submit" class="btn btn-warning" name="RestartLighttpd" value="<?php echo _("Restart lighttpd"); ?>" />
      </div>
    <?php endif ?>
</div>
