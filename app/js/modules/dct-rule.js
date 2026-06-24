
export function getReportingCenterFlag(p) {
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

globalThis.getReportingCenterFlag = getReportingCenterFlag;

/*rule common*/
export function openBox(table_name) {
    $('#popBox').show();
    $('#popLayer').show();
    document.getElementById("popBox").scrollTop = 0;
    selectOperator(table_name);
}

globalThis.openBox = openBox;

export function closeBox() {
    $('#popBox').hide();
    $('#popLayer').hide();
}

globalThis.closeBox = closeBox;

export function selectOperator(table_name) {
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

globalThis.selectOperator = selectOperator;

export function enableAlarm(table_name) {
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

globalThis.enableAlarm = enableAlarm;

export function selectReportType(table_name) {
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

globalThis.selectReportType = selectReportType;

export function doesColumnExist(tableId, columnName) {
    var table = document.getElementById(tableId);
    var headers = table.querySelectorAll('th');
    for (var i = 0; i < headers.length; i++) {
        if (headers[i].dataset.field === columnName) {
            return true;
        }
    }
    return false;
}

globalThis.doesColumnExist = doesColumnExist;

export function writeValueByTag(object) {
    var tds = $(object).parent().parent().find("td");
    var tagName = tds.filter('[name="factor_name"]').text();
    var serverCenter = tds.filter('[name="server_center"]').text();
    if (serverCenter == '' || serverCenter == '-') {
        serverCenter = '-1';
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

globalThis.writeValueByTag = writeValueByTag;

export function insertColumn(tableId, name, headerName, newHeaderName) {
    if (tableId == 'table_system_param') {
        return;
    }
    if (doesColumnExist(tableId, name))
        return;

    if ((tableId == 'table_adc' || tableId == 'table_di' || tableId == 'table_modbus_slave_point' || 
        tableId == 'table_dnp3') && name == 'write_value') {
        return;
    }

    var table = document.getElementById(tableId);
    var rows = table.getElementsByTagName('tr');
    var th_num = 0;

    var headers = table.getElementsByTagName('th');
    var columnIndex = 0;
    
    for (var i = 0; i < headers.length; i++) {
        if (headers[i].dataset.field === headerName) {
            columnIndex = i + 1;
            break;
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
        if (name == 'write_value') {
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
            th.dataset.field = name;
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

globalThis.insertColumn = insertColumn;

export function deleteColumnByHeader(tableId, headerName) {
    if (!doesColumnExist(tableId, headerName))
        return;

    var table = document.getElementById(tableId);
    const cells = table.getElementsByTagName('th');
    for (let i = 0; i < cells.length; i++) {
        if (cells[i].dataset.field === headerName) {
            const columnIndex = i;
            const rows = table.rows;
            for (let j = 0; j < rows.length; j++) {
                rows[j].deleteCell(columnIndex);
            }
            break;
        }
    }
}

globalThis.deleteColumnByHeader = deleteColumnByHeader;

export function get_data_type_value(table_name) {
    var data_type_value = [];

    if (table_name == 'modbus' || table_name == 'modbus_slave_point') {
        data_type_value = ['Bit', 'Unsigned 16Bits AB', 'Unsigned 16Bits BA', 'Signed 16Bits AB', 'Signed 16Bits BA',
        'Unsigned 32Bits ABCD', 'Unsigned 32Bits BADC', 'Unsigned 32Bits CDAB', 'Unsigned 32Bits DCBA',
        'Signed 32Bits ABCD', 'Signed 32Bits BADC', 'Signed 32Bits CDAB', 'Signed 32Bits DCBA',
        'Float ABCD', 'Float BADC', 'Float CDAB', 'Float DCBA',
        'Unsigned 64Bits ABCDEFGH', 'Unsigned 64Bits BADCFEHG', 'Unsigned 64Bits HGFEDCBA', 'Unsigned 64Bits GHEFCDAB',
        'Signed 64Bits ABCDEFGH', 'Signed 64Bits BADCFEHG', 'Signed 64Bits HGFEDCBA', 'Signed 64Bits GHEFCDAB',
        'Double ABCDEFGH', 'Double BADCFEHG','Double HGFEDCBA', 'Double GHEFCDAB'];
    } else if (table_name == 'fx') {
        data_type_value = ['Bit', 'Byte', 'Word', 'DWord', 'Real'];
    } else if (table_name == 's7') {
        data_type_value = ['Bit', 'Byte', 'Word', 'DWord', 'Real', 'Counter', 'Timer'];
    } else if (table_name == 'mc' || table_name == 'iec104') {
        data_type_value = ['Bit', 'Int', 'Float'];
    } else if (table_name == 'opcuacli') {
        data_type_value = ['Bool', 'Int8', 'Uint8', 'Int16', 'UInt16', 'Int32', 'UInt32', 'Int64', 'UInt64', 'Float', 'Double', 'String', 'DateTime', 'GUID', 'ByteString', 'Opaque'];
    } else if (table_name == 'ethernetip') {
        data_type_value = ['Bool', 'Int16', 'UInt16', 'Int32', 'UInt32', 'Int64', 'UInt64', 'Float', 'Double', 'String'];
    } else if (table_name == 'snmpcli') {
        data_type_value = ['Int32', 'UInt32', 'Counter64', 'String'];
    } else if (table_name == 'mbuscli') {
        data_type_value = ['Double', 'String'];
    } else if (table_name == 'iec1107' || table_name == 'dlms') {
        data_type_value = ['Int', 'Float', 'String'];
    } else if (table_name == 'iec61850cli') {
        data_type_value = ['Bool', 'Int', 'Float', 'String'];
    }

    return data_type_value;
}

globalThis.get_data_type_value = get_data_type_value;

export function addSectionTable(table_name, jsonData, option_list) {
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
    var fc_value = ['ST', 'MX', 'SP', 'SV', 'CF', 'DC', 'SG', 'SE', 'SR', 'OR', 'BL', 'EX', 'CO', 'US', 'MS', 'RP', 'BR', 'LG', 'GO'];

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
                key == 'email' || key == 'event_server_center' || key == 'contents' || key == 'retry_interval' || 
                key == 'again_interval' || key == 'command') {
                    contents += '   <td style="display:none" name="'+key+'">-</td>\n';
                } else if (key == 'enabled' || key == 'sms_reporting' || key == 'interpreter') {
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
            key == 'email' || key == 'event_server_center' || key == 'contents' || key == 'retry_interval' || 
            key == 'again_interval' || key == 'command') {
                contents += '   <td style="display:none" name="'+key+'">'+ (jsonData[i][key] != null ? jsonData[i][key] : "-") +'</td>\n';
            } else if (key == 'data_type') {
                contents += '   <td style="text-align:center" name="'+key+'">'+ (data_type_value[Number(jsonData[i][key])]) +'</td>\n';
            } else if (key == 'reg_type') {
                contents += '   <td style="text-align:center" name="'+key+'">'+ (reg_type_value[Number(jsonData[i][key])]) +'</td>\n';
            } else if (key == 'fc') {
                contents += '   <td style="text-align:center" name="'+key+'">'+ (fc_value[Number(jsonData[i][key])]) +'</td>\n';
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
            } else if (key == 'enabled' || key == 'sms_reporting' || key == 'interpreter') {
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

    const curValue = document.querySelector('[data-i18n="cur_value"]').value;
    const writeValue = document.querySelector('[data-i18n="write_value"]').value;
    insertColumn("table_" + table_name, 'cur_value', 'factor_name', curValue);
    insertColumn("table_" + table_name, 'write_value', 'cur_value', writeValue);

    var result = get_table_data(table_name, option_list);
    var json_data = JSON.stringify(result);
    $('#hidTD_'+table_name).val(json_data);
}

globalThis.addSectionTable = addSectionTable;

export function snmpScan() {
    $('#loading').show();
    const btn = document.getElementById("btn_scan");
    btn.disabled = true;
    const scan_interface = document.getElementById('scan_interface').value;
    const oid = document.getElementById('scan_oid').value;
    $.get('ajax/dct/get_dctcfg.php?type=snmp_scan&interface=' + scan_interface + '&oid=' + oid, function(data) {
        // console.log(data);
        $('#snmp_result_area').val(data);
        $('#loading').hide();
        btn.disabled = false;
    })
}

globalThis.snmpScan = snmpScan;

export function dlmsScan() {
    $('#loading').show();
    const btn = document.getElementById("btn_scan");
    btn.disabled = true;
    const scan_interface = document.getElementById('scan_interface').value;
    $.get('ajax/dct/get_dctcfg.php?type=dlms_scan&interface=' + scan_interface, function(data) {
        // console.log(data);
        $('#dlms_result_area').val(data);
        $('#loading').hide();
        btn.disabled = false;
    })
}

globalThis.dlmsScan = dlmsScan;

export function iec61850cliScan() {
    $('#loading').show();
    const btn = document.getElementById("btn_scan");
    btn.disabled = true;
    const scan_interface = document.getElementById('scan_interface').value;
    $.get('ajax/dct/get_dctcfg.php?type=iec61850cli_scan&interface=' + scan_interface, function(data) {
        $('#iec61850cli_result_area').val(data);
        $('#loading').hide();
        btn.disabled = false;
    })
}

globalThis.iec61850cliScan = iec61850cliScan;

export function formatValue(value, unit) {
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

globalThis.formatValue = formatValue;

export function parseMBusXML(xmlString) {
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

globalThis.parseMBusXML = parseMBusXML;

export function mbusScan() {
    $('#loading').show();
    const btn = document.getElementById("btn_scan");
    btn.disabled = true;
    document.getElementById("output").innerHTML = "";
    const scan_interface = document.getElementById('scan_interface').value;
    const address = document.getElementById('scan_address').value;
    $.get('ajax/dct/get_dctcfg.php?type=mbus_scan&interface=' + scan_interface + '&address=' + address, function(data) {
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

globalThis.mbusScan = mbusScan;

export function get_bacnet_server_discover(callback) {
    const scan_interface = document.getElementById('baccli.belonged_com').value;
    // console.log(interface);
    $.get('ajax/dct/get_dctcfg.php?type=bacdiscover&interface=' + scan_interface, function(data) {
        callback(data);
    })
}

globalThis.get_bacnet_server_discover = get_bacnet_server_discover;

export function updateDeviceIdList() {
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

globalThis.updateDeviceIdList = updateDeviceIdList;

export function get_iec104_server_discover(callback) {
    const scan_interface = document.getElementById('iec104.belonged_com').value;
    // console.log(interface);
    $.get('ajax/dct/get_dctcfg.php?type=iec104discover&interface=' + scan_interface, function(data) {
        callback(data);
    })
}

globalThis.get_iec104_server_discover = get_iec104_server_discover;

/*rules common*/
export function addData(table_name) {
    openBox(table_name);
    document.getElementById("page_type").value = "0"; /* 0 is add. other is edit */
    enableAlarm(table_name);
    if (table_name == 'dnp3') {
        groupIdChange();
    }
}

globalThis.addData = addData;

export function findKey (data, value, compare = (a, b) => a === b) {
    return Object.keys(data).find(k => compare(data[k], value))
}

globalThis.findKey = findKey;

export function get_table_data(table_name, option_list) {
    var data_type_value = [];
    var reg_type_value = [];
    var cap_type_value = ['4-20mA', '0-10V'];
    var mode_value = ['Counting Mode', 'Status Mode'];
    var count_method_value = ['Rising Edge', 'Falling Edge'];
    var status_value = ['Open', 'Close'];
    var type_id_list = {'1':'M_SP_NA_1', '30':'M_SP_TB_1', '3':'M_DP_NA_1', '31':'M_DP_TB_1', '5':'M_ST_NA_1', '32':'M_ST_TB_1',
    '7':'M_BO_NA_1', '33':'M_BO_TB_1', '9':'M_ME_NA_1', '34':'M_ME_TD_1', '21':'M_ME_ND_1', '11':'M_ME_NB_1', '35':'M_ME_TE_1', '13':'M_ME_NC_1', 
    '36':'M_ME_TF_1', '15':'M_IT_NA_1', '37':'M_IT_TB_1', '38':'M_EP_TD_1'};
    var fc_value = ['ST', 'MX', 'SP', 'SV', 'CF', 'DC', 'SG', 'SE', 'SR', 'OR', 'BL', 'EX', 'CO', 'US', 'MS', 'RP', 'BR', 'LG', 'GO'];

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

                if (option == 'enabled' || option == 'sms_reporting' || option == 'interpreter') {
                    var check = tds.find('input[name="' + option + '"]').is(':checked');
                    // console.log(check);
                    tmp += '"' + option + '":"' + ( check ? 1 : 0) + '",';
                } else if (option == 'data_type') {
                    tmp += '"' + option + '":"' + data_type_value.indexOf(val) + '",';
                } else if (option == 'reg_type') {
                    tmp += '"' + option + '":"' + reg_type_value.indexOf(val) + '",';
                } else if (option == 'fc') {
                    tmp += '"' + option + '":"' + fc_value.indexOf(val) + '",';
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

globalThis.get_table_data = get_table_data;

export function saveData(table_name) {
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
    var fc_value = ['ST', 'MX', 'SP', 'SV', 'CF', 'DC', 'SG', 'SE', 'SR', 'OR', 'BL', 'EX', 'CO', 'US', 'MS', 'RP', 'BR', 'LG', 'GO'];

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
        } else if (option == 'fc') {
            option_value[option] = fc_value[Number(document.getElementById(table_name + '.'  + option).value)];
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
        } else if (option == 'enabled' || option == 'sms_reporting' || option == 'interpreter') {
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
        deleteColumnByHeader("table_" + table_name, 'cur_value');
        deleteColumnByHeader("table_" + table_name, 'write_value');
        var contents = '';
        contents += '<tr  class="tr cbi-section-table-descr">\n';
        option_list.forEach(function(option){
            if (option == 'operator' || option == 'operand' || option == 'ex' || option == 'accuracy' ||
                option == 'report_type' || option == 'alarm_up' || option == 'alarm_down' || option == 'phone_num' || 
                option == 'email' || option == 'event_server_center' || option == 'contents' || option == 'retry_interval' || 
                option == 'again_interval' || option == 'command' || option == 'interpreter') {
                contents += '   <td style="display:none" name="'+option+'">'+ (option_value[option].length > 0 ? option_value[option] : "-") +'</td>\n';
            } else if (option == 'enabled' || option == 'sms_reporting' || option == 'interpreter') {
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

        insertColumn("table_" + table_name, 'cur_value', 'factor_name', 'Current Value');
        insertColumn("table_" + table_name, 'write_value', 'cur_value', 'Write Value');
    } else {
        var num = 0;
        option_list.forEach(function (option){
            if (option == 'enabled' || option == 'sms_reporting' || option == 'interpreter') {
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

globalThis.saveData = saveData;

export function updateData(table_name) {
    var tmp = $('#option_list_'+table_name).val();
    var option_list = tmp.split(",");

    var result = get_table_data(table_name, option_list);
    var json_data = JSON.stringify(result);
    $('#hidTD_'+table_name).val(json_data);
}

globalThis.updateData = updateData;

export function delData(object, table_name) {
    var table = object.parentNode.parentNode.parentNode;
    var tr = object.parentNode.parentNode;
    var tmp = $('#option_list_'+table_name).val();

    var option_list = tmp.split(",");
    table.removeChild(tr);

    var result = get_table_data(table_name, option_list);
    var json_data = JSON.stringify(result);
    $('#hidTD_'+table_name).val(json_data);
}

globalThis.delData = delData;

export function setSelectByText(id, text)
{
    var select = document.getElementById(id);

    for (var i = 0; i < select.options.length; i++){  
        if (select.options[i].text == text){  
            select.options[i].selected = true;  
            break;  
        }  
    }  
}

globalThis.setSelectByText = setSelectByText;

export function editData(object, table_name) {
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
            option == 'cap_type' || option == 'mode' || option == 'count_method' || option == 'init_status' ||
            option == 'fc') {
            setSelectByText(table_name + '.'  + option, val);
        } else if (option == 'index') {
            document.getElementById(table_name + '.'  + option + '.' + io_type).value = val;
        } else if (option == 'enabled' || option == 'sms_reporting' || option == 'interpreter') {
            var check = tds.find('input[name="' + option + '"]').is(':checked');
            document.getElementById(table_name + '.'  + option).checked = check;
        } else if (option == 'cur_status') {
            document.getElementById(table_name + '.'  + option).innerHTML = val;
        } else if (option == "belonged_com"  && val.includes('Network')) {
            document.getElementById(table_name + '.'  + option).value = 'TCP' + val.slice(-1);
        } else {
            document.getElementById(table_name + '.'  + option).value = val;
            if (table_name == 'dnp3' && option == 'group_id') {
                groupIdChange();
            }
        }
    })
    
    openBox(table_name);
    if (table_name == 'io')
        switchPage('btn' + io_type.toUpperCase());

    if (table_name == 'system_param') {
        systemParamChange(table_name)
    }

    enableAlarm(table_name);
}

globalThis.editData = editData;

/*configs import and export*/
export function openConfBox() {
    $('#confBox').show();
    $('#confLayer').show();
    document.getElementById("confBox").scrollTop = 0;
}

globalThis.openConfBox = openConfBox;

export function closeConfBox() {
    $('#confBox').hide();
    $('#confLayer').hide();
}

globalThis.closeConfBox = closeConfBox;

export function conf_im_ex(conf_name) {
    const title = document.querySelector('input[name="confBox"]');
    if (conf_name == "Iec1107") {
        conf_name = "IEC62056-21";
    }
    document.getElementById('title').innerHTML = conf_name + ' ' + title.value;
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

globalThis.conf_im_ex = conf_im_ex;

export function downloadFile(conf_name) {
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

globalThis.downloadFile = downloadFile;

/*io*/
function selectMode() {
    var mode = document.getElementById("io.mode").value;

    if (mode == "0") {
		$('#pageCount').show();
    } else {
		$('#pageCount').hide();
    }
}

globalThis.selectMode = selectMode;

export function switchPage(name) {
    const setting = document.querySelector('input[data-i18n="setting"]').value;
    if (name == "btnADC") {
        document.getElementById("popBoxTitle").innerHTML="ADC "+ setting;
        document.getElementById("page_name").value = "0"; /* 0 is ADC. 1 is DI, 2 is DO */
        $('#pageIndexADC').show();
        $('#pageIndexDI').hide();
        $('#pageIndexDO').hide();
        $('#pageADCMod').show();
        $('#pageDIMod').hide();
        $('#pageDOMod').hide();
    } else if (name == "btnDI") {
        document.getElementById("popBoxTitle").innerHTML="DI "+ setting;
        document.getElementById("page_name").value = "1";
        $('#pageIndexADC').hide();
        $('#pageIndexDI').show();
        $('#pageIndexDO').hide();
        $('#pageADCMod').hide();
        $('#pageDIMod').show();
        $('#pageDOMod').hide();
        selectMode();
    } else if (name == "btnDO") {
        document.getElementById("popBoxTitle").innerHTML="DO "+ setting;
        document.getElementById("page_name").value = "2";
        $('#pageIndexADC').hide();
        $('#pageIndexDI').hide();
        $('#pageIndexDO').show();
        $('#pageADCMod').hide();
        $('#pageDIMod').hide();
        $('#pageDOMod').show();
    }
}

globalThis.switchPage = switchPage;

export function addDataIO(object, table_name) {
    openBox(table_name);
    document.getElementById("page_type").value = "0"; /* 0 is add. other is edit */
    var name = object.name;
    switchPage(name);
    enableAlarm(table_name);
}

globalThis.addDataIO = addDataIO;

export function saveDataIO() {
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

globalThis.saveDataIO = saveDataIO;

/*Realtime Data*/
export function getRealtimeData() {
    $.get('ajax/dct/get_dctcfg.php?type=datadisplay', function(data) {
        if (!data.includes('"data"')) {
            return;
        }
        // console.log(data);
        const tmp = JSON.parse(data);
        const jsonData = JSON.parse(tmp['data']);
        
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

globalThis.getRealtimeData = getRealtimeData;

export function loadRealtimeData() {
    if (getRealtimeData()) {
        setInterval(getRealtimeData, 1000);
    }  
}

globalThis.loadRealtimeData = loadRealtimeData;


export function selectItemIec104(value) {
    const input = document.getElementById('iec104.type_id');
    const list = document.getElementById('typeIdList');
    // console.log(value);
    input.value = value;
    list.classList.remove('show');
}

globalThis.selectItemIec104 = selectItemIec104;

export function iec104FilterFunction() {
    const list = document.getElementById('typeIdList');
    let options_type_id = [];
    let filteredOptions = [];
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

globalThis.iec104FilterFunction = iec104FilterFunction;

export function updateTypeIdList() {
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

globalThis.updateTypeIdList = updateTypeIdList;

export function get_iec1107_server_discover(callback) {
    const scan_interface = document.getElementById('iec1107.belonged_com').value;
    // console.log(interface);
    $.get('ajax/dct/get_dctcfg.php?type=iec1107discover&interface=' + scan_interface, function(data) {
        callback(data);
    })
}

globalThis.get_iec1107_server_discover = get_iec1107_server_discover;

export function selectItemIec1107(value) {
    const input = document.getElementById('iec1107.obis');
    const list = document.getElementById('obisList');
    // console.log(value);
    input.value = value;
    list.classList.remove('show');
}

globalThis.selectItemIec1107 = selectItemIec1107;

export function iec1107FilterFunction() {
    const list = document.getElementById('obisList');
    let options_type_id = [];
    let filteredOptions = [];
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

globalThis.iec1107FilterFunction = iec1107FilterFunction;

export function updateObisList() {
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

globalThis.updateObisList = updateObisList;

export function selectItem(value) {
    const input = document.getElementById('baccli.object_device_id');
    const device_id_list = document.getElementById('deviceIdList');

    input.value = value;
    device_id_list.classList.remove('show');

    filterFunctionObject();
}

globalThis.selectItem = selectItem;

export function selectItemObject(value) {
    const input = document.getElementById('baccli.object_id');
    const device_id_list = document.getElementById('objectIdList');

    input.value = value;
    device_id_list.classList.remove('show');
}

globalThis.selectItemObject = selectItemObject;

export function filterFunction() {
    const input = document.getElementById('baccli.object_device_id');
    const device_id_list = document.getElementById('deviceIdList');
    let options_device_id = [];
    let filteredOptions = [];
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

globalThis.filterFunction = filterFunction;

export function filterFunctionObject() {
    const object_id_list = document.getElementById('objectIdList');
    let options_object_id = [];
    let filteredOptions = [];
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

globalThis.filterFunctionObject = filterFunctionObject;

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

export function systemParamChange($table) {
    var val = document.getElementById($table+'.param').value;

    if (val == 'custom') {
        $('#page_cmd').show();
    } else {
        $('#page_cmd').hide();
    }
}

globalThis.systemParamChange = systemParamChange;

export function initDctRule(table_name) {
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

        if (table_name != 'system_param')
            loadRealtimeData();
    }
    
    globalThis.loadRulesConfig = loadRulesConfig;
    loadRulesConfig(table_name);
}