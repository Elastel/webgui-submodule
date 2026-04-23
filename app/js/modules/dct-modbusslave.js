export function enableModbusSlave(state) {
    if (state) {
    $('#page_modbus_slave').show();
    } else {
    $('#page_modbus_slave').hide();
    }
    modbusSlaveProtocolChange();
}

globalThis.enableModbusSlave = enableModbusSlave;

export function modbusSlaveProtocolChange() {
    var proto = document.getElementById('proto').value;

    if (proto == 'RTU') {
        $('#page_proto_rtu').show();
        $('#page_proto_ip').hide();
    } else {
        $('#page_proto_rtu').hide();
        $('#page_proto_ip').show();
    }
}

globalThis.modbusSlaveProtocolChange = modbusSlaveProtocolChange;

export function initDctModbusSlave() {
   /* Modbus Slave*/
    function loadModbusSlaveConfig() {
        $('#loading').show();
        $.get('ajax/dct/get_dctcfg.php?type=modbus_slave',function(data){
            const jsonData = JSON.parse(data);
            var arr = jsonData.option;
            if (jsonData.hasOwnProperty("modbus_slave")) {
                var modbus_slave = JSON.parse(jsonData.modbus_slave);

                $('#enabled').val(modbus_slave.enabled);
                if (modbus_slave.enabled == '1') {
                    $('#page_modbus_slave').show();
                    $('#modbus_slave_enable').prop('checked', true);

                    arr.forEach(function (info) {
                        if (info == null) {
                            return true;    // continue: return true; break: return false
                        }

                        $('#' + info).val(modbus_slave[info]);
                    })
                } else {
                    $('#page_modbus_slave').hide();
                    $('#modbus_slave_disable').prop('checked', true);
                }
                modbusSlaveProtocolChange();
            }

            var table_name = 'modbus_slave_point';
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
            
            if (jsonData.hasOwnProperty(table_name)) {
                var tmpData = JSON.parse(jsonData.modbus_slave_point);
                var option_list = jsonData.option_list;

                addSectionTable(table_name, tmpData, option_list);

                loadRealtimeData();
            }
            $('#loading').hide();
        });
    }
    
    globalThis.loadModbusSlaveConfig = loadModbusSlaveConfig;
    loadModbusSlaveConfig();
}