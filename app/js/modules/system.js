
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