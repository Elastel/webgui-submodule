<?php ob_start() ?>
  <?php if (!RASPI_MONITOR_ENABLED) : ?>
    <div class="cbi-page-actions">
      <input type="submit" class="btn btn-outline btn-primary" name="UpdateAdminPassword" value="<?php echo _("Save settings"); ?>" />
      <input type="submit" class="btn btn-warning" name="logout" value="<?php echo _("Logout") ?>" onclick="disableValidation(this.form)"/>
    </div>
  <?php endif ?>
<?php $buttons = ob_get_clean(); ob_end_clean() ?>
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
	        <div class="col">
						<?php echo _("Authentication"); ?>
          </div>
        </div><!-- /.row -->
      </div><!-- /.card-header -->
      <div class="card-body">
        <?php $status->showMessages(); ?>
        <h4><?php echo _("Authentication settings for $username") ;?></h4>
        <form role="form" action="auth_conf" method="POST">
          <?php echo \ElastPro\Tokens\CSRF::hiddenField(); ?>
          <div class="row">
            <div class="mb-3 col-md-6">
              <div class="mb-2"><?php echo _("Old password"); ?></div>
              <div class="input-group has-validation">
                <input type="password" class="form-control" name="oldpass" />
                <div class="input-group-text js-toggle-password" data-bs-target="[name=oldpass]" data-toggle-with="fas fa-eye-slash"><i class="fas fa-eye mx-2"></i></div>
                <div class="invalid-feedback">
                  <?php echo _("Please enter your old password."); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col-md-6">
              <div class="mb-2"><?php echo _("New password"); ?></div>
              <div class="input-group has-validation">
                <input type="password" class="form-control" name="newpass" />
                <div class="input-group-text js-toggle-password" data-bs-target="[name=newpass]" data-toggle-with="fas fa-eye-slash"><i class="fas fa-eye mx-2"></i></div>
                <div class="invalid-feedback">
                  <?php echo _("Please enter a new password."); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col-md-6">
              <div class="mb-2"><?php echo _("Repeat new password"); ?></div>
              <div class="input-group has-validation">
                <input type="password" class="form-control" name="newpassagain" />
                <div class="input-group-text js-toggle-password" data-bs-target="[name=newpassagain]" data-toggle-with="fas fa-eye-slash"><i class="fas fa-eye mx-2"></i></div>
                <div class="invalid-feedback">
                  <?php echo _("Please re-enter your new password."); ?>
                </div>
              </div>
            </div>
          </div>
          <?php echo $buttons ?>
          <?php if ($username == 'superadmin') : ?>
          <input type="hidden" name="table_data" value="" id="hidTD">
          <div class="cbi-section cbi-tblsection" id="page_auth" name="page_auth">
            <table class="table cbi-section-table" style="table-layout: auto" name="table_auth" id="table_auth">
              <tr class="tr cbi-section-table-titles">
                <th class="th cbi-section-table-cell"><?php echo _("Username"); ?></th>
                <th class="th cbi-section-table-cell" style="display:none"><?php echo _("Password"); ?></th>
                <th class="th cbi-section-table-cell"><?php echo _("Purview"); ?></th>
                <th class="th cbi-section-table-cell cbi-section-actions"></th>
                <th class="th cbi-section-table-cell cbi-section-actions"></th>
              </tr>
              <tr class="tr cbi-section-table-descr">
                <th class="th cbi-section-table-cell" ></th>
                <th class="th cbi-section-table-cell" ></th>
                <th class="th cbi-section-table-cell" ></th>
                <th class="th cbi-section-table-cell cbi-section-actions"></th>
              </tr>
              <?php
                $usernameList = array();
                foreach ($config as $key => $value) {
                  if (is_array($value)) {
                      if ($value['admin_user'] != 'admin' && $value['admin_user'] != 'superadmin') {
                        echo '<tbody>
                        <tr  class="tr cbi-section-table-descr">
                        <td style="text-align:center" name="user">'.$value['admin_user'].'</td>
                        <td style="display:none" name="password">'.$value['admin_pass'].'</td>
                        <td style="text-align:center" name="purview">'.$value['purview'].'</td>
                        <td style="width:10rem"><a href="javascript:void(0);" onclick="editDataAuth(this);" >Edit</a></td>
                        <td style="width:10rem"><a href="javascript:void(0);" onclick="delDataAuth(this);" >Del</a></td>
                        </tr>
                        </tbody>';
                      }
                      array_push($usernameList, $value['admin_user']);
                  }
                }
                $str = json_encode($usernameList);
              ?>
              <input type="hidden" name="username_list" id="username_list" value='<?php echo $str; ?>' id="hidTD">
            </table>
            <div class="cbi-section-create">
              <input type="button" class="cbi-button-add" name="popBox" value="Add" onclick="addDataAuth(); updateAllGroupCounts();">
            </div>
          </div>
          <div class="cbi-page-actions">
            <input type="submit" class="btn btn-outline btn-primary" value="<?php echo _("Save settings"); ?>" name="UpdateAdminSettings" />
          </div>
          <?php endif; ?>
        </form>
      </div><!-- /.card-body -->
      <div class="card-footer"></div>
    </div><!-- /.card -->
  </div><!-- /.col-lg-12 -->
</div><!-- /.row -->

<?php if ($username == 'superadmin') : ?>
<style>
  .purview-toolbar { display: flex; align-items: center; gap: 8px; margin: 24px 0 10px; }
  .purview-toolbar span { font-weight: bold; margin-right: auto; }
  .purview-group { border: 1px solid #ddd; border-radius: 4px; margin-bottom: 6px; }
  .purview-group-header { display: flex; align-items: center; padding: 6px 10px; background: #f5f5f5; cursor: pointer; user-select: none; }
  .purview-group-header:hover { background: #ececec; }
  .purview-group-title { font-weight: bold; flex: 1; }
  .purview-group-count { color: #666; font-size: 0.85rem; margin-right: 10px; }
  .purview-group-actions button { margin-left: 4px; font-size: 0.8rem; padding: 2px 8px; }
  .purview-group-toggle { margin-left: 8px; color: #666; }
  .purview-group-body { display: none; padding: 4px 10px; }
  .purview-subgroup { margin: 6px 0 2px 12px; border-left: 2px solid #ccc; padding-left: 8px; }
  .purview-subgroup-title { font-weight: 600; color: #555; font-size: 0.9rem; margin-bottom: 2px; }
</style>
<div id="popLayer"></div>
<div id="popBox" style="overflow:auto">
  <input hidden="hidden" name="page_type" id="page_type" value="0">
  <h4><?php echo _("Authentication Setting"); ?></h4>
  <div class="purview-toolbar">
    <span><?php echo _("Purview"); ?></span>
    <button type="button" class="cbi-button" onclick="toggleAllGroups(true)"><?php echo _("Expand All"); ?></button>
    <button type="button" class="cbi-button" onclick="toggleAllGroups(false)"><?php echo _("Collapse All"); ?></button>
  </div>
  <div class="cbi-section">
    <div class="cbi-value">
      <label class="cbi-value-title" for="auth.username"><?php echo _("Username"); ?></label>
      <input id="auth.username" type="text" class="cbi-input-text">
    </div>

    <div class="cbi-value">
      <label class="cbi-value-title" for="auth.password"><?php echo _("Password"); ?></label>
      <input id="auth.password" type="text" class="cbi-input-text">
    </div>

    <?php
      // Build purview groups from single source of truth (with subgroup nesting)
      $tree = array();
      foreach (getVisibleMenuList() as $item) {
          $tree[$item['group']][$item['subgroup']][] = $item;
      }

      $head_name = 'auth';
      $gid = 0;
      foreach ($tree as $group_title => $subgroups) {
        // Flatten count for the group header
        $group_count = 0;
        foreach ($subgroups as $sg_items) { $group_count += count($sg_items); }

        echo '<div class="purview-group" id="purview-group-' . $gid . '">';
        echo '<div class="purview-group-header" onclick="togglePurviewGroup(this)">';
        echo '<span class="purview-group-title">' . htmlspecialchars($group_title) . '</span>';
        echo '<span class="purview-group-count">0/' . $group_count . '</span>';
        echo '<span class="purview-group-actions">';
        echo '<button type="button" class="cbi-button" onclick="event.stopPropagation(); groupSelectAll(this, true);">' . _('Select All') . '</button>';
        echo '<button type="button" class="cbi-button" onclick="event.stopPropagation(); groupSelectAll(this, false);">' . _('Clear All') . '</button>';
        echo '</span>';
        echo '<span class="purview-group-toggle">&#9654;</span>';
        echo '</div>';
        echo '<div class="purview-group-body">';

        // Direct items (no subgroup)
        if (!empty($subgroups[''])) {
          foreach ($subgroups[''] as $item) {
            echo '<div class="cbi-value">
              <label class="cbi-value-title">' . htmlspecialchars($item['title']) . '</label>
              <input type="checkbox" class="cbi-input-checkbox" name="' . $head_name . '.' . $item['name'] . '" id="' . $head_name . '.' . $item['name'] . '" value="1" onchange="updateGroupCount(this)"/>
            </div>';
          }
        }
        // Subgroup items
        foreach ($subgroups as $sg_label => $items) {
          if ($sg_label === '' || empty($items)) continue;
          echo '<div class="purview-subgroup">';
          echo '<div class="purview-subgroup-title">' . htmlspecialchars($sg_label) . '</div>';
          foreach ($items as $item) {
            echo '<div class="cbi-value">
              <label class="cbi-value-title">' . htmlspecialchars($item['title']) . '</label>
              <input type="checkbox" class="cbi-input-checkbox" name="' . $head_name . '.' . $item['name'] . '" id="' . $head_name . '.' . $item['name'] . '" value="1" onchange="updateGroupCount(this)"/>
            </div>';
          }
          echo '</div>';
        }

        echo '</div>';
        echo '</div>';
        $gid++;
      }

      // Output JS constant so system.js shares the same list (bit indices, names, hrefs)
      $all_items = getMenuPurviewList();
      $js_list = array();
      foreach ($all_items as $item) {
          // item: [bit, href, name, group, subgroup, title, condition]
          $js_list[] = array(
              'bit'      => $item[0],
              'href'     => $item[1],
              'name'     => $item[2],
              'group'    => $item[3],
              'subgroup' => $item[4],
              'title'    => $item[5],
          );
      }
      echo '<script>window.PURVIEW_MENU_LIST = ' . json_encode($js_list) . ';</script>';
    ?>
  </div>
  <div class="right">
    <button class="cbi-button" onclick="closeBox()"><?php echo _("Dismiss"); ?></button>
    <button class="cbi-button cbi-button-positive important" onclick="saveDataAuth()"><?php echo _("Save"); ?></button>
  </div>
  <?php endif;?>
</div><!-- popBox -->
