<!-- logging tab -->
<div class="tab-pane fade" id="openvpnstatus">
  <div class="row">
    <div class="card-body">
      <div class="row">
        <div class="col-sm-6 align-items-stretch">
          <div class="card h-100">
              <h4 class="card-title"><?php echo _("Tunnel Status"); ?></h4>
              <div class="row ml-1">
                <div class="col-sm">
                  <div class="row mb-1">
                    <div class="col-xs-3" style="color: #858796; width: 7rem"><?php echo _("Local IP:"); ?></div>
                    <div class="col-xs-3" name="local_ip" id="local_ip"></div>
                  </div>
                  <div class="row mb-1">
                    <div class="col-xs-3" style="color: #858796; width: 7rem"><?php echo _("Netmask:"); ?></div>
                    <div class="col-xs-3" name="netmask" id="netmask"></div>
                  </div>
                  <div class="row mb-1">
                    <div class="col-xs-3" style="color: #858796; width: 7rem"><?php echo _("Remote IP:"); ?></div>
                    <div class="col-xs-3" name="remote_ip" id="remote_ip"></div>
                  </div>
                  <div class="row mb-1">
                    <div class="col-xs-3" style="color: #858796; width: 7rem"><?php echo _("Status:"); ?></div>
                    <div class="col-xs-3" name="status" id="status"><?php echo _("Disconnected") ?></div>
                  </div>
                </div>
              </div>
          </div><!-- /.card -->
        </div><!-- /.col-md-6 -->
      </div><!-- /.row -->
    </div><!-- /.card-body -->
  </div>
</div><!-- /.tab-pane -->

