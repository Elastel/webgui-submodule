
import "./modules/system.js";
import {
    setCSRFTokenHeader,
    getCookie,
    setCookie,
    disableValidation,
    setDarkMode,
    setLightMode
} from "./helpers.js";

import { initLogin } from "./modules/login.js";
import { initSession } from "./modules/session.js";
import { initDashboard } from "./modules/dashboard.js";
import { initNetworking } from "./modules/networking.js";
import { initDHCP } from "./modules/dhcp.js";
import { initHostapd } from "./modules/hostapd.js"
import { initWPA } from "./modules/wpa.js"
import { initLorawan } from "./modules/lorawan.js"
import { initDctBasic } from "./modules/dct-basic.js"
import { initDctInterface } from "./modules/dct-interface.js"
import { initDctRule } from "./modules/dct-rule.js"
import { initDctServer } from "./modules/dct-server.js"
import { initDctModbusSlave } from "./modules/dct-modbusslave.js"
import { initDctOpcuaServer } from "./modules/dct-opcuaserver.js"
import { initDctBacnetServer } from "./modules/dct-bacnetserver.js"
import { initDctDnp3Server } from "./modules/dct-dnp3server.js"
import { initDctDataDisplay } from "./modules/dct-datadisplay.js"
import { initAdblock } from "./modules/adblock.js"
import { initFirewall } from "./modules/firewall.js"
import { initOpenVPN } from "./modules/openvpn.js"
import { initWireGuard } from "./modules/wg.js"
import { initModbusRouter } from "./modules/modbus-router.js"
import { initBacnetRouter } from "./modules/bacnet-router.js"
import { initDDNS } from "./modules/ddns.js"
import { initServiceIotedge } from "./modules/service-iotedge.js"
import { initGps } from "./modules/gps.js"
import { initPlugins } from "./modules/plugins.js"
import { initRestApi } from "./modules/restapi.js"

$('#chirpstack_region').change(function(){
    $('#loading').show();
    $.get('ajax/service/get_service.php?type=chirpstack&region=' + $('#chirpstack_region').val(),function() {
        $('#loading').hide();
    }) 
})

// Enable Bootstrap tooltips
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})


// Add the following code if you want the name of the file appear on select
$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});

// Event listener for Bootstrap's form validation
window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
          //console.log(event.submitter);
          if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
    });
}, false);

$(document).on("click", "#js-session-expired-login", function(e) {
    const loginModal = $('#modal-admin-login');
    const redirectUrl = window.location.pathname;
    window.location.href = `/login?action=${encodeURIComponent(redirectUrl)}`;
});

// Static Array method
Array.range = (start, end) => Array.from({length: (end - start)}, (v, k) => k + start);

$(document).on("click", ".js-toggle-password", function(e) {
    var button = $(e.currentTarget);
    var field  = $(button.data("bsTarget"));
    if (field.is(":input")) {
        e.preventDefault();

        if (!button.data("__toggle-with-initial")) {
            $("i", button).removeClass("fas fa-eye").addClass(button.attr("data-toggle-with"));
        }

        if (field.attr("type") === "password") {
            field.attr("type", "text");
        } else {
            $("i", button).removeClass("fas fa-eye-slash").addClass("fas fa-eye");
            field.attr("type", "password");
        }
    }
});

// Toggles the sidebar navigation.
// Overrides the default SB Admin 2 behavior
$("#sidebarToggleTopbar").on('click', function(e) {
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled d-none");
});

// Overrides SB Admin 2
$("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
    var toggled = $(".sidebar").hasClass("toggled");
    // Persist state in cookie
    setCookie('sidebarToggled',toggled, 90);
});

// Adds active class to current nav-item
$(window).bind("load", function() {
    var url = window.location;
    $('ul.navbar-nav a').filter(function() {
      return this.href == url;
    }).parent().addClass('active');
});

$(document).ready(function(){
    $('.sidebar li a').each(function(){
        if ($($(this))[0].href == String(window.location)) {
        $(this).parent().addClass('active');
        }
    });

    $('.nav-item').each(function() {
        if ($(this).hasClass('active')) {
            var id = $($(this))[0].id;
            if (id.includes('dct_')) {
                if (id.includes('dct_south')) {
                    $('#navbar-collapse-south').addClass('show')
                    $('#south').removeClass('collapsed');
                } else if (id.includes('dct_north')) {
                    $('#navbar-collapse-north').addClass('show')
                    $('#north').removeClass('collapsed');
                }

                $('#navbar-collapse-dct').addClass('show')
                $('#dct').removeClass('collapsed');
            } else if (id.includes('remote_')) {
                if (id.includes('remote_vpn')) {
                    $('#navbar-collapse-vpn').addClass('show');
                    $('#vpn').removeClass('collapsed');
                }

                $('#navbar-collapse-remote').addClass('show');
                $('#remote').removeClass('collapsed');
            } else if (id.includes('network_')) {
                if (id.includes('network_wan')) {
                    $('#navbar-collapse-wan').addClass('show')
                    $('#wan').removeClass('collapsed');
                }

                $('#navbar-collapse-network').addClass('show');
                $('#network').removeClass('collapsed');
            } else if (id.includes('convert_')) {
                $('#navbar-collapse-convert').addClass('show');
                $('#convert').removeClass('collapsed');
            } else if (id.includes('services_')) {
                $('#navbar-collapse-services').addClass('show');
                $('#services').removeClass('collapsed');
            }  else if (id.includes('system_')) {
                $('#navbar-collapse-system').addClass('show');
                $('#system').removeClass('collapsed');
            }
        }
    });

    function itemChange(id) {
        var idArr = ['dct', 'remote', 'network', 'protocol_convert', 'services', 'system'];
        if (id.includes('page_')) {
        var key = id.slice(5);
        // console.log(key);
        if (idArr.includes(key)) {
            idArr.forEach(function (info) {
                if (id != 'page_' + info) {
                // console.log("info:" + info);
                if ($('#navbar-collapse-' + info).hasClass('show')) {
                    $('#navbar-collapse-' + info).removeClass('show');
                    $('#' + info).addClass('collapsed');
                }
                }
            });
        }
        }
    }

    $('.nav-item').click(function() {
        var id = $($(this))[0].id;
        itemChange(id);
    });
});

function contentLoaded() {
    const pageCurrent = window.location.pathname.split("/").pop();
    switch(pageCurrent) {
        case "dashboard":
            initDashboard();
            break;
        case "wired_conf":
        case "lte_conf":
        case "wlan0_conf":
            initNetworking(pageCurrent.split('_')[0]);
            break;
        case "hostapd_conf":
            initHostapd();
            break;
        case "dhcpd_conf":
            initDHCP();
            break;
        case "wpa_conf":
            initWPA();
            break;
        case "lorawan_conf":
            initLorawan();
            break;
        case "basic_conf":
            initDctBasic();
            break;
        case "interfaces_conf":
            initDctInterface();
            break;
        case "modbus_conf":
        case "ascii_conf":
        case "s7_conf":
		case "fx_conf":
        case "mc_conf":
        case "iec104_conf":
        case "opcuacli_conf":
        case "baccli_conf":
        case "dnp3cli_conf":
        case "ethernetip_conf":
        case "mbuscli_conf":
        case "snmpcli_conf":
        case "iec1107_conf":
        case "dlms_conf":
        case "iec61850cli_conf":
            initDctRule(pageCurrent.split('_')[0]);
            break;
        case "io_conf":
            initDctRule('adc');
            initDctRule('di');
            initDctRule('do');
            break;
        case "system_param_conf":
            initDctRule('system_param');
            break;
        case "server_conf":
            initDctServer();
            break;
        case "ddns":
            initDDNS();
            break;
        case "opcua":
            initDctOpcuaServer();
            break;
        case "bacnet":
            initDctBacnetServer();
            break;
        case "dnp3":
            initDctDnp3Server();
            break;
        case "modbus_slave":
            initDctModbusSlave();
            break;
        case "datadisplay":
            initDctDataDisplay();
            break;
        case "openvpn":
            initOpenVPN();
            break;
        case "wireguard":
            initWireGuard();
            break;
        case "gps":
            initGps();
            break;
        case "bacnet_router":
            initBacnetRouter();
            break;
        case "modbus_router":
            initModbusRouter();
            break;
        case "firewall_conf":
            initFirewall();
            break;
        case "iotedge":
            initServiceIotedge();
            break;
        case "restapi":
            initRestApi();
            break;
        case "login":
            initLogin();
            break;
    }
}

// --------- Global initialization ---------
initSession();

$(document)
    .ajaxSend(setCSRFTokenHeader)
    .ready(contentLoaded);