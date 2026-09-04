
export function updateProgress(percentage) {
    const progressBar = document.querySelector('.progress-bar');
    progressBar.style.width = `${percentage}%`;
}

globalThis.updateProgress = updateProgress;

export function downloadBackup() {
    fetch("ajax/system/system.php?type=download_backup")
    .then(response => response.blob())
    .then(blob => {
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        const now = new Date();
        const year = now.getFullYear();
        const month = ('0' + (now.getMonth() + 1)).slice(-2);
        const day = ('0' + now.getDate()).slice(-2);
        const hours = ('0' + now.getHours()).slice(-2);
        const minutes = ('0' + now.getMinutes()).slice(-2);
        const formattedTime = year + month + day + hours + minutes;
        link.download = 'backup-elastpro-' + formattedTime + '.tar.gz';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    })
    .catch(error => console.error("Fail to download:", error));
}

globalThis.downloadBackup = downloadBackup;

export function actionBackupFile() {
    $('#hostapdModal').modal('show'); 
    fetch("ajax/system/system.php?type=action_backup")
    
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert(data.message);
        }
    })
    .catch(error => console.error("Fail to action:", error));
}

globalThis.actionBackupFile = actionBackupFile;

$('#install-user-plugin').on('shown.bs.modal', function (e) {
    var button = $(e.relatedTarget);
    $(this).data('button', button);
    var manifestData = button.data('plugin-manifest');
    var installed = button.data('plugin-installed') || false;
    var repoPublic = button.data('repo-public') || false;
    var installPath = manifestData.install_path;

    if (!installed && repoPublic && installPath === 'plugins-available') {
        insidersHTML = 'Available with <i class="fas fa-heart heart me-1"></i><a href="https://docs.raspap.com/insiders" target="_blank" rel="noopener">Insiders</a>';
        $('#plugin-additional').html(insidersHTML);
    } else {
        $('#plugin-additional').empty();
    }
    if (manifestData) {
        $('#plugin-docs').html(manifestData.plugin_docs
            ? `<a href="${manifestData.plugin_docs}" target="_blank">${manifestData.plugin_docs}</a>`
            : 'Unknown');
        $('#plugin-icon').attr('class', `${manifestData.icon || 'fas fa-plug'} link-secondary h5 me-2`);
        $('#plugin-name').text(manifestData.name || 'Unknown');
        $('#plugin-version').text(manifestData.version || 'Unknown');
        $('#plugin-description').text(manifestData.description || 'No description provided');
        $('#plugin-author').html(manifestData.author
            ? manifestData.author + (manifestData.author_uri
            ? ` (<a href="${manifestData.author_uri}" target="_blank">profile</a>)` : '') : 'Unknown');
        $('#plugin-license').text(manifestData.license || 'Unknown');
        $('#plugin-locale').text(manifestData.default_locale || 'Unknown');
        $('#plugin-configuration').html(formatProperty(manifestData.configuration || 'None'));
        $('#plugin-packages').html(formatProperty(manifestData.keys || 'None'));
        $('#plugin-dependencies').html(formatProperty(manifestData.dependencies || 'None'));
        $('#plugin-javascript').html(formatProperty(manifestData.javascript || 'None'));
        $('#plugin-sudoers').html(formatProperty(manifestData.sudoers || 'None'));
        $('#plugin-user-name').html((manifestData.user_nonprivileged && manifestData.user_nonprivileged.name) || 'None');
    }
    if (installed) {
        $('#js-install-plugin-confirm').html('OK');
    } else if (!installed && repoPublic && installPath == 'plugins-available') {
        $('#js-install-plugin-confirm').html('Get Insiders');
    } else {
        $('#js-install-plugin-confirm').html('Install now');
    }
});

$('#js-install-plugin-ok').on('click', function (e) {
    $("#install-plugin-progress").modal('hide');
    window.location.reload();
});

$('#theme-select').change(function() {
    var theme = themes[$( "#theme-select" ).val() ]; 
    set_theme(theme);
});

$('#night-mode').change(function() {
    var state = $(this).is(':checked');
    if (state == true && getCookie('theme') != 'lightsout.css') {
        set_theme('lightsout.css');
    } else {
        set_theme('custom.php');
    }
});

$('.node_online_update').click(function(){
    $('#loading').show();
    $.get('ajax/system/system.php?type=node_online_update',function(data) {
        var jsonData = JSON.parse(data);
        // console.log(jsonData);
        if (jsonData['new_node'] != null) {
            $('#new_node').html(jsonData['new_node']);
        }

        if (jsonData['cur_node'] != jsonData['new_node']) {
            $("#update_node").prop("disabled", false);
            $('#update_node').css('background-color', '#3392CC');
        }

        $('#loading').hide();
    }) 
})

$('#update_node').click(function(){
    if (confirm("Please confirm whether to execute the update node？")) {
        $('#page_progress').css('display', 'block');
        let randomPercentage = 0;
        var intervalId = setInterval(() => {
            randomPercentage = randomPercentage + 5;
            updateProgress(randomPercentage);
        }, 2000);
    
        $.get('ajax/system/system.php?type=update_node',function(data) {
            var jsonData = JSON.parse(data);
            // console.log(jsonData);
            if (jsonData.hasOwnProperty('error')) {
                clearInterval(intervalId);
                $('#progress_info').html(jsonData['error']);
                $('#progress_info').css('color', 'red');
            } else {
                clearInterval(intervalId);
                updateProgress(100);
            }
        })
    }
})

$('#reset_configs').click(function(){
    if (confirm("Please confirm whether to perform a restore？")) {
        $('#progress_info').html('Please do not power off or operate the page, restore in progress...');
        $('#page_progress').css('display', 'block');
        let randomPercentage = 0;
        var intervalId = setInterval(() => {
            randomPercentage = randomPercentage + 5;
            updateProgress(randomPercentage);
        }, 2000);

        $.get('ajax/system/system.php?type=reset_configs',function(data) {
            clearInterval(intervalId);
            updateProgress(100);
            $('#progress_info').html('Restore done, it will reboot...');
        })
    }
})

$('.download_backup').click(function(){
    var req = new XMLHttpRequest();
    var url = 'ajax/dct/system.php?type=download_configs';
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
            link.download = 'configs_' + formattedTime + '.tar';
            link.click();
        }
    }
    req.send();
})

function getTableDataAuth() {
    var tr = $("#table_auth tr");
    var result = [];
    for (var i = 2; i < tr.length; i++) {
        var tds = $(tr[i]).find("td");
        if (tds.length > 0) {
            var j = 0;
            result.push({
                'username':$(tds[j++]).html(), 
                'password':$(tds[j++]).html(),
                'purview':$(tds[j++]).html(),
            });
        }
    }

    return result;    
}

function getPurviewMenuList() {
    return (typeof window.PURVIEW_MENU_LIST !== 'undefined' && window.PURVIEW_MENU_LIST.length > 0)
        ? window.PURVIEW_MENU_LIST
        : [];
}

function editDataAuth(object) {
    var row = $(object).parent().parent().parent().prevAll().length + 1;
    document.getElementById("page_type").value = row;
    var num = 0;
    var value = $(object).parent().parent().find("td");
    var username = value.eq(num++).text();
    var password = value.eq(num++).text();
    var purview = String(value.eq(num++).text()).trim();
    var menuList = getPurviewMenuList();
    document.getElementById("auth.username").value = username;
    document.getElementById("auth.username").disabled = true;
    document.getElementById("auth.password").value = password;

    // Dash means full authority — every checkbox checked
    if (purview === '-') {
        menuList.forEach(function(item) {
            var cb = document.getElementById('auth.' + item.name);
            if (cb) cb.checked = true;
        });
    } else {
        var hex = purview.replace(/^0x/i, '');
        if (hex.length % 2 != 0) {
            hex = '0' + hex;
        }
        var bitLength = hex.length * 4;
        var big = BigInt('0x' + (hex || '0'));
        // If the entire purview is zero, the user explicitly set no permissions —
        // do NOT apply the legacy fallback for out-of-range bits.
        var isZero = (big === 0n);
        menuList.forEach(function(item) {
            var cb = document.getElementById('auth.' + item.name);
            if (cb) {
                var status;
                if (item.bit >= bitLength) {
                    // Out of range: legacy fallback (1) unless explicitly zero
                    status = isZero ? 0 : 1;
                } else {
                    status = Number((big >> BigInt(item.bit)) & 1n);
                }
                cb.checked = (status == 1) ? true : false;
            }
        });
    }

    updateAllGroupCounts();
    openBox();
}

globalThis.editDataAuth = editDataAuth;

function delDataAuth(object) {
    var table = object.parentNode.parentNode.parentNode;
    var tr = object.parentNode.parentNode;
    table.removeChild(tr);

    var result = getTableDataAuth();
    var json_data = JSON.stringify(result);
    $('#hidTD').val(json_data);
}

globalThis.delDataAuth = delDataAuth;

function addDataAuth() {
    openBox();
    document.getElementById("auth.username").value = "";
    document.getElementById("auth.username").disabled = false;
    document.getElementById("auth.password").value = "";
    document.getElementById("page_type").value = "0";

    getPurviewMenuList().forEach(function(item) {
        var cb = document.getElementById('auth.' + item.name);
        if (cb) cb.checked = false;
    });
}
globalThis.addDataAuth = addDataAuth;

function saveDataAuth() {
    var result = [];
    var menuList = getPurviewMenuList();
    var username = document.getElementById("auth.username").value;
    var password = document.getElementById("auth.password").value;
    var page_type = document.getElementById("page_type").value;

    var visibleCbs = [];
    menuList.forEach(function(item) {
        var cb = document.getElementById('auth.' + item.name);
        if (cb) visibleCbs.push({ item: item, cb: cb });
    });

    var purview;
    // Only default users (admin, superadmin) use '-' for full authority.
    // New/edited users always get explicit hex, so their permissions are unambiguous.
    var int_purview = 0n;
    var maxBit = 0;
    visibleCbs.forEach(function(entry) {
        if (entry.item.bit > maxBit) maxBit = entry.item.bit;
        var status = entry.cb.checked ? 1n : 0n;
        int_purview = int_purview | (status << BigInt(entry.item.bit));
    });
    purview = int_purview.toString(16).toUpperCase();
    // Pad to full byte coverage so high bits are explicitly 0,
    // not relying on legacy fallback behavior.
    var neededLen = Math.ceil((maxBit + 1) / 4);
    while (purview.length < neededLen) {
        purview = '0' + purview;
    }

    if (page_type == "0") {
        var usernameList = document.getElementById("username_list").value;
        var json_usernameList = JSON.parse(usernameList);
        var found = false;
        json_usernameList.forEach(function(info) {
            if (info == username) {
                alert("The username already exists, please re-enter it");
                found = true;
                return;
            }
        });

        if (found) {
            return;
        }

        var table = document.getElementsByTagName("table")[0];
        table.innerHTML += "<tr  class=\"tr cbi-section-table-descr\">\n" +
            "        <td style='text-align:center' name='username'>"+ (username.length > 0 ? username : "-") + "</td>\n" +
            "        <td style='display:none'  name='password'>"+ (password.length > 0 ? password : "-") +"</td>\n" +
            "        <td style='text-align:center' name='purview'>"+ String(purview) +"</td>\n" +
            "        <td style='width:10rem'><a href=\"javascript:void(0);\" onclick=\"editDataAuth(this);\" >Edit</a></td>\n" +
            "        <td style='width:10rem'><a href=\"javascript:void(0);\" onclick=\"delDataAuth(this);\" >Del</a></td>\n" +
            "    </tr>";
    } else {
        var table = document.getElementById("table_auth");
        var num = 0;
        table.rows[Number(page_type)].cells[num++].innerHTML = (username.length > 0 ? username : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = (password.length > 0 ? password : "-");
        table.rows[Number(page_type)].cells[num++].innerHTML = String(purview);
    }

    result = getTableDataAuth();
    var json_data = JSON.stringify(result);
    $('#hidTD').val(json_data);
    closeBox();
}

globalThis.saveDataAuth = saveDataAuth;

function updateGroupCountByGroup(group) {
    if (!group || group.length === 0) return;
    var count = group.find('.purview-group-body input[type="checkbox"]:checked').length;
    var total = group.find('.purview-group-body input[type="checkbox"]').length;
    group.find('.purview-group-count').text(count + '/' + total);
}

function updateGroupCount(checkbox) {
    updateGroupCountByGroup($(checkbox).closest('.purview-group'));
}

globalThis.updateGroupCount = updateGroupCount;

function updateAllGroupCounts() {
    $('.purview-group').each(function () {
        updateGroupCountByGroup($(this));
    });
}

globalThis.updateAllGroupCounts = updateAllGroupCounts;

function groupSelectAll(btn, checked) {
    var group = $(btn).closest('.purview-group');
    group.find('.purview-group-body input[type="checkbox"]').prop('checked', checked);
    updateGroupCountByGroup(group);
}

globalThis.groupSelectAll = groupSelectAll;

function togglePurviewGroup(header) {
    var group = $(header).closest('.purview-group');
    var body = group.find('.purview-group-body');
    body.toggle();
    group.find('.purview-group-toggle').html(body.is(':visible') ? '&#9660;' : '&#9654;');
}

globalThis.togglePurviewGroup = togglePurviewGroup;

function toggleAllGroups(expand) {
    $('.purview-group').each(function () {
        var body = $(this).find('.purview-group-body');
        body.toggle(expand);
        $(this).find('.purview-group-toggle').html(expand ? '&#9660;' : '&#9654;');
    });
}

globalThis.toggleAllGroups = toggleAllGroups;