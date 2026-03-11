<?php 
  $msg = _('Configuration restore');
  page_progressbar($msg, _("System reboot..."));
?>
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
	        <div class="col">
						<?php echo _("Backup/Restore"); ?>
          </div>
        </div><!-- /.row -->
      </div><!-- /.card-header -->
      <div class="card-body">
        <form role="form" action="backup_restore" enctype="multipart/form-data" method="POST">
        <?php echo \ElastPro\Tokens\CSRF::hiddenField();; ?>
          <!-- Nav tabs -->
          <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link active" href="#actions" data-toggle="tab"><?php echo _("Actions"); ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#configuration" data-toggle="tab"><?php echo _("Configuration") ?></a></li>
          </ul>

          <!-- Tab panes -->
          <div class="tab-content">
            <?php echo renderTemplate("backup/actions", $__template_data) ?>
            <?php echo renderTemplate("backup/configuration", $__template_data) ?>
          </div><!-- /.tab-content -->
        </form>
      </div><!-- /.card-body -->
      <div class="card-footer"></div>
    </div><!-- /.card -->
  </div><!-- /.col-lg-12 -->
</div><!-- /.row -->
