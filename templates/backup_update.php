<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
	        <div class="col">
						<?php echo _("Update/Restore"); ?>
          </div>
        </div><!-- /.row -->
      </div><!-- /.card-header -->
      <div class="card-body">
        <form role="form" action="backup_update" enctype="multipart/form-data" method="POST">
          <div style="display: none;" id="page_progress" name="page_progress">
            <label id="progress_info" name="progress_info"><?php echo _("Please do not power off or operate the page, updating in progress...");?></label>
            <div class="progress" style="height: 2rem">
              <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
            </div>
          </div>
          <div class="card-body">
            <h5><?php echo _("Status") ;?></h5>
            <?php 
              echo \ElastPro\Tokens\CSRF::hiddenField();;
              LabelControlCustom(_("Version").':', 'cur_version', 'cur_version', RASPI_VERSION);
              $cmd_get_local_node = "cd /var/www/html; git for-each-ref --format='%(objectname)' refs/heads/$(git rev-parse --abbrev-ref HEAD)";
              $cmd_get_remote_node = "cd /var/www/html; git for-each-ref --format='%(objectname)' refs/remotes/origin/$(git rev-parse --abbrev-ref HEAD)";
              exec($cmd_get_local_node, $cur_node);
              exec($cmd_get_remote_node, $new_node);
            ?>
            <div class="cbi-value">
              <label class="cbi-value-title"><?php echo _("Current node");?>:</label>
              <label id="cur_node" name="cur_node"><?php echo _(empty($cur_node[0]) ? "-" : $cur_node[0]);?></label>
            </div>
            <div class="cbi-value">
              <label class="cbi-value-title"><?php echo _("Online newest node");?>:</label>
              <label id="new_node" name="new_node"><?php echo _(empty($new_node[0]) ? "-" : $new_node[0]);?></label>
              <button class="btn rounded-right node_online_update" type="button"><i class="fas fa-sync"></i></button>
            </div>
            <div class="cbi-value">
              <label class="cbi-value-title"><?php echo _("Node update"); ?></label>
              <input type="button" class="cbi-button-add" name="update_node" id="update_node" value="<?=_('Perform update')?>" <?php if ($cur_node[0] == $new_node[0]) {echo 'disabled="disabled" style="background-color:grey"';}?>>
            </div>
            <!-- <h5><?php echo _("Backup") ;?></h5>
            <div class="cbi-value">
              <label class="cbi-value-title"><?php echo _("Download backup"); ?></label>
              <input type="button" class="cbi-button-add" name="download_backup" id="download_backup" value="Generate archive">
            </div> -->
            <h5><?php echo _("Restore") ;?></h5>
            <div class="cbi-value">
              <label class="cbi-value-title"><?php echo _("Reset configs"); ?>:</label>
              <input type="button" class="cbi-button-add" name="reset_configs" id="reset_configs" value="<?=_('Perform reset')?>">
            </div>
            <!-- <div class="cbi-value">
              <label class="cbi-value-title"><?php echo _("Restore backup"); ?></label>
              <label for="upload" class="cbi-file-lable">
                <input type="file" name="upload_file" id="upload_file">
                <input type="submit" value="Upload" name="restore_backup" data-toggle="modal" data-target="#hostapdModal">
              </label>
            </div> -->
          </div>
        </form>
      </div><!-- /.card-body -->
      <div class="card-footer"></div>
    </div><!-- /.card -->
  </div><!-- /.col-lg-12 -->
</div><!-- /.row -->
