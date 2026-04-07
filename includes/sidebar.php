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
                            <?php if (file_exists('/dev/ttyUSB1')) : ?>
                            <li class="nav-item" name="lte" id="network_wan_lte"><a class="nav-link" href="lte_conf"><?php echo _("LTE"); ?></a></li>
                            <?php endif; ?>
                            <?php if (isRunning('wpa_supplicant')) : ?>
                            <li class="nav-item" name="wpa" id="network_wan_wpa"><a class="nav-link" href="wlan0_conf"><?php echo _("WiFi Client"); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <li class="nav-item" name="lan" id="network_lan" ><a class="nav-link" href="dhcpd_conf"><?php echo _("LAN"); ?></a></li>
                <li class="nav-item" name="wifi" id="network_wifi" ><a class="nav-link" href="hostapd_conf"><?php echo _("WiFi AP"); ?></a></li>
                <li class="nav-item" name="wifi_client" id="network_wifi_client" ><a class="nav-link" href="wpa_conf"><?php echo _("WiFi Client"); ?></a></li>
                <?php if (isBinExists("failoverd")) : ?>
                <li class="nav-item" name="online_detection" id="network_online_detection" ><a class="nav-link" href="detection_conf"><?php echo _("Online Detection"); ?></a></li>
                <?php endif; ?>
                <?php if (isBinExists("lora_pkt_fwd")) : ?>
                <li class="nav-item" name="lorawan" id="network_lorawan" ><a class="nav-link" href="lorawan_conf"><?php echo _("LoRaWan"); ?></a></li>
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
                <li class="nav-item" name="dct_basic" id="dct_basic" ><a class="nav-link" href="basic_conf"><?php echo _("Basic"); ?></a></li>
                <li class="nav-item" name="interfaces" id="dct_interfaces"><a class="nav-link" href="interfaces_conf"><?php echo _("Interfaces"); ?></a></li>
                <li class="nav-item" id="page_south">
                    <a class="nav-link navbar-toggle collapsed" id="south" href="#" data-toggle="collapse" data-target="#navbar-collapse-south">
                        <?php echo _("South Devices"); ?>
                    </a>
                    <div class="collapse navbar-collapse" id="navbar-collapse-south">
                        <ul class="nav navbar-nav navbar-right">
                            <li class="nav-item" name="modbus" id="dct_south_modbus"><a class="nav-link" href="modbus_conf"><?php echo _("Modbus Rules"); ?></a></li>
                            <li class="nav-item" name="ascii" id="dct_south_ascii"><a class="nav-link" href="ascii_conf"><?php echo _("ASCII Rules"); ?></a></li>
                            <li class="nav-item" name="s7" id="dct_south_s7"><a class="nav-link" href="s7_conf"><?php echo _("S7 Rules"); ?></a></li>
                                <li class="nav-item" name="fx" id="dct_south_fx"><a class="nav-link" href="fx_conf"><?php echo _("FX Rules"); ?></a></li>
                            <li class="nav-item" name="mc" id="dct_south_mc"><a class="nav-link" href="mc_conf"><?php echo _("MC Rules"); ?></a></li>
                            <li class="nav-item" name="iec104" id="dct_south_iec104"><a class="nav-link" href="iec104_conf"><?php echo _("IEC104 Rules"); ?></a></li>
                            <li class="nav-item" name="dnp3_client" id="dct_south_dnp3_client"><a class="nav-link" href="dnp3cli_conf"><?php echo _("DNP3 Rules"); ?></a></li>
                            <li class="nav-item" name="opcua_client" id="dct_south_opcua_client"><a class="nav-link" href="opcuacli_conf"><?php echo _("OPCUA Rules"); ?></a></li>
                            <?php if (isBinExists("baccli")) : ?>
                            <li class="nav-item" name="bacnet_client" id="dct_south_bacnet_client"><a class="nav-link" href="baccli_conf"><?php echo _("BACnet Rules"); ?></a></li>
                            <?php endif; ?>
                            <li class="nav-item" name="ethernetip" id="dct_south_ethernetip"><a class="nav-link" href="ethernetip_conf"><?php echo _("EtherNet/IP Rules"); ?></a></li>
                            <li class="nav-item" name="mbus_client" id="dct_south_mbus_client"><a class="nav-link" href="mbuscli_conf"><?php echo _("Mbus Rules"); ?></a></li>
                            <li class="nav-item" name="snmp_client" id="dct_south_snmp_client"><a class="nav-link" href="snmpcli_conf"><?php echo _("SNMP Rules"); ?></a></li>
                            <li class="nav-item" name="iec1107" id="dct_south_iec1107"><a class="nav-link" href="iec1107_conf"><?php echo _("IEC62056-21 Rules"); ?></a></li>
                            <li class="nav-item" name="dlms" id="dct_south_dlms"><a class="nav-link" href="dlms_conf"><?php echo _("DLMS Rules"); ?></a></li>
                            <li class="nav-item" name="iec61850_client" id="dct_south_iec61850_client"><a class="nav-link" href="iec61850cli_conf"><?php echo _("IEC61850 Rules"); ?></a></li>
                            <?php if (isIoExistts()) : ?>
                            <li class="nav-item" name="io" id="dct_south_io"><a class="nav-link" href="io_conf"><?php echo _("IO"); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <li class="nav-item" id="page_north">
                    <a class="nav-link navbar-toggle collapsed" id="north" href="#" data-toggle="collapse" data-target="#navbar-collapse-north">
                        <?php echo _("North Apps"); ?>
                    </a>
                    <div class="collapse navbar-collapse" id="navbar-collapse-north">
                        <ul class="nav navbar-nav navbar-right">
                            <li class="nav-item" name="server" id="dct_north_server"><a class="nav-link" href="server_conf"><?php echo _("Reporting Center"); ?></a></li>
                            <li class="nav-item" name="modbus_slave" id="dct_north_modbus_slave"><a class="nav-link" href="modbus_slave"><?php echo _("Modbus Slave"); ?></a></li>
                            <li class="nav-item" name="opcua" id="dct_north_opcua"><a class="nav-link" href="opcua"><?php echo _("OPCUA Server"); ?></a></li>
                            <?php if(isBinExists("bacserv")) : ?>
                            <li class="nav-item" name="bacnet" id="dct_north_bacnet"><a class="nav-link" href="bacnet"><?php echo _("BACnet Server"); ?></a></li>
                            <?php endif; ?>
                            <li class="nav-item" name="dnp3" id="dct_north_dnp3"><a class="nav-link" href="dnp3"><?php echo _("DNP3 Server"); ?></a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item" name="datadisplay" id="dct_datadisplay"><a class="nav-link" href="datadisplay"><?php echo _("Data Monitoring"); ?></a></li>
            </ul>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isBinExists("router-mstp") || isBinExists("router-modbus")) : ?>
        <li class="nav-item" id="page_convert">
            <a class="nav-link navbar-toggle collapsed" id="protocol_convert" href="#" data-toggle="collapse" data-target="#navbar-collapse-convert">
                <i class="fas fa-server fa-fw mr-2"></i>
                <span class="nav-label"><?php echo _("Protocol Convert"); ?></a>
            </a>
            <div class="collapse navbar-collapse" id="navbar-collapse-convert">
            <ul class="nav navbar-nav navbar-right">
                <?php if(isBinExists("router-mstp")) : ?>
                    <li class="nav-item" name="bacnet_router" id="convert_bacnet_router"> <a class="nav-link" href="bacnet_router"><?php echo _("BACnet Router"); ?></a></li>
                <?php endif; ?>
                <?php if(isBinExists("router-modbus")) : ?>
                    <li class="nav-item" name="modbus_router" id="convert_modbus_router"> <a class="nav-link" href="modbus_router"><?php echo _("Modbus Router"); ?></a></li>
                <?php endif; ?>
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
                        <?php if(isBinExists("baseagent") || file_exists("/usr/local/baseagent/baseagent")) : ?>
                        <li class="nav-item" name="things_wing" id="remote_things_wing"> <a class="nav-link" href="things_wing"><?php echo _("ThingsWing"); ?></a></li>
                        <?php endif; ?>
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
                    <?php if(isBinExists("node-red")) : ?>
                    <li class="nav-item" name="nodered" id="services_nodered"> <a class="nav-link" href="nodered"><?php echo _("Node Red"); ?></a></li>
                    <?php endif; ?>
                    <?php if(isBinExists("dockerd")) : ?>
                    <li class="nav-item" name="docker" id="services_docker"> <a class="nav-link" href="docker"><?php echo _("Docker"); ?></a></li>
                    <?php endif; ?>
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
                <?php if(isBinExists("gpsd")) : ?>
                <li class="nav-item" name="gps" id="system_gps"> <a class="nav-link" href="gps"><?php echo _("GPS Location"); ?></a></li>
                <?php endif; ?>
                <?php if(isBinExists("ttyd") || file_exists("/usr/local/bin/ttyd")) : ?>
                <li class="nav-item" name="terminal" id="system_terminal"> <a class="nav-link" href="terminal"><?php echo _("Terminal"); ?></a></li>
                <?php endif; ?>
                <?php if(isBinExists("chromium-browser") && strpos($target, 'EH607') !== false) : ?>
                <li class="nav-item" name="hmi" id="system_hmi"> <a class="nav-link" href="hmi"><?php echo _("HMI"); ?></a></li>
                <?php endif; ?>
                <?php if(isBinExists("scheduled")) : ?>
                <li class="nav-item" name="scheduled" id="system_scheduled"> <a class="nav-link" href="scheduled"><?php echo _("Scheduled Tasks"); ?></a></li>
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
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" href="logout"><i class="fas fa-sign-out-alt mr-2"></i><span class="nav-label"><?php echo _("Logout"); ?></a>
        </li>
        <!-- Divider -->
        <hr class="sidebar-divider d-block">
    </ul>
