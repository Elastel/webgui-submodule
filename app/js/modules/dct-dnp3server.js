
/* DNP3 Server*/
export function enableDnp3(state, num) {
    if (state) {
      $('#page_dnp3' + num).show();
    } else {
      $('#page_dnp3' + num).hide();
    }
    dnp3ProtocolChange(num);
}

globalThis.enableDnp3 = enableDnp3;

export function dnp3ProtocolChange(num) {
    var proto = document.getElementById('proto' + num).value;

    if (proto == 'RTU') {
        $('#page_proto_rtu' + num).show();
        $('#page_proto_ip' + num).hide();
    } else {
        $('#page_proto_rtu' + num).hide();
        $('#page_proto_ip' + num).show();
    }
}

globalThis.dnp3ProtocolChange = dnp3ProtocolChange;

const dnp3LinkMap = {
    'BINARY_INPUT': {
        static_var: ['var1', 'var2'],
        event_var: ['var1', 'var2', 'var3']
    },
    'DOUBLE_INPUT': {
        static_var: ['var1', 'var2'],
        event_var: ['var1', 'var2', 'var3']
    },
    'BINARY_OUTPUT': {
        static_var: ['var1', 'var2'],
        event_var: ['var1', 'var2']
    },
    'COUNTER_INPUT': {
        static_var: ['var1', 'var2', 'var5', 'var6'],
        event_var: ['var1', 'var2', 'var5', 'var6']
    },
    'ANALOG_INPUT': {
        static_var: ['var1', 'var2', 'var3', 'var4', 'var5', 'var6'],
        event_var: ['var1', 'var2', 'var3', 'var4', 'var5', 'var6', 'var7', 'var8']
    },
    'ANALOG_OUTPUTS': {
        static_var: ['var1', 'var2', 'var3', 'var4'],
        event_var: ['var1', 'var2', 'var3', 'var4', 'var5', 'var6', 'var7', 'var8']
    }
};

function setSelectOptions(selectId, options) {
    var select = document.getElementById(selectId);
    if (!select) return;
    select.innerHTML = '';
    options.forEach(function(opt) {
        var option = document.createElement('option');
        option.value = opt;
        option.text = opt;
        select.appendChild(option);
    });
}

function groupIdChange() {
    var groupId = document.getElementById('dnp3.group_id').value;
    var link = dnp3LinkMap[groupId];
    if (link) {
        setSelectOptions('dnp3.event_var', link.event_var);
        setSelectOptions('dnp3.static_var', link.static_var);
    }
}

globalThis.groupIdChange = groupIdChange;

export function initDctDnp3Server() {
    function loadDnp3Config() {
        $('#loading').show();
        var table_name = 'dnp3';
        $.get('ajax/dct/get_dctcfg.php?type=dnp3',function(data){
            const jsonData = JSON.parse(data);
            var arr = jsonData.option;
            if (jsonData.hasOwnProperty("dnp3_server")) {
                var dnp3_server = JSON.parse(jsonData.dnp3_server);
                for (var i = 1; i <= 4; i++) {
                    $('#enabled' + i).val(dnp3_server['enabled' + i]);
                    if (dnp3_server['enabled' + i] == '1') {
                        $('#page_dnp3' + i).show();
                        $('#dnp3_server_enable' + i).prop('checked', true);

                        arr.forEach(function (info) {
                            if (info == null) {
                                return true;    // continue: return true; break: return false
                            }

                            $('#' + info + i).val(dnp3_server[info + i]);
                        })
                    } else {
                        $('#page_dnp3' + i).hide();
                        $('#dnp3_server_disable' + i).prop('checked', true);
                    }

                    dnp3ProtocolChange(i);
                }
            }
            

            if (jsonData.hasOwnProperty("factor_list")) {
                var factor_list = jsonData.factor_list;
                var select = document.getElementById(table_name + '.source_object');
                if (factor_list != null) {
                    factor_list.forEach(function(factor) {
                        var newOption = document.createElement('option');
                        newOption.value = factor;
                        newOption.text = factor;
                        select.appendChild(newOption);
                    });
                }
            }
            
            if (jsonData.hasOwnProperty("dnp3")) {
                var tmpData = JSON.parse(jsonData.dnp3);
                var option_list = jsonData.option_list;

                addSectionTable(table_name, tmpData, option_list);

                loadRealtimeData();
            }
            $('#loading').hide();
        });
    }

    globalThis.loadDnp3Config = loadDnp3Config;
    loadDnp3Config();
}