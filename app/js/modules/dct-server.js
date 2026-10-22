export function enableServer(state, num) {
    if (state) {
        $('#page_server' + num).show();
        protocolChange(num);
    } else {
        $('#page_server' + num).hide();
    }
}

globalThis.enableServer = enableServer;

export function protocolChange(num) {
    var selectElement = document.getElementById('proto' + num);
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var selectedText = selectedOption.text;

    enableTls(num);
    cerChange(num);
    if (selectedText != 'SparkPlugB') {
        enableVar(num);
        enableHeader(num);
        encapChange(num);
    } else {
        $('#page_json' + num).hide();
        $('#page_hj212_' + num).hide();
    }
    
    $('#page_http' + num).hide();
    if (selectedText == 'TCP' || selectedText == 'UDP') {
        $('#page_mqtt' + num).hide();
        $('#page_url' + num).hide(); 
        $('#page_tcp' + num).show(); 
        $('#page_addr' + num).show(); 
        $('#page_port' + num).show(); 
        $('#page_encap' + num).show(); 
        $('#page_status' + num).show();
        $('#page_cache' + num).show();
    } else if (selectedText == 'MQTT' || selectedText == 'SparkPlugB') {
        $('#page_mqtt' + num).show();
        $('#page_url' + num).hide(); 
        $('#page_tcp' + num).hide(); 
        $('#page_addr' + num).show(); 
        $('#page_port' + num).show(); 
        $('#page_status' + num).show();
        $('#page_cache' + num).show();
        if (selectedText == 'MQTT') {
            $('#page_encap' + num).show();
            $('#page_topic' + num).show();
            $('#page_sparkplug' + num).hide();
        } else {
            $('#page_encap' + num).hide();
            $('#page_topic' + num).hide();
            $('#page_sparkplug' + num).show();
        }
    } else if (selectedText == 'HTTP')  {
        $('#page_mqtt' + num).hide();
        $('#page_url' + num).show(); 
        $('#page_tcp' + num).hide(); 
        $('#page_addr' + num).hide(); 
        $('#page_port' + num).show(); 
        $('#page_encap' + num).show(); 
        $('#page_status' + num).hide();
        $('#page_cache' + num).hide();
        $('#page_http' + num).show();
    } else if (selectedText == 'MODBUS TCP' || selectedText == 'TCP Server') {
        $('#page_mqtt' + num).hide();
        $('#page_url' + num).hide(); 
        $('#page_tcp' + num).hide(); 
        $('#page_addr' + num).hide(); 
        $('#page_port' + num).show(); 
        $('#page_encap' + num).hide(); 
        $('#page_status' + num).hide();
        $('#page_json' + num).hide();
        $('#page_hj212_' + num).hide();
        $('#page_cache' + num).hide();
    }
}

globalThis.protocolChange = protocolChange;

export function encapChange(num) {
    var encap_type = document.getElementById('encap_type' + num).value;

    if (encap_type == 0) {
        $('#page_json' + num).hide();
        $('#page_hj212_' + num).hide();
    } else if (encap_type == 1) {
        $('#page_json' + num).show();
        $('#page_hj212_' + num).hide();
        jsonChange(num);
    } else if (encap_type == 2) {
        $('#page_json' + num).hide();
        $('#page_hj212_' + num).show();
    }
}

globalThis.encapChange = encapChange;

export function jsonChange(num) {
    var select = document.getElementById('json_format' + num);
    var icon = select.nextElementSibling;

    var value = select.value;
    /* JSON format 4 (custom-format) shows the template boxes, the others hide them. */
    if (value === "4") {
        $('#page_custom' + num).show();
        /* custom-format uses its own template: self defined variables do not apply, hide the whole block */
        $('#page_selfvar' + num).hide();
        $('#page_var' + num).hide();
    } else {
        $('#page_custom' + num).hide();
        enableVar(num);
    }

    if (!icon || !icon.classList.contains('fa-question-circle')) return;

    if (value === "0") {
        icon.setAttribute('title', '{"ts":1747208633000,"temperature":27}');
    } else if (value === "1") {
        icon.setAttribute('title', '{"ts":1747208633000,"params":{"temperature":27}}');
    } else if (value === "2") {
        icon.setAttribute('title', '{"ts":1747208633000,"params":[{"name":"temperature", "value":27}]}');
    } else if (value === "3") {
        icon.setAttribute('title', '{"ts":1747208633000,"params":[{"device_id":"meter1", "data":{"temperature":27}}]}');
    } else if (value === "4") {
        icon.setAttribute('title', 'Custom JSON frame, variables: ${ts} ${time} ${device_name} ${tag_name} ${tag_value}');
    }

    if ($(icon).data('bs.tooltip')) {
        $(icon).attr('data-original-title', icon.getAttribute('title')).tooltip('update');
    }
}

globalThis.jsonChange = jsonChange;

export function enableHeader(num) {
    var enable = document.getElementById('self_define_header' + num).checked;

    if (enable == true) {
        $('#page_header' + num).show();
    } else {
        $('#page_header' + num).hide();
    }
}

globalThis.enableHeader = enableHeader;

export function enableVar(num) {
    var enable_var = document.getElementById('self_define_var' + num).checked;
    var isCustom = ($('#json_format' + num).val() === '4');

    /* custom-format does not use self defined variables: this block is not shown at all */
    if (isCustom) {
        $('#page_selfvar' + num).hide();
        $('#page_var' + num).hide();
        return;
    }

    $('#page_selfvar' + num).show();

    if (enable_var == true) {
        $('#page_var' + num).show();
    } else {
        $('#page_var' + num).hide();
    }
}

globalThis.enableVar = enableVar;

export function enableTls(num) {
    var enable_tls = document.getElementById('mqtt_tls_enabled' + num).checked;

    if (enable_tls == true) {
        $('#page_mqtt_tls' + num).show();
    } else {
        $('#page_mqtt_tls' + num).hide();
    }
}

globalThis.enableTls = enableTls;

export function cerChange(num) {
    var cer_type = document.getElementById('certificate_type' + num).value;

    if (cer_type == '0') {
        $('#page_one' + num).hide();
        $('#page_two' + num).hide(); 
    } else if (cer_type == '1') {
        $('#page_one' + num).show();
        $('#page_two' + num).hide(); 
    } else {
        $('#page_one' + num).show();
        $('#page_two' + num).show(); 
    }
}

globalThis.cerChange = cerChange;

export function caFileChange(num) {
    $('#ca_text' + num).html($('#mqtt_ca' + num)[0].files[0].name);
}

globalThis.caFileChange = caFileChange;

export function cerFileChange(num) {
    $('#cer_text' + num).html($('#mqtt_cert' + num)[0].files[0].name);
}

globalThis.cerFileChange = cerFileChange;

export function keyFileChange(num) {
    $('#key_text' + num).html($('#mqtt_key' + num)[0].files[0].name);
}

globalThis.keyFileChange = keyFileChange;

export function initDctServer() {
    /*reporting server*/
    function loadServerConfig() {
        $('#loading').show();
        $.get('ajax/dct/get_dctcfg.php?type=server',function(data){
            var jsonData = JSON.parse(data);

            var arr = ["proto", "encap_type", "json_format", "server_addr", "http_url", "server_port", "cache_enabled", 
            "register_packet", "register_packet_hex", "heartbeat_packet", "heartbeat_packet_hex", "heartbeat_interval",
            "mqtt_heartbeat_interval", "mqtt_pub_topic", "mqtt_sub_topic", "mqtt_username", "mqtt_password", "sparkplug_group_id",
            "sparkplug_node_id", "sparkplug_device_id", "mqtt_client_id", "mqtt_tls_enabled", "certificate_type", "mqtt_ca", "mqtt_cert", "mqtt_key", 
            "self_define_header", "header_name1_", "header_value1_", "header_name2_", "header_value2_", "header_name3_", "header_value3_",
            "self_define_var", "var_name1_", "var_value1_", "var_name2_", "var_value2_", "var_name3_", "var_value3_", 
            "mn", "st", "pw"];

            for (var i = 1; i <= 5; i++) {
                $('#enabled' + i).val(jsonData['enabled' + i]);
                if (jsonData['enabled' + i] == '1') {
                    $('#page_server' + i).show();
                    $('#enable' + i).prop('checked', true);
                    arr.forEach(function (info) {
                        if (info == "cache_enabled" || info == "register_packet_hex" || info == "heartbeat_packet_hex" ||
                            info == "mqtt_tls_enabled" ||  info == "self_define_var" ||  info == "self_define_header") {
                            $('#' + info + i).prop('checked', (jsonData[info + i] == '1') ? true:false);
                        } else if (info == "mqtt_ca") {
                            if (jsonData['mqtt_ca' + i]) {
                                $('#ca_text' + i).html(jsonData['mqtt_ca' + i]);
                            }
                        } else if (info == "mqtt_cert") {
                            if (jsonData['mqtt_cert' + i]) {
                                $('#cer_text' + i).html(jsonData['mqtt_cert' + i]);
                            }
                        } else if (info == "mqtt_key") {
                            if (jsonData['mqtt_key' + i]) {
                                $('#key_text' + i).html(jsonData['mqtt_key' + i]);
                            }
                        } else {
                            $('#' + info + i).val(jsonData[info + i]);
                        }
                        protocolChange(i);
                        jsonChange(i);
                    });

                    /* JSON Format = custom-format: the frame text lives in
                       /etc/config/custom_json_format, shared by every server. */
                    if ($('#json_format' + i).val() == '4') {
                        $('#custom_json_format' + i).val(jsonData['custom_json_format'] || '');
                    }
                } else {
                    $('#page_server' + i).hide(); 
                    $('#disable' + i).prop('checked', true);
                }
            }

            $('#loading').hide();
        });
    }
    
    globalThis.loadServerConfig = loadServerConfig;
    loadServerConfig();
}