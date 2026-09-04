<?php $_SESSION['lastActivity'] = time(); ?>
    <ul class="navbar-nav sidebar sidebar-light d-block accordion <?php echo (isset($toggleState)) ? $toggleState : null ; ?>" id="accordionSidebar">
        <!-- Divider -->
        <hr class="sidebar-divider my-0">
        <div class="sidebar-logo-wrap">
            <div class="sidebar-brand-icon">
            <?php setSidbarLogo($target, $hostname); ?>
            </div>
        </div>
        <li class="nav-item">
            <a class="nav-link" href="dashboard"><i class="fas fa-tachometer-alt fa-fw mr-2"></i><span class="nav-label"><?php echo _("Dashboard"); ?></span></a>
        </li>

        <?php
        // Collect ALL visible items in their original order,
        // then split into per-group ordered sequences.
        $purview = getPurview();
        $all_visible = getVisibleMenuList();

        // Meta for each group (id, collapse, icon)
        $group_meta = array(
            _('Network')        => array('id' => 'page_network',  'collapse' => 'navbar-collapse-network',  'icon' => 'fa-network-wired'),
            _('Data Collect')   => array('id' => 'page_dct',      'collapse' => 'navbar-collapse-dct',      'icon' => 'fa-exchange-alt'),
            _('Protocol Convert') => array('id' => 'page_convert','collapse' => 'navbar-collapse-convert',  'icon' => 'fa-server'),
            _('Remote Access')  => array('id' => 'page_remote',   'collapse' => 'navbar-collapse-remote',   'icon' => 'fa-key'),
            _('Services')       => array('id' => 'page_services', 'collapse' => 'navbar-collapse-services', 'icon' => 'fa-cube'),
            _('System')         => array('id' => 'page_system',   'collapse' => 'navbar-collapse-system',   'icon' => 'fa-cogs'),
        );

        // Subgroup meta (id, collapse) — derived from label
        $subgroup_meta = array(
            _('WAN')          => array('id' => 'page_wan',   'collapse' => 'navbar-collapse-wan'),
            _('South Devices') => array('id' => 'page_south', 'collapse' => 'navbar-collapse-south'),
            _('North Apps')    => array('id' => 'page_north',  'collapse' => 'navbar-collapse-north'),
            _('VPN')          => array('id' => 'page_vpn',   'collapse' => 'navbar-collapse-vpn'),
        );

        // Process each group in group_meta order (top-to-bottom)
        foreach ($group_meta as $group_label => $meta) :
            // Filter items for this group, preserving original order
            $group_items = array();
            foreach ($all_visible as $item) {
                if ($item['group'] === $group_label) $group_items[] = $item;
            }
            if (empty($group_items)) continue;
        ?>
        <li class="nav-item" id="<?php echo $meta['id']; ?>">
            <a class="nav-link navbar-toggle collapsed" href="#" data-toggle="collapse" data-target="#<?php echo $meta['collapse']; ?>">
                <i class="fas <?php echo $meta['icon']; ?> fa-fw mr-2"></i>
                <span class="nav-label"><?php echo $group_label; ?></span>
            </a>
            <div class="collapse navbar-collapse" id="<?php echo $meta['collapse']; ?>">
            <ul class="nav navbar-nav navbar-right">
                <?php
                // Walk items in original order. Track when subgroup changes
                // so we can open/close subgroup wrappers correctly.
                $current_sg = null;
                $open_sg = false;
                foreach ($group_items as $item) :
                    $sg = $item['subgroup'];
                    if ($sg !== $current_sg) :
                        // Close previous subgroup if open
                        if ($open_sg) :
                ?>
                        </ul>
                    </div>
                </li>
                <?php
                            $open_sg = false;
                        endif;
                        $current_sg = $sg;
                        if ($sg !== '') :
                            $sgm = isset($subgroup_meta[$sg]) ? $subgroup_meta[$sg] : array(
                                'id' => 'page_' . strtolower(str_replace(' ', '_', $sg)),
                                'collapse' => 'navbar-collapse-' . strtolower(str_replace(' ', '-', $sg)),
                            );
                ?>
                <li class="nav-item" id="<?php echo $sgm['id']; ?>">
                    <a class="nav-link navbar-toggle collapsed" href="#" data-toggle="collapse" data-target="#<?php echo $sgm['collapse']; ?>">
                        <?php echo $sg; ?>
                    </a>
                    <div class="collapse navbar-collapse" id="<?php echo $sgm['collapse']; ?>">
                        <ul class="nav navbar-nav navbar-right">
                <?php
                            $open_sg = true;
                        endif;
                    endif; // subgroup changed
                    // Emit the menu item
                    menuPurviewMatch($purview, $item['name'], $item['name'], $item['href'], $item['title']);
                endforeach;
                // Close trailing subgroup if open
                if ($open_sg) :
                ?>
                        </ul>
                    </div>
                </li>
                <?php endif; ?>
            </ul>
            </div>
        </li>
        <?php endforeach; // foreach group ?>

        <?php if ($target == null || $target == 'EC211' || $target == 'EH607') : ?>
        <li class="nav-item">
            <a class="nav-link" href="about"><i class="fas fa-info-circle fa-fw mr-2"></i><span class="nav-label"><?php echo _("About Elastel"); ?></span></a>
        </li>
        <?php elseif ($target == '4logit') : ?>
        <li class="nav-item">
            <a class="nav-link" href="about"><i class="fas fa-info-circle fa-fw mr-2"></i><span class="nav-label"><?php echo _("About 4Logit"); ?></span></a>
        </li>   
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" href="logout"><i class="fas fa-sign-out-alt mr-2"></i><span class="nav-label"><?php echo _("Logout"); ?></a>
        </li>
        <!-- Divider -->
        <hr class="sidebar-divider d-block">
    </ul>
