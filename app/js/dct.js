/*
version: 1.0.0
*/

function getReportingCenterFlag(p) {
    let flag = 0;

    if (p && p.length > 0) {
        const parts = p.split('-');
        for (let i = 0; i < parts.length && i < 5; i++) {
            const n = parseInt(parts[i], 10);
            if (n > 0 && n <= 5) {
                flag |= (1 << (n - 1));
            }
        }
    }

    return flag;
}

function doesColumnExist(tableId, columnName) {
    var table = document.getElementById(tableId);
    var headers = table.querySelectorAll('th');
    for (var i = 0; i < headers.length; i++) {
        if (headers[i].textContent.trim() === columnName) {
            return true;
        }
    }
    return false;
}

function writeValueByTag(object) {
    var tds = $(object).parent().parent().find("td");
    var tagName = tds.filter('[name="factor_name"]').text();
    var serverCenter = tds.filter('[name="server_center"]').text();
    if (serverCenter == '' || serverCenter == '-') {
        serverCenter = '0';
    }

    var flag = getReportingCenterFlag(serverCenter);

    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    overlay.style.zIndex = '999';

    const popup = document.createElement('div');
    popup.style.position = 'fixed';
    popup.style.top = '30%';
    popup.style.left = '50%';
    popup.style.backgroundColor = 'white';
    popup.style.padding = '10px';
    popup.style.border = '1px solid #ccc';
    popup.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.1)';
    popup.style.zIndex = '1000';

    const container = document.createElement("div");
    container.style.display = "flex";
    container.style.alignItems = "center";
    container.style.gap = "10px";

    let labelOrSelect;
    if (tagName.includes(';')) {
        const select = document.createElement('select');
        select.style.display = 'block';
        select.style.marginBottom = '10px';
        select.style.padding = '5px';
        tagName.split(';').forEach(function(item) {
            const option = document.createElement('option');
            option.value = item;
            option.textContent = item;
            select.appendChild(option);
        });
        labelOrSelect = select;
    } else {
        const label = document.createElement('label');
        label.textContent = tagName + ':';
        label.style.display = 'block';
        label.style.marginBottom = '10px';
        labelOrSelect = label;
    }

    const input = document.createElement('input');
    // input.type = 'number';
    input.style.display = 'block';
    input.style.marginBottom = '10px';
    input.style.width = '60%';
    input.style.padding = '5px';

    const writeButton = document.createElement('button');
    writeButton.textContent = 'Write';
    writeButton.style.display = 'block';
    writeButton.style.marginTop = '-10px';
    writeButton.style.padding = '5px 10px';
    writeButton.style.backgroundColor = '#007BFF';
    writeButton.style.color = 'white';
    writeButton.style.border = 'none';
    writeButton.style.cursor = 'pointer';

    const container_close = document.createElement("div");
    container_close.style.display = "flex";
    container_close.style.justifyContent = "flex-end";
    container_close.style.width = "100%";

    const closeButton = document.createElement('button');
    closeButton.textContent = 'X';
    closeButton.style.display = 'block';
    closeButton.style.marginBottom = '30px';
    closeButton.style.backgroundColor = 'red';
    closeButton.style.color = 'white';
    closeButton.style.border = 'none';
    closeButton.style.cursor = 'pointer';

    container.appendChild(labelOrSelect);
    container.appendChild(input);
    container.appendChild(writeButton);

    container_close.appendChild(closeButton);

    popup.appendChild(container_close);
    popup.appendChild(container);

    document.body.appendChild(popup);
    document.body.appendChild(overlay);

    closeButton.addEventListener('click', () => {
        popup.style.display = 'none';
        overlay.style.display = 'none';
    });

    writeButton.addEventListener('click', () => {
        let params = '';
        if (tagName.includes(';')) {
            const selectedValue = labelOrSelect.value;
            params = 'tagName=' + selectedValue + ';' + flag + '&' + 'value=' + input.value;
        } else {
            params = 'tagName=' + tagName + ';' + flag + '&' + 'value=' + input.value
        }
        
        // console.log(params);
        if (input.value.length > 0) {
            $.get('ajax/dct/get_dctcfg.php?type=tag_write&' + params, function(data) {}); 
        } else {
            alert("The input cannot be empty!");
        }
    });
}

function insertColumn(tableId, name, headerName, newHeaderName) {
    if (doesColumnExist(tableId, newHeaderName))
        return;

    if ((tableId == 'table_adc' || tableId == 'table_di' || tableId == 'table_modbus_slave_point' || 
        tableId == 'table_dnp3') && newHeaderName == 'Write Value') {
        return;
    }

    var table = document.getElementById(tableId);
    var rows = table.getElementsByTagName('tr');
    var th_num = 0;

    var headers = table.getElementsByTagName('th');
    var columnIndex = 0;
    
    for (var i = 0; i < headers.length; i++) {
        if (headers[i].textContent === headerName) {
            columnIndex = i + 1;
        } else if (headers[i].textContent === 'Source Object') {
            columnIndex = i + 1;
        }
    }

    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var td = document.createElement('td');
        td.setAttribute('name', name);
        td.style.fontWeight = "bold";
        td.style.color = "blue";
        td.style.textAlign = 'center';
        if (newHeaderName == 'Write Value') {
            let button = document.createElement("button");
            button.textContent = "Write";
            button.classList.add("btn-primary");
            button.style = "border-radius: 0.5rem;";
            button.addEventListener("click", function (event) {
                event.preventDefault();
                writeValueByTag(this);
            });

            td.appendChild(button);
        } else {
            td.innerHTML = '-';
        }
        

        if (row.getElementsByTagName('th').length > 0) {
            var th = document.createElement('th');
            th.classList.add("th");
            th.classList.add("cbi-section-table-cell");
            if (th_num == 0) {
                th.innerHTML = newHeaderName;
                th_num++;
            }

            row.insertBefore(th, row.cells[columnIndex]);
        } else {
            row.insertBefore(td, row.cells[columnIndex]);
        }
    }
}

function deleteColumnByHeader(tableId, headerName) {
    if (!doesColumnExist(tableId, headerName))
        return;

    var table = document.getElementById(tableId);
    const cells = table.getElementsByTagName('th');
    for (let i = 0; i < cells.length; i++) {
        if (cells[i].textContent === headerName) {
            const columnIndex = i;
            const rows = table.rows;
            for (let j = 0; j < rows.length; j++) {
                rows[j].deleteCell(columnIndex);
            }
            break;
        }
    }
}

/*basic*/
function loadBasicConfig() {
    $('#loading').show();
    $.get('ajax/dct/get_dctcfg.php?type=basic',function(data) {
        var jsonData = JSON.parse(data);
        var arr = ['collect_period', 'report_period', 'batch_reporting', 'cache_enabled', 'cache_day', 'minute_enabled',
        'minute_period', 'hour_enabled', 'day_enabled'];

        $('#enabled').val(jsonData.enabled);
        if (jsonData.enabled == '1') {
            $('#page_basic').show();
            $('#basic_enable').prop('checked', true);

            arr.forEach(function (info) {
                if (info == null) {
                    return true;    // continue: return true; break: return false
                }
    
                if (info == 'cache_enabled' || info == 'minute_enabled' || info == 'hour_enabled' || 
                    info == 'day_enabled' || info == 'batch_reporting') {
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

function enableBasic(state) {
    if (state) {
      $('#page_basic').show();
      enableCache(document.getElementById('cache_enabled'));
    } else {
      $('#page_basic').hide();
    }
}

function enableCache(checkbox) {
    if (checkbox.checked == true) {
        $("#page_cache_days").show();
    } else {
        $("#page_cache_days").hide();
    }
}

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

function comProtocolChange(num) {
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

function dlmsAuthChangeCom(num) {
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

function dlmsAuthChangeTcp(num) {
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

function dlmsSecurityChangeCom(num)
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

function dlmsSecurityChangeTcp(num)
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

function snmpVersionChangeTcp(num) {
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

function securityLevelChangeTcp(num) {
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

function tcpProtocolChange(num) {
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
    } else {
        $('#tcp_page_protocol_modbus' + numStr).show();
    }
}

function enableCom(state, num) {
    var numStr = num.toString();

    if (state) {
        $('#page_com' + numStr).show();
        comProtocolChange(num);
    } else {
        $('#page_com' + numStr).hide();
    }
}

function enableTcp(state, num) {
    var numStr = num.toString();

    if (state) {
        $('#page_tcp' + numStr).show();
        tcpProtocolChange(num);
    } else {
        $('#page_tcp' + numStr).hide();
    }
}

function securityChangeTcp(num) {
    if ($('#security_policy' + num).val() == '0')  {
        $('#page_security' + num).hide();
    } else {
        $('#page_security' + num).show();
    }
}

function certChangeTcp(num) {
    $('#cert_text' + num).html($('#certificate' + num)[0].files[0].name);
}

function keyChangeTcp(num) {
    $('#key_text' + num).html($('#private_key' + num)[0].files[0].name);
}

function trustChangeTcp(num) {
    var file = $('#trust_crt' + num)[0].files;
    var str = '';
    for (var i = 0, len = file.length; i < len; i++) {
        str += file[i].name;
        if (i < len - 1)
        str += ";";
    }

    $('#trust_text' + num).html(str);
}

function anonymousCheckTcp(num) {
    if ($('#anonymous' + num).is(':checked'))  {
        $('#page_anonymous' + num).hide();
    } else {
        $('#page_anonymous' + num).show();
    }
}

/*rule common*/
function openBox(table_name) {
    $('#popBox').show();
    $('#popLayer').show();
    document.getElementById("popBox").scrollTop = 0;
    selectOperator(table_name);
}

function closeBox() {
    $('#popBox').hide();
    $('#popLayer').hide();
}

function selectOperator(table_name) {
    if (document.getElementById(table_name+'.operator')) {
        var operator = document.getElementById(table_name+'.operator').value;

        $('#page_operand').hide();
        $('#page_ex').hide();
        if (operator == "0") {
            $('#page_operand').hide();
            $('#page_ex').hide();
        } else if (operator == "5") {
            $('#page_operand').hide();
            $('#page_ex').show();
        } else {
            $('#page_operand').show();
            $('#page_ex').hide();
        }
    }
}

function enableAlarm(table_name) {
    var checkbox = document.getElementById(table_name+'.sms_reporting')
    if (checkbox != null) {
        if (checkbox.checked == true) {
            $('#page_sms').show();
            selectReportType(table_name);
        } else {
            $('#page_sms').hide();
        }
    }
}

function selectReportType(table_name) {
    if (document.getElementById(table_name+'.report_type')) {
        var operator = document.getElementById(table_name+'.report_type').value;

        $('#page_alarm').hide();
        if (operator == "0") {
            $('#page_alarm').hide();
        } else {
            $('#page_alarm').show();
        }
    }
}

function get_data_type_value(table_name) {
    var data_type_value = [];

    if (table_name == 'modbus' || table_name == 'modbus_slave_point') {
        data_type_value = ['Bit', 'Unsigned 16Bits AB', 'Unsigned 16Bits BA', 'Signed 16Bits AB', 'Signed 16Bits BA',
        'Unsigned 32Bits ABCD', 'Unsigned 32Bits BADC', 'Unsigned 32Bits CDAB', 'Unsigned 32Bits DCBA',
        'Signed 32Bits ABCD', 'Signed 32Bits BADC', 'Signed 32Bits CDAB', 'Signed 32Bits DCBA',
        'Float ABCD', 'Float BADC', 'Float CDAB', 'Float DCBA'];
    } else if (table_name == 'fx') {
        data_type_value = ['Bit', 'Byte', 'Word', 'DWord', 'Real'];
    } else if (table_name == 's7') {
        data_type_value = ['Bit', 'Byte', 'Word', 'DWord', 'Real', 'Counter', 'Timer'];
    } else if (table_name == 'mc' || table_name == 'iec104') {
        data_type_value = ['Bit', 'Int', 'Float'];
    } else if (table_name == 'opcuacli') {
        data_type_value = ['Bool', 'Byte', 'Int16', 'UInt16', 'Int32', 'UInt32', 'Float', 'String'];
    } else if (table_name == 'ethernetip') {
        data_type_value = ['Bool', 'Int16', 'UInt16', 'Int32', 'UInt32', 'Int64', 'UInt64', 'Float', 'Double', 'String'];
    } else if (table_name == 'snmpcli') {
        data_type_value = ['Int32', 'UInt32', 'Counter64', 'String'];
    } else if (table_name == 'mbuscli') {
        data_type_value = ['Double', 'String'];
    } else if (table_name == 'iec1107' || table_name == 'dlms') {
        data_type_value = ['Int', 'Float', 'String'];
    }

    return data_type_value;
}

function addSectionTable(table_name, jsonData, option_list) {
    var mode = 0;
    var data_type_value = [];
    var reg_type_value = [];
    var cap_type_value = ['4-20mA', '0-10V'];
    var mode_value = ['Counting Mode', 'Status Mode'];
    var count_method_value = ['Rising Edge', 'Falling Edge'];
    var status_value = ['Open', 'Close'];
    var type_id_list = {'1':'M_SP_NA_1', '30':'M_SP_TB_1', '3':'M_DP_NA_1', '31':'M_DP_TB_1', '5':'M_ST_NA_1', '32':'M_ST_TB_1',
    '7':'M_BO_NA_1', '33':'M_BO_TB_1', '9':'M_ME_NA_1', '34':'M_ME_TD_1', '21':'M_ME_ND_1', '11':'M_ME_NB_1', '35':'M_ME_TE_1', '13':'M_ME_NC_1', 
    '36':'M_ME_TF_1', '15':'M_IT_NA_1', '37':'M_IT_TB_1', '38':'M_EP_TD_1'};

    if (option_list != null)
        $('#option_list_'+table_name).val(option_list);

    if (jsonData == null)
        return;

    if (table_name == 'fx') {
        reg_type_value = ['X', 'Y', 'M', 'S', 'D'];
    } else if (table_name == 's7') {
        reg_type_value = ['I', 'Q', 'M', 'DB', 'V', 'C', 'T'];
    }

    data_type_value = get_data_type_value(table_name);
    
    var len = Number(jsonData.length);
    for (var i = 0; i < len; i++) {
        var table = document.getElementById("table_" + table_name);
        var contents = '';
        contents += '<tr  class="tr cbi-section-table-descr">\n';
        
        if (jsonData[i].hasOwnProperty('mode')) {
            mode = Number(jsonData[i]['mode']);
        }

        option_list.forEach(function(key){
            if (!jsonData[i].hasOwnProperty(key)) {
                if (key == 'operator' || key == 'operand' || key == 'ex' || key == 'accuracy' ||
                key == 'report_type' || key == 'alarm_up' || key == 'alarm_down' || key == 'phone_num' || 
                key == 'email' || key == 'event_server_center' || key == 'contents' || key == 'retry_interval' || key == 'again_interval') {
                    contents += '   <td style="display:none" name="'+key+'">-</td>\n';
                } else if (key == 'enabled' || key == 'sms_reporting') {
                    contents += '   <td style="' + ((key == 'enabled') ? 'text-align:center' : 'display:none') + '"><input type="checkbox" name="' +
                             key + '" ' + (jsonData[i][key] == '1' ? 'checked' : ' ') + 
                             ' onclick="updateData(\''+table_name+'\')"></td>\n';
                } else {
                    contents += '   <td style="text-align:center" name="'+key+'">-</td>\n';
                }
                
                return;
            }

            if (key == "tx_cmd") {
                if (jsonData[i][key].includes('\r\n')) {
                    jsonData[i][key] = jsonData[i][key].replace(/\r/g, "\\\\r").replace(/\n/g, "\\\\n");
                } else if (jsonData[i][key].includes('\r')) {
                    jsonData[i][key] = jsonData[i][key].replace(/\r/g, "\\\\r");
                } else if (jsonData[i][key].includes('\n')) {
                    jsonData[i][key] = jsonData[i][key].replace(/\n/g, "\\\\n");
                }
            }

            if (key == 'operator' || key == 'operand' || key == 'ex' || key == 'accuracy' ||
            key == 'report_type' || key == 'alarm_up' || key == 'alarm_down' || key == 'phone_num' || 
            key == 'email' || key == 'event_server_center' || key == 'contents' || key == 'retry_interval' || key == 'again_interval') {
                contents += '   <td style="display:none" name="'+key+'">'+ (jsonData[i][key] != null ? jsonData[i][key] : "-") +'</td>\n';
            } else if (key == 'data_type') {
                contents += '   <td style="text-align:center" name="'+key+'">'+ (data_type_value[Number(jsonData[i][key])]) +'</td>\n';
            } else if (key == 'reg_type') {
                contents += '   <td style="text-align:center" name="'+key+'">'+ (reg_type_value[Number(jsonData[i][key])]) +'</td>\n';
            } else if (key == 'word_len') {
                contents += '   <td style="text-align:center" name="'+key+'">'+ (data_type_value[Number(jsonData[i][key])]) +'</td>\n';
            } else if (key == 'cap_type') {
                contents += '   <td style="text-align:center" name="'+key+'">'+ (cap_type_value[Number(jsonData[i][key])]) +'</td>\n';
            } else if (key == 'mode') {
                contents += '   <td style="text-align:center" name="'+key+'">'+ (mode_value[Number(jsonData[i][key])]) +'</td>\n';
            } else if (key == 'count_method') {
                contents += ('   <td style="text-align:center" name="'+key+'">'+ (((mode == 1) ? '-' : count_method_value[Number(jsonData[i][key])])) +'</td>\n');
            } else if (key == 'init_status') {
                contents += '   <td style="text-align:center" name="'+key+'">'+ (status_value[Number(jsonData[i][key])]) +'</td>\n';
            } else if (key == 'cur_status') {
                var cur_status = jsonData[i][key];
                if (cur_status == '0' || cur_status == '1')
                    cur_status = status_value[Number(cur_status)];
                
                contents += '   <td style="text-align:center" name="'+key+'">'+ cur_status +'</td>\n';
            } else if (key == 'enabled' || key == 'sms_reporting') {
                contents += '   <td style="' + ((key == 'enabled') ? 'text-align:center' : 'display:none') + '"><input type="checkbox" name="' +
                             key + '" ' + (jsonData[i][key] == '1' ? 'checked' : ' ') + 
                             ' onclick="updateData(\''+table_name+'\')"></td>\n';
            } else if (key == 'belonged_com' && jsonData[i][key].includes('TCP')) {
                contents += '   <td style="text-align:center" name="'+key+'">Network Node'+ jsonData[i][key][3] +'</td>\n';
            } else {
                contents += '   <td style="text-align:center" name="'+key+'">'+ ((mode == 1 && key == 'debounce_interval') ? '-' : jsonData[i][key]) +'</td>\n';
            }

        })
        contents += '   <td><a href="javascript:void(0);" onclick="editData(this, \''+table_name+'\');" >Edit</a></td>\n' +
            '       <td><a href="javascript:void(0);" onclick="delData(this, \''+table_name+'\');" >Del</a></td>\n' +
            '   </tr>';
        table.innerHTML += contents;
    }

    insertColumn("table_" + table_name, 'cur_value', 'Tag Name', 'Current Value');
    insertColumn("table_" + table_name, 'write_value', 'Current Value', 'Write Value');

    var result = get_table_data(table_name, option_list);
    var json_data = JSON.stringify(result);
    $('#hidTD_'+table_name).val(json_data);
}

/*datadisplay*/
function getRealtimeData() {
    $.get('ajax/dct/get_dctcfg.php?type=datadisplay', function(data) {
        if (!data.includes('"data"')) {
            return;
        }
        // console.log(data);
        tmp = JSON.parse(data);
        jsonData = JSON.parse(tmp['data']);
        
        if (jsonData == null)
            return false;

        const jsonResult = Object.entries(jsonData).map(([key, value]) => {
            if (key.includes(";"))  {
                const [name, index] = key.split(";");
                return [name, index, value];
            } else {
                return [key, 0, value];
            }
        });

        const trList = document.querySelectorAll('table tr');
        var dnp3 = document.getElementById('option_list_dnp3');
        var modbus_slave = document.getElementById('option_list_modbus_slave_point');
        trList.forEach((tr) => {
            var cur_value = '';
            if (dnp3 || modbus_slave) {
                if (tr.querySelector('td[name="source_object"]')) {
                    var factor = tr.querySelector('td[name="source_object"]').innerHTML;
                    factor = factor.substring(factor.indexOf('-') + 1)
                    const jsonItem = jsonResult.find(([name]) => name === factor);
                    if (jsonItem) {
                        cur_value += jsonItem[2];
                    }
                }
            } else {
                if (tr.querySelector('td[name="factor_name"]')) {
                    //console.log(tr.querySelector('td[name="factor_name"]').innerHTML);
                    var factor = tr.querySelector('td[name="factor_name"]').innerHTML;
                    var serverCenter = tr.querySelector('td[name="server_center"]').innerHTML;
                    var factorList = factor.split(';');
                    var flag = getReportingCenterFlag(serverCenter);
                    factorList.forEach((key) => {
                        // console.log(flag);
                        var jsonValue = '';
                        jsonResult.forEach(item => {
                            const [name, index, value] = item;
                            if (name == key && (flag == parseInt(index) || index == 0)) {
                                jsonValue = value;
                                return;
                            }
                        });
                        cur_value += jsonValue + ';'; 
                    })

                    if (cur_value.slice(-1) === ';') {
                        cur_value = cur_value.slice(0, -1);
                    }
                }
            }
            
            if (tr.querySelector('td[name="cur_value"]')) {
                tr.querySelector('td[name="cur_value"]').innerHTML = cur_value.length > 0 ? cur_value : '-';
            }
        });
    });

    return true;
}

function loadRealtimeData() {
    if (getRealtimeData()) {
        setInterval(getRealtimeData, 1000);
    }  
}

/*Rules*/
function loadRulesConfig(table_name) {
    $('#loading').show();
    $.get('ajax/dct/get_dctcfg.php?type=' + table_name + '&rule=1',function(data){
        // console.log(data);
        var jsonData = JSON.parse(data);
        if (jsonData == null)
            return;

        var option_list = jsonData.option;
        var tmpData = JSON.parse(jsonData[table_name]);
        addSectionTable(table_name, tmpData, option_list);
        $('#loading').hide();
    });

    loadRealtimeData();
}

function snmpScan() {
    $('#loading').show();
    const btn = document.getElementById("btn_scan");
    btn.disabled = true;
    const interface = document.getElementById('scan_interface').value;
    const oid = document.getElementById('scan_oid').value;
    $.get('ajax/dct/get_dctcfg.php?type=snmp_scan&interface=' + interface + '&oid=' + oid, function(data) {
        // console.log(data);
        $('#snmp_result_area').val(data);
        $('#loading').hide();
        btn.disabled = false;
    })
}

function dlmsScan() {
    $('#loading').show();
    const btn = document.getElementById("btn_scan");
    btn.disabled = true;
    const interface = document.getElementById('scan_interface').value;
    $.get('ajax/dct/get_dctcfg.php?type=dlms_scan&interface=' + interface, function(data) {
        // console.log(data);
        $('#dlms_result_area').val(data);
        $('#loading').hide();
        btn.disabled = false;
    })
}

function parseMBusXML(xmlString) {
  const parser = new DOMParser();
  const xmlDoc = parser.parseFromString(xmlString, "application/xml");

  // SlaveInformation
  const slaveInfo = {};
  const infoNode = xmlDoc.querySelector("SlaveInformation");
  if (infoNode) {
    infoNode.childNodes.forEach(node => {
      if (node.nodeType === 1) {
        slaveInfo[node.nodeName] = node.textContent;
      }
    });
  }

  // DataRecord
  const records = [];
  xmlDoc.querySelectorAll("DataRecord").forEach(rec => {
    const obj = { id: rec.getAttribute("id") };
    rec.childNodes.forEach(node => {
      if (node.nodeType === 1) {
        obj[node.nodeName] = node.textContent.trim();
      }
    });
    records.push(obj);
  });

  return { slaveInfo, records };
}

function formatValue(value, unit) {
  if (value === null || value === undefined || value.trim() === "") {
    return "";
  }

  let num = Number(value);
  if (isNaN(num)) return value;

  if (unit && unit.trim() !== "" && unit !== "-") {
    return num.toFixed(6).replace(/\.?0+$/, "");
  } else {
    return Math.floor(num).toString();
  }
}

function mbusScan() {
    $('#loading').show();
    const btn = document.getElementById("btn_scan");
    btn.disabled = true;
    document.getElementById("output").innerHTML = "";
    const interface = document.getElementById('scan_interface').value;
    const address = document.getElementById('scan_address').value;
    $.get('ajax/dct/get_dctcfg.php?type=mbus_scan&interface=' + interface + '&address=' + address, function(data) {
        let isXml = data.trim().startsWith('<') && data.trim().endsWith('>');
        const output = document.getElementById("output");
        if (isXml) {
            const { slaveInfo, records } = parseMBusXML(data);
            let html = "";

            // Slave Information
            html += `<div class="section"><div class="title">Slave Information</div>`;
            html += `<table><tbody>`;
            for (const key in slaveInfo) {
                html += `<tr><th>${key}</th><td>${slaveInfo[key]}</td></tr>`;
            }
            html += `</tbody></table></div>`;

            // Data Records
            html += `<div class="section"><div class="title">Data Records</div>`;
            html += `<table><thead><tr><th>ID</th><th>Quantity</th><th>Value</th><th>Unit</th></tr></thead><tbody>`;
            records.forEach(rec => {
                const valueText = formatValue(rec.Value, rec.Unit);
                html += `<tr><td>${rec.id}</td><td>${rec.Quantity}</td><td>${valueText}</td><td>${rec.Unit}</td></tr>`;
            });
            html += `</tbody></table></div>`;

            output.innerHTML = html;
        } else {
            output.innerHTML = '<span style="color:red;">' + data + '</span>';
        }
        $('#loading').hide();
        btn.disabled = false;
    })
}

function get_bacnet_server_discover(callback) {
    const interface = document.getElementById('baccli.belonged_com').value;
    // console.log(interface);
    $.get('ajax/dct/get_dctcfg.php?type=bacdiscover&interface=' + interface, function(data) {
        callback(data);
    })
}

function updateDeviceIdList() {
    const options_device_id = [];
    const device_id_list = document.getElementById('deviceIdList');

    get_bacnet_server_discover(function(data) {
        // console.log(data);
        if (data && data != 'null' && data != '[]') {
            $('#bacnet_discover_data').val(data);
            var jsonData = JSON.parse(data);
            for (var i = 0; i < jsonData.length; i++) {
                options_device_id[i] = jsonData[i].device_id;
            }

            device_id_list.innerHTML = '';
            options_device_id.forEach(option => {
                const div = document.createElement('div');
                div.textContent = option;
                div.onclick = () => selectItem(option);
                device_id_list.appendChild(div);
            });
            device_id_list.classList.add('show');
        } else {
            $('#bacnet_discover_data').val("");
            device_id_list.innerHTML = '';
            document.getElementById('baccli.object_device_id').value = "-";
        }
    });
}

function get_iec104_server_discover(callback) {
    const interface = document.getElementById('iec104.belonged_com').value;
    // console.log(interface);
    $.get('ajax/dct/get_dctcfg.php?type=iec104discover&interface=' + interface, function(data) {
        callback(data);
    })
}

function selectItemIec104(value) {
    const input = document.getElementById('iec104.type_id');
    const list = document.getElementById('typeIdList');
    // console.log(value);
    input.value = value;
    list.classList.remove('show');
}

function iec104FilterFunction() {
    const list = document.getElementById('typeIdList');
    const options_type_id = [];
    var data = document.getElementById('iec104_discover_data').value;

    if (data.length < 3)
        return;

    // console.log(data);
    var jsonData = JSON.parse(data);
    
    for (var i = 0; i < jsonData.length; i++) {
        options_type_id[i] = jsonData[i];
    }

    filteredOptions = options_type_id;
    list.innerHTML = '';
    if (filteredOptions.length > 0) {
        filteredOptions.forEach(option => {
            // console.log(option);
            const div = document.createElement('div');
            div.textContent = option;
            div.onclick = () => selectItemIec104(option);
            list.appendChild(div);
        });
        list.classList.add('show');
    } else {
        list.classList.remove('show');
    }
}

function updateTypeIdList() {
    const options_type_id = [];
    const list = document.getElementById('typeIdList');

    get_iec104_server_discover(function(data) {
        if (data && data != 'null' && data != '[]') {
            $('#iec104_discover_data').val(data);
            var jsonData = JSON.parse(data);
            list.innerHTML = '';
            for (var i = 0; i < jsonData.length; i++) {
                options_type_id[i] = jsonData[i];
            }

            list.innerHTML = '';
            options_type_id.forEach(option => {
                const div = document.createElement('div');
                div.textContent = option;
                div.onclick = () => selectItemIec104(option);
                list.appendChild(div);
            });
            list.classList.add('show');
        } else {
            $('#iec104_discover_data').val("");
            list.innerHTML = '';
            document.getElementById('iec104.type_id').value = "-";
        }
    });
}

function get_iec1107_server_discover(callback) {
    const interface = document.getElementById('iec1107.belonged_com').value;
    // console.log(interface);
    $.get('ajax/dct/get_dctcfg.php?type=iec1107discover&interface=' + interface, function(data) {
        callback(data);
    })
}

function selectItemIec1107(value) {
    const input = document.getElementById('iec1107.obis');
    const list = document.getElementById('obisList');
    // console.log(value);
    input.value = value;
    list.classList.remove('show');
}

function iec1107FilterFunction() {
    const list = document.getElementById('obisList');
    const options_type_id = [];
    var data = document.getElementById('iec1107_discover_data').value;

    if (data.length < 3)
        return;

    // console.log(data);
    var jsonData = JSON.parse(data);
    
    for (var i = 0; i < jsonData.length; i++) {
        options_type_id[i] = jsonData[i];
    }

    filteredOptions = options_type_id;
    list.innerHTML = '';
    if (filteredOptions.length > 0) {
        filteredOptions.forEach(option => {
            // console.log(option);
            const div = document.createElement('div');
            div.textContent = option;
            div.onclick = () => selectItemIec1107(option);
            list.appendChild(div);
        });
        list.classList.add('show');
    } else {
        list.classList.remove('show');
    }
}


function updateObisList() {
    const options_type_id = [];
    const list = document.getElementById('obisList');

    get_iec1107_server_discover(function(data) {
        if (data && data != 'null' && data != '[]') {
            $('#iec1107_discover_data').val(data);
            var jsonData = JSON.parse(data);
            list.innerHTML = '';
            for (var i = 0; i < jsonData.length; i++) {
                options_type_id[i] = jsonData[i];
            }

            list.innerHTML = '';
            options_type_id.forEach(option => {
                const div = document.createElement('div');
                div.textContent = option;
                div.onclick = () => selectItemIec1107(option);
                list.appendChild(div);
            });
            list.classList.add('show');
        } else {
            $('#iec1107_discover_data').val("");
            list.innerHTML = '';
            document.getElementById('iec1107.obis').value = "-";
        }
    });
}

function selectItem(value) {
    const input = document.getElementById('baccli.object_device_id');
    const device_id_list = document.getElementById('deviceIdList');

    input.value = value;
    device_id_list.classList.remove('show');

    filterFunctionObject();
}

function selectItemObject(value) {
    const input = document.getElementById('baccli.object_id');
    const device_id_list = document.getElementById('objectIdList');

    input.value = value;
    device_id_list.classList.remove('show');
}

function filterFunction() {
    const input = document.getElementById('baccli.object_device_id');
    const device_id_list = document.getElementById('deviceIdList');
    const options_device_id = [];
    var data = document.getElementById('bacnet_discover_data').value;

    if (data.length < 3)
        return;

    // console.log(data);
    var jsonData = JSON.parse(data);

    for (var i = 0; i < jsonData.length; i++) {
        options_device_id[i] = jsonData[i].device_id;
    }

    // console.log(options_device_id);
    // const filter = input.value.toLowerCase();
    filteredOptions = options_device_id;
    //const filteredOptions = options_device_id.filter(option => option.toLowerCase().includes(filter));
    device_id_list.innerHTML = '';
    if (filteredOptions.length > 0) {
        filteredOptions.forEach(option => {
            const div = document.createElement('div');
            div.textContent = option;
            div.onclick = () => selectItem(option);
            device_id_list.appendChild(div);
        });
        device_id_list.classList.add('show');
    } else {
        device_id_list.classList.remove('show');
    }
}

function filterFunctionObject() {
    const object_id_list = document.getElementById('objectIdList');
    const options_object_id = [];
    var cur_device_id = document.getElementById('baccli.object_device_id');
    if (!cur_device_id.value) {
        return;
    } else {
        cur_device_id = cur_device_id.value;
    }

    var data = document.getElementById('bacnet_discover_data').value;
    if (data.length < 3)
        return;

    // console.log(data);
    var jsonData = JSON.parse(data);
    var jsonObject = '';

    for (var i = 0; i < jsonData.length; i++) {
        if (jsonData[i].device_id == cur_device_id) {
            jsonObject = jsonData[i].object_identifier;
            break;
        }
    }

    if (jsonObject.length > 0) {
        for (var i = 0; i < jsonObject.length; i++) {
            options_object_id[i] = jsonObject[i];
        }
    } else {
        return;
    }

    // console.log(options_device_id);
    // const filter = input.value.toLowerCase();
    filteredOptions = options_object_id;
    //const filteredOptions = options_device_id.filter(option => option.toLowerCase().includes(filter));
    object_id_list.innerHTML = '';
    if (filteredOptions.length > 0) {
        filteredOptions.forEach(option => {
            const div = document.createElement('div');
            div.textContent = option;
            div.onclick = () => selectItemObject(option);
            object_id_list.appendChild(div);
        });
        object_id_list.classList.add('show');
    } else {
        object_id_list.classList.remove('show');
    }
}

/*BACnet Rules*/
function loadBACnetClientConfig() {
    $('#loading').show();
    var table_name = 'baccli';
    $.get('ajax/dct/get_dctcfg.php?type=' + table_name,function(data){
        var jsonData = JSON.parse(data);
        if (jsonData == null)
            return;

        var option_list = jsonData.option;
        var tmpData = JSON.parse(jsonData[table_name]);
        addSectionTable(table_name, tmpData, option_list);

        loadRealtimeData();
        $('#loading').hide();
    });

    const input = document.getElementById('baccli.object_device_id');
    const input_object = document.getElementById('baccli.object_id');
    const device_id_list = document.getElementById('deviceIdList');
    const object_id_list = document.getElementById('objectIdList');

    input.addEventListener('focus', () => {
        filterFunction();
    });

    input_object.addEventListener('focus', () => {
        filterFunctionObject();
    });

    document.addEventListener('click', (event) => {
        const escapedId = CSS.escape('baccli.object_device_id');
        const escapedIdObject = CSS.escape('baccli.object_id');
        if (!event.target.matches(`#${escapedId}`)) {
            device_id_list.classList.remove('show');
        }

        if (!event.target.matches(`#${escapedIdObject}`)) {
            object_id_list.classList.remove('show');
        }
    });

    // updateDeviceIdList();
}

$('.btn_bacdiscover').click(function(){
    // console.log("btn_bacdiscover");
    updateDeviceIdList();
})

$('.btn_iec104discover').click(function(){
    // console.log("btn_iec104discover");
    updateTypeIdList();
})

$('.btn_iec1107discover').click(function(){
    // console.log("btn_iec104discover");
    updateObisList();
})

function enableBACnet(state) {
    if (state) {
      $('#page_bacnet').show();
    } else {
      $('#page_bacnet').hide();
    }
}

function enableModbus(state) {
    if (state) {
      $('#page_modbus').show();
    } else {
      $('#page_modbus').hide();
    }
}


function bacnetProtocolChange()
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

function enableBBMD() {
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

/*reporting server*/
function loadServerConfig() {
    $('#loading').show();
    $.get('ajax/dct/get_dctcfg.php?type=server',function(data){
        var jsonData = JSON.parse(data);

        var arr = ["proto", "encap_type", "json_format", "server_addr", "http_url", "server_port", "cache_enabled", 
        "register_packet", "register_packet_hex", "heartbeat_packet", "heartbeat_packet_hex", "heartbeat_interval",
        "mqtt_heartbeat_interval", "mqtt_pub_topic", "mqtt_sub_topic", "mqtt_username", "mqtt_password", "sparkplug_group_id",
        "sparkplug_node_id", "sparkplug_device_id", "mqtt_client_id", "mqtt_tls_enabled", "certificate_type", "mqtt_ca", "mqtt_cert", "mqtt_key", 
        "self_define_var", "var_name1_", "var_value1_", "var_name2_", "var_value2_", "var_name3_", "var_value3_", 
        "mn", "st", "pw"];

        for (var i = 1; i <= 5; i++) {
            $('#enabled' + i).val(jsonData['enabled' + i]);
            if (jsonData['enabled' + i] == '1') {
                $('#page_server' + i).show();
                $('#enable' + i).prop('checked', true);
                arr.forEach(function (info) {
                    if (info == "cache_enabled" || info == "register_packet_hex" || info == "heartbeat_packet_hex" ||
                        info == "mqtt_tls_enabled" ||  info == "self_define_var") {
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
            } else {
                $('#page_server' + i).hide(); 
                $('#disable' + i).prop('checked', true);
            }
        }

        $('#loading').hide();
    });
}

function enableServer(state, num) {
    if (state) {
        $('#page_server' + num).show();
        protocolChange(num);
    } else {
        $('#page_server' + num).hide();
    }
}

function protocolChange(num) {
    var selectElement = document.getElementById('proto' + num);
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var selectedText = selectedOption.text;

    enableTls(num);
    cerChange(num);
    if (selectedText != 'SparkPlugB') {
        enableVar(num);
        encapChange(num);
    } else {
        $('#page_json' + num).hide();
        $('#page_hj212_' + num).hide();
    }
    
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

function encapChange(num) {
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

function jsonChange(num) {
    var select = document.getElementById('json_format' + num);
    var icon = select.nextElementSibling;
    if (!icon || !icon.classList.contains('fa-question-circle')) return;

    var value = select.value;
    if (value === "0") {
        icon.setAttribute('title', '{"ts":1747208633000,"temperature":27}');
    } else if (value === "1") {
        icon.setAttribute('title', '{"ts":1747208633000,"params":{"temperature":27}}');
    } else if (value === "2") {
        icon.setAttribute('title', '{"ts":1747208633000,"params":[{"name":"temperature", "value":27}]}');
    }

    if ($(icon).data('bs.tooltip')) {
        $(icon).attr('data-original-title', icon.getAttribute('title')).tooltip('update');
    }
}

function enableVar(num) {
    var enable_var = document.getElementById('self_define_var' + num).checked;

    if (enable_var == true) {
        $('#page_var' + num).show();
    } else {
        $('#page_var' + num).hide();
    }
}

function enableTls(num) {
    var enable_tls = document.getElementById('mqtt_tls_enabled' + num).checked;

    if (enable_tls == true) {
        $('#page_mqtt_tls' + num).show();
    } else {
        $('#page_mqtt_tls' + num).hide();
    }
}

function cerChange(num) {
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

function caFileChange(num) {
    $('#ca_text' + num).html($('#mqtt_ca' + num)[0].files[0].name);
}

function cerFileChange(num) {
    $('#cer_text' + num).html($('#mqtt_cert' + num)[0].files[0].name);
}

function keyFileChange(num) {
    $('#key_text' + num).html($('#mqtt_key' + num)[0].files[0].name);
}

/*BACnet Server*/
function loadBACnetConfig() {
    $.get('ajax/dct/get_dctcfg.php?type=bacnet',function(data){
        jsonData = JSON.parse(data);
        var arr = ['proto', 'ifname', 'port', 'interface', 'baudrate', 'mac',
                    'max_master', 'frames', 'device_id', 'object_name'];

        $('#enabled').val(jsonData.enabled);
        if (jsonData.enabled == '1') {
            $('#page_bacnet').show();
            $('#bacnet_enable').prop('checked', true);

            arr.forEach(function (info) {
                if (info == null) {
                    return true;    // continue: return true; break: return false
                }

                $('#' + info).val(jsonData[info]);
            })
        } else {
            $('#page_bacnet').hide();
            $('#bacnet_disable').prop('checked', true);
        }
        bacnetProtocolChange();
    });
}

/*OPCUA Server*/
function loadOpcuaConfig() {
    $.get('ajax/dct/get_dctcfg.php?type=opcua',function(data){
        jsonData = JSON.parse(data);
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

function anonymousCheck(check) {
    if (check.checked == true)  {
        $('#page_anonymous').hide();
    } else {
        $('#page_anonymous').show();
    }
}

function enableOpcua(state) {
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

function securityChange(state) {
    if (state.value == '0') {
        $('#page_security').hide();
    } else {
        $('#page_security').show();
    }
}

function certChange() {
    $('#cert_text').html($('#certificate')[0].files[0].name);
}

function keyChange() {
    $('#key_text').html($('#private_key')[0].files[0].name);
}

function trustChange() {
    var file = $('#trust_crt')[0].files;
    var str = '';
    for (var i = 0, len = file.length; i < len; i++) {
        str += file[i].name;
        if (i < len - 1)
        str += ";";
    }

    $('#trust_text').html(str);
}

/*configs import and export*/
function openConfBox() {
    $('#confBox').show();
    $('#confLayer').show();
    document.getElementById("confBox").scrollTop = 0;
}

function closeConfBox() {
    $('#confBox').hide();
    $('#confLayer').hide();
}

function downloadFile(conf_name) {
    let lowerConfName;
    if (conf_name == 'IO') {
        lowerConfName = document.getElementById("page_im_ex_name").value;
    } else {
        lowerConfName = conf_name.toLowerCase();
    }
    
    var req = new XMLHttpRequest();
    var url = 'ajax/dct/get_dctcfg.php?type=download_' + lowerConfName;
    req.open('get', url, true);
    req.responseType = 'blob';
    req.setRequestHeader('Content-type', 'text/plain; charset=UTF-8');
    req.onreadystatechange = function (event) {
        if(req.readyState == 4 && req.status == 200) {
            var blob = req.response;
            var link=document.createElement('a');
            link.href=window.URL.createObjectURL(blob);
            const now = new Date();
            const year = now.getFullYear();
            const month = ('0' + (now.getMonth() + 1)).slice(-2);
            const day = ('0' + now.getDate()).slice(-2);
            const hours = ('0' + now.getHours()).slice(-2);
            const minutes = ('0' + now.getMinutes()).slice(-2);
            const seconds = ('0' + now.getSeconds()).slice(-2);
            const formattedTime = year + month + day + hours + minutes;
            link.download = lowerConfName + '_' + formattedTime + '.csv';
            link.click();
        }
    }
    req.send();
}

function conf_im_ex(conf_name) {
    document.getElementById('title').innerHTML = conf_name + ' Configure Import Export';
    openConfBox();
    if (conf_name == "ADC") {
        document.getElementById("page_im_ex_name").value = "adc";
    } else if (conf_name == "DI") {
        document.getElementById("page_im_ex_name").value = "di";
        selectMode();
    } else if (conf_name == "DO") {
        document.getElementById("page_im_ex_name").value = "do";
    }
}

/*rules common*/
function addData(table_name) {
    openBox(table_name);
    document.getElementById("page_type").value = "0"; /* 0 is add. other is edit */
    enableAlarm(table_name);
}

function findKey (data, value, compare = (a, b) => a === b) {
    return Object.keys(data).find(k => compare(data[k], value))
}

function get_table_data(table_name, option_list) {
    var data_type_value = [];
    var reg_type_value = [];
    var cap_type_value = ['4-20mA', '0-10V'];
    var mode_value = ['Counting Mode', 'Status Mode'];
    var count_method_value = ['Rising Edge', 'Falling Edge'];
    var status_value = ['Open', 'Close'];
    var type_id_list = {'1':'M_SP_NA_1', '30':'M_SP_TB_1', '3':'M_DP_NA_1', '31':'M_DP_TB_1', '5':'M_ST_NA_1', '32':'M_ST_TB_1',
    '7':'M_BO_NA_1', '33':'M_BO_TB_1', '9':'M_ME_NA_1', '34':'M_ME_TD_1', '21':'M_ME_ND_1', '11':'M_ME_NB_1', '35':'M_ME_TE_1', '13':'M_ME_NC_1', 
    '36':'M_ME_TF_1', '15':'M_IT_NA_1', '37':'M_IT_TB_1', '38':'M_EP_TD_1'};

    if (table_name == 'fx') {
        reg_type_value = ['X', 'Y', 'M', 'S', 'D'];
    } else if (table_name == 's7') {
        reg_type_value = ['I', 'Q', 'M', 'DB', 'V', 'C', 'T'];
    }

    data_type_value = get_data_type_value(table_name);

    var tr = $('#table_' + table_name + ' tr');
    var result = [];
    for (var i = 2; i < tr.length; i++) {
        var tds = $(tr[i]).find("td");
        if (tds.length > 0) {
            var tmp = [];
            var num = 0;
            tmp += '{';
            option_list.forEach(function (option) {
                var val = tds.filter('[name="'+ option +'"]').text();
                // console.log(val);

                if (option == 'enabled' || option == 'sms_reporting') {
                    var check = tds.find('input[name="' + option + '"]').is(':checked');
                    // console.log(check);
                    tmp += '"' + option + '":"' + ( check ? 1 : 0) + '",';
                } else if (option == 'data_type') {
                    tmp += '"' + option + '":"' + data_type_value.indexOf(val) + '",';
                } else if (option == 'reg_type') {
                    tmp += '"' + option + '":"' + reg_type_value.indexOf(val) + '",';
                } else if (option == 'word_len') {
                    tmp += '"' + option + '":"' + data_type_value.indexOf(val) + '",';
                } else if (option == 'cap_type') {
                    tmp += '"' + option + '":"' + cap_type_value.indexOf(val) + '",';
                } else if (option == 'mode') {
                    tmp += '"' + option + '":"' + mode_value.indexOf(val) + '",';
                } else if (option == 'count_method') {
                    tmp += '"' + option + '":"' + count_method_value.indexOf(val) + '",';
                } else if (option == 'init_status') {
                    tmp += '"' + option + '":"' + status_value.indexOf(val) + '",';
                } else if (option == 'cur_status') {
                    var cur_status = val;
                    if (cur_status == '0' || cur_status == '1')
                        cur_status = status_value.indexOf(val);
                    
                    tmp += '"' + option + '":"' + cur_status + '",';
                } else if (option == "belonged_com"  && val.includes('Network')) {
                    tmp += '"' + option + '":"TCP' + val.slice(-1) + '",'
                } else  {
                    tmp += '"' + option + '":"' + val + '",';
                }
            })

            tmp = tmp.slice(-1) === "," ? tmp.slice(0, -1) + "}" : tmp + "}";
            var obj = JSON.parse(tmp);
            result.push(obj);
        }
    }

    return result;
}

function saveData(table_name) {
    var result = [];
    var mode = 0;
    var option_value = [];
    var io_type;
    var data_type_value = [];
    var reg_type_value = [];
    var cap_type_value = ['4-20mA', '0-10V'];
    var mode_value = ['Counting Mode', 'Status Mode'];
    var count_method_value = ['Rising Edge', 'Falling Edge'];
    var status_value = ['Open', 'Close'];
    var type_id_list = {'1':'M_SP_NA_1', '30':'M_SP_TB_1', '3':'M_DP_NA_1', '31':'M_DP_TB_1', '5':'M_ST_NA_1', '32':'M_ST_TB_1',
    '7':'M_BO_NA_1', '33':'M_BO_TB_1', '9':'M_ME_NA_1', '34':'M_ME_TD_1', '21':'M_ME_ND_1', '11':'M_ME_NB_1', '35':'M_ME_TE_1', '13':'M_ME_NC_1', 
    '36':'M_ME_TF_1', '15':'M_IT_NA_1', '37':'M_IT_TB_1', '38':'M_EP_TD_1'};

    if (table_name == 'fx') {
        reg_type_value = ['X', 'Y', 'M', 'S', 'D'];
    } else if (table_name == 's7') {
        reg_type_value = ['I', 'Q', 'M', 'DB', 'V', 'C', 'T'];
    }

    data_type_value = get_data_type_value(table_name);

    var page_type = document.getElementById("page_type").value;
    var tmp = $('#option_list_'+table_name).val();
    var option_list = tmp.split(",");

    if (table_name == 'adc' || table_name == 'di' || table_name == 'do') {
        io_type = table_name;
        table_name = 'io';
    }

    if (option_list.includes('mode')) {
        mode = Number(document.getElementById(table_name + '.'  + 'mode').value);
    }
    
    option_list.forEach(function (option) {
        if (option == 'data_type') {
            option_value[option] = data_type_value[Number(document.getElementById(table_name + '.'  + option).value)];
        } else if (option == 'reg_type') {
            option_value[option] = reg_type_value[Number(document.getElementById(table_name + '.'  + option).value)];
        } else if (option == 'word_len') {
            option_value[option] = data_type_value[Number(document.getElementById(table_name + '.'  + option).value)];
        } else if (option == 'cap_type') {
            option_value[option] = cap_type_value[Number(document.getElementById(table_name + '.'  + option).value)];
        } else if (option == 'mode') {
            option_value[option] = mode_value[Number(document.getElementById(table_name + '.'  + option).value)];
        } else if (option == 'count_method') {
            option_value[option] = (mode == 1) ? '' : count_method_value[Number(document.getElementById(table_name + '.'  + option).value)];
        } else if (option == 'init_status') {
            option_value[option] = status_value[Number(document.getElementById(table_name + '.'  + option).value)];
        } else if (option == 'cur_status') {
            var cur_status = document.getElementById(table_name + '.'  + option).innerHTML;
            if (cur_status == '0' || cur_status == '1')
                option_value[option] = status_value[Number(cur_status)];
            else
                option_value[option] = cur_status;
        } else if (option == 'enabled' || option == 'sms_reporting') {
            option_value[option] = document.getElementById(table_name + '.'  + option).checked ? '1' : '0';
        } else if (option == 'index') {
            option_value[option] = document.getElementById(table_name + '.'  + option + '.' + io_type).value;
        } else {
            // console.log(option);
            if (option != null)
                option_value[option] = (mode == 1 && option == 'debounce_interval') ? '-' : document.getElementById(table_name + '.'  + option).value;
        }
    })

    if (option_value['belonged_com'] == "No Interface Is Enabled") {
        alert("No Interface Is Enabled, please enabled the interface first.");
        return;
    }

    if (table_name == 'io')
        table_name = io_type;

    var table = document.getElementById("table_" + table_name);
    if (page_type == "0") {
        deleteColumnByHeader("table_" + table_name, 'Current Value');
        deleteColumnByHeader("table_" + table_name, 'Write Value');
        var contents = '';
        contents += '<tr  class="tr cbi-section-table-descr">\n';
        option_list.forEach(function(option){
            if (option == 'operator' || option == 'operand' || option == 'ex' || option == 'accuracy' ||
                option == 'report_type' || option == 'alarm_up' || option == 'alarm_down' || option == 'phone_num' || 
                option == 'email' || option == 'event_server_center' || option == 'contents' || option == 'retry_interval' || option == 'again_interval') {
                contents += '   <td style="display:none" name="'+option+'">'+ (option_value[option].length > 0 ? option_value[option] : "-") +'</td>\n';
            } else if (option == 'enabled' || option == 'sms_reporting') {
                contents += '   <td style="' + ((option == 'enabled') ? 'text-align:center' : 'display:none') + '"><input type="checkbox" name="' + option
                +'" ' + (option_value[option] == '1' ? 'checked' : ' ') + ' onclick="updateData(\''+table_name+'\')"></td>\n';
            } else if (option == "belonged_com"  && option_value[option].includes('TCP')) {
                contents += '   <td style="text-align:center" name="'+option+'">Network Node' + (option_value[option][3]) +'</td>\n';
            } else {
                contents += '   <td style="text-align:center" name="'+option+'">'+ (option_value[option] ? option_value[option] : "-") +'</td>\n';
            }
        })
        contents += '   <td><a href="javascript:void(0);" onclick="editData(this, \''+table_name+'\');" >Edit</a></td>\n' +
            '       <td><a href="javascript:void(0);" onclick="delData(this, \''+table_name+'\');" >Del</a></td>\n' +
            '   </tr>';
        table.innerHTML += contents;

        insertColumn("table_" + table_name, 'cur_value', 'Tag Name', 'Current Value');
        insertColumn("table_" + table_name, 'write_value', 'Current Value', 'Write Value');
    } else {
        var num = 0;
        option_list.forEach(function (option){
            if (option == 'enabled' || option == 'sms_reporting') {
                // Get all checkbox elements
                var trs = table.getElementsByTagName("tr");
                var checkboxes = trs[page_type].getElementsByTagName("input");
                // Traverse checkbox elements
                for (var i = 0; i < checkboxes.length; i++) {
                    // Determine if it is of checkbox type
                    var m_option = option;
                    // console.log(m_option);
                    if ((checkboxes[i].type === "checkbox" && checkboxes[i].name == m_option)) {
                        checkboxes[i].checked = (option_value[option] == '1') ? true : false;
                        num++;
                        break;  
                    }
                }
            } else {
                // console.log(table.rows[Number(page_type)].querySelector('td[name="'+ option +'"]').innerHTML);
                var option_value_display = '';
                if (option == "belonged_com"  && option_value[option].includes('TCP')) {
                    option_value_display = (option_value[option].length > 0 ? option_value[option].replace('TCP', 'Network Node') : "-")
                } else {
                    option_value_display = (option_value[option].length > 0 ? option_value[option] : "-")
                }
                table.rows[Number(page_type)].querySelector('td[name="'+ option +'"]').innerHTML = option_value_display;
            }
        })
    }

    result = get_table_data(table_name, option_list);
    var json_data = JSON.stringify(result);
    $('#hidTD_'+table_name).val(json_data);
    closeBox();
}

function updateData(table_name) {
    var tmp = $('#option_list_'+table_name).val();
    var option_list = tmp.split(",");

    var result = get_table_data(table_name, option_list);
    var json_data = JSON.stringify(result);
    $('#hidTD_'+table_name).val(json_data);
}

function delData(object, table_name) {
    var table = object.parentNode.parentNode.parentNode;
    var tr = object.parentNode.parentNode;
    var tmp = $('#option_list_'+table_name).val();

    var option_list = tmp.split(",");
    table.removeChild(tr);

    var result = get_table_data(table_name, option_list);
    var json_data = JSON.stringify(result);
    $('#hidTD_'+table_name).val(json_data);
}

function setSelectByText(id, text)
{
    var select = document.getElementById(id);

    for (var i = 0; i < select.options.length; i++){  
        if (select.options[i].text == text){  
            select.options[i].selected = true;  
            break;  
        }  
    }  
}

function editData(object, table_name) {
    var row = $(object).parent().parent().parent().prevAll().length + 1;
    document.getElementById("page_type").value = row;
    var num = 0;
    var tmp = $('#option_list_'+table_name).val();
    var option_list = tmp.split(",");
    var tds = $(object).parent().parent().find("td");
    var io_type;

    if (table_name == 'adc' || table_name == 'di' || table_name == 'do') {
        io_type = table_name;
        table_name = 'io';
    }

    option_list.forEach(function(option) {
        var val = tds.filter('[name="'+ option +'"]').text();

        if (option == 'data_type' || option == 'reg_type' || option == 'word_len' || option == 'cap_type' ||
            option == 'cap_type' || option == 'mode' || option == 'count_method' || option == 'init_status') {
            setSelectByText(table_name + '.'  + option, val);
        } else if (option == 'index') {
            document.getElementById(table_name + '.'  + option + '.' + io_type).value = val;
        } else if (option == 'enabled' || option == 'sms_reporting') {
            var check = tds.find('input[name="' + option + '"]').is(':checked');
            document.getElementById(table_name + '.'  + option).checked = check;
        } else if (option == 'cur_status') {
            document.getElementById(table_name + '.'  + option).innerHTML = val;
        } else if (option == "belonged_com"  && val.includes('Network')) {
            document.getElementById(table_name + '.'  + option).value = 'TCP' + val.slice(-1);
        } else {
            document.getElementById(table_name + '.'  + option).value = val;
        }
    })
    
    openBox(table_name);
    if (table_name == 'io')
        switchPage('btn' + io_type.toUpperCase());

    enableAlarm(table_name);
}

function selectMode() {
    var mode = document.getElementById("io.mode").value;

    if (mode == "0") {
		$('#pageCount').show();
    } else {
		$('#pageCount').hide();
    }
}

function switchPage(name) {
    if (name == "btnADC") {
        document.getElementById("popBoxTitle").innerHTML="ADC Setting";
        document.getElementById("page_name").value = "0"; /* 0 is ADC. 1 is DI, 2 is DO */
        $('#pageIndexADC').show();
        $('#pageIndexDI').hide();
        $('#pageIndexDO').hide();
        $('#pageADCMod').show();
        $('#pageDIMod').hide();
        $('#pageDOMod').hide();
    } else if (name == "btnDI") {
        document.getElementById("popBoxTitle").innerHTML="DI Setting";
        document.getElementById("page_name").value = "1";
        $('#pageIndexADC').hide();
        $('#pageIndexDI').show();
        $('#pageIndexDO').hide();
        $('#pageADCMod').hide();
        $('#pageDIMod').show();
        $('#pageDOMod').hide();
        selectMode();
    } else if (name == "btnDO") {
        document.getElementById("popBoxTitle").innerHTML="DO Setting";
        document.getElementById("page_name").value = "2";
        $('#pageIndexADC').hide();
        $('#pageIndexDI').hide();
        $('#pageIndexDO').show();
        $('#pageADCMod').hide();
        $('#pageDIMod').hide();
        $('#pageDOMod').show();
    }
}

function addDataIO(object, table_name) {
    openBox(table_name);
    document.getElementById("page_type").value = "0"; /* 0 is add. other is edit */
    var name = object.name;
    switchPage(name);
    enableAlarm(table_name);
}

function saveDataIO() {
    var page_name = document.getElementById("page_name").value;

    if (page_name == "0") {
        saveData('adc');
    } else if (page_name == "1") {
        saveData('di');
    } else {
        saveData('do');
    }

    closeBox();
}

/* DNP3 Server*/
function enableDnp3(state) {
    if (state) {
      $('#page_dnp3').show();
    } else {
      $('#page_dnp3').hide();
    }
    dnp3ProtocolChange();
}

function dnp3ProtocolChange() {
    var head = 'dnp3_server';
    var proto = document.getElementById('proto').value;

    if (proto == 'RTU') {
        $('#page_proto_rtu').show();
        $('#page_proto_ip').hide();
    } else {
        $('#page_proto_rtu').hide();
        $('#page_proto_ip').show();
    }
}

function loadDnp3Config() {
    $('#loading').show();
    var table_name = 'dnp3';
    $.get('ajax/dct/get_dctcfg.php?type=dnp3',function(data){
        jsonData = JSON.parse(data);
        var arr = jsonData.option;
        if (jsonData.hasOwnProperty("dnp3_server")) {
            var dnp3_server = JSON.parse(jsonData.dnp3_server);

            $('#enabled').val(dnp3_server.enabled);
            if (dnp3_server.enabled == '1') {
                $('#page_dnp3').show();
                $('#dnp3_server_enable').prop('checked', true);

                arr.forEach(function (info) {
                    if (info == null) {
                        return true;    // continue: return true; break: return false
                    }

                    $('#' + info).val(dnp3_server[info]);
                })
            } else {
                $('#page_dnp3').hide();
                $('#dnp3_server_disable').prop('checked', true);
            }
            dnp3ProtocolChange();
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

/* Modbus Slave*/
function enableModbusSlave(state) {
    if (state) {
      $('#page_modbus_slave').show();
    } else {
      $('#page_modbus_slave').hide();
    }
    modbusSlaveProtocolChange();
}

function modbusSlaveProtocolChange() {
    var proto = document.getElementById('proto').value;

    if (proto == 'RTU') {
        $('#page_proto_rtu').show();
        $('#page_proto_ip').hide();
    } else {
        $('#page_proto_rtu').hide();
        $('#page_proto_ip').show();
    }
}

function loadModbusSlaveConfig() {
    $('#loading').show();
    $.get('ajax/dct/get_dctcfg.php?type=modbus_slave',function(data){
        jsonData = JSON.parse(data);
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

function addDataDisplyItem(tbody, table, key, value, keywords) {
    if (!key.includes(keywords) && keywords.length > 0) {
        return false;
    }

    var td = table.querySelector('td[name="' + key + '"]');
    if (td) {
        var tr = td.parentNode;
        tr.children[1].textContent = value;
    } else {
        var tr = document.createElement('tr');
        tr.className = "tr cbi-section-table-descr";

        // key
        var tdKey = document.createElement('td');
        tdKey.style.textAlign = "center";
        tdKey.setAttribute('name', "factor_name");
        tdKey.textContent = key;
        tr.appendChild(tdKey);

        // value
        var tdValue = document.createElement('td');
        tdValue.style.textAlign = "center";
        tdValue.textContent = value;
        tr.appendChild(tdValue);

        // button
        var tdBtn = document.createElement('td');
        tdBtn.style.textAlign = "center";
        let button = document.createElement("button");
        button.textContent = "Write";
        button.classList.add("btn-primary");
        button.style = "border-radius: 0.5rem;";
        button.addEventListener("click", function (event) {
            event.preventDefault();
            writeValueByTag(this);
        });
        tdBtn.appendChild(button);
        tr.appendChild(tdBtn);
        tbody.appendChild(tr);
    }
}

/*datadisplay*/
function getWebshowDate() {
    $.get('ajax/dct/get_dctcfg.php?type=datadisplay', function(data) {
        if (data.includes("data")) {
            tmp = JSON.parse(data);
            jsonData = JSON.parse(tmp['data']);
            if (data.includes("factor_list")) {
                factorList = JSON.parse(tmp['factor_list']);
            }
        } else {
            return;
        }

        if (jsonData == null || Object.keys(jsonData).length == 0) {
            return;
        }

        const jsonResult = Object.entries(jsonData).map(([key, value]) => {
            if (key.includes(";"))  {
                const [name, index] = key.split(";");
                return [name, index, value];
            } else {
                return [key, 0, value];
            }
        });


        var table = document.getElementsByTagName("table")[0];
        var keywords = document.getElementsByName("keywords")[0].value;
        var select = document.getElementById('current_rule').value || "all";
        var trs = table.querySelectorAll('tr');
        trs.forEach(function(tr) {
            var td = tr.querySelector('td[name]');
            if (td) {
                var key = td.getAttribute('name');
                jsonResult.forEach(item => {
                    if (key = item[0] || (keywords.length > 0 && !key.includes(keywords))) {
                        tr.remove();
                        return;
                    }
                });
            }
        });

        var table = document.getElementById("table_modbus");
        if (!table.tBodies.length) {
            table.appendChild(document.createElement('tbody'));
        }
        var tbody = table.tBodies[0];
        if (select == "all") {
            jsonResult.forEach(item => {
                const [name, index, value] = item;
                addDataDisplyItem(tbody, table, name, value, keywords);
            });
        } else {
            factorList.forEach(function(item) {
                if (!item.startsWith(select + '-')) {
                    return;
                }

                var key = item.substring(item.indexOf('-') + 1);
                const jsonItem = jsonResult.find(([name]) => name === key);
                if (jsonItem) {
                    const value = jsonItem[2];
                    addDataDisplyItem(tbody, table, key, value, keywords);
                }
            });
        }
    });
}

function loadDataDisplay() {
    getWebshowDate();
    setInterval(getWebshowDate, 1000);
}

