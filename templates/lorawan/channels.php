<div class="tab-pane fade" id="channels">
  <div class="row">
    <?php for ($i = 0; $i < 8; $i++) {?>
        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("multiSF channel $i enable"); ?></label>
            <?php
            echo<<<EOF
            <input type="checkbox" class="cbi-input-checkbox" onchange="enableChannel('{$i}')" name="channel_enable{$i}" id="channel_enable{$i}" value="1"/>
            EOF;
            ?>
        </div>

        <div id=<?php echo _("page_channel$i"); ?> name=<?php echo _("page_channel$i"); ?>>
            <div class="cbi-value">
                <label class="cbi-value-title"><?php echo _("multiSF channel $i radio"); ?></label>
                <select id=<?php echo _("channel_radio$i"); ?> name=<?php echo _("channel_radio$i"); ?> class="cbi-input-select">
                    <option value="0">radio0</option>
                    <option value="1">radio1</option>
                </select>
            </div>

            <div class="cbi-value">
                <label class="cbi-value-title"><?php echo _("multiSF channel $i IF"); ?></label>
                <input type="text" class="cbi-input-text" name=<?php echo _("channel_if$i"); ?> id=<?php echo _("channel_if$i"); ?> />
            </div>
        </div>
    <?php } ?>
  </div><!-- /.row -->
</div><!-- /.tab-pane | basic tab -->
<script type="text/javascript">
    function enableChannel(count) {
        var enable = document.getElementById('channel_enable' + count).checked;
        if (enable) {
            $('#page_channel' + count).show();
        } else {
            $('#page_channel' + count).hide();
        }
    }
</script>
