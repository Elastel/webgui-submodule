export function enableBACnet(state) {
    if (state) {
      $('#page_bacnet').show();
    } else {
      $('#page_bacnet').hide();
    }
}

globalThis.enableBACnet = enableBACnet;

export function initBacnetRouter() {
    // console.info("ElastPro bacnet router module initialized");
    function loadBacnetRouter() {
        $.get('ajax/service/get_service.php?type=bacnet_router', function(data) {
            // console.log(data);
            const jsonData = JSON.parse(data);
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
    
    globalThis.loadBacnetRouter = loadBacnetRouter;
    loadBacnetRouter();
}