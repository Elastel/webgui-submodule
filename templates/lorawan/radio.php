<div class="tab-pane fade" id="radio">
  <div>
    <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Radio 0 enable"); ?></label>
        <input type="checkbox"  onchange="enableRadio0(this)" name="radio0_enable" id="radio0_enable" value="1"/>
    </div>

    <div id="page_radio0" name="page_radio0">
        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Radio_0 frequency"); ?></label>
            <input type="text" class="cbi-input-text" name="radio0_frequency" id="radio0_frequency" />
        </div>

        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Radio_0 for tx"); ?></label>
            <input type="checkbox"  onchange="enableRadio0Tx(this)" name="radio0_tx" id="radio0_tx" value="1"/>
        </div>

        <div id="page_radio0_tx" name="page_radio0_tx">
            <div class="cbi-value">
                <label class="cbi-value-title"><?php echo _("Radio_0 tx min frequency"); ?></label>
                <input type="text" class="cbi-input-text" name="radio0_tx_min" id="radio0_tx_min" />
            </div>
            <div class="cbi-value">
                <label class="cbi-value-title"><?php echo _("Radio_0 tx max frequency"); ?></label>
                <input type="text" class="cbi-input-text" name="radio0_tx_max" id="radio0_tx_max" />
            </div>
        </div>
    </div>

    <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Radio 1 enable"); ?></label>
        <input type="checkbox"  onchange="enableRadio1(this)" name="radio1_enable" id="radio1_enable" value="1"/>
    </div>

    <div id="page_radio1" name="page_radio1">
        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Radio_1 frequency"); ?></label>
            <input type="text" class="cbi-input-text" name="radio1_frequency" id="radio1_frequency" />
        </div>

        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Radio_1 for tx"); ?></label>
            <input type="checkbox"  onchange="enableRadio1Tx(this)" name="radio1_tx" id="radio1_tx" value="1"/>
        </div>
    </div>
  </div><!-- /.row -->
</div><!-- /.tab-pane | basic tab -->
<script type="text/javascript">
    function enableRadio0(state) {
        var enable = state.checked;
        if (enable) {
            $('#page_radio0').show();
            enableRadio0Tx(document.getElementById('radio0_tx'));
        } else {
            $('#page_radio0').hide();
        }
    }

    function enableRadio0Tx(state) {
        var enable = state.checked;
        if (enable) {
            $('#page_radio0_tx').show();
        } else {
            $('#page_radio0_tx').hide();
        }
    }

    function enableRadio1(state) {
        var enable = state.checked;
        if (enable) {
            $('#page_radio1').show();
        } else {
            $('#page_radio1').hide();
        }
    }
</script>
