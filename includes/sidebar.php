<?php $_SESSION['lastActivity'] = time(); ?>
    <ul class="navbar-nav sidebar sidebar-light d-block accordion <?php echo (isset($toggleState)) ? $toggleState : null ; ?>" id="accordionSidebar">
        <!-- Divider -->
        <hr class="sidebar-divider my-0">
        <div class="row">
            <div class="col-xs ml-3 sidebar-brand-icon">
            <?php setSidbarLogo($target, $hostname); ?>
            </div>
        </div>
        <li class="nav-item">
            <a class="nav-link" href="dashboard"><i class="fas fa-tachometer-alt fa-fw mr-2"></i><span class="nav-label"><?php echo _("Dashboard"); ?></span></a>
        </li>
        <li class="nav-item" id="page_network">
            <a class="nav-link navbar-toggle collapsed" id="network" href="#" data-toggle="collapse" data-target="#navbar-collapse-network">
                <i class="fas fa-network-wired fa-fw mr-2"></i>
                <span class="nav-label"><?php echo _("Network"); ?></a>
            </a>
            <div class="collapse navbar-collapse" id="navbar-collapse-network">
            <ul class="nav navbar-nav navbar-right">
                <li class="nav-item" id="page_wan">
                    <a class="nav-link navbar-toggle collapsed" id="wan" href="#" data-toggle="collapse" data-target="#navbar-collapse-wan">
                        <?php echo _("WAN"); ?>
                    </a>
                    <div class="collapse navbar-collapse" id="navbar-collapse-wan">
                        <ul class="nav navbar-nav navbar-right">
                            <li class="nav-item" name="wired" id="network_wan_wired"><a class="nav-link" href="wired_conf"><?php echo _("Wired"); ?></a></li>
                            <?php if (file_exists('/dev/ttyUSB1') && isLteEnabled()) : ?>
                            <li class="nav-item" name="lte" id="network_wan_lte"><a class="nav-link" href="lte_conf"><?php echo _("LTE"); ?></a></li>
                            <?php endif; ?>
                            <?php if (isRunning('wpa_supplicant')) : ?>
                            <li class="nav-item" name="wpa" id="network_wan_wpa"><a class="nav-link" href="wlan0_conf"><?php echo _("WiFi Client"); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <li class="nav-item" name="lan" id="network_lan" ><a class="nav-link" href="dhcpd_conf"><?php echo _("LAN"); ?></a></li>
                <?php if(file_exists('/sys/class/net/wlan0')) :?>
                <li class="nav-item" name="wifi" id="network_wifi" ><a class="nav-link" href="hostapd_conf"><?php echo _("WiFi AP"); ?></a></li>
                <li class="nav-item" name="wifi_client" id="network_wifi_client" ><a class="nav-link" href="wpa_conf"><?php echo _("WiFi Client"); ?></a></li>
                <?php endif; ?>
                <?php if (isBinExists("failoverd")) : ?>
                <li class="nav-item" name="online_detection" id="network_online_detection" ><a class="nav-link" href="detection_conf"><?php echo _("Online Detection"); ?></a></li>
                <?php endif; ?>
                <?php if (isBinExists("lora_pkt_fwd")) : ?>
                <li class="nav-item" name="lorawan" id="network_lorawan" ><a class="nav-link" href="lorawan_conf"><?php echo _("LoRaWAN"); ?></a></li>
                <?php endif; ?>
                <?php if (isBinExists("efw")) : ?>
                <li class="nav-item" name="firewall" id="network_firewall" ><a class="nav-link" href="firewall_conf"><?php echo _("Firewall"); ?></a></li>
                <?php endif; ?>
            </ul>
            </div>
        </li>
        <?php if(isBinExists("dctd")) : ?>
        <li class="nav-item" id="page_dct">
            <a class="nav-link navbar-toggle collapsed" id="dct" href="#" data-toggle="collapse" data-target="#navbar-collapse-dct">
                <i class="fas fa-exchange-alt fa-fw mr-2"></i>
                <span class="nav-label"><?php echo _("Data Collect"); ?></a>
            </a>
            <div class="collapse navbar-collapse" id="navbar-collapse-dct">
            <ul class="nav navbar-nav navbar-right">
                <?php
                    menuPurviewMatch($purview, 'dct_basic', 'dct_basic', 'basic_conf', _('Basic'));
                    menuPurviewMatch($purview, 'dct_interfaces', 'dct_interfaces', 'interfaces_conf', _('Interfaces'));
                ?>
                <li class="nav-item" id="page_south">
                    <a class="nav-link navbar-toggle collapsed" id="south" href="#" data-toggle="collapse" data-target="#navbar-collapse-south">
                        <?php echo _("South Devices"); ?>
                    </a>
                    <div class="collapse navbar-collapse" id="navbar-collapse-south">
                        <ul class="nav navbar-nav navbar-right">
                            <?php
                                $array_south = array('modbus', 'ascii', 's7', 'fx', 'mc', 'iec104', 
                                        'dnp3cli', 'opcuacli', 'baccli', 'ethernetip', 'mbuscli', 'snmpcli', 'iec1107', 'dlms', 
                                        'iec61850cli');
                                $array_title = array('Modbus', 'ASCII', 'S7', 'FX', 'MC', 'IEC104', 'DNP3', 'OPCUA', 'BACnet', 
                                        'EtherNet/IP','Mbus','SNMP','IEC62056-21','DLMS','IEC61850');
                                
                                for ($i = 0; $i < count($array_south); $i++) {
                                    $item = $array_south[$i];
                                    $title = $array_title[$i];
                                    menuPurviewMatch($purview, $item, 'dct_south_' . $item, $item . '_conf', $title . " " . _('Rules'));
                                }

                                if (isIoExistts()) {
                                    menuPurviewMatch($purview, 'io', 'dct_south_io', 'io_conf', _('IO'));
                                }

                                menuPurviewMatch($purview, 'system_param', 'dct_south_system_param', 'system_param_conf', _('System Parameters'));
                            ?>
                        </ul>
                    </div>
                </li>
                <li class="nav-item" id="page_north">
                    <a class="nav-link navbar-toggle collapsed" id="north" href="#" data-toggle="collapse" data-target="#navbar-collapse-north">
                        <?php echo _("North Apps"); ?>
                    </a>
                    <div class="collapse navbar-collapse" id="navbar-collapse-north">
                        <ul class="nav navbar-nav navbar-right">
                            <?php
                                menuPurviewMatch($purview, 'server', 'dct_north_server', 'server_conf', _('Reporting Center'));
                                menuPurviewMatch($purview, 'modbus_slave', 'dct_north_modbus_slave', 'modbus_slave', "Modbus "._('Slave'));
                                menuPurviewMatch($purview, 'opcua', 'dct_north_opcua', 'opcua', "OPCUA "._('Server'));
                                if(isBinExists("bacserv")) {
                                    menuPurviewMatch($purview, 'bacnet', 'dct_north_bacnet', 'bacnet', "BACnet "._('Server'));
                                }
                                menuPurviewMatch($purview, 'dnp3', 'dct_north_dnp3', 'dnp3', "DNP3 "._('Server'));
                            ?>
                        </ul>
                    </div>
                </li>
                <?php menuPurviewMatch($purview, 'datadisplay', 'dct_datadisplay', 'datadisplay', _('Data Monitoring')); ?>
            </ul>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isBinExists("router-mstp") || isBinExists("router-modbus")) : ?>
        <li class="nav-item" id="page_convert">
            <a class="nav-link navbar-toggle collapsed" id="convert" href="#" data-toggle="collapse" data-target="#navbar-collapse-convert">
                <i class="fas fa-server fa-fw mr-2"></i>
                <span class="nav-label"><?php echo _("Protocol Convert"); ?></a>
            </a>
            <div class="collapse navbar-collapse" id="navbar-collapse-convert">
            <ul class="nav navbar-nav navbar-right">
                <?php 
                    if(isBinExists("router-mstp")) {
                        menuPurviewMatch($purview, 'bacnet_router', 'convert_bacnet_router', 'bacnet_router', "BACnet "._('Router'));
                    }

                    if(isBinExists("router-modbus")) {
                        menuPurviewMatch($purview, 'modbus_router', 'convert_modbus_router', 'modbus_router', "Modbus "._('Router'));
                    }
                ?>
            </ul>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isBinExists("baseagent") || isBinExists("openvpn") || isBinExists("wg") || isBinExists("noip2")) : ?>
        <li class="nav-item" id="page_remote">
            <a class="nav-link navbar-toggle collapsed" id="remote" href="#" data-toggle="collapse" data-target="#navbar-collapse-remote">
                <i class="fas fa-key fa-fw mr-2"></i>
                <span class="nav-label"><?php echo _("Remote Access"); ?></a>
            </a>
            <div class="collapse navbar-collapse" id="navbar-collapse-remote">
                <ul class="nav navbar-nav navbar-right">
                    <?php if ((strpos($target, "IQEG") === false && strpos($target, "IQEC") === false)) { ?>
                        <li class="nav-item" name="things_wing" id="remote_things_wing"> <a class="nav-link" href="things_wing"><?php echo _("ThingsWing"); ?></a></li>
                    <?php } ?>
                    <?php if(isBinExists("noip2")) : ?>
                    <li class="nav-item" name="ddns" id="remote_ddns"> <a class="nav-link" href="ddns"><?php echo _("DDNS"); ?></a></li>
                    <?php endif; ?>
                    <?php if(isBinExists("openvpn") || isBinExists("wg")) : ?>
                    <li class="nav-item" id="page_vpn">
                        <a class="nav-link navbar-toggle collapsed" id="test" href="#" data-toggle="collapse" data-target="#navbar-collapse-vpn">
                            <?php echo _("VPN"); ?>
                        </a>
                        <div class="collapse navbar-collapse" id="navbar-collapse-vpn">
                            <ul class="nav navbar-nav navbar-right">
                                <?php if(isBinExists("openvpn")) : ?>
                                <li class="nav-item" name="openvpn" id="remote_vpn_openvpn"> <a class="nav-link" href="openvpn"><?php echo _("OpenVPN"); ?></a></li>
                                <?php endif; ?>
                                <?php if(isBinExists("wg") && isBinExists("wg-quick")) : ?>
                                <li class="nav-item" name="wireguard" id="remote_vpn_wireguard"> <a class="nav-link" href="wireguard"><?php echo _("WireGuard"); ?></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isBinExists("node-red") || isBinExists("dockerd") || isBinExists("chirpstack") || isBinExists("iotedge")) : ?>
            <li class="nav-item" id="page_services">
                <a class="nav-link navbar-toggle collapsed" id="services" href="#" data-toggle="collapse" data-target="#navbar-collapse-services">
                    <i class="fas fa-cube fa-fw mr-2"></i>
                    <span class="nav-label"><?php echo _("Services"); ?></a>
                </a>
                <div class="collapse navbar-collapse" id="navbar-collapse-services">
                <ul class="nav navbar-nav navbar-right">
                    <?php 
                        if(isBinExists("node-red")) {
                            menupurviewMatch($purview, 'nodered', 'services_nodered', 'nodered', _('Node Red'));
                        }

                        if(isBinExists("dockerd")) {
                            menupurviewMatch($purview, 'docker', 'services_docker', 'docker', _('Docker'));
                        }
                    ?>
                    <?php if(isBinExists("chirpstack")) : ?>
                    <li class="nav-item" name="chirpstack" id="services_chirpstack"> <a class="nav-link" href="chirpstack"><?php echo _("ChirpStack"); ?></a></li>
                    <?php endif; ?>
                    <?php if(isBinExists("iotedge")) : ?>
                    <li class="nav-item" name="iotedge" id="services_iotedge"> <a class="nav-link" href="iotedge"><?php echo _("Azure IoT Edge"); ?></a></li>
                    <?php endif; ?>
                    <?php if((isBinExists("pip3") || isBinExists("python3")) &&  file_exists('/etc/raspap/api/')): ?>
                    <li class="nav-item" name="restapi" id="services_restapi"> <a class="nav-link" href="restapi"><?php echo _("RestAPI"); ?></a></li>
                    <?php endif; ?>
                </ul>
                </div>
            </li>
        <?php endif; ?>
        <li class="nav-item" id="page_system">
            <a class="nav-link navbar-toggle collapsed" id="system" href="#" data-toggle="collapse" data-target="#navbar-collapse-system">
                <i class="fas fa-cogs fa-fw mr-2"></i>
                <span class="nav-label"><?php echo _("System"); ?></a>
            </a>
            <div class="collapse navbar-collapse" id="navbar-collapse-system">
            <ul class="nav navbar-nav navbar-right">
                <li class="nav-item" name="system_info" id="system_system_info"> <a class="nav-link" href="system_info"><?php echo _("System"); ?></a></li>
                <?php 
                    if(isBinExists("gpsd")) {
                        menuPurviewMatch($purview, 'gps', 'system_gps', 'gps', _('GPS Location'));
                    }

                    if(isBinExists("ttyd") || file_exists("/usr/local/bin/ttyd")) {
                        menuPurviewMatch($purview, 'terminal', 'system_terminal', 'terminal', _('Terminal'));
                    }

                    if(isBinExists("scheduled")) {
                        menuPurviewMatch($purview, 'scheduled', 'system_scheduled', 'scheduled', _('Scheduled Tasks'));
                    }
                ?>
                <?php if(isBinExists("chromium-browser") && strpos($target, 'EH607') !== false) : ?>
                <li class="nav-item" name="hmi" id="system_hmi"> <a class="nav-link" href="hmi"><?php echo _("HMI"); ?></a></li>
                <?php endif; ?>
                <li class="nav-item" name="auth_conf" id="system_auth_conf"> <a class="nav-link" href="auth_conf"><?php echo _("Authentication"); ?></a></li>
                <li class="nav-item" name="backup_restore" id="system_backup_restore"> <a class="nav-link" href="backup_restore"><?php echo _("Backup/Restore"); ?></a></li>
                <li class="nav-item" name="backup_update" id="system_backup_update"> <a class="nav-link" href="backup_update"><?php echo _("Update/Restore"); ?></a></li>
            </ul>
            </div>
        </li>
        <?php if ($target == null || $target == 'EC211' || $target == 'EH607') : ?>
        <li class="nav-item">
            <a class="nav-link" href="about"><i class="fas fa-info-circle fa-fw mr-2"></i><span class="nav-label"><?php echo _("About Elastel"); ?></a>
        </li>
        <?php elseif ($target == '4logit') : ?>
        <li class="nav-item">
            <a class="nav-link" href="about"><i class="fas fa-info-circle fa-fw mr-2"></i><span class="nav-label"><?php echo _("About 4Logit"); ?></a>
        </li>   
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" href="logout"><i class="fas fa-sign-out-alt mr-2"></i><span class="nav-label"><?php echo _("Logout"); ?></a>
        </li>
        <!-- Divider -->
        <hr class="sidebar-divider d-block">
    </ul>
