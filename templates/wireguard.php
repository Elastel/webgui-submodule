<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('savewgsettings', 'applywgsettings');
  endif;
  $msg = _('Restarting Wireguard');
  page_progressbar($msg, _("Executing Wireguard start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
            <?php echo _("WireGuard"); ?>
          </div>
          <div class="col">
            <button class="btn btn-light btn-icon-split btn-sm service-status float-right">
              <span class="icon text-gray-600"><i class="fas fa-circle service-status-<?php echo $serviceStatus ?>"></i></span>
              <span class="text service-status">wg <?php echo _($serviceStatus) ?></span>
            </button>
          </div>
        </div><!-- /.row -->
      </div><!-- /.card-header -->
      <div class="card-body">
      <?php $status->showMessages(); ?>
        <form role="form" action="/wireguard" enctype="multipart/form-data" method="POST">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
          <!-- Nav tabs -->
          <ul class="nav nav-tabs">
              <li class="nav-item"><a class="nav-link active" id="settingstab" href="#wgsettings" data-toggle="tab"><?php echo _("Settings"); ?></a></li>
              <li class="nav-item"><a class="nav-link" id="statustab" href="#wgstatus" data-toggle="tab"><?php echo _("Status"); ?></a></li>
          </ul>

          <!-- Tab panes -->
          <div class="tab-content">
            <?php echo renderTemplate("wg/settings", $__template_data) ?>
            <?php echo renderTemplate("wg/status", $__template_data) ?>
          </div><!-- /.tab-content -->

        <?php echo $buttons ?>
        </form>
      </div><!-- /.card-body -->
    </div><!-- /.card -->
  </div><!-- /.col-lg-12 -->
</div><!-- /.row -->

