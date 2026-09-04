<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          Time Settings
          </div>
        </div><!-- /.row -->
      </div><!-- /.card-header -->
      <div class="card-body">
          <?php $status->showMessages(); ?>
          <form role="form" action="time_setting" method="POST">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
            <div class="cbi-section cbi-tblsection">
              <div class="cbi-value">
                <label class="cbi-value-title">Current System Time</label>
                <label id="current_system_time" name="current_system_time"><?php echo $system_time; ?></label>
                <button type="button" class="btn btn-light btn-sm" onclick="window.location.reload()"><i class="fas fa-sync-alt"></i> Refresh</button>
              </div>
              <div class="cbi-value">
                <label class="cbi-value-title">Current RTC Time</label>
                <label id="current_rtc_time" name="current_rtc_time"><?php echo $rtc_time; ?></label>
              </div>
              <div class="cbi-value">
                <label class="cbi-value-title">Set System Time</label>
                <input type="text" class="cbi-input-text" name="system_time" id="system_time"
                  value="<?php echo $system_time; ?>" placeholder="YYYY-MM-DD HH:MM:SS" />
                <label class="cbi-value-description">YYYY-MM-DD HH:MM:SS</label>
              </div>
            </div>
            <div class="cbi-page-actions">
              <input type="submit" class="btn btn-success" name="applyTimeSetting" value="Apply settings" />
            </div>
          </form>
      </div><!-- /.card-body -->
      <div class="card-footer"></div>
    </div><!-- /.card -->
  </div><!-- /.col-lg-12 -->
</div><!-- /.row -->
