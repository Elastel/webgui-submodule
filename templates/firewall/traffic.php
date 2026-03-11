<div class="tab-pane" id="traffic-rules">
    <input type="hidden" name="table_traffic_data" value="" id="hidTraffic">
    <div class="cbi-section cbi-tblsection" id="page_traffic" name="page_traffic">
        <table class="table cbi-section-table" name="table_traffic" id="table_traffic">
            <tr class="tr cbi-section-table-titles">
            <th class="th cbi-section-table-cell"><?php echo _("Name"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("Protocol"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("Rule"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("Source MAC"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("Source IP"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("Source port"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("Destination IP"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("Destination port"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("Action"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("Enable"); ?></th>
            <th class="th cbi-section-table-cell cbi-section-actions"></th>
            <th class="th cbi-section-table-cell cbi-section-actions"></th>
            </tr>
            <tr class="tr cbi-section-table-descr">
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell cbi-section-actions"></th>
            <th class="th cbi-section-table-cell cbi-section-actions"></th>
            </tr>
        </table>
        <div class="cbi-section-create">
            <input type="button" class="cbi-button-add" name="traffic_popBox" value="Add" onclick="addDataTraffic()">
        </div>
    </div>

    <div id="traffic_popLayer"></div>
    <div id="traffic_popBox" style="overflow:auto">
        <input hidden="hidden" name="traffic.page_type" id="traffic.page_type" value="0">
        <h4><?php echo _("Traffic Rules"); ?></h4>
        <div class="cbi-section">
            <div class="cbi-value">
                <label class="cbi-value-title" for="traffic.name"><?php echo _("Name"); ?></label>
                <input id="traffic.name" type="text" class="cbi-input-text">
            </div>
            <div class="cbi-value">
                <label class="cbi-value-title" for="traffic.proto"><?php echo _("Protocol"); ?></label>
                <select id="traffic.proto" class="cbi-input-select" onchange="protoChangeTraffic()">
                    <option value="tcp udp">TCP UDP</option>
                    <option value="tcp">TCP</option>
                    <option value="udp">UDP</option>
                    <option value="stcp">STCP</option>
                    <option value="icmp">ICMP</option>
                    <option value="gre">GRE</option>
                    <option value="any">Any</option>
                </select>
            </div>
            <div class="cbi-value">
                <label class="cbi-value-title" for="traffic.rule"><?php echo _("Rule"); ?></label>
                <select id="traffic.rule" class="cbi-input-select">
                    <option value="input">input</option>
                    <option value="output">output</option>
                </select>
            </div>
            <div class="cbi-value">
                <label class="cbi-value-title" for="traffic.src_mac"><?php echo _("Source MAC"); ?></label>
                <input id="traffic.src_mac" type="text" class="cbi-input-text">
            </div>
            <div class="cbi-value">
                <label class="cbi-value-title" for="traffic.src_ip"><?php echo _("Source IP"); ?></label>
                <input id="traffic.src_ip" type="text" class="cbi-input-text">
            </div>
            <div class="cbi-value" id="pageSrcPort" name="pageSrcPort">
                <label class="cbi-value-title" for="traffic.src_port"><?php echo _("Source port"); ?></label>
                <input id="traffic.src_port" type="text" class="cbi-input-text">
            </div>
            <!-- <div class="cbi-value">
                <label class="cbi-value-title" for="traffic.dest"><?php // echo _("Destination zone"); ?></label>
                <select id="traffic.dest" class="cbi-input-select">
                    <option value="wan">wan</option>
                    <option value="lan">lan</option>
                </select>
            </div> -->
            <div class="cbi-value">
                <label class="cbi-value-title" for="traffic.dest_ip"><?php echo _("Destination IP"); ?></label>
                <input id="traffic.dest_ip" type="text" class="cbi-input-text">
            </div>
            <div class="cbi-value" id="pageDestPort" name="pageDestPort">
                <label class="cbi-value-title" for="traffic.dest_port"><?php echo _("Destination port"); ?></label>
                <input id="traffic.dest_port" type="text" class="cbi-input-text">
            </div>

            <div class="cbi-value">
                <label class="cbi-value-title"><?php echo _("Action"); ?></label>
                <select id="traffic.action" name="traffic.action" class="cbi-input-select">
                <option value="reject" selected>reject</option>
                <option value="drop">drop</option>
                <option value="accept">accept</option>
                </select>
            </div>

            <div class="cbi-value">
                <label class="cbi-value-title" for="traffic.enabled"><?php echo _("Enable"); ?></label>
                <input type="checkbox" id="traffic.enabled" value="1" checked="" data-widget-id="enabled">
            </div>
        </div>

        <div class="right">
            <input type="button" class="cbi-button" onclick="$('#traffic_popBox').hide();$('#traffic_popLayer').hide();" value="<?php echo _("Dismiss"); ?>"></input>
            <input type="button" class="cbi-button cbi-button-positive important" onclick="saveTraffic()" value="<?php echo _("Save"); ?>"></input>
        </div>
    </div><!-- popBox -->
</div>

<script type="text/javascript">
    function protoChangeTraffic() {
        var proto = document.getElementById("traffic.proto").value;

        if (proto == 'tcp udp' || proto == 'tcp' || proto == 'udp' || proto == 'stcp') {
            $('#pageSrcPort').show();
            $('#pageDestPort').show();
        } else {
            $('#pageSrcPort').hide();
            $('#pageDestPort').hide();
        }
    }
</script>