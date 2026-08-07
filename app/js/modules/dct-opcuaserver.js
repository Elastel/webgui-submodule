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

function getOpcuaDate() {
    $.get('ajax/dct/get_dctcfg.php?type=opcua_nodes', function(data) {
        const jsonData = JSON.parse(data);
        var table = document.getElementById("table_opcuaserv");
        if (table == null) {
            return;
        }
        
        var rows = table.rows;

        for(var key in jsonData) {
            if (key == null) {
                return true;    // continue: return true; break: return false
            }

            // console.log(rows.length);
            for(var i = 0; i < rows.length; i++ ){
                // console.log(rows[i].cells[1].innerHTML + " -- " + key);
                if (rows[i].cells[1].innerHTML == key) {
                    if (rows[i].cells[3].innerHTML == 'bool') {
                        rows[i].cells[2].innerHTML = jsonData[key] == '1' ? 'true' : 'false';
                    } else if (rows[i].cells[3].innerHTML == 'string') {
                        rows[i].cells[2].innerHTML = jsonData[key].length > 10 ? jsonData[key].slice(0, 10) + '...' : jsonData[key];
                    } else {
                        rows[i].cells[2].innerHTML = jsonData[key];
                    }
                    
                    break;
                }
            }
        }
    });
}

export function initDctOpcuaServer() {
    /*OPCUA Server*/
    function loadOpcuaConfig() {
        $('#loading').show();
        var table_name = 'opcuaserv';
        $.get('ajax/dct/get_dctcfg.php?type=opcua',function(data){
            const jsonData = JSON.parse(data);
            var arr = jsonData.option;
            if (jsonData.hasOwnProperty("opcua")) {
                var opcua = JSON.parse(jsonData.opcua);

                $('#enabled').val(opcua.enabled);
                if (opcua.enabled == '1') {
                    $('#page_opcua').show();
                    $('#opcua_enable').prop('checked', true);

                    arr.forEach(function (info) {
                        if (info == null) {
                            return true;    // continue: return true; break: return false
                        }
                        if (info == 'anonymous' || info == 'enable_database') {
                            $('#' + info).prop('checked', (opcua[info] == '1') ? true:false);
                        } else if (info == 'certificate') {
                            if (opcua[info]) {
                                $('#cert_text').html(opcua[info]);
                            }
                        } else if (info == 'private_key') {
                            if (opcua[info]) {
                                $('#key_text').html(opcua[info]);
                            }
                        } else if (info == 'trust_crt') {
                            if (opcua[info]) {
                                $('#trust_text').html(opcua[info]);
                            }
                        } else {
                            $('#' + info).val(opcua[info]);
                        }
                    })
                } else {
                    $('#page_opcua').hide();
                    $('#opcua_disable').prop('checked', true);
                }

                if (opcua['anonymous'] != '1') {
                    $('#page_anonymous').show();
                } else {
                    $('#page_anonymous').hide();
                }

                if (opcua['security_policy'] == '0') {
                    $('#page_security').hide();
                } else {
                    $('#page_security').show();
                }
            }

            if (jsonData.hasOwnProperty("opcuaserv")) {
                var tmpData = JSON.parse(jsonData.opcuaserv);
                var option_list = jsonData.option_list;

                addSectionTable(table_name, tmpData, option_list);
            }
            $('#loading').hide();
        });

        getOpcuaDate();
        setInterval(getOpcuaDate, 1000);
    }

    globalThis.loadOpcuaConfig = loadOpcuaConfig;
    loadOpcuaConfig();
}