<?php
  $iface_options = array('' => _('Any'));
  foreach ($interfaces as $if) {
      $iface_options[$if] = $if;
  }

  $ping_counts = array('1', '2', '3', '4', '5', '10');

  // Only offer the tabs whose underlying tool actually exists on the device
  // (either directly or as a busybox applet).
  $tab_defs = array(
      'ping'       => _('Ping'),
      'traceroute' => _('Traceroute'),
      'nslookup'   => _('Nslookup'),
  );
  $available_tabs = array();
  foreach ($tab_defs as $key => $label) {
      if (!empty($tools[$key])) {
          $available_tabs[$key] = $label;
      }
  }
  $active_tab = empty($available_tabs) ? '' : key($available_tabs);
?>
<div class="row">
  <div class="col-lg-12">
    <div class="card">

      <div class="card-header">
        <div class="row">
          <div class="col">
            <?php echo _("Diagnostics"); ?>
          </div>
        </div><!-- /.row -->
      </div><!-- /.card-header -->

      <div class="card-body">
        <?php $status->showMessages(); ?>

        <?php if (empty($available_tabs)) : ?>
          <div class="alert alert-warning mb-0">
            <?php echo _("No diagnostic tools are available on this device."); ?>
          </div>
        <?php else : ?>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs mb-3">
          <?php foreach ($available_tabs as $key => $label) : ?>
          <li class="nav-item">
            <a class="nav-link<?php echo ($key === $active_tab) ? ' active' : ''; ?>"
               href="#diag-<?php echo $key; ?>" data-toggle="tab"><?php echo $label; ?></a>
          </li>
          <?php endforeach; ?>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">

          <!-- Ping -->
          <?php if (isset($available_tabs['ping'])) : ?>
          <div class="tab-pane<?php echo ($active_tab === 'ping') ? ' active' : ''; ?>" id="diag-ping">
            <div class="cbi-section">
              <div class="cbi-value diag-row">
                <label class="cbi-value-title" for="diag_ping_host"><?php echo _("Host"); ?></label>
                <input type="text" class="cbi-input-text diag-control" id="diag_ping_host" name="diag_ping_host"
                       placeholder="<?php echo _("Hostname or IP address"); ?>"/>
              </div>
              <div class="cbi-value diag-row">
                <label class="cbi-value-title" for="diag_ping_count"><?php echo _("Ping count"); ?></label>
                <select id="diag_ping_count" name="diag_ping_count" class="cbi-input-select diag-control">
                  <?php foreach ($ping_counts as $c) : ?>
                  <option value="<?php echo htmlspecialchars($c, ENT_QUOTES); ?>"<?php echo ($c === '4') ? ' selected' : ''; ?>><?php echo htmlspecialchars($c, ENT_QUOTES); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="cbi-value diag-row">
                <label class="cbi-value-title" for="diag_ping_iface"><?php echo _("Interface"); ?></label>
                <select id="diag_ping_iface" name="diag_ping_iface" class="cbi-input-select diag-control">
                  <?php foreach ($iface_options as $val => $label) : ?>
                  <option value="<?php echo htmlspecialchars($val, ENT_QUOTES); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="cbi-value cbi-value-last">
                <input type="button" class="btn btn-outline btn-primary" id="btn_diag_ping"
                       value="<?php echo _("Ping"); ?>"/>
              </div>
              <div class="cbi-value">
                <pre class="diag-output" id="diag_ping_result"><?php echo _("No data yet."); ?></pre>
              </div>
            </div>
          </div><!-- /#diag-ping -->
          <?php endif; ?>

          <!-- Traceroute -->
          <?php if (isset($available_tabs['traceroute'])) : ?>
          <div class="tab-pane<?php echo ($active_tab === 'traceroute') ? ' active' : ''; ?>" id="diag-traceroute">
            <div class="cbi-section">
              <div class="cbi-value diag-row">
                <label class="cbi-value-title" for="diag_trace_host"><?php echo _("Host"); ?></label>
                <input type="text" class="cbi-input-text diag-control" id="diag_trace_host" name="diag_trace_host"
                       placeholder="<?php echo _("Hostname or IP address"); ?>"/>
              </div>
              <div class="cbi-value diag-row">
                <label class="cbi-value-title" for="diag_trace_iface"><?php echo _("Interface"); ?></label>
                <select id="diag_trace_iface" name="diag_trace_iface" class="cbi-input-select diag-control">
                  <?php foreach ($iface_options as $val => $label) : ?>
                  <option value="<?php echo htmlspecialchars($val, ENT_QUOTES); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="cbi-value cbi-value-last">
                <input type="button" class="btn btn-outline btn-primary" id="btn_diag_traceroute"
                       value="<?php echo _("Traceroute"); ?>"/>
              </div>
              <div class="cbi-value">
                <pre class="diag-output" id="diag_trace_result"><?php echo _("No data yet."); ?></pre>
              </div>
            </div>
          </div><!-- /#diag-traceroute -->
          <?php endif; ?>

          <!-- Nslookup -->
          <?php if (isset($available_tabs['nslookup'])) : ?>
          <div class="tab-pane<?php echo ($active_tab === 'nslookup') ? ' active' : ''; ?>" id="diag-nslookup">
            <div class="cbi-section">
              <div class="cbi-value diag-row">
                <label class="cbi-value-title" for="diag_ns_host"><?php echo _("Host"); ?></label>
                <input type="text" class="cbi-input-text diag-control" id="diag_ns_host" name="diag_ns_host"
                       placeholder="<?php echo _("Hostname or IP address"); ?>"/>
              </div>
              <div class="cbi-value diag-row">
                <label class="cbi-value-title" for="diag_ns_server"><?php echo _("DNS Server"); ?></label>
                <input type="text" class="cbi-input-text diag-control" id="diag_ns_server" name="diag_ns_server"
                       placeholder="<?php echo _("Optional, defaults to system resolver"); ?>"/>
              </div>
              <div class="cbi-value cbi-value-last">
                <input type="button" class="btn btn-outline btn-primary" id="btn_diag_nslookup"
                       value="<?php echo _("Nslookup"); ?>"/>
              </div>
              <div class="cbi-value">
                <pre class="diag-output" id="diag_ns_result"><?php echo _("No data yet."); ?></pre>
              </div>
            </div>
          </div><!-- /#diag-nslookup -->
          <?php endif; ?>

        </div><!-- /.tab-content -->

        <?php endif; ?>
      </div><!-- /.card-body -->
    </div><!-- /.card -->
  </div><!-- /.col-lg-12 -->
</div><!-- /.row -->

<style>
  /* Align only the input rows: labels share one column width so every control
     starts at the same x position, regardless of label text length. The
     result area below keeps the original full-width, left-aligned layout. */
  .diag-row {
    display: flex;
    align-items: center;
    flex-wrap: nowrap;
  }
  .diag-row > .cbi-value-title {
    flex: 0 0 150px;
    max-width: 150px;
    margin: 0;
    padding-right: 10px;
  }
  .diag-row > .diag-control {
    flex: 0 1 420px;
    max-width: 420px;
    width: 100%;
  }
  pre.diag-output {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: .25rem;
    padding: .75rem 1rem;
    margin-bottom: 0;
    max-height: 26rem;
    overflow: auto;
    white-space: pre-wrap;
    word-break: break-all;
    font-family: Menlo, Consolas, monospace;
    font-size: .875rem;
    min-height: 4rem;
  }
  .diag-busy {
    opacity: .55;
  }
</style>
