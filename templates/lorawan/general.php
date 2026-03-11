<div class="tab-pane active" id="general">
  <div>
    <div class="cbi-value">
        <label class="cbi-value-title"><?php echo _("Working Mode"); ?></label>
        <select id="type" name="type" class="cbi-input-select" onchange="typeChangeLorawan()">
            <option value="0">Disabled</option>
            <option value="1">Packet Forwarder</option>
            <option value="2">Basic Station</option>
        </select>
    </div>
    
    <div id="page_eui">
        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Gateway EUI"); ?></label>
            <label><?php echo $gateway_eui;?></label>
        </div>
        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Gateway ID"); ?></label>
            <input type="text" class="cbi-input-text" name="gateway_ID" id="gateway_ID" value="" />
        </div>
    </div>

    <div id="page_packet_forwarder">
        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Server Address"); ?></label>
            <input type="text" class="cbi-input-text" name="server_address" id="server_address" value="127.0.0.1" />
        </div>

        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Server port for upstream"); ?></label>
            <input type="text" class="cbi-input-text" name="serv_port_up" id="serv_port_up" value="1700" />
        </div>

        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Server port for downstream"); ?></label>
            <input type="text" class="cbi-input-text" name="serv_port_down" id="serv_port_down" value="1700" />
        </div>

        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Keepalive interval in seconds"); ?></label>
            <input type="text" class="cbi-input-text" name="keepalive_interval" id="keepalive_interval" value="10" />
        </div>

        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Status interval in seconds"); ?></label>
            <input type="text" class="cbi-input-text" name="stat_interval" id="stat_interval" value="30" />
        </div>

        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Frequency Plan"); ?></label>
            <select id="frequency" name="frequency" class="cbi-input-select" onchange="freqPlanChange()">
                <option value="0"><?php echo _("Europe 868MHz(863~870)--EU868"); ?></option>
                <option value="1"><?php echo _("China 490MHz--CN490"); ?></option>
                <option value="2"><?php echo _("United States 915MHz(902~928)--US915"); ?></option>
                <option value="3"><?php echo _("Australia 915MHz(915~928)--AU915"); ?></option>
                <option value="4"><?php echo _("Asia 920~923MHz--AS923"); ?></option>
            </select>
        </div>
    </div>
    <div id="page_basic_station">
        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Protocol"); ?></label>
            <select id="protocol" name="protocol" class="cbi-input-select">
                <option value="lns">LNS</option>
                <option value="cups">CUPS</option>
            </select>
        </div>

        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("URI"); ?></label>
            <input type="text" class="cbi-input-text" name="uri" id="uri" value="wws://127.0.0.1:443" />
        </div>

        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("Authentication Mode"); ?></label>
            <select id="auth_mode" name="auth_mode" class="cbi-input-select" onchange="modeChange()">
                <option value="0">TLS Server Authentication</option>
                <option value="1">TLS Server & Client Authentication</option>
                <option value="2">TLS Server Authentication & Client Token</option>
            </select>
        </div>

        <div class="cbi-value">
            <label class="cbi-value-title"><?php echo _("CA File(*.trust)"); ?></label>
            <label for="lora_ca" class="cbi-file-lable">
                <input type="button" class="cbi-file-btn" id="ca_btn" value="<?php echo _("Choose file"); ?>">
                <span id="ca_text"><?php echo _("No file chosen"); ?></span>
                <input type="file" class="cbi-file" name="lora_ca" id="lora_ca" onchange="caFileChangeLora()">
            </label>
        </div>

        <div name="page_one" id="page_one">
            <div class="cbi-value">
                <label class="cbi-value-title"><?php echo _("Client Certificate File(*.crt)"); ?></label>
                <label class="cbi-file-lable" for="lora_crt">
                    <input type="button" class="cbi-file-btn" id="crt_btn" value="<?php echo _("Choose file"); ?>">
                    <span id="crt_text"><?php echo _("No file chosen"); ?></span>
                    <input type="file" class="cbi-file" name="lora_crt" id="lora_crt"  onchange="cerFileChangeLora()">
                </label>
            </div>
        </div>
        <div name="page_two" id="page_two">
            <div class="cbi-value">
                <label class="cbi-value-title"><?php echo _("Client Key File(*.key)"); ?></label>
                <label class="cbi-file-lable" for="lora_key">
                    <input type="button" class="cbi-file-btn" id="key_btn" value="<?php echo _("Choose file"); ?>">
                    <span id="key_text"><?php echo _("No file chosen"); ?></span>
                    <input type="file" class="cbi-file" name="lora_key" id="lora_key" onchange="keyFileChangeLora()">
                </label>
            </div>
        </div>
    </div>
  </div><!-- /.row -->
</div><!-- /.tab-pane | basic tab -->
