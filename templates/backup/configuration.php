<div class="tab-pane" id="configuration">
    <div class="cbi-section">
        <h3>Configuration</h3>
        <div class="cbi-section-descr">The following list allows you to customize the files that need to be backed up. After setting, you need to execute the Save button.</div>
        <div class="cbi-value" id="cbi-json-config-editlist">
            <?php
                $i = 0;
                foreach ($checkBoxList as $key => $value) {
                    list($name, $filePaths, $status) = $value;
                    if ( $i % 4 == 0) {
                        echo '<div class="cbi-value">';
                    }
                    
                    $checked = ($status == '1') ? 'checked' : '';
                    echo '<label class="cbi-value-title">' . $name . '</label>
                        <input type="checkbox" value="1" name="' . $key . '" id="' . $key . '" ' . $checked . '>';
                    
                    $i++;
                    if ( $i % 4 == 0) {
                        echo '</div>';
                    }
                    // CheckboxControlCustom(_($name), $key, $key);
                }
            ?>
            <div id="editlist" style="width:100%">
                <textarea id="backup_list" name="backup_list" class="cbi-input-textarea" style="width:100%" rows="15"><?php echo $backupList ?></textarea>
            </div>
        </div>
        <div class="cbi-page-actions">
            <input type="submit" class="btn btn-success" value="<?php echo _("Save"); ?>" name="<?php echo htmlspecialchars('saveBackupList', ENT_QUOTES); ?>" />
        </div>
    </div>
</div>