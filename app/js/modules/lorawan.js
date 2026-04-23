export function initLorawan() {
    // console.info("ElastPro lorawan ajax module initialized");
    function loadDataLorawan(){
        $.get('ajax/networking/get_loragw.php?type=lorawan', function(data) {
            const jsonData = JSON.parse(data);

            var type = jsonData['type'];

            if (type != null) {
                $('#type').val(type);
            } else {
                $('#type').val('0');
            }

            typeChangeLorawan();
            
            var general = ['server_address', 'serv_port_up', 'serv_port_down', 'gateway_ID',
            'keepalive_interval', 'stat_interval', 'frequency'];

            general.forEach(function (info) {
                if (info == null) {
                    return true;    // continue: return true; break: return false
                }

                $('#' + info).val(jsonData[info]);
            })

            var radio = ['radio0_enable', 'radio0_frequency', 'radio0_tx', 'radio0_tx_min', 'radio0_tx_max',
            'radio1_enable', 'radio1_frequency', 'radio1_tx'];

            radio.forEach(function (info) {
                if (info == null) {
                    return true;    // continue: return true; break: return false
                }

                if (info == 'radio0_enable' || info == 'radio0_tx' || info == 'radio1_enable' ||
                    info == 'radio1_tx') {
                    $('#' + info).prop('checked', (jsonData[info] == '1') ? true:false);
                } else {
                    $('#' + info).val(jsonData[info]);
                }
            })

            if (jsonData['radio0_enable'] == '1') {
                $('#page_radio0').show();
            } else {
                $('#page_radio0').hide();
            }

            if (jsonData['radio0_tx'] == '1') {
                $('#page_radio0_tx').show();
            } else {
                $('#page_radio0_tx').hide();
            }

            if (jsonData['radio1_enable'] == '1') {
                $('#page_radio1').show();
            } else {
                $('#page_radio1').hide();
            }

            var channels = ['channel_enable', 'channel_radio', 'channel_if'];
            for (var i = 0; i < 8; i++) {
                channels.forEach(function (info) {
                    if (info == null) {
                        return true;    // continue: return true; break: return false
                    }

                    if (info == 'channel_enable') {
                        $('#' + info + i).prop('checked', (jsonData[info + i] == '1') ? true:false);
                    } else {
                        $('#' + info + i).val(jsonData[info + i]);
                    }
                })
            }

            // $general = array('protocol', 'uri', 'auth_mode', 'client_token');
            if (type == '2')
                $('#gateway_ID').val(jsonData['gateway_ID_station']);

            $('#protocol').val(jsonData['protocol']);
            $('#uri').val(jsonData['uri']);
            $('#auth_mode').val(jsonData['auth_mode']);
            modeChange();
        
            if (jsonData['lora_ca']) {
                $('#ca_text').html(jsonData['lora_ca']);
            }

            if (jsonData['lora_crt']) {
                $('#crt_text').html(jsonData['lora_crt']);
            }

            if (jsonData['lora_key']) {
                $('#key_text').html(jsonData['lora_key']);
            }
        });
    }
    
    globalThis.loadDataLorawan = loadDataLorawan;
    loadDataLorawan();

    function freqPlanChange() {
        var a = document.getElementById('frequency').value;
        if (a == '0') { // EU868
            $('#radio0_frequency').val('867500000');
            $('#radio0_tx_min').val('863000000');
            $('#radio0_tx_max').val('870000000');
            $('#radio1_frequency').val('868500000');
        } else if (a == '1') { // CN490
            $('#radio0_frequency').val('471400000');
            $('#radio0_tx_min').val('500000000');
            $('#radio0_tx_max').val('510000000');
            $('#radio1_frequency').val('475000000');
        } else if (a == '2') { // US915
            $('#radio0_frequency').val('904300000');
            $('#radio0_tx_min').val('923000000');
            $('#radio0_tx_max').val('928000000');
            $('#radio1_frequency').val('905000000');
        } else if (a == '3') { // AU915
            $('#radio0_frequency').val('917200000');
            $('#radio0_tx_min').val('915000000');
            $('#radio0_tx_max').val('928000000');
            $('#radio1_frequency').val('917900000');
        }  else if (a == '4') { // AS923
            $('#radio0_frequency').val('922300000');
            $('#radio0_tx_min').val('920000000');
            $('#radio0_tx_max').val('924000000');
            $('#radio1_frequency').val('923100000');
        }
    }

    globalThis.freqPlanChange = freqPlanChange;

    function modeChange() {
        var select = document.getElementById('auth_mode');

        if (!select.value || select.value === "") {
            if (select.querySelector('option[value="0"]')) {
                select.value = "0";
            }
        }

        var mode = select.value;

        if (mode == '1') {
            $('#page_one').show();
            $('#page_two').show();
        } else if (mode == '2') {
            $('#page_one').hide();
            $('#page_two').show();
        } else {
            $('#page_one').hide();
            $('#page_two').hide();
        }
    }

    globalThis.modeChange = modeChange;

    function typeChangeLorawan() {
        var type = document.getElementById('type').value;
        if (type == '0') {
            $('#page_eui').hide();
            $('#page_packet_forwarder').hide();
            $('#page_basic_station').hide();
        } else if (type == '1') {
            $('#page_eui').show();
            $('#page_packet_forwarder').show();
            $('#page_basic_station').hide();
        } else if (type == '2') {
            modeChange();
            $('#page_eui').show();
            $('#page_packet_forwarder').hide();
            $('#page_basic_station').show();
        }
    }

    globalThis.typeChangeLorawan = typeChangeLorawan;

    function caFileChangeLora() {
        $('#ca_text').html($('#lora_ca')[0].files[0].name);
    }

    globalThis.caFileChangeLora = caFileChangeLora;

    function cerFileChangeLora() {
        $('#crt_text').html($('#lora_crt')[0].files[0].name);
    }

    globalThis.cerFileChangeLora = cerFileChangeLora;

    function keyFileChangeLora() {
        $('#key_text').html($('#lora_key')[0].files[0].name);
    }

    globalThis.keyFileChangeLora = keyFileChangeLora;
}