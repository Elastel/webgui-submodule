<div class="tab-pane active" id="openvpnsettings">
  <div class="row">
    <div class="cbi-value">
      <label class="cbi-value-title"><?php echo _("Start Selection"); ?></label>
      <select id="type" name="type" class="cbi-input-select" onchange="typeChange()">
        <option value="off">OFF</option>
        <option value="config">Config Enabled</option>
        <option value="ovpn">OVPN File Enabled</option>
      </select>
    </div>

    <div class="cbi-value" id="page_role">
      <label class="cbi-value-title"><?php echo _("Role"); ?></label>
      <select id="role" name="role" class="cbi-input-select" onchange="roleChange()">
        <option value="client">Client</option>
        <option value="server">Server</option>
      </select>
    </div>
    
    <div id="page_config" name="page_config">
      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Protocol"); ?></label>
        <select id="proto" name="proto" class="cbi-input-select">
          <option value="udp">UDP</option>
          <option value="tcp">TCP</option>
        </select>
      </div>

      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Port"); ?></label>
        <input type="text" class="cbi-input-text" name="port" id="port" value="1194" />
      </div>

      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Device Type"); ?></label>
        <select id="dev" name="dev" class="cbi-input-select">
          <option value="tun">TUN</option>
          <option value="tap">TAP</option>
        </select>
      </div>

      <div id="page_client" name="page_client">
        <div class="cbi-value">
          <label class="cbi-value-title"><?php echo _("OpenVPN Server"); ?></label>
          <input type="text" class="cbi-input-text" name="vpn_server" id="vpn_server" value=""/>
        </div>
      </div>

      <div id="page_server" name="page_server">
        <div class="cbi-value">
          <label class="cbi-value-title"><?php echo _("Server Tunnel Subnet"); ?></label>
          <input type="text" class="cbi-input-text" name="tunnel_subnet" id="tunnel_subnet" value=""/>
        </div>

        <div class="cbi-value">
          <label class="cbi-value-title"><?php echo _("Server Tunnel Mask"); ?></label>
          <input type="text" class="cbi-input-text" name="tunnel_mask" id="tunnel_mask" value=""/>
        </div>

        <div class="cbi-value">
          <label class="cbi-value-title"><?php echo _("Keepalive"); ?></label>
          <input type="text" class="cbi-input-text" name="keepalive" id="keepalive" value="10 120"/>
        </div>
      </div>

      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Cipher Algorithm"); ?></label>
        <select id="cipher" name="cipher" class="cbi-input-select">
          <option value="none">None</option>
          <?php 
            foreach ($cipher as $info) {
              echo ("<option value=$info>$info</option>");
            }
          ?>
        </select>
      </div>

      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Authentication algorithm"); ?></label>
        <select id="auth" name="auth" class="cbi-input-select">
          <option value="none">None</option>
          <?php 
            foreach ($auth as $info) {
              echo ("<option value=$info>$info</option>");
            }
          ?>
          <option value="ignore">Ignore</option>
        </select>
      </div>

      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("LZO Compression"); ?></label>
        <input type="checkbox" class="cbi-input-checkbox" name="comp_lzo" id="comp_lzo" value="1"/>
      </div>

      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("TA Key(.key)"); ?></label>
        <label class="cbi-file-lable" for="ta">
            <input type="button" class="cbi-file-btn" id="ta_btn" value="<?php echo _("Choose file"); ?>">
            <span id="ta_text"><?php echo _("No file chosen"); ?></span>
            <input type="file" class="cbi-file" name="ta" id="ta" onchange="taFileChangeVpn()">
        </label>
      </div>

      <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("CA(.crt)"); ?></label>
        <label for="ca" class="cbi-file-lable">
            <input type="button" class="cbi-file-btn" id="ca_btn" value="<?php echo _("Choose file"); ?>">
            <span id="ca_text"><?php echo _("No file chosen"); ?></span>
            <input type="file" class="cbi-file" name="ca" id="ca" onchange="caFileChangeVpn()">
        </label>
      </div>

      <div name="page_cert" id="page_cert">
        <div class="cbi-value">
          <label class="cbi-value-title"><?php echo _("Public Certificate(.crt)"); ?></label>
          <label class="cbi-file-lable" for="cert">
              <input type="button" class="cbi-file-btn" id="cert_btn" value="<?php echo _("Choose file"); ?>">
              <span id="cert_text"><?php echo _("No file chosen"); ?></span>
              <input type="file" class="cbi-file" name="cert" id="cert"  onchange="certFileChangeVpn()">
          </label>
        </div>
        <div class="cbi-value">
          <label class="cbi-value-title"><?php echo _("Private Key(.key)"); ?></label>
          <label class="cbi-file-lable" for="key">
              <input type="button" class="cbi-file-btn" id="key_btn" value="<?php echo _("Choose file"); ?>">
              <span id="key_text"><?php echo _("No file chosen"); ?></span>
              <input type="file" class="cbi-file" name="key" id="key" onchange="keyFileChangeVpn()">
          </label>
        </div>
      </div>

      <div name="page_dh" id="page_dh">
        <div class="cbi-value">
          <label class="cbi-value-title"><?php echo _("DH(.pem)"); ?></label>
          <label class="cbi-file-lable" for="dh">
              <input type="button" class="cbi-file-btn" id="dh_btn" value="<?php echo _("Choose file"); ?>">
              <span id="dh_text"><?php echo _("No file chosen"); ?></span>
              <input type="file" class="cbi-file" name="dh" id="dh" onchange="dhFileChangeVpn()">
          </label>
        </div>
      </div>
    </div>
    
    <div id="page_ovpn" name="page_ovpn" class="cbi-value">
      <label class="cbi-value-title"><?php echo _("OVPN File(.ovpn)"); ?></label>
      <label class="cbi-file-lable" for="ovpn">
          <input type="button" class="cbi-file-btn" id="ovpn_btn" value="<?php echo _("Choose file"); ?>">
          <span id="ovpn_text"><?php echo _("No file chosen"); ?></span>
          <input type="file" class="cbi-file" name="ovpn" id="ovpn" onchange="ovpnFileChangeVpn()">
      </label>
    </div>

    <div class="cbi-value" id="page_user_pwd" name="page_user_pwd">
      <label class="cbi-value-title"><?php echo _("Username&Password"); ?></label>
      <textarea name="text_user_pwd" id="text_user_pwd" style="vertical-align: middle; width: 100%; max-width: 15rem; height: 5em; min-height: 3em; resize: vertical;"></textarea>
      <label class="cbi-value-description"><?php echo _("eg: username passwd"); ?></label>
    </div>
  </div>
</div>

<script type="text/javascript">
  function typeChange() {
    var type = document.getElementById("type").value;

    if (type == "config") {
      $('#page_config').show();
      $('#page_ovpn').hide();
      $('#page_role').show();
      $('#page_user_pwd').show();
      roleChange();
    } else if (type == "ovpn") {
      $('#page_config').hide();
      $('#page_ovpn').show();
      $('#page_role').show();
      $('#page_user_pwd').show();
    } else {
      $('#page_config').hide();
      $('#page_ovpn').hide();
      $('#page_role').hide();
      $('#page_user_pwd').hide();
    }
  }

  function roleChange() {
    var role = document.getElementById("role").value;

    if (role == "client") {
      $('#page_client').show();
      $('#page_server').hide();
    } else {
      $('#page_client').hide();
      $('#page_server').show();
    }

    if (role == 'server') {
      $('#page_dh').show();
    } else {
      $('#page_dh').hide();
    }
  }

  function caFileChangeVpn() {
    $('#ca_text').html($('#ca')[0].files[0].name);
  }

  function taFileChangeVpn() {
    $('#ta_text').html($('#ta')[0].files[0].name);
  }

  function certFileChangeVpn() {
    $('#cert_text').html($('#cert')[0].files[0].name);
  }

  function keyFileChangeVpn() {
    $('#key_text').html($('#key')[0].files[0].name);
  }

  function ovpnFileChangeVpn() {
    $('#ovpn_text').html($('#ovpn')[0].files[0].name);
  }

  function dhFileChangeVpn() {
    $('#dh_text').html($('#dh')[0].files[0].name);
  }

</script>
