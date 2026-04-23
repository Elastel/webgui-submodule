export function initWPA() {
    // console.info("ElastPro WPA ajax module initialized");

    function loadWifiStations(refresh) {
        var complete = function() {
            $(this).removeClass('loading-spinner');
        };

        var qs = refresh === true ? '?refresh' : '';

        $('.js-wifi-stations')
            .addClass('loading-spinner')
            .empty()
            .load('ajax/networking/wifi_stations.php' + qs, complete);
    }

    $(".js-reload-wifi-stations").on("click", () => loadWifiStations(true));

    $('.js-enable-wifi-stations').on('click', function() {
        let isChecked = $(this).is(':checked');

        var complete = function() { $(this).removeClass('loading-spinner'); }
        $('.js-wifi-stations').addClass('loading-spinner').empty().load('ajax/networking/wifi_stations.php?enable=' + (isChecked ? '1' : '0'), complete);
    });

    globalThis.loadWifiStations = loadWifiStations;
    loadWifiStations();
}