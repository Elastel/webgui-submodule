<div class="tab-pane" id="port-forwards">
    <input type="hidden" name="table_forwards_data" value="" id="hidForwards">
    <div class="cbi-section cbi-tblsection" id="page_forwards" name="page_forwards">
        <table class="table cbi-section-table" name="table_forwards" id="table_forwards">
            <tr class="tr cbi-section-table-titles">
            <th class="th cbi-section-table-cell"><?php echo _("Name"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("Protocol"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("External port"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("Internal IP"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("Internal Port"); ?></th>
            <th class="th cbi-section-table-cell"><?php echo _("Enable"); ?></th>
            <th class="th cbi-section-table-cell cbi-section-actions"></th>
            <th class="th cbi-section-table-cell cbi-section-actions"></th>
            </tr>
            <tr class="tr cbi-section-table-descr">
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell" ><?php echo _("Leave blank to indicate any IP address"); ?></th>
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell" ></th>
            <th class="th cbi-section-table-cell cbi-section-actions"></th>
            <th class="th cbi-section-table-cell cbi-section-actions"></th>
            </tr>
        </table>
        <div class="cbi-section-create">
            <input type="button" class="cbi-button-add" name="forwards_popBox" value="Add" onclick="addDataForwards()">
        </div>
    </div>

    <div id="forwards_popLayer"></div>
    <div id="forwards_popBox" style="overflow:auto; height:50%">
        <input hidden="hidden" name="forwards.page_type" id="forwards.page_type" value="0">
        <h4><?php echo _("Port Forwards"); ?></h4>
        <div class="cbi-section">
            <div class="cbi-value">
                <label class="cbi-value-title" for="forwards.name"><?php echo _("Name"); ?></label>
                <input id="forwards.name" type="text" class="cbi-input-text">
            </div>
            <div class="cbi-value">
                <label class="cbi-value-title" for="forwards.proto"><?php echo _("Protocol"); ?></label>
                <select id="forwards.proto" class="cbi-input-select" onchange="protoChangeForwards()">
                    <option value="tcp udp">TCP UDP</option>
                    <option value="tcp">TCP</option>
                    <option value="udp">UDP</option>
                    <option value="stcp">STCP</option>
                    <option value="icmp">ICMP</option>
                    <option value="gre">GRE</option>
                    <option value="any">Any</option>
                </select>
            </div>

            <div class="cbi-value" id="pageEport" name="pageEport">
                <label class="cbi-value-title" for="forwards.src_port"><?php echo _("External port"); ?></label>
                <input id="forwards.src_port" type="text" class="cbi-input-text">
            </div>
            
            <div class="cbi-value">
                <label class="cbi-value-title" for="forwards.dest_ip"><?php echo _("Internal IP address"); ?></label>
                <input id="forwards.dest_ip" type="text" class="cbi-input-text">
                <label class="cbi-value-description"><?php echo _("Leave blank to indicate any IP address"); ?></label>
            </div>

            <div class="cbi-value" id="pageIPort" name="pageIPort">
                <label class="cbi-value-title" for="forwards.dest_port"><?php echo _("Internal port"); ?></label>
                <input id="forwards.dest_port" type="text" class="cbi-input-text">
            </div>

            <div class="cbi-value">
                <label class="cbi-value-title" for="forwards.enabled"><?php echo _("Enable"); ?></label>
                <input type="checkbox" id="forwards.enabled" value="1" checked="" data-widget-id="enabled">
            </div>
        </div>

        <div class="right">
            <input type="button" class="cbi-button" onclick="$('#forwards_popBox').hide();$('#forwards_popLayer').hide();" value="<?php echo _("Dismiss"); ?>"></input>
            <input type="button" class="cbi-button cbi-button-positive important" onclick="saveForwards()" value="<?php echo _("Save"); ?>"></input>
        </div>
    </div><!-- popBox -->
</div>

<script type="text/javascript">
    function protoChangeForwards() {
        var proto = document.getElementById("forwards.proto").value;

        if (proto == 'tcp udp' || proto == 'tcp' || proto == 'udp' || proto == 'stcp') {
            $('#pageEport').show();
            $('#pageIPort').show();
        } else {
            $('#pageEport').hide();
            $('#pageIPort').hide();
        }
    }
</script>