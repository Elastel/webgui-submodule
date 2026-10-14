
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
import { initDiagnostics } from "./modules/diagnostics.js"
import { initFirewall } from "./modules/firewall.js"
import { initOpenVPN } from "./modules/openvpn.js"
import { initWireGuard } from "./modules/wg.js"
import { initModbusRouter } from "./modules/modbus-router.js"
import { initBacnetRouter } from "./modules/bacnet-router.js"
import { initDDNS } from "./modules/ddns.js"
import { initServiceIotedge } from "./modules/service-iotedge.js"
import { initGps } from "./modules/gps.js"
import { initPlugins } from "./modules/plugins.js"
import { initSyslog } from "./modules/syslog.js"
import { initRestApi } from "./modules/restapi.js"

function initFormValidation() {
    document.addEventListener('submit', function (e) {
        const form = e.target;

        if (!form.classList.contains('needs-validation')) return;

        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }

        form.classList.add('was-validated');
    });
}

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
        case "system_param_conf":
            initDctRule(pageCurrent.replace(/_conf$/, ''));
            break;
        case "io_conf":
            initDctRule('adc');
            initDctRule('di');
            initDctRule('do');
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
        case "diagnostics_conf":
            initDiagnostics();
            break;
        case "firewall_conf":
            initFirewall();
            break;
        case "iotedge":
            initServiceIotedge();
            break;
        case "syslog":
            initSyslog();
            break;
        case "restapi":
            initRestApi();
            break;
        case "login":
            initLogin();
            break;
    }
}

function bindEvents() {
    const $doc = $(document);
    const $body = $("body");
    const $sidebar = $(".sidebar");
    const $loading = $("#loading");

    function apiGet(url, data) {
        $loading.show();
        return $.get(url, data)
            .fail(err => console.error("API error:", err))
            .always(() => $loading.hide());
    }

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    $('#chirpstack_region').on('change', function () {
        apiGet('ajax/service/get_service.php', {
            type: 'chirpstack',
            region: this.value
        });
    });

    $doc.on("click", ".js-toggle-password", function (e) {
        e.preventDefault();

        const $btn = $(this);
        const $field = $($btn.data("bsTarget"));

        if (!$field.length) return;

        const isPwd = $field.attr("type") === "password";
        $field.attr("type", isPwd ? "text" : "password");

        $btn.find("i").toggleClass("fa-eye fa-eye-slash");
    });

    function goLogin() {
        const redirect = encodeURIComponent(
            location.pathname + location.search + location.hash
        );
        location.assign(`/login?action=${redirect}`);
    }

    $doc.on("click", "#js-session-expired-login", function (e) {
        e.preventDefault();
        goLogin();
    });

    function toggleSidebar() {
        $body.toggleClass("sidebar-toggled");
        $sidebar.toggleClass("toggled d-none");

        setCookie("sidebarToggled", $sidebar.hasClass("toggled"), 90);
    }

    $("#sidebarToggleTopbar, #sidebarToggle, #sidebarToggleTop")
        .on("click", toggleSidebar);

        $('#hostapdModal').on('shown.bs.modal', function (e) {
        var seconds = 9;
        var countDown = setInterval(function(){
        if(seconds <= 0){
            clearInterval(countDown);
        }
        var pct = Math.floor(100-(seconds*100/9));
        document.getElementsByClassName('progress-bar').item(0).setAttribute('style','width:'+Number(pct)+'%');
        seconds --;
        }, 1000);
    });
    $('#configureClientModal').on('shown.bs.modal', function (e) {});
}

function initMenu() {
    const currentUrl = location.href;
    const $sidebar = $('.sidebar');
    const $sidebarLinks = $sidebar.find('a');
    const $navItems = $('.nav-item');
    
    const MENU_GROUPS = ['dct', 'remote', 'network', 'convert', 'services', 'system'];

    // The rail becomes a fly-out menu in two cases: the <768px media query
    // (e.g. the page is zoomed in) or a manually collapsed rail. There the
    // sub-menus are pop-up panels, so they must never be auto-expanded - doing
    // that on every page load would cover the content the user just opened.
    const narrowQuery = window.matchMedia('(max-width: 767.98px)');
    const isFlyoutMode = () => $sidebar.hasClass('toggled') || narrowQuery.matches;

    const closeFlyouts = () => {
        $sidebar.find('.collapse.show').collapse('hide');
    };

    const expandActiveBranch = () => {
        const $active = $sidebar.find('li.active');
        $active.parents('.collapse').addClass('show');
        $active.parents('.nav-item').children('a').removeClass('collapsed');
    };
    
    const MENU_MAP = [
        { match: 'dct_', parent: 'dct', extra: [
            { match: 'dct_south', id: 'south', collapse: 'navbar-collapse-south' },
            { match: 'dct_north', id: 'north', collapse: 'navbar-collapse-north' }
        ]},
        { match: 'remote_', parent: 'remote', extra: [
            { match: 'remote_vpn', id: 'vpn', collapse: 'navbar-collapse-vpn' }
        ]},
        { match: 'network_', parent: 'network', extra: [
            { match: 'network_wan', id: 'wan', collapse: 'navbar-collapse-wan' }
        ]},
        { match: 'convert_', parent: 'convert' },
        { match: 'services_', parent: 'services' },
        { match: 'system_', parent: 'system' }
    ];
    
    const activateMenuItem = (selector, addClass, removeClass) => {
        const $element = $(selector);
        if ($element.length) {
            if (addClass) $element.addClass(addClass);
            if (removeClass) $element.removeClass(removeClass);
        }
    };
    
    $sidebarLinks.each(function() {
        if (this.href === currentUrl) {
            const $this = $(this);
            $this.parent().addClass('active');
            if (!isFlyoutMode()) $this.parents('.collapse').addClass('show');
            $this.parents('.nav-item').children('a').removeClass('collapsed');
        }
    });
    
    $navItems.each(function() {
        const $item = $(this);
        if (!$item.hasClass('active')) return;
        
        const id = this.id;
        if (!id) return;
        
        const matchedGroup = MENU_MAP.find(group => id.includes(group.match));
        if (!matchedGroup) return;
        
        const parentCollapseId = `#navbar-collapse-${matchedGroup.parent}`;
        const parentId = `#${matchedGroup.parent}`;
        
        // fly-out mode keeps the panels closed, it only marks the current branch
        if (!isFlyoutMode()) $(parentCollapseId).addClass('show');
        $(parentId).removeClass('collapsed');

        if (matchedGroup.extra) {
            matchedGroup.extra.forEach(sub => {
                if (id.includes(sub.match)) {
                    if (!isFlyoutMode()) $(`#${sub.collapse}`).addClass('show');
                    $(`#${sub.id}`).removeClass('collapsed');
                }
            });
        }
    });
    
    const collapseOthers = (activeKey) => {
        MENU_GROUPS.forEach(key => {
            if (key !== activeKey) {
                $(`#navbar-collapse-${key}`).removeClass('show');
                $(`#${key}`).addClass('collapsed');
            }
        });
    };
    
    $('.nav-item').on('click', function() {
        const id = this.id;
        if (!id || !id.startsWith('page_')) return;
        
        const key = id.slice(5);
        if (MENU_GROUPS.includes(key)) {
            collapseOthers(key);
        }
    });

    // Fly-out mode: the 2nd/3rd level entries pop up as panels. Selecting an
    // entry dismisses them so they stop covering the page content.
    $sidebar.on('click', 'a.nav-link', function() {
        const $link = $(this);

        // sub-group toggles only open/close their own panel
        if ($link.attr('data-toggle') === 'collapse' || $link.attr('data-bs-toggle') === 'collapse') return;

        // the expanded sidebar keeps its sub-menu in normal flow - nothing to dismiss
        if (!isFlyoutMode()) return;

        closeFlyouts();
    });

    // Keep the two modes in sync: entering fly-out mode hides the panels,
    // leaving it restores the branch of the page that is currently open.
    const syncFlyoutMode = () => {
        if (isFlyoutMode()) closeFlyouts(); else expandActiveBranch();
    };

    if (narrowQuery.addEventListener) narrowQuery.addEventListener('change', syncFlyoutMode);
    else if (narrowQuery.addListener) narrowQuery.addListener(syncFlyoutMode);

    if ($sidebar.length && window.MutationObserver) {
        new MutationObserver(syncFlyoutMode)
            .observe($sidebar[0], { attributes: true, attributeFilter: ['class'] });
    }
}

function hideEmptyMenus() {
    // Hide top-level / second-level menu items that contain no visible child
    // items (the child items are rendered server-side according to purview).
    var changed = true;
    while (changed) {
        changed = false;
        $('.sidebar li.nav-item').each(function () {
            var $li = $(this);
            if ($li.data('emptyHidden')) return;
            var $collapse = $li.children('.collapse');
            if ($collapse.length === 0) return;
            var hasChild = $collapse.children('ul').first()
                .children('li.nav-item')
                .filter(function () { return !$(this).data('emptyHidden'); })
                .length > 0;
            if (!hasChild) {
                $li.hide();
                $li.data('emptyHidden', true);
                changed = true;
            }
        });
    }
}

function initApp() {
    initSession();
    initFormValidation();
    bindEvents();
    initMenu();
    hideEmptyMenus();
    contentLoaded();

    $(document).ajaxSend(setCSRFTokenHeader);
    globalThis.getCookie = getCookie;
    globalThis.setCookie = setCookie;
    globalThis.disableValidation = disableValidation;
}

document.addEventListener('DOMContentLoaded', () => {
    $(initApp);
});
