<div class="tab-pane active" id="module-list">
<?php
    $html = "<table class=\"table cbi-section-table\">";
    $html .= '<tr class="tr cbi-section-table-titles">
    <th style=\"text-align:left;width:30%;font-weight:bold; padding:0.5rem; font-size:0.8rem\">Name</th>
    <th style=\"text-align:left;width:30%;font-weight:bold; padding:0.5rem; font-size:0.8rem\">Status</th>
    <th style=\"text-align:left;width:30%;font-weight:bold; padding:0.5rem; font-size:0.8rem\">Description</th>
    <th style=\"text-align:left;width:30%;font-weight:bold; padding:0.5rem; font-size:0.8rem\">Config</th></tr>';

    foreach ($iotedge_data as $row) {
        $html .= "<tr class=\"tr cbi-section-table-titles\">";
        $html .= "<td style=\"text-align:left; padding:0.5rem; font-size:0.8rem\">{$row['name']}</td>";
        $html .= "<td style=\"text-align:left; padding:0.5rem; font-size:0.8rem\">{$row['status']}</td>";
        $html .= "<td style=\"text-align:left; padding:0.5rem; font-size:0.8rem\">{$row['description']}</td>";
        $html .= "<td style=\"text-align:left; padding:0.5rem; font-size:0.8rem\">{$row['config']}</td>";
        $html .= "</tr>";
    }

    $html .= "</table>";
    
    echo $html;
?>
</div>

