/*
version: 1.0.0
*/

function msgShow(retcode,msg) {
    if(retcode == 0) { var alertType = 'success';
    } else if(retcode == 2 || retcode == 1) {
        var alertType = 'danger';
    }
    var htmlMsg = '<div class="alert alert-'+alertType+' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+msg+'</div>';
    return htmlMsg;
}

function createNetmaskAddr(bitCount) {
  var mask=[];
  for(i=0;i<4;i++) {
    var n = Math.min(bitCount, 8);
    mask.push(256 - Math.pow(2, 8-n));
    bitCount -= n;
  }
  return mask.join('.');
}

function loadSummary(strInterface) {
    $.post('ajax/networking/get_ip_summary.php',{interface:strInterface},function(data){
        jsonData = JSON.parse(data);
        console.log(jsonData);
        if(jsonData['return'] == 0) {
            $('#'+strInterface+'-summary').html(jsonData['output'].join('<br />'));
        } else if(jsonData['return'] == 2) {
            $('#'+strInterface+'-summary').append('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+jsonData['output'].join('<br />')+'</div>');
        }
    });
}

function getAllInterfaces() {
    $.get('ajax/networking/get_all_interfaces.php',function(data){
        jsonData = JSON.parse(data);
        $.each(jsonData,function(ind,value){
            loadSummary(value)
        });
    });
}

function setupTabs() {
    $('a[data-toggle="tab"]').on('shown.bs.tab',function(e){
        var target = $(e.target).attr('href');
        if(!target.match('summary')) {
            var int = target.replace("#","");
            loadCurrentSettings(int);
        }
    });
}

$(document).on("click", ".js-add-dhcp-static-lease", function(e) {
    e.preventDefault();
    var container = $(".js-new-dhcp-static-lease");
    var mac = $("input[name=mac]", container).val().trim();
    var ip  = $("input[name=ip]", container).val().trim();
    var comment = $("input[name=comment]", container).val().trim();
    if (mac == "" || ip == "") {
        return;
    }
    var row = $("#js-dhcp-static-lease-row").html()
        .replace("{{ mac }}", mac)
        .replace("{{ ip }}", ip)
        .replace("{{ comment }}", comment);
    $(".js-dhcp-static-lease-container").append(row);

    $("input[name=mac]", container).val("");
    $("input[name=ip]", container).val("");
    $("input[name=comment]", container).val("");
});

$(document).on("click", ".js-remove-dhcp-static-lease", function(e) {
    e.preventDefault();
    $(this).parents(".js-dhcp-static-lease-row").remove();
});

$(document).on("submit", ".js-dhcp-settings-form", function(e) {
    $(".js-add-dhcp-static-lease").trigger("click");
});

$(document).on("click", ".js-add-dhcp-upstream-server", function(e) {
    e.preventDefault();

    var field = $("#add-dhcp-upstream-server-field")
    var row = $("#dhcp-upstream-server").html().replace("{{ server }}", field.val())

    if (field.val().trim() == "") { return }

    $(".js-dhcp-upstream-servers").append(row)

    field.val("")
});

$(document).on("click", ".js-remove-dhcp-upstream-server", function(e) {
    e.preventDefault();
    $(this).parents(".js-dhcp-upstream-server").remove();
});

$(document).on("submit", ".js-dhcp-settings-form", function(e) {
    $(".js-add-dhcp-upstream-server").trigger("click");
});

/**
 * mark a form field, e.g. a select box, with the class `.js-field-preset`
 * and give it an attribute `data-field-preset-target` with a text field's
 * css selector.
 *
 * now, if the element marked `.js-field-preset` receives a `change` event,
 * its value will be copied to all elements matching the selector in
 * data-field-preset-target.
 */
$(document).on("change", ".js-field-preset", function(e) {
    var selector = this.getAttribute("data-field-preset-target")
    var value = "" + this.value
    var syncValue = function(el) { el.value = value }

    if (value.trim() === "") { return }

    document.querySelectorAll(selector).forEach(syncValue)
});

$(document).on("click", "#gen_wpa_passphrase", function(e) {
    $('#txtwpapassphrase').val(genPassword(63));
});

// Enable Bootstrap tooltips
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

function genPassword(pwdLen) {
    var pwdChars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    var rndPass = Array(pwdLen).fill(pwdChars).map(function(x) { return x[Math.floor(Math.random() * x.length)] }).join('');
    return rndPass;
}

function setupBtns() {
    $('#btnSummaryRefresh').click(function(){getAllInterfaces();});
    $('.intsave').click(function(){
        var int = $(this).data('int');
        saveNetworkSettings(int);
    });
    $('.intapply').click(function(){
        applyNetworkSettings();
    });
}

function loadBacnetRouter() {
    $.get('ajax/service/get_service.php?type=bacnet_router', function(data) {
        // console.log(data);
        jsonData = JSON.parse(data);
        var arr = ['mode', 'ifname', 'port', 'interface', 'baudrate', 'mac',
                    'max_master', 'frames'];

        $('#enabled').val(jsonData.enabled);
        if (jsonData.enabled == '1') {
            $('#page_bacnet').show();
            $('#bacnet_enable').prop('checked', true);

            arr.forEach(function (info) {
                if (info == null) {
                    return true;    // continue: return true; break: return false
                }

                $('#' + info).val(jsonData[info]);
            })
        } else {
            $('#page_bacnet').hide();
            $('#bacnet_disable').prop('checked', true);
        }
    })
}

function getDashboardData() {
    $.get('ajax/service/get_dashboard_data.php', function(data) {
        jsonData = JSON.parse(data);
        $('#local_time').html(jsonData['local_time']);
        $('#uptime').html(jsonData['uptime']);
    })
}

function loadDashboard() {
    getDashboardData();
    setInterval(getDashboardData, 1000);
}

function loadFirewall() {
    $('#loading').show();
    $.get('ajax/networking/get_firewall.php', function(data) {
        //console.log(data);
        jsonData = JSON.parse(data);
        // general
        var arrGeneral = ['synflood_protect', 'drop_invalid', 'input', 'output', 'forward'];

        arrGeneral.forEach(function (info) {
            if (info == null) {
                return true;    // continue: return true; break: return false
            }

            if ( info == 'synflood_protect' || info == 'drop_invalid') {
                $('#' + info).prop('checked', (jsonData[info] == '1') ? true:false);
            } else {
                $('#' + info).val(jsonData[info]);
            }
        })

        // forwards
        var arrForwards = ['name', 'proto', 'src_port', 'dest_ip', 'dest_port', 'enabled'];
        var forwardsCount = jsonData['forwards.count'];
        for (var i = 0; i < Number(forwardsCount); i++) {
            var table = document.getElementsByTagName("table")[0];
            var forwardsHtml = "<tr  class=\"tr cbi-section-table-descr\">\n";
            arrForwards.forEach(function (info) {
                if (info == null) {
                    return true;    // continue: return true; break: return false
                }

                forwardsHtml += "        <td style='text-align:center'>" + (jsonData['forwards.' + info][i] != null ? jsonData['forwards.' + info][i] : "-") + "</td>\n";
            })
            forwardsHtml += "        <td><a href=\"javascript:void(0);\" onclick=\"editForwards(this);\" >Edit</a></td>\n" +
                            "        <td><a href=\"javascript:void(0);\" onclick=\"delForwards(this);\" >Del</a></td>\n" +
                            "    </tr>";

            table.innerHTML += forwardsHtml;
        }

        result = getTableDataForwards();
        var dataForwards = JSON.stringify(result);
        $('#hidForwards').val(dataForwards);

        // traffic
        var arrTraffic = ['name', 'proto', 'rule', 'src_mac', 'src_ip', 'src_port',
                        'dest_ip', 'dest_port', 'action', 'enabled'];
        var trafficCount = jsonData['traffic.count'];
        for (var i = 0; i < Number(trafficCount); i++) {
            var table = document.getElementsByTagName("table")[1];
            var trafficHtml = "<tr  class=\"tr cbi-section-table-descr\">\n";
            arrTraffic.forEach(function (info) {
                if (info == null) {
                    return true;    // continue: return true; break: return false
                }

                trafficHtml += "        <td style='text-align:center'>" + (jsonData['traffic.' + info][i] != null ? jsonData['traffic.' + info][i] : "-") + "</td>\n";
            })
            trafficHtml += "        <td><a href=\"javascript:void(0);\" onclick=\"editTraffic(this);\" >Edit</a></td>\n" +
                            "        <td><a href=\"javascript:void(0);\" onclick=\"delTraffic(this);\" >Del</a></td>\n" +
                            "    </tr>";

            table.innerHTML += trafficHtml;
        }

        result = getTableDataTraffic();
        var dataTraffic = JSON.stringify(result);
        $('#hidTraffic').val(dataTraffic);
        $('#loading').hide();
    })
}

function loadGps() {
    $.get('ajax/service/get_service.php?type=gps', function(data) {
        //console.log(data);
        jsonData = JSON.parse(data);
        var arr = ['output_mode', 'server_addr', 'server_port', 'report_mode', 'register_packet',
        'heartbeat_packet', 'report_interval', 'heartbeat_interval', 'baudrate', 'databit', 'stopbit',
        'parity', 'accuracy'];

        $('#enabled').val(jsonData.enabled);
        if (jsonData.enabled == '1') {
            $('#gps_enable').prop('checked', true);
            for(var key in jsonData){ 
                if (key == null) {
                    return true;    // continue: return true; break: return false
                }
                $('#' + key).val(jsonData[key]); 
            }

            $('#page_gps').show();
            if (jsonData['output_mode'] == 'network') {
                $('#gps_network').show();
                $('#tcp_status').show();
                $('#gps_serial').hide();
                $('#gps_report').show();
            } else if (jsonData['output_mode'] == 'serial') {
                $('#gps_network').hide();
                $('#tcp_status').hide();
                $('#gps_serial').show();
                $('#gps_report').show();
            } else {
                $('#gps_network').hide();
                $('#tcp_status').hide();
                $('#gps_serial').hide();
                $('#gps_report').hide();
            }
        } else {
            $('#gps_disable').prop('checked', true);
            $('#page_gps').hide();
        }
    })
}

function loadWireguard() {
    $.get('ajax/networking/get_wgcfg.php?type=settings', function(data) {
        //console.log(data);
        jsonData = JSON.parse(data);
        if (jsonData['type'] != 'off') {
            if (jsonData['type'] == 'config') {
                $('#page_config').show();
                $('#page_role').show();
                $('#page_wg').hide();
            } else {
                $('#page_config').hide();
                $('#page_role').hide();
                $('#page_wg').show();
            }

            if (jsonData['role'] == 'client') {
                $('#page_client').show();
                $('#page_server').hide();
            } else {
                $('#page_client').hide();
                $('#page_server').show();
            }
        } else {
            $('#page_role').hide();
            $('#page_config').hide();
            $('#page_wg').hide();
        }

        for(var key in jsonData){ 
            if (key == null) {
                return true;    // continue: return true; break: return false
            }
            //console.log(key + ":" + jsonData[key]);
            if (key == 'wg') {
                $('#' + key + '_text').html(jsonData[key]); 
            } else {
                $('#' + key).val(jsonData[key]); 
            }
        }
        
    });
}

function getOpenvpnStatus() {
    $.get('ajax/openvpn/get_openvpnstatus.php', function(data) {
        //console.log(data);
        jsonData = JSON.parse(data);
        for(var key in jsonData){ 
            if (key == null) {
                return true;    // continue: return true; break: return false
            }
            //console.log(key + ":" + jsonData[key]);

            $('#' + key).html(jsonData[key]); 
        }
    });
}

function loadOpenvpn() {
    getOpenvpnStatus();
    setInterval(getOpenvpnStatus, 60000);
    $.get('ajax/openvpn/get_openvpncfg.php', function(data) {
        // console.log(data);
        jsonData = JSON.parse(data);
        if (jsonData['type'] != 'off') {
            $('#page_role').show();
            if (jsonData['type'] == 'config') {
                $('#page_config').show();
                $('#page_ovpn').hide();
                $('#page_user_pwd').show();
            } else {
                $('#page_config').hide();
                $('#page_ovpn').show();
                $('#page_user_pwd').show();
            }

            if (jsonData['role'] == 'client') {
                $('#page_client').show();
                $('#page_server').hide();
            } else {
                $('#page_client').hide();
                $('#page_server').show();
            }

            if (jsonData['role'] == 'server') {
                $('#page_dh').show();
            } else {
                $('#page_dh').hide();
            }

            for(var key in jsonData){ 
                if (key == null) {
                    return true;    // continue: return true; break: return false
                }
                //console.log(key + ":" + jsonData[key]);
                if (key == 'ca' || key == 'ta' || key == 'cert' || key == 'key' || key == 'ovpn' || key == 'dh') {
                    $('#' + key + '_text').html(jsonData[key]); 
                } else if (key == 'comp_lzo' || key == 'enable_auth') {
                    $('#' + key).prop('checked', (jsonData[key] == '1') ? true:false);
                } else {
                    $('#' + key).val(jsonData[key]); 
                }
            }
        } else {
            $('#page_role').hide();
            $('#page_config').hide();
            $('#page_ovpn').hide();
            $('#page_user_pwd').hide();
        }
        
    });
}

function loadDataLorawan(){
    $.get('ajax/networking/get_loragw.php?type=lorawan', function(data) {
        jsonData = JSON.parse(data);

        var type = jsonData['type'];

        if (type != null) {
            $('#type').val(type);
        } else {
            $('#type').val('0');
        }

        typeChangeLorawan();
        
        var general = ['server_address', 'serv_port_up', 'serv_port_down', 'gateway_ID',
        'keepalive_interval', 'stat_interval', 'frequency'];

        general.forEach(function (info) {
            if (info == null) {
                return true;    // continue: return true; break: return false
            }

            $('#' + info).val(jsonData[info]);
        })

        var radio = ['radio0_enable', 'radio0_frequency', 'radio0_tx', 'radio0_tx_min', 'radio0_tx_max',
        'radio1_enable', 'radio1_frequency', 'radio1_tx'];

        radio.forEach(function (info) {
            if (info == null) {
                return true;    // continue: return true; break: return false
            }

            if (info == 'radio0_enable' || info == 'radio0_tx' || info == 'radio1_enable' ||
                info == 'radio1_tx') {
                $('#' + info).prop('checked', (jsonData[info] == '1') ? true:false);
            } else {
                $('#' + info).val(jsonData[info]);
            }
        })

        if (jsonData['radio0_enable'] == '1') {
            $('#page_radio0').show();
        } else {
            $('#page_radio0').hide();
        }

        if (jsonData['radio0_tx'] == '1') {
            $('#page_radio0_tx').show();
        } else {
            $('#page_radio0_tx').hide();
        }

        if (jsonData['radio1_enable'] == '1') {
            $('#page_radio1').show();
        } else {
            $('#page_radio1').hide();
        }

        var channels = ['channel_enable', 'channel_radio', 'channel_if'];
        for (var i = 0; i < 8; i++) {
            channels.forEach(function (info) {
                if (info == null) {
                    return true;    // continue: return true; break: return false
                }

                if (info == 'channel_enable') {
                    $('#' + info + i).prop('checked', (jsonData[info + i] == '1') ? true:false);
                } else {
                    $('#' + info + i).val(jsonData[info + i]);
                }
            })
        }

        // $general = array('protocol', 'uri', 'auth_mode', 'client_token');
        if (type == '2')
            $('#gateway_ID').val(jsonData['gateway_ID_station']);

        $('#protocol').val(jsonData['protocol']);
        $('#uri').val(jsonData['uri']);
        $('#auth_mode').val(jsonData['auth_mode']);
        modeChange();
    
        if (jsonData['lora_ca']) {
            $('#ca_text').html(jsonData['lora_ca']);
        }

        if (jsonData['lora_crt']) {
            $('#crt_text').html(jsonData['lora_crt']);
        }

        if (jsonData['lora_key']) {
            $('#key_text').html(jsonData['lora_key']);
        }
    });
}

function loadDDNSConfig() {
    $.get('ajax/networking/get_ddnscfg.php?type=ddns',function(data){
        jsonData = JSON.parse(data);
        var arr = ['interface', 'server_type', 'username', 'password', 'hostname', 'interval'];

        $('#enabled').val(jsonData.enabled);
        if (jsonData.enabled == '1') {
            $('#page_ddns').show();
            $('#ddns_enable').prop('checked', true);

            arr.forEach(function (info) {
                if (info == null) {
                    return true;    // continue: return true; break: return false
                }
                
                $('#' + info).val(jsonData[info]);
            })
        } else {
            $('#page_ddns').hide(); 
            $('#ddns_disable').prop('checked', true);
        }
    });
}

function loadWifiStations(refresh) {
    return function() {
        var complete = function() { $(this).removeClass('loading-spinner'); }
        var qs = refresh === true ? '?refresh' : '';
        $('.js-wifi-stations')
            .addClass('loading-spinner')
            .empty()
            .load('ajax/networking/wifi_stations.php'+qs, complete);
    };
}
$(".js-reload-wifi-stations").on("click", loadWifiStations(true));

$('.js-enable-wifi-stations').on('click', function() {
    let isChecked = $(this).is(':checked');

    var complete = function() { $(this).removeClass('loading-spinner'); }
    $('.js-wifi-stations').addClass('loading-spinner').empty().load('ajax/networking/wifi_stations.php?enable=' + (isChecked ? '1' : '0'), complete);
});

/*
Populates the wired network form fields
Option toggles are set dynamically depending on the loaded configuration
*/
function loadInterfaceWiredSelect(type) {
    var strInterface = $('#cbxdhcpiface').val();
    $.get('ajax/networking/get_netcfg.php?iface='+strInterface,function(data){
        jsonData = JSON.parse(data);
        if (type == "wired") {
            $('#txtipaddress').val(jsonData.StaticIP);
            $('#txtsubnetmask').val(jsonData.SubnetMask);
            $('#txtgateway').val(jsonData.StaticRouters);
            $('#default-route').prop('checked', jsonData.DefaultRoute);
            $('#txtdns1').val(jsonData.StaticDNS1);
            $('#txtdns2').val(jsonData.StaticDNS2);
            $('#txtmetric').val(jsonData.Metric);
            $('#wan-multi').prop('checked', (jsonData.wan_multi == '1') ? true : false);

            if (jsonData.StaticIP !== null && jsonData.StaticIP !== '') {
                $('#chkstatic').closest('.btn').button('toggle');
                $('#chkstatic').closest('.btn').button('toggle').blur();
                $('#chkstatic').blur();
                $('#chkfallback').prop('disabled', true);
                $('#static_ip').show(); 
            } else {
                $('#chkdhcp').closest('.btn').button('toggle');
                $('#chkdhcp').closest('.btn').button('toggle').blur();
                $('#chkdhcp').blur();
                $('#chkfallback').prop('disabled', false);
                $('#static_ip').hide();
            }
        } else if (type == "lte") {
           $('#txtapn').val(jsonData.Apn);
            $('#txtpin').val(jsonData.Pin);
            $('#txtusername').val(jsonData.ApnUser);
            $('#txtpassword').val(jsonData.ApnPass);
            $('#auth_type').val(jsonData.AuthType);
            $('#data_saving_mode').prop('checked', (jsonData.data_saving_mode == '1') ? true : false);
            $('#lte_metric').val(jsonData.lte_metric);

            if (jsonData.AuthType == 'none') {
                $('#username').hide();
                $('#password').hide();
            } else {
                $('#username').show();
                $('#password').show();
            }
        } else if (type == "wlan0") {
            $('#wlan0_txtipaddress').val(jsonData.StaticIP);
            $('#wlan0_txtsubnetmask').val(jsonData.SubnetMask);
            $('#wlan0_txtgateway').val(jsonData.StaticRouters);
            $('#wlan0_default-route').prop('checked', jsonData.DefaultRoute);
            $('#wlan0_txtdns1').val(jsonData.StaticDNS1);
            $('#wlan0_txtdns2').val(jsonData.StaticDNS2);
            $('#wlan0_txtmetric').val(jsonData.Metric);

            if (jsonData.StaticIP !== null && jsonData.StaticIP !== '') {
                $('#wlan0_chkstatic').closest('.btn').button('toggle');
                $('#wlan0_chkstatic').closest('.btn').button('toggle').blur();
                $('#wlan0_chkstatic').blur();
                $('#wlan0_chkfallback').prop('disabled', true);
                $('#static_ip').show(); 
            } else {
                console.log(jsonData.StaticIP);
                $('#wlan0_chkdhcp').closest('.btn').button('toggle');
                $('#wlan0_chkdhcp').closest('.btn').button('toggle').blur();
                $('#wlan0_chkdhcp').blur();
                $('#wlan0_chkfallback').prop('disabled', false);
                $('#static_ip').hide();
            }
        }
    });
}

/*
Populates the DHCP server form fields
Option toggles are set dynamically depending on the loaded configuration
*/
function loadInterfaceDHCPSelect() {
    var strInterface = $('#cbxdhcpiface').val();
    $.get('ajax/networking/get_netcfg.php?iface='+strInterface,function(data){
        jsonData = JSON.parse(data);
        $('#dhcp-iface')[0].checked = jsonData.DHCPEnabled;
        $('#txtipaddress').val(jsonData.StaticIP);
        $('#txtsubnetmask').val(jsonData.SubnetMask);
        $('#txtgateway').val(jsonData.StaticRouters);
        // $('#chkfallback')[0].checked = jsonData.FallbackEnabled;
        $('#default-route').prop('checked', jsonData.DefaultRoute);
        $('#txtrangestart').val(jsonData.RangeStart);
        $('#txtrangeend').val(jsonData.RangeEnd);
        $('#txtrangeleasetime').val(jsonData.leaseTime);
        $('#txtdns1').val(jsonData.DNS1);
        $('#txtdns2').val(jsonData.DNS2);
        $('#cbxrangeleasetimeunits').val(jsonData.leaseTimeInterval);
        // $('#no-resolv')[0].checked = jsonData.upstreamServersEnabled;
        $('#cbxdhcpupstreamserver').val(jsonData.upstreamServers[0]);
        $('#txtmetric').val(jsonData.Metric);

        // if (jsonData.StaticIP !== null && jsonData.StaticIP !== '' && !jsonData.FallbackEnabled) {
        //     $('#chkstatic').closest('.btn').button('toggle');
        //     $('#chkstatic').closest('.btn').button('toggle').blur();
        //     $('#chkstatic').blur();
        //     $('#chkfallback').prop('disabled', true);
        // } else {
        //     $('#chkdhcp').closest('.btn').button('toggle');
        //     $('#chkdhcp').closest('.btn').button('toggle').blur();
        //     $('#chkdhcp').blur();
        //     $('#chkfallback').prop('disabled', false);
        // }
        // if (jsonData.FallbackEnabled || $('#chkdhcp').is(':checked')) {
        //     $('#dhcp-iface').prop('disabled', true);
        // }
    });
}

function setDHCPToggles(state) {
    if ($('#chkfallback').is(':checked') && state) {
        $('#chkfallback').prop('checked', state);
    }
    if ($('#dhcp-iface').is(':checked') && !state) {
        $('#dhcp-iface').prop('checked', state);
    }

    $('#chkfallback').prop('disabled', state);
    $('#dhcp-iface').prop('disabled', !state);
    //$('#dhcp-iface').prop('checked', state);
}

function loadChannel() {
    $.get('ajax/networking/get_channel.php',function(data){
        var jsonData = JSON.parse(data);
        loadChannelSelect(jsonData);
    });
}

$('#hostapdModal').on('shown.bs.modal', function (e) {
    var seconds = 9;
    var countDown = setInterval(function(){
      if(seconds <= 0){
        clearInterval(countDown);
      }
      var pct = Math.floor(100-(seconds*100/9));
      document.getElementsByClassName('progress-bar').item(0).setAttribute('style','width:'+Number(pct)+'%');
      seconds --;
    }, 1000);
});

$('#configureClientModal').on('shown.bs.modal', function (e) {
});

$('#ovpn-confirm-delete').on('click', '.btn-delete', function (e) {
    var cfg_id = $(this).data('recordId');
    $.post('ajax/openvpn/del_ovpncfg.php',{'cfg_id':cfg_id},function(data){
        jsonData = JSON.parse(data);
        $("#ovpn-confirm-delete").modal('hide');
        var row = $(document.getElementById("openvpn-client-row-" + cfg_id));
        row.fadeOut( "slow", function() {
            row.remove();
        });
    });
});

$('#ovpn-confirm-delete').on('show.bs.modal', function (e) {
    var data = $(e.relatedTarget).data();
    $('.btn-delete', this).data('recordId', data.recordId);
});

$('#ovpn-confirm-activate').on('click', '.btn-activate', function (e) {
    var cfg_id = $(this).data('record-id');
    $.post('ajax/openvpn/activate_ovpncfg.php',{'cfg_id':cfg_id},function(data){
        jsonData = JSON.parse(data);
        $("#ovpn-confirm-activate").modal('hide');
        setTimeout(function(){
            window.location.reload();
        },300);
    });
});

$('#ovpn-confirm-activate').on('shown.bs.modal', function (e) {
    var data = $(e.relatedTarget).data();
    $('.btn-activate', this).data('recordId', data.recordId);
});

$('#ovpn-userpw,#ovpn-certs').on('click', function (e) {
    if (this.id == 'ovpn-userpw') {
        $('#PanelCerts').hide();
        $('#PanelUserPW').show();
    } else if (this.id == 'ovpn-certs') {
        $('#PanelUserPW').hide();
        $('#PanelCerts').show();
    }
});

// $(document).ready(function(){
//   $("#PanelManual").hide();
// });

$('#wg-upload,#wg-manual').on('click', function (e) {
    if (this.id == 'wg-upload') {
        $('#PanelManual').hide();
        $('#PanelUpload').show();
    } else if (this.id == 'wg-manual') {
        $('#PanelUpload').hide();
        $('#PanelManual').show();
    }
});

// Add the following code if you want the name of the file appear on select
$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});

/*
Sets the wirelss channel select options based on hw_mode and country_code.

Methodology: In North America up to channel 11 is the maximum allowed WiFi 2.4Ghz channel,
except for the US that allows channel 12 & 13 in low power mode with additional restrictions.
Canada allows channel 12 in low power mode. Because it's unsure if low powered mode can be
supported the channels are not selectable for those countries. Also Uzbekistan and Colombia
allow up to channel 11 as maximum channel on the 2.4Ghz WiFi band.
Source: https://en.wikipedia.org/wiki/List_of_WLAN_channels
Additional: https://git.kernel.org/pub/scm/linux/kernel/git/sforshee/wireless-regdb.git
*/
// function loadChannelSelect(selected) {
//     // Fetch wireless regulatory data
//     $.getJSON("config/wireless.json", function(json) {
//         var hw_mode = $('#cbxhwmode').val();
//         var country_code = $('#cbxcountries').val();
//         var channel_select = $('#cbxchannel');
//         var data = json["wireless_regdb"];
//         var selectablechannels = Array.range(1,14);

//         // Assign array of countries to valid frequencies (channels)
//         var countries_2_4Ghz_max11ch = data["2_4GHz_max11ch"].countries;
//         var countries_2_4Ghz_max14ch = data["2_4GHz_max14ch"].countries;
//         var countries_5Ghz_max48ch = data["5Ghz_max48ch"].countries;

//         // Map selected hw_mode and country to determine channel list
//         if (hw_mode === 'a') {
//             selectablechannels = data["5Ghz_max48ch"].channels;
//         } else if (($.inArray(country_code, countries_2_4Ghz_max11ch) !== -1) && (hw_mode !== 'ac') ) {
//             selectablechannels = data["2_4GHz_max11ch"].channels;
//         } else if (($.inArray(country_code, countries_2_4Ghz_max14ch) !== -1) && (hw_mode === 'b')) {
//             selectablechannels = data["2_4GHz_max14ch"].channels;
//         } else if (($.inArray(country_code, countries_5Ghz_max48ch) !== -1) && (hw_mode === 'ac')) {
//             selectablechannels = data["5Ghz_max48ch"].channels;
//         }

//         // Set channel select with available values
//         selected = (typeof selected === 'undefined') ? selectablechannels[0] : selected;
//         channel_select.empty();
//         $.each(selectablechannels, function(key,value) {
//             channel_select.append($("<option></option>").attr("value", value).text(value));
//         });
//         channel_select.val(selected);
//     });
// }

function loadChannelSelect(selected) {
    var hw_mode = $('#cbxhwmode').val();
    var channel_select = $('#cbxchannel');
    var btn_save = $('#btnSaveHostapd');
    if (selected === null || typeof selected === 'undefined') {
        selected = $('#cbxchannel').val();
    }
    var selectableChannels = [];

    // Map selected hw_mode to available channels
    if (hw_mode === 'b' || hw_mode === 'g' || hw_mode === 'n') {
        selectableChannels = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'];
    } else {
        selectableChannels = ['34', '36', '38', '40', '42', '44', '46', '48', '149', '153', '157', '161', '165'];
    }

    // Set channel select with available values
    channel_select.empty();
    if (selectableChannels[0] === null) {
        channel_select.append($("<option></option>").attr("value", "").text("---"));
        channel_select.prop("disabled", true);
        btn_save.prop("disabled", true);
    } else {
        channel_select.prop("disabled", false);
        btn_save.prop("disabled", false);

        selectableChannels.forEach(channel => {
            channel_select.append($("<option></option>").attr("value", channel).text(channel));
        });
        channel_select.val(selected);
    }
}

/* Updates the selected blocklist
 * Request is passed to an ajax handler to download the associated list.
 * Interface elements are updated to indicate current progress, status.
 */
function updateBlocklist() {
    var blocklist_id = $('#cbxblocklist').val();
    if (blocklist_id == '') { return; }
    $('#cbxblocklist-status').find('i').removeClass('fas fa-check').addClass('fas fa-cog fa-spin');
    $('#cbxblocklist-status').removeClass('check-hidden').addClass('check-progress');
    $.post('ajax/adblock/update_blocklist.php',{ 'blocklist_id':blocklist_id },function(data){
        var jsonData = JSON.parse(data);
        if (jsonData['return'] == '0') {
            $('#cbxblocklist-status').find('i').removeClass('fas fa-cog fa-spin').addClass('fas fa-check');
            $('#cbxblocklist-status').removeClass('check-progress').addClass('check-updated').delay(500).animate({ opacity: 1 }, 700);
            $('#'+blocklist_id).text("Just now");
        }
    })
}

function clearBlocklistStatus() {
    $('#cbxblocklist-status').removeClass('check-updated').addClass('check-hidden');
}

// Handler for the wireguard generate key button
$('.wg-keygen').click(function(){
    var entity_pub = $(this).parent('div').prev('input[type="text"]');
    var entity_priv = $(this).parent('div').next('input[type="hidden"]');
    var updated = entity_pub.attr('name')+"-pubkey-status";
    $.post('ajax/networking/get_wgkey.php',{'entity':entity_pub.attr('name') },function(data){
        var jsonData = JSON.parse(data);
        entity_pub.val(jsonData.pubkey);
        $('#wg-srvprikey').val(jsonData.privkey);
        $('#' + updated).removeClass('check-hidden').addClass('check-updated').delay(500).animate({ opacity: 1 }, 700);
    })
})

// Handler for wireguard client.conf download
$('.wg-client-dl').click(function(){
    var req = new XMLHttpRequest();
    var url = 'ajax/networking/get_wgcfg.php?type=download';
    req.open('get', url, true);
    req.responseType = 'blob';
    req.setRequestHeader('Content-type', 'text/plain; charset=UTF-8');
    req.onreadystatechange = function (event) {
        if(req.readyState == 4 && req.status == 200) {
            var blob = req.response;
            var link=document.createElement('a');
            link.href=window.URL.createObjectURL(blob);
            link.download = 'client.conf';
            link.click();
        }
    }
    req.send();
})

// Event listener for Bootstrap's form validation
window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
          //console.log(event.submitter);
          if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
    });
}, false);

let sessionCheckInterval = setInterval(checkSession, 5000);

function checkSession() {
    // skip session check if on login page
    if (window.location.pathname === '/login') {
        return;
    }
    var csrfToken = $('meta[name=csrf_token]').attr('content');
    $.post('ajax/session/do_check_session.php',{'csrf_token': csrfToken},function (data) {
        if (data.status === 'session_expired') {
            clearInterval(sessionCheckInterval);
            showSessionExpiredModal();
        }
    }).fail(function (jqXHR, status, err) {
        console.error("Error checking session status:", status, err);
    });
}

function showSessionExpiredModal() {
    $('#sessionTimeoutModal').modal('show');
}

$(document).on("click", "#js-session-expired-login", function(e) {
    const loginModal = $('#modal-admin-login');
    const redirectUrl = window.location.pathname;
    window.location.href = `/login?action=${encodeURIComponent(redirectUrl)}`;
});

// show modal login on page load
$(document).ready(function () {
    const params = new URLSearchParams(window.location.search);
    const redirectUrl = $('#redirect-url').val() || params.get('action') || '/';
    $('#modal-admin-login').modal('show');
    $('#redirect-url').val(redirectUrl);
    $('#username').focus();
    $('#username').addClass("focusedInput");
});

// Static Array method
Array.range = (start, end) => Array.from({length: (end - start)}, (v, k) => k + start);

$(document).on("click", ".js-toggle-password", function(e) {
    var button = $(e.currentTarget);
    var field  = $(button.data("bsTarget"));
    if (field.is(":input")) {
        e.preventDefault();

        if (!button.data("__toggle-with-initial")) {
            $("i", button).removeClass("fas fa-eye").addClass(button.attr("data-toggle-with"));
        }

        if (field.attr("type") === "password") {
            field.attr("type", "text");
        } else {
            $("i", button).removeClass("fas fa-eye-slash").addClass("fas fa-eye");
            field.attr("type", "password");
        }
    }
});

$(function() {
    $('#theme-select').change(function() {
        var theme = themes[$( "#theme-select" ).val() ]; 
        set_theme(theme);
   });
});

function set_theme(theme) {
    $('link[title="main"]').attr('href', 'app/css/' + theme);
    // persist selected theme in cookie 
    setCookie('theme',theme,90);
}

$(function() {
    $('#night-mode').change(function() {
        var state = $(this).is(':checked');
        if (state == true && getCookie('theme') != 'lightsout.css') {
            set_theme('lightsout.css');
        } else {
            set_theme('custom.php');
        }
   });
});

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var regx = new RegExp(cname + "=([^;]+)");
    var value = regx.exec(document.cookie);
    return (value != null) ? unescape(value[1]) : null;
}

// Define themes
var themes = {
    "default": "custom.php",
    "hackernews" : "hackernews.css",
    "lightsout" : "lightsout.css",
}

// Toggles the sidebar navigation.
// Overrides the default SB Admin 2 behavior
$("#sidebarToggleTopbar").on('click', function(e) {
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled d-none");
});

// Overrides SB Admin 2
$("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
    var toggled = $(".sidebar").hasClass("toggled");
    // Persist state in cookie
    setCookie('sidebarToggled',toggled, 90);
});

// $(function() {
//     if ($(window).width() < 768) {
//         $('.sidebar').addClass('toggled');
//         setCookie('sidebarToggled',false, 90);
//     }
// });

// $(window).on("load resize",function(e) {
//     if ($(window).width() > 768) {
//         $('.sidebar').removeClass('d-none d-md-block');
//         if (getCookie('sidebarToggled') == 'false') {
//             $('.sidebar').removeClass('toggled');
//         }
//     }
// });

// Adds active class to current nav-item
$(window).bind("load", function() {
    var url = window.location;
    $('ul.navbar-nav a').filter(function() {
      return this.href == url;
    }).parent().addClass('active');
});

function freqPlanChange() {
    var a = document.getElementById('frequency').value;
    if (a == '0') { // EU868
        $('#radio0_frequency').val('867500000');
        $('#radio0_tx_min').val('863000000');
        $('#radio0_tx_max').val('870000000');
        $('#radio1_frequency').val('868500000');
    } else if (a == '1') { // CN490
        $('#radio0_frequency').val('471400000');
        $('#radio0_tx_min').val('500000000');
        $('#radio0_tx_max').val('510000000');
        $('#radio1_frequency').val('475000000');
    } else if (a == '2') { // US915
        $('#radio0_frequency').val('904300000');
        $('#radio0_tx_min').val('923000000');
        $('#radio0_tx_max').val('928000000');
        $('#radio1_frequency').val('905000000');
    } else if (a == '3') { // AU915
        $('#radio0_frequency').val('917200000');
        $('#radio0_tx_min').val('915000000');
        $('#radio0_tx_max').val('928000000');
        $('#radio1_frequency').val('917900000');
    }  else if (a == '4') { // AS923
        $('#radio0_frequency').val('922300000');
        $('#radio0_tx_min').val('920000000');
        $('#radio0_tx_max').val('924000000');
        $('#radio1_frequency').val('923100000');
    }
}

// Forwards
function openBoxForwards() {
    $('#forwards_popBox').show();
    $('#forwards_popLayer').show();
    document.getElementById("forwards_popBox").scrollTop = 0;
}

function closeBoxForwards() {
    $('#forwards_popBox').hide();
    $('#forwards_popLayer').hide();
}

function addDataForwards() {
    openBoxForwards();
    document.getElementById("forwards.page_type").value = "0"; /* 0 is add. other is edit */
}

function getTableDataForwards() {
    var tr = $("#table_forwards tr");
    var result = [];
    for (var i = 2; i < tr.length; i++) {
        var tds = $(tr[i]).find("td");
        if (tds.length > 0) {
            result.push({
                'name':$(tds[0]).html(), 
                'proto':$(tds[1]).html(),
                'src_port':$(tds[2]).html(),
                'dest_ip':$(tds[3]).html(),
                'dest_port':$(tds[4]).html(),
                'enabled':$(tds[5]).html()
            });
        }
    }

    return result;
}
  
function delForwards(object) {
    var table = object.parentNode.parentNode.parentNode;
    var tr = object.parentNode.parentNode;
    table.removeChild(tr);

    var result = getTableDataForwards();
    var json_data = JSON.stringify(result);
    $('#hidForwards').val(json_data);
}

function saveForwards() {
    var name = document.getElementById("forwards.name").value;
    var proto = document.getElementById("forwards.proto").value;
    var src_port = document.getElementById("forwards.src_port").value;
    var dest_ip = document.getElementById("forwards.dest_ip").value;
    var dest_port = document.getElementById("forwards.dest_port").value;
    var enabled = document.getElementById("forwards.enabled").checked;
    var page_type = document.getElementById("forwards.page_type").value;

    if (proto == 'any' || proto == 'icmp' || proto == 'gre') {
        src_port = '-';
        dest_port = '-';
    }

    if (page_type == "0") {
        var table = document.getElementsByTagName("table")[0];
        table.innerHTML += "<tr  class=\"tr cbi-section-table-descr\">\n" +
            "        <td style='text-align:center'>"+ (name.length > 0 ? name : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (proto.length > 0 ? proto : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (src_port.length > 0 ? src_port : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (dest_ip.length > 0 ? dest_ip : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (dest_port.length > 0 ? dest_port : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ enabled +"</td>\n" +
            "        <td><a href=\"javascript:void(0);\" onclick=\"editForwards(this);\" >Edit</a></td>\n" +
            "        <td><a href=\"javascript:void(0);\" onclick=\"delForwards(this);\" >Del</a></td>\n" +
            "    </tr>";
    } else {
        var table = document.getElementById("table_forwards");
        var num = 0;
        table.rows[Number(page_type)].cells[num++].innerHTML = (name.length > 0 ? name : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (proto.length > 0 ? proto : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (src_port.length > 0 ? src_port : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (dest_ip.length > 0 ? dest_ip : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (dest_port.length > 0 ? dest_port : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = enabled;
    }

    result = getTableDataForwards();
    var json_data = JSON.stringify(result);
    $('#hidForwards').val(json_data);
    closeBoxForwards();
}

function editForwards(object) {
    var row = $(object).parent().parent().parent().prevAll().length + 1;
    var num = 0;
    document.getElementById("forwards.page_type").value = row;

    var value = $(object).parent().parent().find("td");
    var name = value.eq(num++).text();
    var proto = value.eq(num++).text();
    var src_port = value.eq(num++).text();
    var dest_ip = value.eq(num++).text();
    var dest_port = value.eq(num++).text();
    var enabled = value.eq(num++).text();

    document.getElementById("forwards.name").value = name;
    document.getElementById("forwards.proto").value = proto;
    document.getElementById("forwards.src_port").value = src_port;
    document.getElementById("forwards.dest_ip").value = dest_ip;
    document.getElementById("forwards.dest_port").value = dest_port;
    if (enabled == "true") {
        document.getElementById("forwards.enabled").checked = true;
    } else {
        document.getElementById("forwards.enabled").checked = false;
    }

    if (proto == 'tcp udp' || proto == 'tcp' || proto == 'udp' || proto == 'stcp') {
        $('#pageEport').show();
        $('#pageIPort').show();
    } else {
        $('#pageEport').hide();
        $('#pageIPort').hide();
    }

    openBoxForwards();
}

// traffic
function openBoxTraffic() {
    $('#traffic_popBox').show();
    $('#traffic_popLayer').show();
    document.getElementById("traffic_popBox").scrollTop = 0;
}

function closeBoxTraffic() {
    $('#traffic_popBox').hide();
    $('#traffic_popLayer').hide();
}

function addDataTraffic() {
    openBoxTraffic();
    document.getElementById("traffic.page_type").value = "0"; /* 0 is add. other is edit */
}

function getTableDataTraffic() {
    var tr = $("#table_traffic tr");
    var result = [];
    for (var i = 2; i < tr.length; i++) {
        var tds = $(tr[i]).find("td");
        if (tds.length > 0) {
            result.push({
                'name':$(tds[0]).html(), 
                'proto':$(tds[1]).html(),
                'rule':$(tds[2]).html(),
                'src_mac':$(tds[3]).html(),
                'src_ip':$(tds[4]).html(),
                'src_port':$(tds[5]).html(),
                'dest_ip':$(tds[6]).html(),
                'dest_port':$(tds[7]).html(),
                'action':$(tds[8]).html(),
                'enabled':$(tds[9]).html()
            });
        }
    }

    return result;
}
  
function delTraffic(object) {
    var table = object.parentNode.parentNode.parentNode;
    var tr = object.parentNode.parentNode;
    table.removeChild(tr);

    var result = getTableDataTraffic();
    var json_data = JSON.stringify(result);
    $('#hidTraffic').val(json_data);
}

function saveTraffic() {
    var name = document.getElementById("traffic.name").value;
    var proto = document.getElementById("traffic.proto").value;
    var rule = document.getElementById("traffic.rule").value;
    var src_mac = document.getElementById("traffic.src_mac").value;
    var src_ip = document.getElementById("traffic.src_ip").value;
    var src_port = document.getElementById("traffic.src_port").value;
    var dest_ip = document.getElementById("traffic.dest_ip").value;
    var dest_port = document.getElementById("traffic.dest_port").value;
    var action = document.getElementById("traffic.action").value;
    var enabled = document.getElementById("traffic.enabled").checked;
    var page_type = document.getElementById("traffic.page_type").value;

    if (proto == 'any' || proto == 'icmp' || proto == 'gre') {
        src_port = '-';
        dest_port = '-';
    }

    if (page_type == "0") {
        var table = document.getElementsByTagName("table")[1];
        table.innerHTML += "<tr  class=\"tr cbi-section-table-descr\">\n" +
            "        <td style='text-align:center'>"+ (name.length > 0 ? name : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (proto.length > 0 ? proto : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (rule.length > 0 ? rule : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (src_mac.length > 0 ? src_mac : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (src_ip.length > 0 ? src_ip : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (src_port.length > 0 ? src_port : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (dest_ip.length > 0 ? dest_ip : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (dest_port.length > 0 ? dest_port : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (action.length > 0 ? action : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ enabled +"</td>\n" +
            "        <td><a href=\"javascript:void(0);\" onclick=\"editTraffic(this);\" >Edit</a></td>\n" +
            "        <td><a href=\"javascript:void(0);\" onclick=\"delTraffic(this);\" >Del</a></td>\n" +
            "    </tr>";
    } else {
        var table = document.getElementById("table_traffic");
        var num = 0;
        table.rows[Number(page_type)].cells[num++].innerHTML = (name.length > 0 ? name : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (proto.length > 0 ? proto : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (rule.length > 0 ? rule : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (src_mac.length > 0 ? src_mac : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (src_ip.length > 0 ? src_ip : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (src_port.length > 0 ? src_port : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (dest_ip.length > 0 ? dest_ip : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (dest_port.length > 0 ? dest_port : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (action.length > 0 ? action : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = enabled;
    }

    result = getTableDataTraffic();
    var json_data = JSON.stringify(result);
    $('#hidTraffic').val(json_data);
    closeBoxTraffic();
}

function editTraffic(object) {
    var row = $(object).parent().parent().parent().prevAll().length + 1;
    var num = 0;
    document.getElementById("traffic.page_type").value = row;

    var value = $(object).parent().parent().find("td");

    var arrTraffic = ['name', 'proto', 'rule', 'src_mac', 'src_ip', 'src_port', 
                        'dest_ip', 'dest_port', 'action', 'enabled'];
    
    arrTraffic.forEach(function (info) {
        if (info == null) {
            return true;    // continue: return true; break: return false
        }

        var tmp = value.eq(num++).text();
        if (info == 'enabled') {
            if (tmp == 'true') {
                document.getElementById('traffic.' + info).checked = true;
            } else {
                document.getElementById('traffic.' + info).checked = false;
            }
        } else {
            document.getElementById('traffic.' + info).value = tmp;
        }
    })

    var proto = document.getElementById("traffic.proto").value;
    if (proto == 'tcp udp' || proto == 'tcp' || proto == 'udp' || proto == 'stcp') {
        $('#pageSrcPort').show();
        $('#pageDestPort').show();
    } else {
        $('#pageSrcPort').hide();
        $('#pageDestPort').hide();
    }

    openBoxTraffic();
}

function enablePage(state, name) {
    if (state) {
        $('#page_' + name).show();
    } else {
        $('#page_' + name).hide();
    }
}

function typeChangeLorawan() {
    var type = document.getElementById('type').value;
    if (type == '0') {
        $('#page_eui').hide();
        $('#page_packet_forwarder').hide();
        $('#page_basic_station').hide();
    } else if (type == '1') {
        $('#page_eui').show();
        $('#page_packet_forwarder').show();
        $('#page_basic_station').hide();
    } else if (type == '2') {
        modeChange();
        $('#page_eui').show();
        $('#page_packet_forwarder').hide();
        $('#page_basic_station').show();
    }
}

function modeChange() {
    var mode = document.getElementById('auth_mode').value;

    if (mode == '0') {
        $('#page_one').hide();
        $('#page_two').hide();
    } else if (mode == '1') {
        $('#page_one').show();
        $('#page_two').show();
    } else if (mode == '2') {
        $('#page_one').hide();
        $('#page_two').show();
    }
}

function caFileChangeLora() {
    $('#ca_text').html($('#lora_ca')[0].files[0].name);
}

function cerFileChangeLora() {
    $('#crt_text').html($('#lora_crt')[0].files[0].name);
}

function keyFileChangeLora() {
    $('#key_text').html($('#lora_key')[0].files[0].name);
}

function downloadFile(conf_name) {
    let lowerConfName;
    if (conf_name == 'IO') {
        lowerConfName = document.getElementById("page_im_ex_name").value;
    } else {
        lowerConfName = conf_name.toLowerCase();
    }
    
    var req = new XMLHttpRequest();
    var url = 'ajax/dct/get_dctcfg.php?type=download_' + lowerConfName;
    req.open('get', url, true);
    req.responseType = 'blob';
    req.setRequestHeader('Content-type', 'text/plain; charset=UTF-8');
    req.onreadystatechange = function (event) {
        if(req.readyState == 4 && req.status == 200) {
            var blob = req.response;
            var link=document.createElement('a');
            link.href=window.URL.createObjectURL(blob);
            const now = new Date();
            const year = now.getFullYear();
            const month = ('0' + (now.getMonth() + 1)).slice(-2);
            const day = ('0' + now.getDate()).slice(-2);
            const hours = ('0' + now.getHours()).slice(-2);
            const minutes = ('0' + now.getMinutes()).slice(-2);
            const seconds = ('0' + now.getSeconds()).slice(-2);
            const formattedTime = year + month + day + hours + minutes;
            link.download = lowerConfName + '_' + formattedTime + '.csv';
            link.click();
        }
    }
    req.send();
}

$(document).ready(function(){
    $('.sidebar li a').each(function(){
        if ($($(this))[0].href == String(window.location)) {
        $(this).parent().addClass('active');
        }
    });

    $('.nav-item').each(function() {
        if ($(this).hasClass('active')) {
            var id = $($(this))[0].id;
            if (id.includes('dct_')) {
                if (id.includes('dct_south')) {
                    $('#navbar-collapse-south').addClass('show')
                    $('#south').removeClass('collapsed');
                } else if (id.includes('dct_north')) {
                    $('#navbar-collapse-north').addClass('show')
                    $('#north').removeClass('collapsed');
                }

                $('#navbar-collapse-dct').addClass('show')
                $('#dct').removeClass('collapsed');
            } else if (id.includes('remote_')) {
                if (id.includes('remote_vpn')) {
                    $('#navbar-collapse-vpn').addClass('show');
                    $('#vpn').removeClass('collapsed');
                }

                $('#navbar-collapse-remote').addClass('show');
                $('#remote').removeClass('collapsed');
            } else if (id.includes('network_')) {
                if (id.includes('network_wan')) {
                    $('#navbar-collapse-wan').addClass('show')
                    $('#wan').removeClass('collapsed');
                }

                $('#navbar-collapse-network').addClass('show');
                $('#network').removeClass('collapsed');
            } else if (id.includes('convert_')) {
                $('#navbar-collapse-convert').addClass('show');
                $('#convert').removeClass('collapsed');
            } else if (id.includes('services_')) {
                $('#navbar-collapse-services').addClass('show');
                $('#services').removeClass('collapsed');
            }  else if (id.includes('system_')) {
                $('#navbar-collapse-system').addClass('show');
                $('#system').removeClass('collapsed');
            }
        }
    });

    function itemChange(id) {
        var idArr = ['dct', 'remote', 'network', 'protocol_convert', 'services', 'system'];
        if (id.includes('page_')) {
        var key = id.slice(5);
        // console.log(key);
        if (idArr.includes(key)) {
            idArr.forEach(function (info) {
                if (id != 'page_' + info) {
                // console.log("info:" + info);
                if ($('#navbar-collapse-' + info).hasClass('show')) {
                    $('#navbar-collapse-' + info).removeClass('show');
                    $('#' + info).addClass('collapsed');
                }
                }
            });
        }
        }
    }

    $('.nav-item').click(function() {
        var id = $($(this))[0].id;
        itemChange(id);
    });
});

function downloadBackup() {
    fetch("ajax/system/system.php?type=download_backup")
    .then(response => response.blob())
    .then(blob => {
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        const now = new Date();
        const year = now.getFullYear();
        const month = ('0' + (now.getMonth() + 1)).slice(-2);
        const day = ('0' + now.getDate()).slice(-2);
        const hours = ('0' + now.getHours()).slice(-2);
        const minutes = ('0' + now.getMinutes()).slice(-2);
        const formattedTime = year + month + day + hours + minutes;
        link.download = 'backup-elastpro-' + formattedTime + '.tar.gz';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    })
    .catch(error => console.error("Fail to download:", error));
}

function actionBackupFile() {
    $('#hostapdModal').modal('show'); 
    fetch("ajax/system/system.php?type=action_backup")
    
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert(data.message);
        }
    })
    .catch(error => console.error("Fail to action:", error));
}


function modbusRouterModeChange()
{
    var mode = document.getElementById('mode');
    if (mode.value == '0') {
        $('#page_rtu_to_tcp').show();
    } else {
        $('#page_rtu_to_tcp').hide();
    }
}

function enableModbusRouterCom(checkbox, num)
{
    if (checkbox.checked == true) {
        $('#page_modbus_router_com' + num).show();
    } else {
        $('#page_modbus_router_com' + num).hide();
    }
}

function enableModbusRouter(state) {
    if (state) {
      $('#page_modbus_router').show();
      modbusRouterModeChange();
    } else {
      $('#page_modbus_router').hide();
    }
}

function disableValidation(form) {
    form.removeAttribute("novalidate");
    form.classList.remove("needs-validation");
    form.querySelectorAll("[required]").forEach(function (field) {
        field.removeAttribute("required");
    });
}

function setCSRFTokenHeader(event, xhr, settings) {
    var csrfToken = $('meta[name=csrf_token]').attr('content');
    if (/^(POST|PATCH|PUT|DELETE)$/i.test(settings.type)) {
        xhr.setRequestHeader("X-CSRF-Token", csrfToken);
    }
}

function contentLoaded() {
    pageCurrent = window.location.href.split("/").pop();
    switch(pageCurrent) {
        case "dashboard":
            loadDashboard();
            break;
        case "wired_conf":
            loadInterfaceWiredSelect("wired");
            break;
        case "lte_conf":
            loadInterfaceWiredSelect("lte");
            break;
        case "wlan0_conf":
            loadInterfaceWiredSelect("wlan0");
            break;
        case "hostapd_conf":
            loadChannel();
            break;
        case "dhcpd_conf":
            loadInterfaceDHCPSelect();
            break;
        case "basic_conf":
            loadBasicConfig();
            break;
        case "interfaces_conf":
            loadInterfacesConfig();
            break;
        case "modbus_conf":
        case "ascii_conf":
        case "s7_conf":
		case "fx_conf":
        case "mc_conf":
        case "iec104_conf":
        case "opcuacli_conf":
        case "baccli_conf":
        case "dnp3cli_conf":
        case "ethernetip_conf":
        case "mbuscli_conf":
        case "snmpcli_conf":
        case "iec1107_conf":
        case "dlms_conf":
            loadRulesConfig(pageCurrent.split('_')[0]);
            break;
        case "io_conf":
            loadRulesConfig('adc');
            loadRulesConfig('di');
            loadRulesConfig('do');
            break;
        case "server_conf":
            loadServerConfig();
            break;
        case "ddns":
            loadDDNSConfig();
            break;
        case "opcua":
            loadOpcuaConfig();
            break;
        case "bacnet":
            loadBACnetConfig();
            break;
        case "dnp3":
            loadDnp3Config();
            break;
        case "modbus_slave":
            loadModbusSlaveConfig();
            break;
        case "datadisplay":
            loadDataDisplay();
            break;
        case "lorawan_conf":
            loadDataLorawan();
            break;
        case "openvpn":
            loadOpenvpn();
            break;
        case "wireguard":
            loadWireguard();
            break;
        case "gps":
            loadGps();
            break;
        case "bacnet_router":
            loadBacnetRouter();
            break;
        case "firewall_conf":
            loadFirewall();
            break;
        case "iotedge":
            loadIotedge();
            break;
    }
}

$(document)
    .ajaxSend(setCSRFTokenHeader)
    .ready(contentLoaded)
    .ready(loadWifiStations());