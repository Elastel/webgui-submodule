<?php 
  ob_start();
  if (!RASPI_MONITOR_ENABLED) :
    BtnSaveApplyCustom('saveinterfacesettings', 'applyinterfacesettings');
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
          <?php echo _("Interface Setting"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form role="form" action="interfaces_conf" enctype="multipart/form-data" method="POST">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>

          <?php if ($model != "ElastBox400") { ?>
          <div class="cbi-section">
            <h4>Serial Port Setting</h4>
            <ul class="nav nav-tabs">
              <?php if ($model == "EG500" || $model == "EG410" || $model == "EG510") { ?>
                <li role="presentation" class="nav-item"><a class="nav-link active" href="#com1" aria-controls="com1" role="tab" data-toggle="tab"><?php echo _("COM1/RS485"); ?></a></li>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#com2" aria-controls="com2" role="tab" data-toggle="tab"><?php echo _("COM2/RS232"); ?></a></li>
              <?php } else if ($model == "EC212") { ?>
                <li role="presentation" class="nav-item"><a class="nav-link active" href="#com1" aria-controls="com1" role="tab" data-toggle="tab"><?php echo _("COM1/RS485/RS232"); ?></a></li>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#com2" aria-controls="com2" role="tab" data-toggle="tab"><?php echo _("COM2/RS485/RS232"); ?></a></li>
              <?php } else { ?>
                <li role="presentation" class="nav-item"><a class="nav-link active" href="#com1" aria-controls="com1" role="tab" data-toggle="tab"><?php echo _("COM1/RS485"); ?></a></li>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#com2" aria-controls="com2" role="tab" data-toggle="tab"><?php echo _("COM2/RS485"); ?></a></li>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#com3" aria-controls="com3" role="tab" data-toggle="tab"><?php echo _("COM3/RS485/RS232"); ?></a></li>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#com4" aria-controls="com4" role="tab" data-toggle="tab"><?php echo _("COM4/RS485/RS232"); ?></a></li>
              <?php } ?>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <?php page_interface_com(1);?>
                <?php page_interface_com(2);?>
                <?php page_interface_com(3);?>
                <?php page_interface_com(4);?>
            </div><!-- /.tab-content -->
          </div>
          <?php } ?>

          <div class="cbi-section">
            <h4>Network Node Setting</h4>
            <ul class="nav nav-tabs">
              <li role="presentation" class="nav-item"><a class="nav-link active" href="#tcp1" aria-controls="tcp1" role="tab" data-toggle="tab"><?php echo _("Network Node1"); ?></a></li>
              <li role="presentation" class="nav-item"><a class="nav-link" href="#tcp2" aria-controls="tcp2" role="tab" data-toggle="tab"><?php echo _("Network Node2"); ?></a></li>
              <li role="presentation" class="nav-item"><a class="nav-link" href="#tcp3" aria-controls="tcp3" role="tab" data-toggle="tab"><?php echo _("Network Node3"); ?></a></li>
              <li role="presentation" class="nav-item"><a class="nav-link" href="#tcp4" aria-controls="tcp4" role="tab" data-toggle="tab"><?php echo _("Network Node4"); ?></a></li>
              <li role="presentation" class="nav-item"><a class="nav-link" href="#tcp5" aria-controls="tcp5" role="tab" data-toggle="tab"><?php echo _("Network Node5"); ?></a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <?php page_interface_tcp(1);?>
                <?php page_interface_tcp(2);?>
                <?php page_interface_tcp(3);?>
                <?php page_interface_tcp(4);?>
                <?php page_interface_tcp(5);?>
            </div><!-- /.tab-content -->
          </div>
          <?php echo $buttons ?>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>

