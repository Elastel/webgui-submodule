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
            <h4><?php echo _("Serial Port Setting"); ?></h4>
            <ul class="nav nav-tabs">
              <?php
              $com_count = 4;
              $com_list = array(
                  1 => _("RS485"),
                  2 => _("RS485"),
                  3 => _("RS485/RS232"),
                  4 => _("RS485/RS232")
              );
              switch ($model) {
                case "EG500":
                case "EG410":
                case "EG510":
                    $com_count = 2;
                    $com_list = array(
                        1 => _("RS485"),
                        2 => _("RS232")
                    );
                    break;
                case "EC212":
                    $com_count = 2;
                    $com_list = array(
                        1 => _("RS485/RS232"),
                        2 => _("RS485/RS232")
                    );
                    break;
                case "EG600":
                  {
                    switch ($target) {
                        case "EG600-MG":
                            $com_count = 4;
                            $com_list = array(
                                1 => _("RS485"),
                                2 => _("RS485"),
                                3 => _("RS485"),
                                4 => _("RS232")
                            );
                            break;
                        case "EG600-MU":
                            $com_count = 10;
                            $com_list = array(
                                1 => _("RS485"),
                                2 => _("RS485"),
                                3 => _("RS485"),
                                4 => _("RS485"),
                                5 => _("RS485"),
                                6 => _("RS485"),
                                7 => _("RS485"),
                                8 => _("RS485"),
                                9 => _("RS232"),
                                10 => _("RS232")
                            );
                            break;
                        default:
                            $com_count = 4;
                            $com_list = array(
                                1 => _("RS485"),
                                2 => _("RS485"),
                                3 => _("RS485"),
                                4 => _("RS232")
                            );
                            break;
                    }

                    break;
                  }
              }
              for ($i = 1; $i <= $com_count; $i++) {
                  echo '<li role="presentation" class="nav-item"><a class="nav-link '. ($i == 1 ? "active" : "") .'" href="#com' . $i . '" aria-controls="com' . $i . '" role="tab" data-toggle="tab">' . _("COM") . $i . '/' .$com_list[$i] .'</a></li>';
              }
              ?>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <?php
                for ($i = 1; $i <= $com_count; $i++) {
                    page_interface_com($i);
                }
                ?>
            </div><!-- /.tab-content -->
          </div>
          <?php } ?>

          <div class="cbi-section">
            <h4><?php echo _("Network Node Setting"); ?></h4>
            <ul class="nav nav-tabs">
              <li role="presentation" class="nav-item"><a class="nav-link active" href="#tcp1" aria-controls="tcp1" role="tab" data-toggle="tab"><?php echo _("Network Node")."1"; ?></a></li>
              <li role="presentation" class="nav-item"><a class="nav-link" href="#tcp2" aria-controls="tcp2" role="tab" data-toggle="tab"><?php echo _("Network Node")."2"; ?></a></li>
              <li role="presentation" class="nav-item"><a class="nav-link" href="#tcp3" aria-controls="tcp3" role="tab" data-toggle="tab"><?php echo _("Network Node")."3"; ?></a></li>
              <li role="presentation" class="nav-item"><a class="nav-link" href="#tcp4" aria-controls="tcp4" role="tab" data-toggle="tab"><?php echo _("Network Node")."4"; ?></a></li>
              <li role="presentation" class="nav-item"><a class="nav-link" href="#tcp5" aria-controls="tcp5" role="tab" data-toggle="tab"><?php echo _("Network Node")."5"; ?></a></li>
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

