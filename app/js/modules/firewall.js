// Forwards
export function openBoxForwards() {
    $('#forwards_popBox').show();
    $('#forwards_popLayer').show();
    document.getElementById("forwards_popBox").scrollTop = 0;
}

globalThis.openBoxForwards = openBoxForwards;

export function closeBoxForwards() {
    $('#forwards_popBox').hide();
    $('#forwards_popLayer').hide();
}

globalThis.closeBoxForwards = closeBoxForwards;

export function addDataForwards() {
    openBoxForwards();
    document.getElementById("forwards.page_type").value = "0"; /* 0 is add. other is edit */
}

globalThis.addDataForwards = addDataForwards;

export function getTableDataForwards() {
    var tr = $("#table_forwards tr");
    var result = [];
    for (var i = 2; i < tr.length; i++) {
        var tds = $(tr[i]).find("td");
        if (tds.length > 0) {
            result.push({
                'name':$(tds[0]).html(), 
                'proto':$(tds[1]).html(),
                'src_port':$(tds[2]).html(),
                'dest_ip':$(tds[3]).html(),
                'dest_port':$(tds[4]).html(),
                'enabled':$(tds[5]).html()
            });
        }
    }

    return result;
}

globalThis.getTableDataForwards = getTableDataForwards;

export function delForwards(object) {
    var table = object.parentNode.parentNode.parentNode;
    var tr = object.parentNode.parentNode;
    table.removeChild(tr);

    var result = getTableDataForwards();
    var json_data = JSON.stringify(result);
    $('#hidForwards').val(json_data);
}

globalThis.delForwards = delForwards;

export function saveForwards() {
    var name = document.getElementById("forwards.name").value;
    var proto = document.getElementById("forwards.proto").value;
    var src_port = document.getElementById("forwards.src_port").value;
    var dest_ip = document.getElementById("forwards.dest_ip").value;
    var dest_port = document.getElementById("forwards.dest_port").value;
    var enabled = document.getElementById("forwards.enabled").checked;
    var page_type = document.getElementById("forwards.page_type").value;

    if (proto == 'any' || proto == 'icmp' || proto == 'gre') {
        src_port = '-';
        dest_port = '-';
    }

    if (page_type == "0") {
        var table = document.getElementsByTagName("table")[0];
        table.innerHTML += "<tr  class=\"tr cbi-section-table-descr\">\n" +
            "        <td style='text-align:center'>"+ (name.length > 0 ? name : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (proto.length > 0 ? proto : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (src_port.length > 0 ? src_port : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (dest_ip.length > 0 ? dest_ip : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (dest_port.length > 0 ? dest_port : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ enabled +"</td>\n" +
            "        <td><a href=\"javascript:void(0);\" onclick=\"editForwards(this);\" >Edit</a></td>\n" +
            "        <td><a href=\"javascript:void(0);\" onclick=\"delForwards(this);\" >Del</a></td>\n" +
            "    </tr>";
    } else {
        var table = document.getElementById("table_forwards");
        var num = 0;
        table.rows[Number(page_type)].cells[num++].innerHTML = (name.length > 0 ? name : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (proto.length > 0 ? proto : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (src_port.length > 0 ? src_port : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (dest_ip.length > 0 ? dest_ip : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (dest_port.length > 0 ? dest_port : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = enabled;
    }

    var result = getTableDataForwards();
    var json_data = JSON.stringify(result);
    $('#hidForwards').val(json_data);
    closeBoxForwards();
}

globalThis.saveForwards = saveForwards;

export function editForwards(object) {
    var row = $(object).parent().parent().parent().prevAll().length + 1;
    var num = 0;
    document.getElementById("forwards.page_type").value = row;

    var value = $(object).parent().parent().find("td");
    var name = value.eq(num++).text();
    var proto = value.eq(num++).text();
    var src_port = value.eq(num++).text();
    var dest_ip = value.eq(num++).text();
    var dest_port = value.eq(num++).text();
    var enabled = value.eq(num++).text();

    document.getElementById("forwards.name").value = name;
    document.getElementById("forwards.proto").value = proto;
    document.getElementById("forwards.src_port").value = src_port;
    document.getElementById("forwards.dest_ip").value = dest_ip;
    document.getElementById("forwards.dest_port").value = dest_port;
    if (enabled == "true") {
        document.getElementById("forwards.enabled").checked = true;
    } else {
        document.getElementById("forwards.enabled").checked = false;
    }

    if (proto == 'tcp udp' || proto == 'tcp' || proto == 'udp' || proto == 'stcp') {
        $('#pageEport').show();
        $('#pageIPort').show();
    } else {
        $('#pageEport').hide();
        $('#pageIPort').hide();
    }

    openBoxForwards();
}

globalThis.editForwards = editForwards;

// traffic
export function openBoxTraffic() {
    $('#traffic_popBox').show();
    $('#traffic_popLayer').show();
    document.getElementById("traffic_popBox").scrollTop = 0;
}

globalThis.openBoxTraffic = openBoxTraffic;

export function closeBoxTraffic() {
    $('#traffic_popBox').hide();
    $('#traffic_popLayer').hide();
}

globalThis.closeBoxTraffic = closeBoxTraffic;

export function addDataTraffic() {
    openBoxTraffic();
    document.getElementById("traffic.page_type").value = "0"; /* 0 is add. other is edit */
}

globalThis.addDataTraffic = addDataTraffic;

export function getTableDataTraffic() {
    var tr = $("#table_traffic tr");
    var result = [];
    for (var i = 2; i < tr.length; i++) {
        var tds = $(tr[i]).find("td");
        if (tds.length > 0) {
            result.push({
                'name':$(tds[0]).html(), 
                'proto':$(tds[1]).html(),
                'rule':$(tds[2]).html(),
                'src_mac':$(tds[3]).html(),
                'src_ip':$(tds[4]).html(),
                'src_port':$(tds[5]).html(),
                'dest_ip':$(tds[6]).html(),
                'dest_port':$(tds[7]).html(),
                'action':$(tds[8]).html(),
                'enabled':$(tds[9]).html()
            });
        }
    }

    return result;
}

globalThis.getTableDataTraffic = getTableDataTraffic;

export function delTraffic(object) {
    var table = object.parentNode.parentNode.parentNode;
    var tr = object.parentNode.parentNode;
    table.removeChild(tr);

    var result = getTableDataTraffic();
    var json_data = JSON.stringify(result);
    $('#hidTraffic').val(json_data);
}

globalThis.delTraffic = delTraffic;

export function saveTraffic() {
    var name = document.getElementById("traffic.name").value;
    var proto = document.getElementById("traffic.proto").value;
    var rule = document.getElementById("traffic.rule").value;
    var src_mac = document.getElementById("traffic.src_mac").value;
    var src_ip = document.getElementById("traffic.src_ip").value;
    var src_port = document.getElementById("traffic.src_port").value;
    var dest_ip = document.getElementById("traffic.dest_ip").value;
    var dest_port = document.getElementById("traffic.dest_port").value;
    var action = document.getElementById("traffic.action").value;
    var enabled = document.getElementById("traffic.enabled").checked;
    var page_type = document.getElementById("traffic.page_type").value;

    if (proto == 'any' || proto == 'icmp' || proto == 'gre') {
        src_port = '-';
        dest_port = '-';
    }

    if (page_type == "0") {
        var table = document.getElementsByTagName("table")[1];
        table.innerHTML += "<tr  class=\"tr cbi-section-table-descr\">\n" +
            "        <td style='text-align:center'>"+ (name.length > 0 ? name : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (proto.length > 0 ? proto : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (rule.length > 0 ? rule : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (src_mac.length > 0 ? src_mac : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (src_ip.length > 0 ? src_ip : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (src_port.length > 0 ? src_port : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (dest_ip.length > 0 ? dest_ip : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (dest_port.length > 0 ? dest_port : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ (action.length > 0 ? action : "-") +"</td>\n" +
            "        <td style='text-align:center'>"+ enabled +"</td>\n" +
            "        <td><a href=\"javascript:void(0);\" onclick=\"editTraffic(this);\" >Edit</a></td>\n" +
            "        <td><a href=\"javascript:void(0);\" onclick=\"delTraffic(this);\" >Del</a></td>\n" +
            "    </tr>";
    } else {
        var table = document.getElementById("table_traffic");
        var num = 0;
        table.rows[Number(page_type)].cells[num++].innerHTML = (name.length > 0 ? name : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (proto.length > 0 ? proto : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (rule.length > 0 ? rule : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (src_mac.length > 0 ? src_mac : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (src_ip.length > 0 ? src_ip : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (src_port.length > 0 ? src_port : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (dest_ip.length > 0 ? dest_ip : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (dest_port.length > 0 ? dest_port : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (action.length > 0 ? action : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = enabled;
    }

    var result = getTableDataTraffic();
    var json_data = JSON.stringify(result);
    $('#hidTraffic').val(json_data);
    closeBoxTraffic();
}

globalThis.saveTraffic = saveTraffic;

export function editTraffic(object) {
    var row = $(object).parent().parent().parent().prevAll().length + 1;
    var num = 0;
    document.getElementById("traffic.page_type").value = row;

    var value = $(object).parent().parent().find("td");

    var arrTraffic = ['name', 'proto', 'rule', 'src_mac', 'src_ip', 'src_port', 
                        'dest_ip', 'dest_port', 'action', 'enabled'];
    
    arrTraffic.forEach(function (info) {
        if (info == null) {
            return true;    // continue: return true; break: return false
        }

        var tmp = value.eq(num++).text();
        if (info == 'enabled') {
            if (tmp == 'true') {
                document.getElementById('traffic.' + info).checked = true;
            } else {
                document.getElementById('traffic.' + info).checked = false;
            }
        } else {
            document.getElementById('traffic.' + info).value = tmp;
        }
    })

    var proto = document.getElementById("traffic.proto").value;
    if (proto == 'tcp udp' || proto == 'tcp' || proto == 'udp' || proto == 'stcp') {
        $('#pageSrcPort').show();
        $('#pageDestPort').show();
    } else {
        $('#pageSrcPort').hide();
        $('#pageDestPort').hide();
    }

    openBoxTraffic();
}

globalThis.editTraffic = editTraffic;

export function initFirewall() {
    // console.info("ElastPro firewall module initialized");

    function loadFirewall() {
        $('#loading').show();
        $.get('ajax/networking/get_firewall.php', function(data) {
            //console.log(data);
            const jsonData = JSON.parse(data);
            // general
            var arrGeneral = ['synflood_protect', 'drop_invalid', 'input', 'output', 'forward'];

            arrGeneral.forEach(function (info) {
                if (info == null) {
                    return true;    // continue: return true; break: return false
                }

                if ( info == 'synflood_protect' || info == 'drop_invalid') {
                    $('#' + info).prop('checked', (jsonData[info] == '1') ? true:false);
                } else {
                    $('#' + info).val(jsonData[info]);
                }
            })

            // forwards
            var arrForwards = ['name', 'proto', 'src_port', 'dest_ip', 'dest_port', 'enabled'];
            var forwardsCount = jsonData['forwards.count'];
            for (var i = 0; i < Number(forwardsCount); i++) {
                var table = document.getElementsByTagName("table")[0];
                var forwardsHtml = "<tr  class=\"tr cbi-section-table-descr\">\n";
                arrForwards.forEach(function (info) {
                    if (info == null) {
                        return true;    // continue: return true; break: return false
                    }

                    forwardsHtml += "        <td style='text-align:center'>" + (jsonData['forwards.' + info][i] != null ? jsonData['forwards.' + info][i] : "-") + "</td>\n";
                })
                forwardsHtml += "        <td><a href=\"javascript:void(0);\" onclick=\"editForwards(this);\" >Edit</a></td>\n" +
                                "        <td><a href=\"javascript:void(0);\" onclick=\"delForwards(this);\" >Del</a></td>\n" +
                                "    </tr>";

                table.innerHTML += forwardsHtml;
            }

            var result = getTableDataForwards();
            var dataForwards = JSON.stringify(result);
            $('#hidForwards').val(dataForwards);

            // traffic
            var arrTraffic = ['name', 'proto', 'rule', 'src_mac', 'src_ip', 'src_port',
                            'dest_ip', 'dest_port', 'action', 'enabled'];
            var trafficCount = jsonData['traffic.count'];
            for (var i = 0; i < Number(trafficCount); i++) {
                var table = document.getElementsByTagName("table")[1];
                var trafficHtml = "<tr  class=\"tr cbi-section-table-descr\">\n";
                arrTraffic.forEach(function (info) {
                    if (info == null) {
                        return true;    // continue: return true; break: return false
                    }

                    trafficHtml += "        <td style='text-align:center'>" + (jsonData['traffic.' + info][i] != null ? jsonData['traffic.' + info][i] : "-") + "</td>\n";
                })
                trafficHtml += "        <td><a href=\"javascript:void(0);\" onclick=\"editTraffic(this);\" >Edit</a></td>\n" +
                                "        <td><a href=\"javascript:void(0);\" onclick=\"delTraffic(this);\" >Del</a></td>\n" +
                                "    </tr>";

                table.innerHTML += trafficHtml;
            }

            var result = getTableDataTraffic();
            var dataTraffic = JSON.stringify(result);
            $('#hidTraffic').val(dataTraffic);
            $('#loading').hide();
        })
    }
    
    globalThis.loadFirewall = loadFirewall;
    loadFirewall();
}