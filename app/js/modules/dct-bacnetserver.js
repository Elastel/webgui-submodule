/* Named enableBACnetServer: bacnet-router.js used to shadow this global
   (both modules exported "enableBACnet" onto globalThis and the router is
   imported later in app.js), so the radio toggle never updated the
   Mappings section. */
export function enableBACnetServer(state) {
    if (state) {
      $('#page_bacnet').show();
      $('#page_bacnet_mapping').show();
    } else {
      $('#page_bacnet').hide();
      $('#page_bacnet_mapping').hide();
    }
}

globalThis.enableBACnetServer = enableBACnetServer;

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

/* Object type linkage: COV Increment exists only for analog objects (AI/AO/AV);
   this property is meaningless for BI/BO/BV/MSV and is hidden in the edit dialog.
   Note: Hiding simply means the property is not displayed; when saving, `saveData` will still clear `cov_increment` values for unsupported
   types based on the final `object_type`, and the list will display “-” accordingly */
function objectTypeChange() {
    var type = document.getElementById('bacnet_point.object_type').value;
    if (type == 'AI' || type == 'AO' || type == 'AV') {
        $('#page_cov_increment').show();
    } else {
        $('#page_cov_increment').hide();
    }
}

globalThis.objectTypeChange = objectTypeChange;

export function initDctBacnetServer() {
    /*BACnet Server*/
    function loadBACnetConfig() {
        $('#loading').show();
        var table_name = 'bacnet_point';
        $.get('ajax/dct/get_dctcfg.php?type=' + table_name, function(data){
            const jsonData = JSON.parse(data);

            /* 1. Restore the server settings */
            if (jsonData.hasOwnProperty('bacnet')) {
                var bacnet = JSON.parse(jsonData.bacnet);
                var arr = ['proto', 'ifname', 'port', 'interface', 'baudrate', 'mac',
                            'max_master', 'frames', 'device_id', 'object_name'];

                /*
                 * "on" / "true" / "yes" are legacy values written by an older
                 * bug where this radio shared the name "enabled" with the
                 * mapping table checkbox; treat them as enabled.
                 */
                var isEnabled = (bacnet.enabled == '1' || bacnet.enabled == 'on' ||
                                 bacnet.enabled == 'true' || bacnet.enabled == 'yes');

                if (isEnabled) {
                    $('#page_bacnet').show();
                    $('#page_bacnet_mapping').show();
                    $('#bacnet_enable').prop('checked', true);

                    arr.forEach(function (info) {
                        if (info == null) {
                            return true;    // continue: return true; break: return false
                        }

                        $('#' + info).val(bacnet[info]);
                    })
                } else {
                    $('#page_bacnet').hide();
                    $('#page_bacnet_mapping').hide();
                    $('#bacnet_disable').prop('checked', true);
                }

                bacnetProtocolChange();
            }

            /* 2. Source object list (from /tmp/factor_list) */
            if (jsonData.hasOwnProperty('factor_list')) {
                var factor_list = jsonData.factor_list;
                var select = document.getElementById(table_name + '.source_object');
                if (select != null && factor_list != null) {
                    factor_list.forEach(function(factor) {
                        var newOption = document.createElement('option');
                        newOption.value = factor;
                        newOption.text = factor;
                        select.appendChild(newOption);
                    });
                }
            }

            /* 3. Mapping table (with the realtime value column) */
            if (jsonData.hasOwnProperty('bacnet_point')) {
                var tmpData = JSON.parse(jsonData.bacnet_point);
                var option_list = jsonData.option_list;

                addSectionTable(table_name, tmpData, option_list);

                loadRealtimeData();
            }
            $('#loading').hide();
        });
    }

    globalThis.loadBACnetConfig = loadBACnetConfig;
    loadBACnetConfig();
}
