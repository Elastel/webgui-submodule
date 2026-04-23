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

export function getAllInterfaces() {
    $.get('ajax/networking/get_all_interfaces.php',function(data){
        jsonData = JSON.parse(data);
        $.each(jsonData,function(ind,value){
            loadSummary(value)
        });
    });
}

export function initNetworking(type) {
    // console.info("ElastPro Networking ajax module initialized");

    $('#btnSummaryRefresh').click(function(){getAllInterfaces();});

    $('.intsave').click(function(){
        var int = $(this).data('int');
        saveNetworkSettings(int);
    });
    
    $('.intapply').click(function(){
        applyNetworkSettings();
    });

    function loadInterfaceWiredSelect(type) {
        var strInterface = $('#cbxdhcpiface').val();
        $.get('ajax/networking/get_netcfg.php?iface='+strInterface,function(data){
            const jsonData = JSON.parse(data);
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
                    $('#wlan0_chkdhcp').closest('.btn').button('toggle');
                    $('#wlan0_chkdhcp').closest('.btn').button('toggle').blur();
                    $('#wlan0_chkdhcp').blur();
                    $('#wlan0_chkfallback').prop('disabled', false);
                    $('#static_ip').hide();
                }
            }
        });
    }

    globalThis.loadInterfaceWiredSelect = loadInterfaceWiredSelect;
    loadInterfaceWiredSelect(type);
}