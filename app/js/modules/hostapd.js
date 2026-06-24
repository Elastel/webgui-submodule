
import { genPassword } from "../helpers.js";
/*
Sets the wirelss channel select options based on frequencies reported by iw.

See: https://git.kernel.org/pub/scm/linux/kernel/git/sforshee/wireless-regdb.git
Also: https://en.wikipedia.org/wiki/List_of_WLAN_channels
*/
function loadChannelSelect(selected) {
    var hw_mode = $('#cbxhwmode').val();
    var channel_select = $('#cbxchannel');
    var btn_save = $('#btnSaveHostapd');
    if (selected === null || typeof selected === 'undefined') {
        selected = $('#cbxchannel').val();
    }
    var selectableChannels = [];

    // Map selected hw_mode to available channels
    if (hw_mode === 'b' || hw_mode === 'g' || hw_mode === 'n') {
        selectableChannels = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'];
    } else {
        selectableChannels = ['34', '36', '38', '40', '42', '44', '46', '48', '149', '153', '157', '161', '165'];
    }

    // Set channel select with available values
    channel_select.empty();
    if (selectableChannels[0] === null) {
        channel_select.append($("<option></option>").attr("value", "").text("---"));
        channel_select.prop("disabled", true);
        btn_save.prop("disabled", true);
    } else {
        channel_select.prop("disabled", false);
        btn_save.prop("disabled", false);

        selectableChannels.forEach(channel => {
            channel_select.append($("<option></option>").attr("value", channel).text(channel));
        });
        channel_select.val(selected);
    }
}

globalThis.loadChannelSelect = loadChannelSelect;

export function initHostapd() {
    // console.info("ElastPro Hostapd ajax module initialized");
    function loadChannel() {
        $.get('ajax/networking/get_channel.php',function(data){
            const jsonData = JSON.parse(data);
            loadChannelSelect(jsonData);
        });
    }

    globalThis.loadChannel = loadChannel;
    loadChannel();

    $(document).on("click", "#gen_wpa_passphrase", function(e) {
        $('#txtwpapassphrase').val(genPassword(63));
    });
}