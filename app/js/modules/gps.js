
export function initGps() {
    // console.info("ElastPro gps ajax module initialized");
    function loadGps() {
        $.get('ajax/service/get_service.php?type=gps', function(data) {
            //console.log(data);
            const jsonData = JSON.parse(data);
            // const arr = ['output_mode', 'server_addr', 'server_port', 'report_mode', 'register_packet',
            // 'heartbeat_packet', 'report_interval', 'heartbeat_interval', 'baudrate', 'databit', 'stopbit',
            // 'parity', 'accuracy'];

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
    
    loadGps();
}