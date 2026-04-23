

export function comProtocolChange(num) {
    var numStr = num.toString();
    var selectElement = document.getElementById('com_proto' + numStr);
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var selectedText = selectedOption.text;

    $('#com_page_protocol_modbus' + numStr).hide();
    $('#com_page_protocol_transparent' + numStr).hide();
    $('#com_page_protocol_dnp3' + numStr).hide();
    $('#com_page_protocol_bacnet' + numStr).hide();
    $('#com_page_controller_model' + numStr).hide();
    $('#com_page_protocol_dlms' + numStr).hide();

    if (selectedText == 'Transparent') {
        $('#com_page_protocol_transparent' + numStr).show();
    } else if (selectedText == 'DNP3') {
        $('#com_page_protocol_dnp3' + numStr).show();
    } else if (selectedText == 'BACnet/MSTP') {
        $('#com_page_protocol_bacnet' + numStr).show();
    } else if (selectedText == 'Modbus2io') {
        $('#com_page_controller_model' + numStr).show();
    } else if (selectedText == 'DLMS') {
        $('#com_page_protocol_dlms' + numStr).show();
        dlmsAuthChangeCom(num);
    } else {
        $('#com_page_protocol_modbus' + numStr).show();
    }
}

globalThis.comProtocolChange = comProtocolChange;

export function dlmsAuthChangeCom(num) {
    var numStr = num.toString();
    var selectElement = document.getElementById('com_dlms_auth' + numStr);
    if (!selectElement.value) {
        selectElement.value = "0";
        selectElement.dispatchEvent(new Event('change'));
        return;
    }
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var selectedText = selectedOption.text;

    if (selectedText == 'None') {
        $('#com_page_dlms_password' + numStr).hide();
        $('#com_page_security_dlms' + numStr).hide();
    } else if (selectedText == 'Low' || selectedText == 'High' || 
        selectedText == 'HighMd5' || selectedText == 'HighSha1' ||
        selectedText == 'HighSha256') {
        $('#com_page_dlms_password' + numStr).show();
        $('#com_page_security_dlms' + numStr).hide();
    } else if (selectedText == 'HighGmac') {
        $('#com_page_dlms_password' + numStr).hide();
        $('#com_page_security_dlms' + numStr).show();
    }
    dlmsSecurityChangeCom(num);
}

globalThis.dlmsAuthChangeCom = dlmsAuthChangeCom;

export function dlmsAuthChangeTcp(num) {
    var numStr = num.toString();
    var selectElement = document.getElementById('tcp_dlms_auth' + numStr);
    if (!selectElement.value) {
        selectElement.value = "0";
        selectElement.dispatchEvent(new Event('change'));
        return;
    }
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var selectedText = selectedOption.text;

    if (selectedText == 'None') {
        $('#tcp_page_dlms_password' + numStr).hide();
        $('#tcp_page_security_dlms' + numStr).hide();
    } else if (selectedText == 'Low' || selectedText == 'High' || 
        selectedText == 'HighMd5' || selectedText == 'HighSha1' ||
        selectedText == 'HighSha256') {
        $('#tcp_page_dlms_password' + numStr).show();
        $('#tcp_page_security_dlms' + numStr).hide();
    } else if (selectedText == 'HighGmac') {
        $('#tcp_page_dlms_password' + numStr).hide();
        $('#tcp_page_security_dlms' + numStr).show();
    }
    dlmsSecurityChangeTcp(num);
}

globalThis.dlmsAuthChangeTcp = dlmsAuthChangeTcp;

export function iec61850AuthChangeTcp(num) {
    var numStr = num.toString();
    var selectElement = document.getElementById('tcp_iec61850_auth' + numStr);
    if (!selectElement.value) {
        selectElement.value = "0";
        selectElement.dispatchEvent(new Event('change'));
        return;
    }
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var selectedText = selectedOption.text;

    if (selectedText == 'Password') {
        $('#tcp_page_password_iec61850' + numStr).show();
        $('#tcp_page_tls_iec61850' + numStr).hide();
    } else if (selectedText == 'TLS') {
        $('#tcp_page_password_iec61850' + numStr).hide();
        $('#tcp_page_tls_iec61850' + numStr).show();
    } else {
        $('#tcp_page_password_iec61850' + numStr).hide();
        $('#tcp_page_tls_iec61850' + numStr).hide();
    }
    dlmsSecurityChangeTcp(num);
}

globalThis.iec61850AuthChangeTcp = iec61850AuthChangeTcp;

export function dlmsSecurityChangeCom(num)
{
    var numStr = num.toString();
    var selectElement = document.getElementById('com_dlms_security_level' + numStr);
    if (!selectElement.value) {
        selectElement.value = "0";
        selectElement.dispatchEvent(new Event('change'));
        return;
    }
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var selectedText = selectedOption.text;
    if (selectedText == 'None') {
        $('#com_page_authentication_dlms' + numStr).hide();
        $('#com_page_encrypted_dlms' + numStr).hide();
    } else if (selectedText == 'Authentication') {
        $('#com_page_authentication_dlms' + numStr).show();
        $('#com_page_encrypted_dlms' + numStr).hide();
    } else if (selectedText == 'Encryption') {
        $('#com_page_authentication_dlms' + numStr).hide();
        $('#com_page_encrypted_dlms' + numStr).show();
    } else if (selectedText == 'AuthenticationEncryption') {
        $('#com_page_authentication_dlms' + numStr).show();
        $('#com_page_encrypted_dlms' + numStr).show();
    }
}

globalThis.dlmsSecurityChangeCom = dlmsSecurityChangeCom;

export function dlmsSecurityChangeTcp(num)
{
    var numStr = num.toString();
    var selectElement = document.getElementById('tcp_dlms_security_level' + numStr);
    if (!selectElement.value) {
        selectElement.value = "0";
        selectElement.dispatchEvent(new Event('change'));
        return;
    }
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var selectedText = selectedOption.text;
    if (selectedText == 'None') {
        $('#tcp_page_authentication_dlms' + numStr).hide();
        $('#tcp_page_encrypted_dlms' + numStr).hide();
    } else if (selectedText == 'Authentication') {
        $('#tcp_page_authentication_dlms' + numStr).show();
        $('#tcp_page_encrypted_dlms' + numStr).hide();
    } else if (selectedText == 'Encryption') {
        $('#tcp_page_authentication_dlms' + numStr).hide();
        $('#tcp_page_encrypted_dlms' + numStr).show();
    } else if (selectedText == 'AuthenticationEncryption') {
        $('#tcp_page_authentication_dlms' + numStr).show();
        $('#tcp_page_encrypted_dlms' + numStr).show();
    }
}

globalThis.dlmsSecurityChangeTcp = dlmsSecurityChangeTcp;

export function snmpVersionChangeTcp(num) {
    var numStr = num.toString();
    var selectElement = document.getElementById('snmp_version' + numStr);
    if (!selectElement.value) {
        selectElement.value = "0";
        selectElement.dispatchEvent(new Event('change'));
        return;
    }
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var selectedText = selectedOption.text;

    if (selectedText == 'SNMPv3') {
        $('#tcp_page_snmpv2' + numStr).hide();
        $('#tcp_page_snmpv3' + numStr).show();
    } else {
        $('#tcp_page_snmpv3' + numStr).hide();
        $('#tcp_page_snmpv2' + numStr).show();
    }
}

globalThis.snmpVersionChangeTcp = snmpVersionChangeTcp;

export function securityLevelChangeTcp(num) {
    var numStr = num.toString();
    var selectElement = document.getElementById('security_level' + numStr);
    if (!selectElement.value) {
        selectElement.value = "0";
        selectElement.dispatchEvent(new Event('change'));
        return;
    }
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var selectedText = selectedOption.text;

    if (selectedText == 'noAuthNoPriv') {
        $('#page_snmpv3_auth' + numStr).hide();
        $('#page_snmpv3_privacy' + numStr).hide();
    } else if (selectedText == 'authNoPriv') {
        $('#page_snmpv3_auth' + numStr).show();
        $('#page_snmpv3_privacy' + numStr).hide();
    } else {
        $('#page_snmpv3_auth' + numStr).show();
        $('#page_snmpv3_privacy' + numStr).show();
    }
}

globalThis.securityLevelChangeTcp = securityLevelChangeTcp;

export function tcpProtocolChange(num) {
    var numStr = num.toString();
    var selectElement = document.getElementById('tcp_proto' + numStr);
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var selectedText = selectedOption.text;

    $('#tcp_page_protocol_modbus' + numStr).hide();
    $('#tcp_page_protocol_transparent' + numStr).hide();
    $('#tcp_page_protocol_s7' + numStr).hide();
    $('#tcp_page_protocol_plc' + numStr).hide();
    $('#tcp_page_protocol_opcua' + numStr).hide();
    $('#tcp_page_protocol_dnp3' + numStr).hide();
    $('#tcp_page_protocol_bacnet' + numStr).hide();
    $('#tcp_page_protocol_snmp' + numStr).hide();
    $('#tcp_page_protocol_dlms' + numStr).hide();
    $('#tcp_page_protocol_iec61850' + numStr).hide();

    if (selectedText == 'Transparent') {
        $('#tcp_page_protocol_transparent' + numStr).show();
    } else if (selectedText == 'S7' || selectedText == 'Ethernet/IP') {
        $('#tcp_page_protocol_plc' + numStr).show();
        if (selectedText == 'S7')
            $('#tcp_page_protocol_s7' + numStr).show();
    } else if (selectedText == 'OPCUA') {
        $('#tcp_page_protocol_opcua' + numStr).show();
        anonymousCheckTcp(numStr);
        securityChangeTcp(numStr)
    } else if (selectedText == 'DNP3') {
        $('#tcp_page_protocol_dnp3' + numStr).show();
    } else if (selectedText == 'BACnet/IP') {
        $('#tcp_page_protocol_bacnet' + numStr).show();
    } else if (selectedText == 'SNMP') {
        $('#tcp_page_protocol_snmp' + numStr).show();
        snmpVersionChangeTcp(num);
        securityLevelChangeTcp(num);
    } else if (selectedText == 'DLMS') {
        $('#tcp_page_protocol_dlms' + numStr).show();
        dlmsAuthChangeTcp(num);
    } else if (selectedText == 'IEC61850') {
        $('#tcp_page_protocol_iec61850' + numStr).show();
        iec61850AuthChangeTcp(num);
    } else {
        $('#tcp_page_protocol_modbus' + numStr).show();
    }
}

globalThis.tcpProtocolChange = tcpProtocolChange;

export function enableCom(state, num) {
    var numStr = num.toString();

    if (state) {
        $('#page_com' + numStr).show();
        comProtocolChange(num);
    } else {
        $('#page_com' + numStr).hide();
    }
}

globalThis.enableCom = enableCom;

export function enableTcp(state, num) {
    var numStr = num.toString();

    if (state) {
        $('#page_tcp' + numStr).show();
        tcpProtocolChange(num);
    } else {
        $('#page_tcp' + numStr).hide();
    }
}

globalThis.enableTcp = enableTcp;

export function securityChangeTcp(num) {
    if ($('#security_policy' + num).val() == '0')  {
        $('#page_security' + num).hide();
    } else {
        $('#page_security' + num).show();
    }
}

globalThis.securityChangeTcp = securityChangeTcp;

export function certChangeTcp(num) {
    $('#cert_text' + num).html($('#certificate' + num)[0].files[0].name);
}

globalThis.certChangeTcp = certChangeTcp;

export function keyChangeTcp(num) {
    $('#key_text' + num).html($('#private_key' + num)[0].files[0].name);
}

globalThis.keyChangeTcp = keyChangeTcp;

export function iec61850KeyChangeTcp(num) {
    $('#iec61850_key_text' + num).html($('#iec61850_key' + num)[0].files[0].name);
}

globalThis.iec61850KeyChangeTcp = iec61850KeyChangeTcp;

export function iec61850CertChangeTcp(num) {
    $('#iec61850_cert_text' + num).html($('#iec61850_cert' + num)[0].files[0].name);
}

globalThis.iec61850CertChangeTcp = iec61850CertChangeTcp;

export function iec61850RootCertChangeTcp(num) {
    $('#iec61850_root_cert_text' + num).html($('#iec61850_root_cert' + num)[0].files[0].name);
}

globalThis.iec61850RootCertChangeTcp = iec61850RootCertChangeTcp;

export function trustChangeTcp(num) {
    var file = $('#trust_crt' + num)[0].files;
    var str = '';
    for (var i = 0, len = file.length; i < len; i++) {
        str += file[i].name;
        if (i < len - 1)
        str += ";";
    }

    $('#trust_text' + num).html(str);
}

globalThis.trustChangeTcp = trustChangeTcp;

export function anonymousCheckTcp(num) {
    if ($('#anonymous' + num).is(':checked'))  {
        $('#page_anonymous' + num).hide();
    } else {
        $('#page_anonymous' + num).show();
    }
}

globalThis.anonymousCheckTcp = anonymousCheckTcp;

export function initDctInterface() {
    /*interfaces*/
    function loadInterfacesConfig() {
        $('#loading').show();
        $.get('ajax/dct/get_dctcfg.php?type=interface',function(data) {
            // console.log(data);
            var interface_data = JSON.parse(data);
            var arrCom = interface_data.com_option;
            var jsonData = JSON.parse(interface_data.interface);

            if (arrCom.length > 0) {
                for (var i = 1; i <= 4; i++) {
                    $('#com_enabled' + i).val(jsonData['com_enabled' + i]);
                    if (jsonData['com_enabled' + i] == '1') {
                        $('#page_com' + i).show();
                        $('#com_enable' + i).prop('checked', true);

                        arrCom.forEach(function (info) {
                            if (jsonData[info + i] == null) {
                                return true;    // continue: return true; break: return false
                            }

                            $('#' + info + i).val(jsonData[info + i]);
                        })

                        comProtocolChange(i);
                    } else {
                        $('#page_com' + i).hide(); 
                        $('#com_disable' + i).prop('checked', true);
                    }
                }
            }
            
            var arrTcp = interface_data.tcp_server_option;

            if (arrTcp.length > 0) {
            for (var i = 1; i <= 5; i++) {
                    $('#tcp_enabled' + i).val(jsonData['tcp_enabled' + i]);
                    if (jsonData['tcp_enabled' + i] == '1') {
                        $('#page_tcp' + i).show();
                        $('#tcp_enable' + i).prop('checked', true);

                        arrTcp.forEach(function (info) {
                            if (jsonData[info + i] == null) {
                                return true;    // continue: return true; break: return false
                            }
                            if (info == 'anonymous') {
                                $('#' + info + i).prop('checked', jsonData[info + i] == 1 ? true : false);
                            } else if (info == 'certificate') {
                                if (jsonData[info + i]) {
                                    $('#cert_text' + i).html(jsonData[info + i]);
                                }
                            } else if (info == 'private_key') {
                                if (jsonData[info + i]) {
                                    $('#key_text' + i).html(jsonData[info + i]);
                                }
                            } else if (info == 'trust_crt') {
                                if (jsonData[info + i]) {
                                    $('#trust_text' + i).html(jsonData[info + i]);
                                }
                            } else if (info == 'iec61850_key') {
                                if (jsonData[info + i]) {
                                    $('#iec61850_key_text' + i).html(jsonData[info + i]);
                                }
                            }  else if (info == 'iec61850_cert') {
                                if (jsonData[info + i]) {
                                    $('#iec61850_cert_text' + i).html(jsonData[info + i]);
                                }
                            }  else if (info == 'iec61850_root_cert') {
                                if (jsonData[info + i]) {
                                    $('#iec61850_root_cert_text' + i).html(jsonData[info + i]);
                                }
                            } else {
                                $('#' + info + i).val(jsonData[info + i]);
                            } 
                        })

                        tcpProtocolChange(i);
                        if (jsonData['security_policy' + i] == '0') {
                            $('#page_security' + i).hide();
                        } else {
                            $('#page_security' + i).show();
                        }
                    } else {
                        $('#page_tcp' + i).hide(); 
                        $('#tcp_disable' + i).prop('checked', true);
                    }
                } 
            }
            

            $('#loading').hide();
        });
    }
    
    globalThis.loadInterfacesConfig = loadInterfacesConfig;
    loadInterfacesConfig();
}