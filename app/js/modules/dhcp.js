import { getCSRFToken } from "../helpers.js";

export function initDHCP() {
    // console.info("ElastPro DHCP ajax module initialized");

    $(document).on("click", "#js-cleardnsmasq-log", function(e) {
        var csrfToken = getCSRFToken();
        $.post('ajax/logging/clearlog.php?', {
                'logfile':'/var/log/dnsmasq.log',
                'csrf_token': csrfToken
            }, function(data) {
                let jsonData = JSON.parse(data);
                $("#dnsmasq-log").val("");
            });
    });

    /*
    Populates the DHCP server form fields
    Option toggles are set dynamically depending on the loaded configuration
    */
    function loadInterfaceDHCPSelect() {
        var strInterface = $('#cbxdhcpiface').val();
        $.get('ajax/networking/get_netcfg.php?iface='+strInterface,function(data){
            const jsonData = JSON.parse(data);
            $('#dhcp-iface')[0].checked = jsonData.DHCPEnabled;
            $('#txtipaddress').val(jsonData.StaticIP);
            $('#txtsubnetmask').val(jsonData.SubnetMask);
            $('#txtgateway').val(jsonData.StaticRouters);
            // $('#chkfallback')[0].checked = jsonData.FallbackEnabled;
            $('#default-route').prop('checked', jsonData.DefaultRoute);
            $('#txtrangestart').val(jsonData.RangeStart);
            $('#txtrangeend').val(jsonData.RangeEnd);
            $('#txtrangeleasetime').val(jsonData.leaseTime);
            $('#txtdns1').val(jsonData.DNS1);
            $('#txtdns2').val(jsonData.DNS2);
            $('#cbxrangeleasetimeunits').val(jsonData.leaseTimeInterval);
            // $('#no-resolv')[0].checked = jsonData.upstreamServersEnabled;
            $('#cbxdhcpupstreamserver').val(jsonData.upstreamServers[0]);
            $('#txtmetric').val(jsonData.Metric);
        });
    }

    loadInterfaceDHCPSelect();

    // DHCP or Static IP option group
    $('#chkstatic').on('change', function() {
        if (this.checked) {
            $('#chkstatic').closest('.btn').addClass('btn-primary').removeClass('btn-outline-primary');
            $('#chkdhcp').closest('.btn').addClass('btn-outline-primary').removeClass('btn-primary');
            setStaticFieldsEnabled();
        } else {
            $('#chkstatic').closest('.btn').addClass('btn-outline-primary').removeClass('btn-primary');
            $('#chkdhcp').closest('.btn').addClass('btn-primary').removeClass('btn-outline-primary');
        }
    });

    $('input[name="dhcp-iface"]').change(function() {
        if ($('input[name="dhcp-iface"]:checked').val() == '1') {
            setDhcpFieldsEnabled();
        } else {
            setDhcpFieldsDisabled();
        }
    });

    $('#chkdhcp').on('change', function() {
        if (this.checked) {
            $('#chkdhcp').closest('.btn').addClass('btn-primary').removeClass('btn-outline-primary');
            $('#chkstatic').closest('.btn').addClass('btn-outline-primary').removeClass('btn-primary');
            setStaticFieldsDisabled();
        } else {
            $('#chkdhcp').closest('.btn').addClass('btn-outline-primary').removeClass('btn-primary');
            $('#chkstatic').closest('.btn').addClass('btn-primary').removeClass('btn-outline-primary');
        }
    });

    $('#chkfallback').change(function() {
        if ($('#chkfallback').is(':checked')) {
            setStaticFieldsEnabled();
        } else {
            setStaticFieldsDisabled();
        }
    });

    $(document).on("click", ".js-add-dhcp-static-lease", function(e) {
        e.preventDefault();
        var container = $(".js-new-dhcp-static-lease");
        var mac = $("input[name=mac]", container).val().trim();
        var ip  = $("input[name=ip]", container).val().trim();
        var comment = $("input[name=comment]", container).val().trim();
        if (mac == "" || ip == "") {
            return;
        }
        var row = $("#js-dhcp-static-lease-row").html()
            .replace("{{ mac }}", mac)
            .replace("{{ ip }}", ip)
            .replace("{{ comment }}", comment);
        $(".js-dhcp-static-lease-container").append(row);

        $("input[name=mac]", container).val("");
        $("input[name=ip]", container).val("");
        $("input[name=comment]", container).val("");
    });

    $(document).on("click", ".js-remove-dhcp-static-lease", function(e) {
        e.preventDefault();
        $(this).parents(".js-dhcp-static-lease-row").remove();
    });

    $(document).on("submit", ".js-dhcp-settings-form", function(e) {
        $(".js-add-dhcp-static-lease").trigger("click");
    });

    $(document).on("click", ".js-add-dhcp-upstream-server", function(e) {
        e.preventDefault();

        var field = $("#add-dhcp-upstream-server-field")
        var row = $("#dhcp-upstream-server").html().replace("{{ server }}", field.val())

        if (field.val().trim() == "") { return }

        $(".js-dhcp-upstream-servers").append(row)

        field.val("")
    });

    $(document).on("click", ".js-remove-dhcp-upstream-server", function(e) {
        e.preventDefault();
        $(this).parents(".js-dhcp-upstream-server").remove();
    });

    $(document).on("submit", ".js-dhcp-settings-form", function(e) {
        $(".js-add-dhcp-upstream-server").trigger("click");
    });

    $(document).on("change", ".js-field-preset", function(e) {
        var selector = this.getAttribute("data-field-preset-target")
        var value = "" + this.value
        var syncValue = function(el) { el.value = value }

        if (value.trim() === "") { return }

        document.querySelectorAll(selector).forEach(syncValue)
    });

    const dhcpCheckbox = document.getElementById('dhcp-iface');
    const rangeStart = document.getElementById('txtrangestart');
    const rangeEnd = document.getElementById('txtrangeend');
    const leaseTime = document.getElementById('txtrangeleasetime');

    function updateRequiredFields() {
        const isChecked = dhcpCheckbox.checked === true;

        if (isChecked) {
            rangeStart.setAttribute('required', 'required');
            rangeEnd.setAttribute('required', 'required');
            leaseTime.setAttribute('required', 'required');
        } else {
            rangeStart.removeAttribute('required');
            rangeEnd.removeAttribute('required');
            leaseTime.removeAttribute('required');

            rangeStart.classList.remove('is-invalid', 'is-valid');
            rangeEnd.classList.remove('is-invalid', 'is-valid');
            leaseTime.classList.remove('is-invalid', 'is-valid');
        }
    }

    $(document).on("change", ".js-field-preset", function(e) {
        var selector = this.getAttribute("data-field-preset-target")
        var value = "" + this.value
        var syncValue = function(el) { el.value = value }

        if (value.trim() === "") { return }

        document.querySelectorAll(selector).forEach(syncValue)
    });

    // set initial state
    if (dhcpCheckbox) {
        updateRequiredFields();
        setTimeout(updateRequiredFields, 100);
        dhcpCheckbox.addEventListener('change', updateRequiredFields);
    }

    function setStaticFieldsEnabled() {
        $('#txtipaddress').prop('required', true);
        $('#txtsubnetmask').prop('required', true);
        $('#txtgateway').prop('required', true);

        $('#txtipaddress').removeAttr('disabled');
        $('#txtsubnetmask').removeAttr('disabled');
        $('#txtgateway').removeAttr('disabled');
    }

    function setStaticFieldsDisabled() {
        $('#txtipaddress').prop('disabled', true);
        $('#txtsubnetmask').prop('disabled', true);
        $('#txtgateway').prop('disabled', true);

        $('#txtipaddress').removeAttr('required');
        $('#txtsubnetmask').removeAttr('required');
        $('#txtgateway').removeAttr('required');
    }

    function setDhcpFieldsEnabled() {
        $('#txtrangestart').prop('required', true);
        $('#txtrangeend').prop('required', true);
        $('#txtrangeleasetime').prop('required', true);
        $('#cbxrangeleasetimeunits').prop('required', true);

        $('#txtrangestart').removeAttr('disabled');
        $('#txtrangeend').removeAttr('disabled');
        $('#txtrangeleasetime').removeAttr('disabled');
        $('#cbxrangeleasetimeunits').removeAttr('disabled');
        $('#txtdns1').removeAttr('disabled');
        $('#txtdns2').removeAttr('disabled');
        $('#txtmetric').removeAttr('disabled');
    }

    function setDhcpFieldsDisabled() {
        $('#txtrangestart').removeAttr('required');
        $('#txtrangeend').removeAttr('required');
        $('#txtrangeleasetime').removeAttr('required');
        $('#cbxrangeleasetimeunits').removeAttr('required');

        $('#txtrangestart').prop('disabled', true);
        $('#txtrangeend').prop('disabled', true);
        $('#txtrangeleasetime').prop('disabled', true);
        $('#cbxrangeleasetimeunits').prop('disabled', true);
        $('#txtdns1').prop('disabled', true);
        $('#txtdns2').prop('disabled', true);
        $('#txtmetric').prop('disabled', true);
    }
}
