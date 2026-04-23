export function initDashboard() {
    // console.info("ElastPro dashboard module initialized");

    function getDashboardData() {
        $.get('ajax/service/get_dashboard_data.php', function(data) {
            const jsonData = JSON.parse(data);
            $('#local_time').html(jsonData['local_time']);
            $('#uptime').html(jsonData['uptime']);
        })
    }

    function loadDashboard() {
        getDashboardData();
        setInterval(getDashboardData, 1000);
    }

    loadDashboard();
}