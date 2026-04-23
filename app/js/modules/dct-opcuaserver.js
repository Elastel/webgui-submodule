export function anonymousCheck(check) {
    if (check.checked == true)  {
        $('#page_anonymous').hide();
    } else {
        $('#page_anonymous').show();
    }
}

globalThis.anonymousCheck = anonymousCheck;

export function enableOpcua(state) {
    if (state) {
        $('#page_opcua').show();
        if ($('#security_policy').val() == "0") {
        $('#page_security').hide();
        } else {
        $('#page_security').show();
        }

        if ($('#anonymous').is(':checked')) {
        $('#page_anonymous').hide();
        } else {
        $('#page_anonymous').show();
        }
    } else {
        $('#page_opcua').hide();
    }
}

globalThis.enableOpcua = enableOpcua;

export function securityChange(state) {
    if (state.value == '0') {
        $('#page_security').hide();
    } else {
        $('#page_security').show();
    }
}

globalThis.securityChange = securityChange;

export function certChange() {
    $('#cert_text').html($('#certificate')[0].files[0].name);
}

globalThis.certChange = certChange;

export function keyChange() {
    $('#key_text').html($('#private_key')[0].files[0].name);
}

globalThis.keyChange = keyChange;

export function trustChange() {
    var file = $('#trust_crt')[0].files;
    var str = '';
    for (var i = 0, len = file.length; i < len; i++) {
        str += file[i].name;
        if (i < len - 1)
        str += ";";
    }

    $('#trust_text').html(str);
}

globalThis.trustChange = trustChange;

export function initDctOpcuaServer() {
    /*OPCUA Server*/
    function loadOpcuaConfig() {
        $.get('ajax/dct/get_dctcfg.php?type=opcua',function(data){
            const jsonData = JSON.parse(data);
            $('#enabled').val(jsonData.enabled);
            if (jsonData.enabled == '1') {
                $('#page_opcua').show();
                $('#opcua_enable').prop('checked', true);

                for(var key in jsonData){
                    if (key == null) {
                        return true;    // continue: return true; break: return false
                    }
                    if (key == 'anonymous' || key == 'enable_database') {
                        $('#' + key).prop('checked', (jsonData[key] == '1') ? true:false);
                    } else if (key == 'certificate') {
                        if (jsonData[key]) {
                            $('#cert_text').html(jsonData[key]);
                        }
                    } else if (key == 'private_key') {
                        if (jsonData[key]) {
                            $('#key_text').html(jsonData[key]);
                        }
                    } else if (key == 'trust_crt') {
                        if (jsonData[key]) {
                            $('#trust_text').html(jsonData[key]);
                        }
                    } else {
                        $('#' + key).val(jsonData[key]);
                    }
                }
            } else {
                $('#page_opcua').hide();
                $('#opcua_disable').prop('checked', true);
            }

            if (jsonData['anonymous'] != '1') {
                $('#page_anonymous').show();
            } else {
                $('#page_anonymous').hide();
            }

            if (jsonData['security_policy'] == '0') {
                $('#page_security').hide();
            } else {
                $('#page_security').show();
            }
        });
    }

    globalThis.loadOpcuaConfig = loadOpcuaConfig;
    loadOpcuaConfig();
}