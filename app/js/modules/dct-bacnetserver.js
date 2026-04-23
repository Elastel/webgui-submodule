export function enableBACnet(state) {
    if (state) {
      $('#page_bacnet').show();
    } else {
      $('#page_bacnet').hide();
    }
}

globalThis.enableBACnet = enableBACnet;

export function enableBBMD() {
    var state = document.getElementById('bbmd_enabled');
    if (state) {
        var checked = state.checked;

        if (checked) {
            $('#page_bbmd').show();
        } else {
            $('#page_bbmd').hide();
        }
    }
}

globalThis.enableBBMD = enableBBMD;

export function bacnetProtocolChange()
{
    if ($('#proto').val() == '0') {
        $('#page_proto_ip').show();
        $('#page_proto_mstp').hide();
    } else {
        $('#page_proto_ip').hide();
        $('#page_proto_mstp').show();
    }
    enableBBMD();
}

globalThis.bacnetProtocolChange = bacnetProtocolChange;

export function initDctBacnetServer() {
    /*BACnet Server*/
    function loadBACnetConfig() {
        $.get('ajax/dct/get_dctcfg.php?type=bacnet',function(data){
            const jsonData = JSON.parse(data);
            var arr = ['proto', 'ifname', 'port', 'interface', 'baudrate', 'mac',
                        'max_master', 'frames', 'device_id', 'object_name'];

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
            bacnetProtocolChange();
        });
    }


    globalThis.loadBACnetConfig = loadBACnetConfig;
    loadBACnetConfig();
}