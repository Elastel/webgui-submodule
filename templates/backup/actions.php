<style>
textarea {
  width: 100%;
  font-size: 1.5rem;
	/* font-size-adjust: 0.35; */
	overflow: auto;
	margin-bottom: 0.5em;
	padding: 8.5px;
	cursor: auto;
	white-space: pre-wrap;
	color: #eee;
	outline: 0;
	background-color: #101010;
	box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.16), 0 0 2px 0 rgba(0, 0, 0, 0.12);
}
</style>

<div class="tab-pane active" id="actions">
    <div class="cbi-section">
        <h3>Backup</h3>
        <div class="cbi-section-descr">Click "Generate archive" to download a tar archive of the current configuration files.</div>
        </br>
        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Download backup") ?></label>
            <input type="submit" class="btn btn-success" value="<?php echo _("Generate archive"); ?> " name="download_backup" onclick="downloadBackup()">
        </div>
    </div>
    <div class="cbi-section">
        <h3>Restore</h3>
        <div class="cbi-section-descr">To restore configuration files, you can upload a previously generated backup archive here.</div>
        </br>
        <div class="cbi-value">
          <input hidden="hidden" name="page_im_ex_name" id="page_im_ex_name" value="0">
          <label class="cbi-value-title"><?php echo _("Restore backup"); ?></label>
          <label for="upload" class="cbi-file-lable\">
            <input type="file" name="upload_file" id="upload_file">
            <input type="submit" value="Upload" name="upload">
          </label>
        </div>
    </div>

    <div style="display:<?php echo strlen($upload_backup_list) > 0 ? "block" : "none" ?>">
      <div style="position: fixed; top: 10%; background-color: white; padding: 10px; border: 1px solid rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 10px; z-index: 1000;">
        <h4>Apply backup?</h4>
        <p>The uploaded backup archive appears to be valid and contains the files listed below. Press "Continue" to restore the backup and reboot, or "Cancel" to abort the operation.</p>
        <textarea rows="10"><?php echo $upload_backup_list; ?></textarea>
        <div class="right">
          <button class="btn btn-outline btn-primary"><?php echo _("Cancel") ?></button> 
          <button class="btn btn-success" onclick="actionBackupFile()"><?php echo _("Continue"); ?></button>
        </div>
      </div>
      <div style="position: fixed; top: 0px; left: 0px; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 999;"></div>
    </div>
</div><!-- /.tab-pane -->
