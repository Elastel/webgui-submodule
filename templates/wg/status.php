<!-- logging tab -->
<div class="tab-pane fade" id="wgstatus">
  <div class="row">
    <div class="card-body">
      <div class="row">
        <div class="col-sm-6 align-items-stretch">
          <div class="card h-100">
              <h4 class="card-title"><?php echo _("Tunnel Status"); ?></h4>
              <div class="row ml-1">
                <div class="col-sm">
                <?php
                exec("sudo wg", $wg_status);
                $count = count($wg_status);
                for ( $i = 0; $i < $count; $i++) {
                  $arrLine = explode(":", $wg_status[$i]);
                  if (count($arrLine) == 3) {
                    echo '<div class="row mb-1">';
                    echo '<div class="col-xs-3" style="color: #858796; width: 10rem">'. $arrLine[0] .':</div>';
                    echo '<div class="col-xs-3" name="netmask" id="netmask">'. $arrLine[1] .':'. $arrLine[2] .'</div>';
                    echo '</div>';
                  } else if (count($arrLine) == 2) {
                    echo '<div class="row mb-1">';
                    echo '<div class="col-xs-3" style="color: #858796; width: 10rem">'. $arrLine[0] .':</div>';
                    echo '<div class="col-xs-3" name="netmask" id="netmask">'. $arrLine[1] .'</div>';
                    echo '</div>';
                  } else {
                    echo '<div class="row mb-1">';
                    echo '<div class="col-xs-3" name="netmask" id="netmask">' . $wg_status[$i] . '</div>';
                    echo '</div>';
                  }
                  
                }
                ?>
                </div>
              </div>
          </div><!-- /.card -->
        </div><!-- /.col-md-6 -->
      </div><!-- /.row -->
    </div><!-- /.card-body -->
  </div>
</div><!-- /.tab-pane -->

