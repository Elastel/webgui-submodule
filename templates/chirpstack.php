<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
            <?php echo _("ChirpStack"); ?>
          </div>
        </div><!-- /.row -->
      </div><!-- /.card-header -->

      <div class="card-body">
        <div class="row">
          <div class="col-sm-6 align-items-stretch">
            <div class="card h-100">
              <div class="card-body wireless">
                    <h4 class="card-title"><?php echo _("ChirpStack"); ?></h4>
                    <div class="row ml-1">
                        <div class="col-sm">
                        <div class="row mb-1">
                            <div class="col-xs-3" style="color: #858796; width: 10rem"><?php echo _("Version:"); ?></div><div class="col-xs-3"><?php echo $version[0]; ?></div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-xs-3" style="color: #858796; width: 10rem"><?php echo _("Status:"); ?></div>
                            <div class="col-xs-3">
                                <?php echo _(($run_status[0] != null) ? "<font color=\"green\">Running</font>" : "<font color=\"red\">Stop</font>"); ?>
                            </div>
                        </div>
                        <div class="row mb-1" style="margin-bottom: 1rem !important;">
                          <div class="col-xs-3" style="color: #858796; width: 10rem"><?php echo _("Region:"); ?></div>
                          <div class="col-xs-3">
                            <select name="chirpstack_region" id="chirpstack_region">
                            <?php
                            $region_list = array('as923'=>'as923', 'as923_2'=>'as923_2', 'as923_3'=>'as923_3', 'as923_4'=>'as923_4', 'au915_0'=>'au915_0', 
                            'cn470_10'=>'cn470_10', 'cn779'=>'cn779', 'eu433'=>'eu433', 'eu868'=>'eu868', 'in865'=>'in865', 'ism2400'=>'ism2400', 'kr920'=>'kr920', 
                            'ru864'=>'ru864', 'us915_0'=>'us915_0', 'us915_1'=>'us915_1');
                            if (is_array($region_list)) {
                                foreach ($region_list as $opt => $label) {
                                    $select = '';
                                    // $key = isAssoc($options) ? $opt : $label;
                                    if ($label == $cur_region) {
                                        $select = ' selected="selected"';
                                    }
                                    if ($label == $disabled) {
                                        $disabled = ' disabled';
                                    }
                                    echo '<option value="'.htmlspecialchars($opt, ENT_QUOTES).'"'.$select.$disabled.'>'. htmlspecialchars($label, ENT_QUOTES).'</option>' , PHP_EOL;
                                }
                            }
                            ?>
                            </select>
                          </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-xs-3" style="color: #858796; width: 10rem"><?php echo _("URL Entry:"); ?></div>
                            <input class="btn btn-outline btn-primary" type="submit" value="Chirpstack" onClick="window.open(window.location.protocol+'//'+window.location.host+':8080','nr');">
                        </div>
                        <form method="POST" action="chirpstack" role="form">
                          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
                          <div class="row mb-1">
                            <input class="btn btn-success" type="submit" value="<?php echo _("Restart"); ?>" name="restart" />
                          </div>
                        </form>
                        </div>
                    </div>
              </div><!-- /.card-body -->
            </div><!-- /.card -->
          </div><!-- /.col-md-6 -->
        </div><!-- /.row -->
      </div><!-- /.card-body -->
    </div><!-- /.card -->
  </div><!-- /.col-lg-12 -->
</div><!-- /.row -->
