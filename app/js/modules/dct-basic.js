export function enableBasic(state) {
    if (state) {
    $('#page_basic').show();
    enableCache(document.getElementById('cache_enabled'));
    enableSystemReport(document.getElementById('system_enabled'));
    } else {
    $('#page_basic').hide();
    }
}

globalThis.enableBasic = enableBasic;


export function enableCache(checkbox) {
    if (checkbox.checked == true) {
        $("#page_cache_days").show();
    } else {
        $("#page_cache_days").hide();
    }
}

globalThis.enableCache = enableCache;

export function enableSystemReport(checkbox) {
    if (checkbox.checked == true) {
        $("#page_system_report").show();
    } else {
        $("#page_system_report").hide();
    }
}

globalThis.enableSystemReport = enableSystemReport;

export function initDctBasic() {
    /*basic*/
    function loadBasicConfig() {
        $('#loading').show();
        $.get('ajax/dct/get_dctcfg.php?type=basic',function(data) {
            var jsonData = JSON.parse(data);
            var arr = ['collect_period', 'report_period', 'batch_reporting', 'cache_enabled', 'cache_day', 'minute_enabled',
            'minute_period', 'hour_enabled', 'day_enabled', 'system_enabled', 'system_report_period'];

            $('#enabled').val(jsonData.enabled);
            if (jsonData.enabled == '1') {
                $('#page_basic').show();
                $('#basic_enable').prop('checked', true);

                arr.forEach(function (info) {
                    if (info == null) {
                        return true;    // continue: return true; break: return false
                    }
        
                    if (info == 'cache_enabled' || info == 'minute_enabled' || info == 'hour_enabled' || 
                        info == 'day_enabled' || info == 'batch_reporting' || info == 'system_enabled') {
                        $('#' + info).prop('checked', (jsonData[info] == '1') ? true:false);
                    } else {
                        $('#' + info).val(jsonData[info]);
                    }
                })
                
                if (jsonData.cache_enabled == '1') {
                    $('#page_cache_days').show();
                } else {
                    $('#page_cache_days').hide();
                }

                if (jsonData.system_enabled == '1') {
                    $('#page_system_report').show();
                } else {
                    $('#page_system_report').hide();
                }

                if (jsonData.minute_enabled == '1') {
                    $('#page_minute_data').show();
                } else {
                    $('#page_minute_data').hide();
                }
            } else {
                $('#page_basic').hide(); 
                $('#basic_disable').prop('checked', true);
            }

            $('#loading').hide();
        });
    }
    
    globalThis.loadBasicConfig = loadBasicConfig;
    loadBasicConfig();
}