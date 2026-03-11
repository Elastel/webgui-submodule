<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('saveserversettings', 'applyserversettings');
  endif;
  $msg = _('Restarting dct');
  page_progressbar($msg, _("Executing dct start"));
  $buttons = ob_get_clean(); 
  ob_end_clean();
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("Server Setting"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form role="form" action="server_conf" enctype="multipart/form-data" method="POST">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
          <div class="cbi-section cbi-tblsection">
            <h4>Server Setting</h4>
            <ul class="nav nav-tabs">
              <li role="presentation" class="nav-item"><a class="nav-link active" href="#server1" aria-controls="server1" role="tab" data-toggle="tab"><?php echo _("Server1 Setting"); ?></a></li>
              <li role="presentation" class="nav-item"><a class="nav-link" href="#server2" aria-controls="server2" role="tab" data-toggle="tab"><?php echo _("Server2 Setting"); ?></a></li>
              <li role="presentation" class="nav-item"><a class="nav-link" href="#server3" aria-controls="server3" role="tab" data-toggle="tab"><?php echo _("Server3 Setting"); ?></a></li>
              <li role="presentation" class="nav-item"><a class="nav-link" href="#server4" aria-controls="server4" role="tab" data-toggle="tab"><?php echo _("Server4 Setting"); ?></a></li>
              <li role="presentation" class="nav-item"><a class="nav-link" href="#server5" aria-controls="server5" role="tab" data-toggle="tab"><?php echo _("Server5 Setting"); ?></a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <?php page_server(1); ?>
                <?php page_server(2); ?>
                <?php page_server(3); ?>
                <?php page_server(4); ?>
                <?php page_server(5); ?>
            </div><!-- /.tab-content -->
          </div>
          <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>