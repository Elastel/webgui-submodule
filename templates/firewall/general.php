<div class="tab-pane active" id="general-settings">
  <div class="cbi-section cbi-tblsection">
    <div id="page_default" name="page_default">
      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Enable SYN-flood protection"); ?></label>
        <input type="checkbox" class="cbi-input-checkbox" name="synflood_protect" id="synflood_protect" value="1"/>
      </div>
      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Drop invalid packets"); ?></label>
        <input type="checkbox" class="cbi-input-checkbox" name="drop_invalid" id="drop_invalid" value="1"/>
      </div>
      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Input"); ?></label>
        <select id="input" name="input" class="cbi-input-select">
          <option value="reject">reject</option>
          <option value="drop">drop</option>
          <option value="accept" selected>accept</option>
        </select>
      </div>
      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Output"); ?></label>
        <select id="output" name="output" class="cbi-input-select">
          <option value="reject">reject</option>
          <option value="drop">drop</option>
          <option value="accept" selected>accept</option>
        </select>
      </div>
      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Forward"); ?></label>
        <select id="forward" name="forward" class="cbi-input-select">
          <option value="reject">reject</option>
          <option value="drop">drop</option>
          <option value="accept" selected>accept</option>
        </select>
      </div>
    </div>
  </div>
</div><!-- /.tab-pane -->
