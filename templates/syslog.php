<?php
  $line_options = array(
      '100'  => '100',
      '500'  => '500',
      '2000' => '2000',
      '0'    => _('All'),
  );

  $has_system = !empty($sources);
  $has_kernel = !empty($kernelAvailable);
?>
<div class="row">
  <div class="col-lg-12">
    <div class="card">

      <div class="card-header">
        <div class="row">
          <div class="col">
            <?php echo _("System Log"); ?>
          </div>
        </div><!-- /.row -->
      </div><!-- /.card-header -->

      <div class="card-body">
        <?php $status->showMessages(); ?>

        <?php if (!$has_system && !$has_kernel) : ?>
          <div class="alert alert-warning mb-0">
            <?php echo _("No log source is available on this device."); ?>
          </div>
        <?php else : ?>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs mb-3">
          <?php if ($has_system) : ?>
          <li class="nav-item">
            <a class="nav-link active" href="#syslog-system" data-toggle="tab"><?php echo _("System Log"); ?></a>
          </li>
          <?php endif; ?>
          <?php if ($has_kernel) : ?>
          <li class="nav-item">
            <a class="nav-link<?php echo $has_system ? '' : ' active'; ?>" href="#syslog-kernel" data-toggle="tab"><?php echo _("Kernel Log"); ?></a>
          </li>
          <?php endif; ?>
        </ul>

        <div class="tab-content">

          <!-- System Log -->
          <?php if ($has_system) : ?>
          <div class="tab-pane active" id="syslog-system">
            <div class="cbi-section">
              <div class="cbi-value syslog-row">
                <label class="cbi-value-title" for="syslog_source"><?php echo _("Source"); ?></label>
                <select id="syslog_source" name="syslog_source" class="cbi-input-select syslog-control">
                  <?php foreach ($sources as $s) : ?>
                  <option value="<?php echo htmlspecialchars($s['key'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($s['label'], ENT_QUOTES); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="cbi-value syslog-row">
                <label class="cbi-value-title" for="syslog_lines"><?php echo _("Lines"); ?></label>
                <select id="syslog_lines" name="syslog_lines" class="cbi-input-select syslog-control">
                  <?php foreach ($line_options as $v => $l) : ?>
                  <option value="<?php echo htmlspecialchars($v, ENT_QUOTES); ?>"<?php echo ($v === '500') ? ' selected' : ''; ?>><?php echo htmlspecialchars($l, ENT_QUOTES); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="cbi-value syslog-row">
                <label class="cbi-value-title" for="syslog_keyword"><?php echo _("Filter"); ?></label>
                <input type="text" class="cbi-input-text syslog-control" id="syslog_keyword" name="syslog_keyword"
                       placeholder="<?php echo _("Keyword to match"); ?>"/>
              </div>
              <div class="cbi-value syslog-row">
                <label class="cbi-value-title" for="syslog_exclude"><?php echo _("Exclude"); ?></label>
                <input type="text" class="cbi-input-text syslog-control" id="syslog_exclude" name="syslog_exclude"
                       placeholder="<?php echo _("Hide lines matching"); ?>"/>
              </div>
              <div class="cbi-value syslog-row">
                <label class="cbi-value-title"><?php echo _("Options"); ?></label>
                <div class="syslog-options">
                  <label class="syslog-opt" for="syslog_regex">
                    <input type="checkbox" id="syslog_regex" value="1"/><?php echo _("Regex"); ?>
                  </label>
                  <label class="syslog-opt" for="syslog_case">
                    <input type="checkbox" id="syslog_case" value="1"/><?php echo _("Case sensitive"); ?>
                  </label>
                </div>
              </div>
              <div class="cbi-value cbi-value-last">
                <input type="button" class="btn btn-outline btn-primary" id="btn_syslog_refresh"
                       value="<?php echo _("Refresh"); ?>"/>
                <input type="button" class="btn btn-outline btn-secondary" id="btn_syslog_clear"
                       value="<?php echo _("Clear"); ?>"/>
                <input type="button" class="btn btn-outline btn-success" id="btn_syslog_download"
                       value="<?php echo _("Download"); ?>"/>
                <span class="syslog-stats" id="syslog_stats"></span>
              </div>
              <div class="cbi-value">
                <pre class="syslog-output" id="syslog_result"><?php echo _("Loading..."); ?></pre>
              </div>
            </div>
          </div><!-- /#syslog-system -->
          <?php endif; ?>

          <!-- Kernel Log -->
          <?php if ($has_kernel) : ?>
          <div class="tab-pane<?php echo $has_system ? '' : ' active'; ?>" id="syslog-kernel">
            <div class="cbi-section">
              <div class="cbi-value syslog-row">
                <label class="cbi-value-title" for="syslog_kernel_lines"><?php echo _("Lines"); ?></label>
                <select id="syslog_kernel_lines" name="syslog_kernel_lines" class="cbi-input-select syslog-control">
                  <?php foreach ($line_options as $v => $l) : ?>
                  <option value="<?php echo htmlspecialchars($v, ENT_QUOTES); ?>"<?php echo ($v === '500') ? ' selected' : ''; ?>><?php echo htmlspecialchars($l, ENT_QUOTES); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="cbi-value syslog-row">
                <label class="cbi-value-title" for="syslog_kernel_keyword"><?php echo _("Filter"); ?></label>
                <input type="text" class="cbi-input-text syslog-control" id="syslog_kernel_keyword" name="syslog_kernel_keyword"
                       placeholder="<?php echo _("Keyword to match"); ?>"/>
              </div>
              <div class="cbi-value syslog-row">
                <label class="cbi-value-title" for="syslog_kernel_exclude"><?php echo _("Exclude"); ?></label>
                <input type="text" class="cbi-input-text syslog-control" id="syslog_kernel_exclude" name="syslog_kernel_exclude"
                       placeholder="<?php echo _("Hide lines matching"); ?>"/>
              </div>
              <div class="cbi-value syslog-row">
                <label class="cbi-value-title"><?php echo _("Options"); ?></label>
                <div class="syslog-options">
                  <label class="syslog-opt" for="syslog_kernel_regex">
                    <input type="checkbox" id="syslog_kernel_regex" value="1"/><?php echo _("Regex"); ?>
                  </label>
                  <label class="syslog-opt" for="syslog_kernel_case">
                    <input type="checkbox" id="syslog_kernel_case" value="1"/><?php echo _("Case sensitive"); ?>
                  </label>
                </div>
              </div>
              <div class="cbi-value cbi-value-last">
                <input type="button" class="btn btn-outline btn-primary" id="btn_syslog_kernel_refresh"
                       value="<?php echo _("Refresh"); ?>"/>
                <input type="button" class="btn btn-outline btn-secondary" id="btn_syslog_kernel_clear"
                       value="<?php echo _("Clear"); ?>"/>
                <input type="button" class="btn btn-outline btn-success" id="btn_syslog_kernel_download"
                       value="<?php echo _("Download"); ?>"/>
                <span class="syslog-stats" id="syslog_kernel_stats"></span>
              </div>
              <div class="cbi-value">
                <pre class="syslog-output" id="syslog_kernel_result"><?php echo _("Loading..."); ?></pre>
              </div>
            </div>
          </div><!-- /#syslog-kernel -->
          <?php endif; ?>

        </div><!-- /.tab-content -->

        <?php endif; ?>
      </div><!-- /.card-body -->
    </div><!-- /.card -->
  </div><!-- /.col-lg-12 -->
</div><!-- /.row -->

<style>
  /* Field alignment: labels share one column so controls start at the same
     x position regardless of label length. */
  .syslog-row {
    display: flex;
    align-items: center;
    flex-wrap: nowrap;
  }
  .syslog-row > .cbi-value-title {
    flex: 0 0 150px;
    max-width: 150px;
    margin: 0;
    padding-right: 10px;
  }
  .syslog-row > .syslog-control {
    flex: 0 1 420px;
    max-width: 420px;
    width: 100%;
  }
  .syslog-options {
    display: flex;
    align-items: center;
    gap: 1.25rem;
  }
  .syslog-opt {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    margin: 0;
    font-weight: normal;
    cursor: pointer;
  }
  .syslog-opt input[type="checkbox"] {
    margin: 0;
  }
  .syslog-stats {
    margin-left: .75rem;
    font-size: .8125rem;
    color: #6c757d;
  }
  pre.syslog-output {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: .25rem;
    padding: .75rem 1rem;
    margin-bottom: 0;
    max-height: 30rem;
    overflow: auto;
    white-space: pre-wrap;
    word-break: break-all;
    font-family: Menlo, Consolas, monospace;
    font-size: .8125rem;
    min-height: 6rem;
  }
</style>
