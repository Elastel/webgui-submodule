
export function enableIotedge(state) {
    if (state) {
      $('#page_iotedge').show();
      iotedgeSourceChange();
      iotedgeMethodChange();
    } else {
      $('#page_iotedge').hide();
    }
}

globalThis.enableIotedge = enableIotedge;

export function iotedgeSourceChange() {
    var source = document.getElementById('source').value;
    const auth_method = document.querySelector('input[data-i18n="auth_method"]').value;
    const attestion_method = document.querySelector('input[data-i18n="attestion_method"]').value;
    if (source == 'manual') {
        $('#page_source_manual').show();
        $('#page_source_dps').hide();
        changeLabel('attestion_method', auth_method);
    } else {
        $('#page_source_manual').hide();
        $('#page_source_dps').show();
        changeLabel('attestion_method', attestion_method);
    }

    var attestionMethod = document.getElementById('attestion_method');
    if (source == 'manual') {
        attestionMethod.innerHTML = `
            <option value="0">Connection String</option>
            <option value="1">Symmetric encryption</option>
            <option value="2">X.509 certificate</option>
        `;
    } else {
        attestionMethod.innerHTML = `
            <option value="0">TPM</option>
            <option value="1">Symmetric encryption</option>
            <option value="2">X.509 certificate</option>
        `;
    }

    iotedgeMethodChange();
}

globalThis.iotedgeSourceChange = iotedgeSourceChange;

export function iotedgeMethodChange() {
    var method = document.getElementById('attestion_method').value;
    var source = document.getElementById('source').value;

    // console.log(method);

    if (method == '0') {
        $('#page_method_symmetric').hide();
        $('#page_method_x509').hide();
    } else if (method == '1') {
        $('#page_method_symmetric').show();
        $('#page_method_x509').hide();
    } else if (method == '2') {
        $('#page_method_symmetric').hide();
        $('#page_method_x509').show();
    }

    if (source == 'manual' && method == '0') {
        $('#page_source_manual_connection_string').show();
        $('#page_source_manual_key_x509').hide();
    } else {
        $('#page_source_manual_connection_string').hide();
        $('#page_source_manual_key_x509').show();
    }
}

globalThis.iotedgeMethodChange = iotedgeMethodChange;

export function certChangeX509() {
    $('#cert_text').html($('#certificate')[0].files[0].name);
}

globalThis.certChangeX509 = certChangeX509;

export function keyChangeX509() {
    $('#key_text').html($('#private_key')[0].files[0].name);
}

globalThis.keyChangeX509 = keyChangeX509;

export function changeLabel(input_object, label_new_text) {
    let input = document.getElementById(input_object);
    let label = input.previousElementSibling;
    label.textContent = label_new_text;
}

globalThis.changeLabel = changeLabel;

export function initServiceIotedge() {
    $(document).on("click", "#gen_apikey", function(e) {
        $('#txtapikey').val(genPassword(32).toLowerCase());
    });

    function loadIotedge() {
        $('#loading').show();
        $.get('ajax/service/get_service.php?type=iotedge',function(data) {
            var jsonData = JSON.parse(data);
            var arr = [
            'enabled', 
            'source', 
            'connection_string',
            'iothub_hostname',
            'device_id',
            'global_endpoint', 
            'id_scope', 
            'attestion_method', 
            'registration_id', 
            'symmetric_key', 
            'certificate', 
            'private_key'
            ];

            $('#enabled').val(jsonData.enabled);
            if (jsonData.enabled == '1') {
                $('#page_iotedge').show();
                $('#iotedge_enable').prop('checked', true);

                arr.forEach(function (info) {
                    if (info == null) {
                        return true;    // continue: return true; break: return false
                    }

                    if (info == 'certificate') {
                        if (jsonData['certificate']) {
                            $('#cert_text').html(jsonData['certificate']);
                        }
                    } else if (info == 'private_key') {
                        if (jsonData['private_key']) {
                            $('#key_text').html(jsonData['private_key']);
                        }
                    } else {
                        $('#' + info).val(jsonData[info]);
                    }
                    
                })
                
                if (jsonData['source'] == 'manual') {
                    $('#page_source_manual').show();
                    $('#page_source_dps').hide();
                    changeLabel('attestion_method', 'Authentication Method');
                } else {
                    $('#page_source_manual').hide();
                    $('#page_source_dps').show();
                    changeLabel('attestion_method', 'Attestation Method');
                }
                
                iotedgeMethodChange();
            } else {
                $('#page_iotedge').hide(); 
                $('#basic_disable').prop('checked', true);
            }

            $('#loading').hide();
        });
    }

    globalThis.loadIotedge = loadIotedge;
    loadIotedge();
}