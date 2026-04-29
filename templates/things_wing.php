<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
            <?php echo _("ThingsWing"); ?>
          </div>
        </div><!-- /.row -->
      </div><!-- /.card-header -->
      <div class="card-body">
        <?php $status->showMessages(); ?>
        <div class="alert alert-info alert-dismissable" id="install_notice" style="display: none;">
          ThingsWing is being installed, please do not leave this page ...
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        </div>
        <div class="row">
          <div class="col-sm-6 align-items-stretch">
            <div class="card h-100">
              <div class="card-body wireless">
                <h4 class="card-title"><?php echo _("ThingsWing"); ?></h4>
                <form method="POST" action="things_wing" role="form">
                  <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
                  <div class="row ml-1">
                      <div class="col-sm">
                        <div class="row mb-1">
                            <div class="col-xs-3" style="color: #858796; width: 10rem"><?php echo _("Version"); ?>:</div><div class="col-xs-3"><?php echo $version; ?></div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-xs-3" style="color: #858796; width: 10rem"><?php echo _("Status"); ?>:</div>
                            <div class="col-xs-3">
                                <?php 
                                if ($enable) {
                                  echo _(($run_status[0] != null) ? "<font color=\"green\">Running</font>" : "<font color=\"red\">Stop</font>");
                                } else {
                                  echo _("<font color=\"red\">Uninstall</font>");
                                  echo _("&nbsp;&nbsp;<input class=\"btn btn-success\" type=\"submit\" value=\"Install\" name=\"install\" 
                                        onclick=\"document.getElementById('install_notice').style.display = 'block'\"/>");
                                }
                                ?>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-xs-3" style="color: #858796; width: 10rem"><?php echo _("Use SN"); ?>:</div><div class="col-xs-3"><?php echo $use_sn; ?></div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-xs-3" style="color: #858796; width: 10rem"><?php echo _("Auth Code"); ?>:</div><div class="col-xs-3"><?php echo $auth_code; ?></div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-xs-3" style="color: #858796; width: 10rem"><?php echo _("URL Entry"); ?>:</div>
                            <input class="btn btn-outline btn-primary" type="submit" value="ThingsWing" onClick="window.open('https://device.thingswing.com','nr');">
                        </div>
                        </br>
                        <div class="row mb-1">
                          <input class="btn btn-success" type="submit" value="<?php echo _("Restart"); ?>" name="restart" />
                          &nbsp;&nbsp;
                          <input class="btn btn-danger" type="submit" value="<?php echo _("Stop"); ?>" name="stop" />
                          &nbsp;&nbsp;
                          <?php 
                            if ($start_enable) {
                              echo '<input class="btn btn-outline-secondary" type="submit" value="' . _("Disable") . '" name="disable" />';
                            } else {
                              echo '<input class="btn btn-outline-primary" type="submit" value="' . _("Enable") . '" name="enable" />';
                            }
                          ?>
                        </div>
                      </div>
                  </div>
                </form>
              </div><!-- /.card-body -->
            </div><!-- /.card -->
          </div><!-- /.col-md-6 -->
        </div><!-- /.row -->
      </div><!-- /.card-body -->
    </div><!-- /.card -->
  </div><!-- /.col-lg-12 -->
</div><!-- /.row -->
