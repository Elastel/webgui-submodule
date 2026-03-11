<!-- wireguard settings tab -->
<div class="tab-pane active" id="wgsettings">
  <div class="row">
    <div class="cbi-value">
      <label class="cbi-value-title"><?php echo _("Start Selection"); ?></label>
      <select id="type" name="type" class="cbi-input-select" onchange="typeChangeWg()">
        <option value="off">OFF</option>
        <option value="config">Config Enabled</option>
        <option value="wg">Wg File Enabled</option>
      </select>
    </div>

    <div class="cbi-value" id="page_role">
      <label class="cbi-value-title"><?php echo _("Role"); ?></label>
      <select id="role" name="role" class="cbi-input-select" onchange="roleChangeWg()">
        <option value="client">Client</option>
        <option value="server">Server</option>
      </select>
    </div>

    <div id="page_config">
      <div class="cbi-value">
        <label for="code" class="cbi-value-title"><?php echo _("Local Public Key"); ?></label>
        <input type="text" class="cbi-input-text" name="wg-server" id="wg-srvpubkey" readonly value="<?php echo htmlspecialchars($wg_srvpubkey, ENT_QUOTES); ?>" />
        <div style="display: inline-block;">
          <button class="btn btn-outline-secondary rounded-right wg-keygen" type="button"><i class="fas fa-magic"></i></button>
          <span id="wg-server-pubkey-status" class="input-group-addon check-hidden ml-2 mt-1"><i class="fas fa-check"></i></span>
        </div>
      </div>

      <div class="cbi-value">
        <label for="code" class="cbi-value-title"><?php echo _("Local Private Key"); ?></label>
        <input type="text" class="cbi-input-text" name="wg-srvprikey" id="wg-srvprikey" value="<?php echo htmlspecialchars($wg_srvprikey, ENT_QUOTES); ?>" />
      </div>

      <div class="cbi-value">
        <label for="code" class="cbi-value-title"><?php echo _("Local IP Address"); ?></label>
        <input type="text" class="cbi-input-text" name="wg_srvipaddress" value="<?php echo htmlspecialchars($wg_srvipaddress, ENT_QUOTES); ?>" />
        <label class="cbi-value-description"><?php echo _("eg:10.0.10.4/24"); ?></label>
      </div>

      <div class="cbi-value">
        <label for="code" class="cbi-value-title"><?php echo _("Local Port"); ?></label>
        <input type="text" class="cbi-input-text" name="wg_srvport" value="<?php echo htmlspecialchars($wg_srvport, ENT_QUOTES); ?>" />
      </div>
      
      <div class="cbi-value">
        <label for="code" class="cbi-value-title"><?php echo _("DNS"); ?></label>
        <input type="text" class="cbi-input-text" name="wg_srvdns" value="<?php echo htmlspecialchars($wg_srvdns, ENT_QUOTES); ?>" />
      </div>

      <div class="cbi-value">
        <label for="code" class="cbi-value-title"><?php echo _("Peer Public Key"); ?></label>
        <input type="text" class="cbi-input-text" name="wg-peer" id="wg-peerpubkey" value="<?php echo htmlspecialchars($wg_peerpubkey, ENT_QUOTES); ?>" />
      </div>

      <div id="page_client">
        <div class="cbi-value">
          <label for="code" class="cbi-value-title"><?php echo _("Endpoint Address"); ?></label>
          <input type="text" class="cbi-input-text" name="wg_pendpoint" value="<?php echo htmlspecialchars($wg_pendpoint, ENT_QUOTES); ?>" />
          <label class="cbi-value-description"><?php echo _("eg:10.0.10.8:51820"); ?></label>
        </div>
      </div>

      <div class="cbi-value">
        <label for="code" class="cbi-value-title"><?php echo _("Allowed IPs"); ?></label>
        <input type="text" class="cbi-input-text" name="wg_pallowedips" value="<?php echo htmlspecialchars($wg_pallowedips[1], ENT_QUOTES); ?>" />
        <label class="cbi-value-description"><?php echo _("eg:10.0.10.1/24"); ?></label>
      </div>

      <div class="cbi-value">
        <label for="code" class="cbi-value-title"><?php echo _("Persistent Keepalive"); ?></label>
        <input type="text" class="cbi-input-text" name="wg_pkeepalive" value="<?php echo htmlspecialchars($wg_pkeepalive[1], ENT_QUOTES); ?>" />
      </div>

      <div id="page_server">
        <div class="cbi-value">
          <label class="cbi-value-title"><?php echo _("Enable Client2"); ?></label>
          <input type="checkbox" class="cbi-input-checkbox" name="enable_client2" id="enable_client2" <?php echo ($enable_client[2] == true ? 'checked' : ''); ?> value="1" onchange="enableClient(2)"/>
        </div>
        <div style="display:<?php echo ($enable_client[2] == true ? 'block' : 'none'); ?>" id="client2">
          <div class="cbi-value">
            <label for="code" class="cbi-value-title"><?php echo _("Peer Public Key"); ?></label>
            <input type="text" class="cbi-input-text" name="wg-peer2" id="wg-peerpubkey2" value="<?php echo htmlspecialchars($wg_peerpubkey2, ENT_QUOTES); ?>" />
          </div>

          <div class="cbi-value">
            <label for="code" class="cbi-value-title"><?php echo _("Allowed IPs"); ?></label>
            <input type="text" class="cbi-input-text" name="wg_pallowedips2" value="<?php echo htmlspecialchars($wg_pallowedips[2], ENT_QUOTES); ?>" />
            <label class="cbi-value-description"><?php echo _("eg:10.0.10.1/24"); ?></label>
          </div>

          <div class="cbi-value">
            <label for="code" class="cbi-value-title"><?php echo _("Persistent Keepalive"); ?></label>
            <input type="text" class="cbi-input-text" name="wg_pkeepalive2" value="<?php echo htmlspecialchars($wg_pkeepalive[2], ENT_QUOTES); ?>" />
          </div>
        </div>

        <div class="cbi-value">
          <label class="cbi-value-title"><?php echo _("Enable Client3"); ?></label>
          <input type="checkbox" class="cbi-input-checkbox" name="enable_client3" id="enable_client3" <?php echo ($enable_client[3] == true ? 'checked' : ''); ?> value="1" onchange="enableClient(3)"/>
        </div>
        <div style="display:<?php echo ($enable_client[3] == true ? 'block' : 'none'); ?>;" id="client3">
          <div class="cbi-value">
            <label for="code" class="cbi-value-title"><?php echo _("Peer Public Key"); ?></label>
            <input type="text" class="cbi-input-text" name="wg-peer3" id="wg-peerpubkey3" value="<?php echo htmlspecialchars($wg_peerpubkey3, ENT_QUOTES); ?>" />
          </div>

          <div class="cbi-value">
            <label for="code" class="cbi-value-title"><?php echo _("Allowed IPs"); ?></label>
            <input type="text" class="cbi-input-text" name="wg_pallowedips3" value="<?php echo htmlspecialchars($wg_pallowedips[3], ENT_QUOTES); ?>" />
            <label class="cbi-value-description"><?php echo _("eg:10.0.10.1/24"); ?></label>
          </div>

          <div class="cbi-value">
            <label for="code" class="cbi-value-title"><?php echo _("Persistent Keepalive"); ?></label>
            <input type="text" class="cbi-input-text" name="wg_pkeepalive3" value="<?php echo htmlspecialchars($wg_pkeepalive[3], ENT_QUOTES); ?>" />
          </div>
        </div>
      </div>
    </div>

    <div id="page_wg" class="cbi-value">
      <label class="cbi-value-title"><?php echo _("Configuration File(.conf)"); ?></label>
      <label for="wg" class="cbi-file-lable">
          <input type="button" class="cbi-file-btn" id="wg_btn" value="<?php echo _("Choose file"); ?>">
          <span id="wg_text"><?php echo _("No file chosen"); ?></span>
          <input type="file" class="cbi-file" name="wgFile" id="wgFile" onchange="wgFileChange()">
      </label>
    </div>
  </div><!-- /.row -->
</div><!-- /.tab-pane | settings tab -->

<script type="text/javascript">
  function typeChangeWg() {
    var type = document.getElementById("type").value;

    if (type == "config") {
      $('#page_config').show();
      $('#page_wg').hide();
      $('#page_role').show();
      roleChangeWg();
    } else if (type == "wg") {
      $('#page_config').hide();
      $('#page_wg').show();
      $('#page_role').hide();
    } else {
      $('#page_config').hide();
      $('#page_wg').hide();
      $('#page_role').hide();
    }
  }

  function roleChangeWg() {
    var role = document.getElementById("role").value;

    if (role == "client") {
      $('#page_client').show();
      $('#page_server').hide();
    } else {
      $('#page_client').hide();
      $('#page_server').show();
    }
  }

  function wgFileChange() {
    $('#wg_text').html($('#wgFile')[0].files[0].name);
  }

  function enableClient(num) {
    var checkbox = document.getElementById("enable_client" + num);
    var client = document.getElementById("client" + num);

    if (checkbox.checked) {
      client.style.display = "block";
    } else {
      client.style.display = "none";
    }
  }

</script>

