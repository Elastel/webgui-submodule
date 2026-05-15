export function addDataDisplyItem(tbody, table, key, value, keywords) {
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

globalThis.addDataDisplyItem = addDataDisplyItem;

export function initDctDataDisplay() {
    /*datadisplay*/
    function getWebshowDate() {
        $.get('ajax/dct/get_dctcfg.php?type=datadisplay', function(data) {
            let jsonData = '';
            let factorList = '';
            if (data.includes("data")) {
                const tmp = JSON.parse(data);
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
    
    globalThis.loadDataDisplay = loadDataDisplay;
    loadDataDisplay();
}
