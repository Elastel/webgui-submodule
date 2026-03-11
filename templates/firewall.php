<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savefirewallsettings', 'applyfirewallsettings');
  endif;
  $msg = _('Restarting firewall');
  page_progressbar($msg, _("Executing firewall service start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>
<div class="row">
  <div class="col-lg-12">
    <div class="card">

      <div class="card-header">
        <div class="row">
          <div class="col">
            <?php echo _("Firewall"); ?>
          </div>
        </div><!-- /.row -->
      </div><!-- /.card-header -->

      <div class="card-body">
        <?php $status->showMessages(); ?>
        <form method="POST" action="firewall_conf" role="form">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>

          <!-- Nav tabs -->
          <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link active" href="#general-settings" data-toggle="tab"><?php echo _("General Settings"); ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#port-forwards" data-toggle="tab"><?php echo _("Port Forwards"); ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#traffic-rules" data-toggle="tab"><?php echo _("Traffic Rules") ?></a></li>
            <!-- <li class="nav-item"><a class="nav-link" href="#nat-rules" data-toggle="tab"><?php echo _("NAT Rules"); ?></a></li> -->
          </ul>

          <!-- Tab panes -->
          <div class="tab-content">
            <?php echo renderTemplate("firewall/general", $__template_data) ?>
            <?php echo renderTemplate("firewall/forwards", $__template_data) ?>
            <?php echo renderTemplate("firewall/traffic", $__template_data) ?>
            <?php //echo renderTemplate("firewall/nat", $__template_data) ?>
          </div><!-- /.tab-content -->

          <?php echo $buttons ?>
        </form>
      </div><!-- ./ card-body -->
    </div><!-- /.card -->
  </div><!-- /.col-lg-12 -->
</div><!-- /.row -->
