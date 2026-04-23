export function getOpenvpnStatus() {
    $.get('ajax/openvpn/get_openvpnstatus.php', function(data) {
        //console.log(data);
        const jsonData = JSON.parse(data);
        for(var key in jsonData){ 
            if (key == null) {
                return true;    // continue: return true; break: return false
            }
            //console.log(key + ":" + jsonData[key]);

            $('#' + key).html(jsonData[key]); 
        }
    });
}

globalThis.getOpenvpnStatus = getOpenvpnStatus;

export function initOpenVPN() {
    // console.info("ElastPro OpenVPN ajax module initialized");

    $('#ovpn-confirm-delete').on('click', '.btn-delete', function (e) {
        var cfg_id = $(this).data('recordId');
        $.post('ajax/openvpn/del_ovpncfg.php',{'cfg_id':cfg_id},function(data){
            jsonData = JSON.parse(data);
            $("#ovpn-confirm-delete").modal('hide');
            var row = $(document.getElementById("openvpn-client-row-" + cfg_id));
            row.fadeOut( "slow", function() {
                row.remove();
            });
        });
    });

    $('#ovpn-confirm-delete').on('show.bs.modal', function (e) {
        var data = $(e.relatedTarget).data();
        $('.btn-delete', this).data('recordId', data.recordId);
    });

    $('#ovpn-confirm-activate').on('click', '.btn-activate', function (e) {
        var cfg_id = $(this).data('record-id');
        $.post('ajax/openvpn/activate_ovpncfg.php',{'cfg_id':cfg_id},function(data){
            jsonData = JSON.parse(data);
            $("#ovpn-confirm-activate").modal('hide');
            setTimeout(function(){
                window.location.reload();
            },300);
        });
    });

    $('#ovpn-confirm-activate').on('shown.bs.modal', function (e) {
        var data = $(e.relatedTarget).data();
        $('.btn-activate', this).data('recordId', data.recordId);
    });

    $('#ovpn-userpw,#ovpn-certs').on('click', function (e) {
        if (this.id == 'ovpn-userpw') {
            $('#PanelCerts').hide();
            $('#PanelUserPW').show();
        } else if (this.id == 'ovpn-certs') {
            $('#PanelUserPW').hide();
            $('#PanelCerts').show();
        }
    });

    function loadOpenvpn() {
        getOpenvpnStatus();
        setInterval(getOpenvpnStatus, 60000);
        $.get('ajax/openvpn/get_openvpncfg.php', function(data) {
            // console.log(data);
            const jsonData = JSON.parse(data);
            if (jsonData['type'] != 'off') {
                $('#page_role').show();
                if (jsonData['type'] == 'config') {
                    $('#page_config').show();
                    $('#page_ovpn').hide();
                    $('#page_user_pwd').show();
                } else {
                    $('#page_config').hide();
                    $('#page_ovpn').show();
                    $('#page_user_pwd').show();
                }

                if (jsonData['role'] == 'client') {
                    $('#page_client').show();
                    $('#page_server').hide();
                } else {
                    $('#page_client').hide();
                    $('#page_server').show();
                }

                if (jsonData['role'] == 'server') {
                    $('#page_dh').show();
                } else {
                    $('#page_dh').hide();
                }

                for(var key in jsonData){ 
                    if (key == null) {
                        return true;    // continue: return true; break: return false
                    }
                    //console.log(key + ":" + jsonData[key]);
                    if (key == 'ca' || key == 'ta' || key == 'cert' || key == 'key' || key == 'ovpn' || key == 'dh') {
                        $('#' + key + '_text').html(jsonData[key]); 
                    } else if (key == 'comp_lzo' || key == 'enable_auth') {
                        $('#' + key).prop('checked', (jsonData[key] == '1') ? true:false);
                    } else {
                        $('#' + key).val(jsonData[key]); 
                    }
                }
            } else {
                $('#page_role').hide();
                $('#page_config').hide();
                $('#page_ovpn').hide();
                $('#page_user_pwd').hide();
            }
            
        });
    }

    globalThis.loadOpenvpn = loadOpenvpn;
    loadOpenvpn();
}