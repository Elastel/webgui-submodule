<!-- language tab -->  
<div role="tabpanel" class="tab-pane" id="system-language">
    <h4 class="mt-3"><?php echo _("Language settings") ;?></h4>
    <div class="cbi-section cbi-tblsection">
      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Select a language"); ?></label>
        <select class="cbi-input-select" name="locale">
          <?php foreach ($arrLocales as $key => $label) : ?>
          <option value="<?php echo htmlspecialchars($key, ENT_QUOTES); ?>"<?php echo ($_SESSION['locale'] == $key) ? ' selected="selected"' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="cbi-page-actions">
      <input type="submit" class="btn btn-outline btn-primary" name="SaveLanguage" value="<?php echo _("Save settings"); ?>" />
      <button type="button" onClick="window.location.reload();" class="btn btn-outline btn-primary"><i class="fas fa-sync-alt"></i> <?php echo _("Refresh") ?></button>
    </div>
</div>
