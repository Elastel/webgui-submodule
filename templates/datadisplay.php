<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
          <?php echo _("Data Monitoring"); ?>
          </div>
        </div><!-- ./row -->
      </div><!-- ./card-header -->
      <div class="card-body">
          <form method="POST" action="datadisplay" role="form">
            <input type="hidden" name="table_data" value="" id="hidTD">
            <?php echo \ElastPro\Tokens\CSRF::hiddenField();?>
            <div class="cbi-section-create">
              <select id="current_rule" name="current_rule" class="cbi-input-select">
                <?php
                  if (isset($select) && is_array($select)) {
                    foreach ($select as $item) {
                      echo '<option value="' . htmlspecialchars($item) . '">' . htmlspecialchars($item) . '</option>';
                    }
                  }
                ?>
              </select>
              <input type="text" class="cbi-input-text" name="keywords" value="" placeholder="Please enter keywords" />
            </div>
            <div class="cbi-section cbi-tblsection" id="page_datadisplay" name="page_datadisplay">
              <table class="table cbi-section-table" name="table_modbus" id="table_modbus">
                <tr class="tr cbi-section-table-titles">
                <?php
                    $arr= array(
                      array("name"=>"Tag Name",             "style"=>"background-color:#f0f0f0;"),
                      array("name"=>"Value",                "style"=>"background-color:#f0f0f0;"),
                      array("name"=>"Write",                "style"=>"background-color:#f0f0f0;"),
                    );
                    $name_buf = '';
                    for ($i = 0; $i < count($arr); $i++) {
                      $name = _($arr[$i]['name']);
                      $style = strlen($arr[$i]['style']) > 0 ? "style=\"". $arr[$i]['style'] ."\"" : '';
                      $name_buf .= "<th class=\"th cbi-section-table-cell\" $style>$name</th>";
                      unset($name);
                      unset($style);
                    }
                    echo $name_buf;
                ?>
                </tr>
              </table>
            </div>
          </form>
      </div><!-- card-body -->
    </div><!-- card -->
  </div><!-- col-lg-12 -->
</div>