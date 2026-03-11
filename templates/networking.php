<?php ob_start() ?>
  <?php if (!RASPI_MONITOR_ENABLED) : ?>
      <input type="submit" class="btn btn-outline btn-primary" value="<?php echo _("Save settings"); ?>" name="savenetworksettings" />
      <input type="submit" class="btn btn-success" value="<?php echo _("Apply settings"); $msg=_("Restarting networking"); ?>" data-toggle="modal" data-target="#hostapdModal" name="applynetworksettings" />
  <?php endif ?>
  <!-- Modal -->
  <div class="modal fade" id="hostapdModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title" id="ModalLabel"><i class="fas fa-sync-alt mr-2"></i><?php echo $msg ?></div>
          </div>
          <div class="modal-body">
            <div class="col-md-12 mb-3 mt-1"><?php echo _("Executing networking service start") ?>...</div>
            <div class="progress" style="height: 20px;">
              <div class="progress-bar bg-info" role="progressbar" id="progressBar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="9"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline btn-primary" data-dismiss="modal"><?php echo _("Close"); ?></button>
          </div>
        </div>
      </div>
    </div>
<?php $buttons = ob_get_clean(); ob_end_clean() ?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
            <?php
              if ($type == 'wired') {
                echo _("Wired");
                $action = 'wired_conf';
              } else if ($type == 'lte') {
                echo _("LTE");
                $action = 'lte_conf';
              } else if ($type == 'wlan0') {
                echo _("WiFi Client");
                $action = 'wlan0_conf';
              }
            ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
        <?php $status->showMessages(); ?>
        <form role="form" action="<?php echo $action; ?>" method="POST">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField();
            if ($type == 'wired') {
              echo renderTemplate("networking/wired", $__template_data);
            } else if ($type == 'lte') {
              if ($lte_enabled == '1') { 
                echo renderTemplate("networking/lte", $__template_data);
              } 
            } else if ($type == 'wlan0') {
                echo renderTemplate("networking/wlan0", $__template_data);
            }
          ?>
        <?php echo $buttons ?>
        </form>
      </div><!-- /.card-body -->
    </div><!-- /.card -->
  </div><!-- /.col-lg-12 -->
</div><!-- /.row -->

