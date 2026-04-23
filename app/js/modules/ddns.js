export function initDDNS() {
    // console.info("ElastPro DDNS module initialized");

    function loadDDNSConfig() {
        $.get('ajax/networking/get_ddnscfg.php?type=ddns',function(data){
            const jsonData = JSON.parse(data);
            const arr = ['interface', 'server_type', 'username', 'password', 'hostname', 'interval'];

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

    loadDDNSConfig();
}