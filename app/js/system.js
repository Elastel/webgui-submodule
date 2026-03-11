/*
version: 1.0.0
*/

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

function updateProgress(percentage) {
    const progressBar = document.querySelector('.progress-bar');
    progressBar.style.width = `${percentage}%`;
}

$('#update_node').click(function(){
    if (confirm("Please confirm whether to execute the update node？")) {
        $('#page_progress').css('display', 'block');
        let randomPercentage = 0;
        // 模拟进度更新
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
        // 模拟进度更新
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
