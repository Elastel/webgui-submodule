export function initWireGuard() {
    // console.info("ElastPro WireGuard ajax module initialized");

    // Handler for the wireguard generate key button
    $('.wg-keygen').click(function(){
        var entity_pub = $(this).parent('div').prev('input[type="text"]');
        var entity_priv = $(this).parent('div').next('input[type="hidden"]');
        var updated = entity_pub.attr('name')+"-pubkey-status";
        
        var csrfToken = $('meta[name=csrf_token]').attr('content');
        $.post('ajax/networking/get_wgkey.php',{'entity':entity_pub.attr('name'), 'csrf_token': csrfToken },function(data){
            // console.log(data);
            var jsonData = JSON.parse(data);
            entity_pub.val(jsonData.pubkey);
            $('#wg-srvprikey').val(jsonData.privkey);
            $('#' + updated).removeClass('check-hidden').addClass('check-updated').delay(500).animate({ opacity: 1 }, 700);
        })
    })

    // Handler for wireguard client.conf download
    $('.wg-client-dl').click(function(){
        var req = new XMLHttpRequest();
        var url = 'ajax/networking/get_wgcfg.php?type=download';
        req.open('get', url, true);
        req.responseType = 'blob';
        req.setRequestHeader('Content-type', 'text/plain; charset=UTF-8');
        req.onreadystatechange = function (event) {
            if(req.readyState == 4 && req.status == 200) {
                var blob = req.response;
                var link=document.createElement('a');
                link.href=window.URL.createObjectURL(blob);
                link.download = 'client.conf';
                link.click();
            }
        }
        req.send();
    })

    function loadWireguard() {
        $.get('ajax/networking/get_wgcfg.php?type=settings', function(data) {
            //console.log(data);
            const jsonData = JSON.parse(data);
            if (jsonData['type'] != 'off') {
                if (jsonData['type'] == 'config') {
                    $('#page_config').show();
                    $('#page_role').show();
                    $('#page_wg').hide();
                } else {
                    $('#page_config').hide();
                    $('#page_role').hide();
                    $('#page_wg').show();
                }

                if (jsonData['role'] == 'client') {
                    $('#page_client').show();
                    $('#page_server').hide();
                } else {
                    $('#page_client').hide();
                    $('#page_server').show();
                }
            } else {
                $('#page_role').hide();
                $('#page_config').hide();
                $('#page_wg').hide();
            }

            for(var key in jsonData){ 
                if (key == null) {
                    return true;    // continue: return true; break: return false
                }
                //console.log(key + ":" + jsonData[key]);
                if (key == 'wg') {
                    $('#' + key + '_text').html(jsonData[key]); 
                } else {
                    $('#' + key).val(jsonData[key]); 
                }
            }
            
        });
    }

    globalThis.loadWireguard = loadWireguard;
    loadWireguard();
}