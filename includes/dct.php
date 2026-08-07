<?php

abstract class ComProtoEnum {
  const COM_PROTO_MODBUS = 0;
  const COM_PROTO_TRANSPARENT = 1;
  const COM_PROTO_FX = 2;
  const COM_PROTO_MC = 3;
  const COM_PROTO_ASCII = 4;
  const COM_PROTO_DNP3 = 5;
  const COM_PROTO_BACNET = 6;
  const COM_PROTO_MODBUS2IO = 7;
  const COM_PROTO_MODBUS_ASCII = 8;
  const COM_PROTO_MBUS = 9;
  const COM_PROTO_IEC1107 = 10;
  const COM_PROTO_DLMS = 11;
};

abstract class TcpProtoEnum {
  const TCP_PROTO_MODBUS = 0;
  const TCP_PROTO_TRANSPARENT = 1;
  const TCP_PROTO_S7 = 2;
  const TCP_PROTO_FX = 3;
  const TCP_PROTO_MC = 4;
  const TCP_PROTO_ASCII = 5;
  const TCP_PROTO_IEC104 = 6;
  const TCP_PROTO_OPCUA = 7;
  const TCP_PROTO_DNP3 = 8;
  const TCP_PROTO_BACNET = 9;
  const TCP_PROTO_EIP = 10;
  const TCP_PROTO_SNMP = 11;
  const TCP_PROTO_DLMS = 12;
  const TCP_PROTO_IEC61850 = 13;
};

function get_io_maps()
{
  $model = getModel();
  $channel_map = [];
  $adc_index_count = 0;
  $di_index_count = 0;
  $do_index_count = 0;
  $com_count = 4;

  switch ($model) {
      case "EG500":
          $adc_index_count += 3;
          $di_index_count += 6;
          $do_index_count += 6;
          $com_count = 2;
          break;
      case "EG410":
          $di_index_count += 2;
          $do_index_count += 2;
          $com_count = 2;
          break;
      case "EG510":
          $di_index_count += 6;
          $do_index_count += 6;
          $com_count = 2;
          break;
  }

  for ($i = 1; $i <= $com_count; $i++) {
      exec("sudo uci get dct.com.proto$i", $tmp);
      if ($tmp[0] == '7') {
          unset($tmp);
          exec("sudo uci get dct.com.controller_model$i", $tmp);
          switch($tmp[0]) {
              case '0':
                  $channel_map[$i] .= 'DI' . $di_index_count++ . ';DI' . $di_index_count++;
                  $channel_map[$i] .= ';DO' . $do_index_count++ . ';DO' . $do_index_count++;
                  break;
              case '1':
                  $channel_map[$i] .= 'DI' . $di_index_count++ . ';DI' . $di_index_count++ . 
                                      ';DI' . $di_index_count++ . ';DI' . $di_index_count++;
                  $channel_map[$i] .= ';DO' . $do_index_count++ . ';DO' . $do_index_count++ . 
                                      ';DO' . $do_index_count++. ';DO' . $do_index_count++;
                  break;
              case '2':
                  $channel_map[$i] .= 'DI' . $di_index_count++ . ';DI' . $di_index_count++ . 
                                      ';DI' . $di_index_count++ . ';DI' . $di_index_count++ . 
                                      ';DI' . $di_index_count++ . ';DI' . $di_index_count++ . 
                                      ';DI' . $di_index_count++ . ';DI' . $di_index_count++;
                  $channel_map[$i] .= ';DO' . $do_index_count++ . ';DO' . $do_index_count++ . 
                                      ';DO' . $do_index_count++. ';DO' . $do_index_count++ . 
                                      ';DO' . $do_index_count++. ';DO' . $do_index_count++ . 
                                      ';DO' . $do_index_count++. ';DO' . $do_index_count++;
                  break;
              case '3':
                  $channel_map[$i] .= 'ADC' . $adc_index_count++ . ';ADC' . $adc_index_count++ .
                                      ';ADC' . $adc_index_count++ . ';ADC' . $adc_index_count++ .
                                      ';ADC' . $adc_index_count++ . ';ADC' . $adc_index_count++ .
                                      ';ADC' . $adc_index_count++ . ';ADC' . $adc_index_count++;
                  break;
          }
      }
      unset($tmp);
  }

  echo $channel_map[0];
  return $channel_map;
}

function get_belonged_interface($com_proto, $tcp_proto)
{
  $found = false;
  $option_list = array();
  $i = 0;
  exec("sudo uci get dct.com.enabled1", $com1_enable);
  exec("sudo uci get dct.com.enabled2", $com2_enable);
  exec("sudo uci get dct.com.enabled3", $com3_enable);
  exec("sudo uci get dct.com.enabled4", $com4_enable);
  
  exec("sudo uci get dct.com.proto1", $com1_proto);
  exec("sudo uci get dct.com.proto2", $com2_proto);
  exec("sudo uci get dct.com.proto3", $com3_proto);
  exec("sudo uci get dct.com.proto4", $com4_proto);

  exec("sudo uci get dct.tcp_server.enabled1", $tcp1_enable);
  exec("sudo uci get dct.tcp_server.enabled2", $tcp2_enable);
  exec("sudo uci get dct.tcp_server.enabled3", $tcp3_enable);
  exec("sudo uci get dct.tcp_server.enabled4", $tcp4_enable);
  exec("sudo uci get dct.tcp_server.enabled5", $tcp5_enable);
  
  exec("sudo uci get dct.tcp_server.proto1", $tcp1_proto);
  exec("sudo uci get dct.tcp_server.proto2", $tcp2_proto);
  exec("sudo uci get dct.tcp_server.proto3", $tcp3_proto);
  exec("sudo uci get dct.tcp_server.proto4", $tcp4_proto);
  exec("sudo uci get dct.tcp_server.proto5", $tcp5_proto);

  if ($com1_enable[0] == "1" && $com_proto != -1 &&
      ($com1_proto[0] == $com_proto || $com1_proto[0] + $com_proto == ComProtoEnum::COM_PROTO_MODBUS_ASCII )) {
    $option_list["COM1"] = "COM1";
    $found = true;
  }
  if ($com2_enable[0] == "1" && $com_proto != -1 && 
      ($com2_proto[0] == $com_proto || $com2_proto[0] + $com_proto == ComProtoEnum::COM_PROTO_MODBUS_ASCII )) {
    $option_list["COM2"] = "COM2";
    $found = true;
  }
  if ($com3_enable[0] == "1" && $com_proto != -1 && 
      ($com3_proto[0] == $com_proto || $com3_proto[0] + $com_proto == ComProtoEnum::COM_PROTO_MODBUS_ASCII )) {
    $option_list["COM3"] = "COM3";
    $found = true;
  }
  if ($com4_enable[0] == "1" && $com_proto != -1 && 
      ($com4_proto[0] == $com_proto || $com4_proto[0] + $com_proto == ComProtoEnum::COM_PROTO_MODBUS_ASCII )) {
    $option_list["COM4"] = "COM4";
    $found = true;
  }  
  if ($tcp1_enable[0] == "1" && $tcp1_proto[0] == $tcp_proto) {
    $option_list["TCP1"] = _("Network Node")."1";
    $found = true;
  }
  if ($tcp2_enable[0] == "1" && $tcp2_proto[0] == $tcp_proto) {
    $option_list["TCP2"] = _("Network Node")."2";
    $found = true;
  }
  if ($tcp3_enable[0] == "1" && $tcp3_proto[0] == $tcp_proto) {
    $option_list["TCP3"] = _("Network Node")."3";
    $found = true;
  }
  if ($tcp4_enable[0] == "1" && $tcp4_proto[0] == $tcp_proto) {
    $option_list["TCP4"] = _("Network Node")."4";
    $found = true;
  }
  if ($tcp5_enable[0] == "1" && $tcp5_proto[0] == $tcp_proto) {
    $option_list["TCP5"] = _("Network Node")."5";
    $found = true;
  }

  if ($found == false) {
      $option_list["No Interface Is Enabled"] = _("No Interface Is Enabled");
  }
  
  return $option_list;
}

function page_interface_com($num)
{
  if ($num == 1)
    $active = "active";
  else
    $active = "fade";

  echo '<div class="tab-pane '.$active.'" id="com'.$num.'">
        <div class="row">';
  RadioControlCustom(_('Serial Port'), 'com_enabled', 'com', 'enableCom', $num);
  echo '<div id="page_com'.$num.'" name="page_com'.$num.'">';

  $baudrate_list = array('300'=>'300', '600'=>'600', '1200'=>'1200', '2400'=>'2400', '4800'=>'4800', '9600'=>'9600', '19200'=>'19200', '38400'=>'38400',
                   '57600'=>'57600', '115200'=>'115200', '230400'=>'230400');
  SelectControlCustom(_('Baudrate'), 'baudrate'.$num, $baudrate_list, $baudrate_list['9600'], 'baudrate'.$num);

  $databit_list = array('7'=>'7', '8'=>'8');
  SelectControlCustom(_('Databit'), 'databit'.$num, $databit_list, $databit_list['8'], 'databit'.$num);

  $stopbit_list = array('1'=>'1', '2'=>'2');
  SelectControlCustom(_('Stopbit'), 'stopbit'.$num, $stopbit_list, $stopbit_list['1'], 'stopbit'.$num);

  $parity_list = array('N'=>'None', 'O'=>'Odd', 'E'=>'Even');
  SelectControlCustom(_('Parity'), 'parity'.$num, $parity_list, $parity_list['N'], 'parity'.$num);

  InputControlCustom(_("Frame Interval"), 'com_frame_interval'.$num, 'com_frame_interval'.$num, _('ms'), 200);

  $com_proto = array('Modbus RTU', 'Transparent', 'FX', 'MC', 
                  'ASCII', 'DNP3', 'BACnet/MSTP', 'Modbus2io', 
                  'Modbus ASCII', 'Mbus', 'IEC62056-21',
                  'DLMS');
  SelectControlCustom(_('Protocol'), 'com_proto'.$num, $com_proto, $com_proto[0], 'com_proto'.$num, null, "comProtocolChange($num)");

  echo '<div id="com_page_protocol_modbus'.$num.'" name="com_page_protocol_modbus'.$num.'">';
  InputControlCustom(_("Command Interval"), 'com_cmd_interval'.$num, 'com_cmd_interval'.$num, _('ms'), 10);
  echo '</div>';

  echo '<div id="com_page_protocol_transparent'.$num.'" name="com_page_protocol_transparent'.$num.'">';
  InputControlCustom(_("Reporting Center"), 'com_report_center'.$num, 'com_report_center'.$num, _('1-2-3-4-5'));
  echo '</div>';

  echo '<div id="com_page_protocol_dnp3'.$num.'" name="com_page_protocol_dnp3'.$num.'">';
  InputControlCustom(_('Slave Address'), 'com_slave_address'.$num, 'com_slave_address'.$num, "0~65519");
  InputControlCustom(_('Master Address'), 'com_master_address'.$num, 'com_master_address'.$num, "0~65519");
  echo '</div>';

  echo '<div id="com_page_protocol_bacnet'.$num.'" name="com_page_protocol_bacnet'.$num.'">';
  InputControlCustom(_('Source Address'), 'com_src_addr'.$num, 'com_src_addr'.$num);
  InputControlCustom(_('Max Master'), 'com_max_master'.$num, 'com_max_master'.$num, "1~127");
  InputControlCustom(_('Frames'), 'com_frames'.$num, 'com_frames'.$num, "1~127");
  $collect_mode = array('poll'=>'poll', 'cov'=>'cov');
  SelectControlCustom(_('Collect Mode'), 'com_collect_mode'.$num, $collect_mode, $collect_mode['poll'], 'com_collect_mode'.$num);
  echo '</div>';

  echo '<div id="com_page_controller_model'.$num.'" name="com_page_controller_model'.$num.'">';
  $com_controller_model = array('EIO-2DIO', 'EIO-4DIO', 'EIO-8DIO', 'EIO-8AI');
  SelectControlCustom(_('Controller Model'), 'com_controller_model'.$num, $com_controller_model, $com_controller_model[0], 'com_controller_model'.$num);
  $channel_map = get_io_maps();
  LabelControlCustom(_("Channel Map"), 'channel_map'.$num, 'channel_map'.$num, $channel_map[$num] != null ? $channel_map[$num] : '-');
  echo '</div>';

  echo '<div id="com_page_protocol_dlms'.$num.'" name="com_page_protocol_dlms'.$num.'">';
    InputControlCustom(_('Client Address'), 'com_dlms_client_address'.$num, 'com_dlms_client_address'.$num, "1~255");
    InputControlCustom(_('Server Address'), 'com_dlms_server_address'.$num, 'com_dlms_server_address'.$num, "1~255");
    $auth_list = [_('None'), 'Low', 'High', 'HighMd5', 'HighSha1', 'HighGmac', 'HighSha256'];
    SelectControlCustom(_('Authentication'), 'com_dlms_auth'.$num, $auth_list, $auth_list[0], 'com_dlms_auth'.$num, null, "dlmsAuthChangeCom($num)");
    echo '<div id="com_page_dlms_password'.$num.'" name="com_page_dlms_password'.$num.'">';
      InputControlCustom(_('Password'), 'com_dlms_password'.$num, 'com_dlms_password'.$num);
    echo '</div>';
    echo '<div id="com_page_security_dlms'.$num.'" name="com_page_security_dlms'.$num.'">';
      $security_level = [_('None'), 'Authentication', 'Encryption', 'AuthenticationEncryption'];
      SelectControlCustom(_('Security Level'), 'com_dlms_security_level'.$num, $security_level, $security_level[0], 'com_dlms_security_level'.$num, null, "dlmsSecurityChangeCom($num)");
      echo '<div id="com_page_authentication_dlms'.$num.'" name="com_page_authentication_dlms'.$num.'">';
        InputControlCustom(_('Authentication Key'), 'com_dlms_authentication_key'.$num, 'com_dlms_authentication_key'.$num);
      echo '</div>';
      echo '<div id="com_page_encrypted_dlms'.$num.'" name="com_page_encrypted_dlms'.$num.'">';
        InputControlCustom(_('Block Cipher Key'), 'com_dlms_cipher_Key'.$num, 'com_dlms_cipher_Key'.$num);
      echo '</div>';
      InputControlCustom(_('Client System Title'), 'com_dlms_client_title'.$num, 'com_dlms_client_title'.$num);
      InputControlCustom(_('Invocation counter'), 'com_dlms_invocation_counter'.$num, 'com_dlms_invocation_counter'.$num, "eg: 0.0.43.1.0.255");
    echo '</div>';
  echo '</div>';

echo '</div><!-- /.page_com -->
    </div><!-- /.row -->
    </div><!-- /.tab-pane | basic tab -->';
}

function page_interface_tcp($num)
{
  if ($num == 1)
    $active = "active";
  else
    $active = "fade";

  echo '<div class="tab-pane '.$active.'" id="tcp'.$num.'">
        <div class="row">';
  RadioControlCustom(_('Network Node'), 'tcp_enabled', 'tcp', 'enableTcp', $num);
  echo '<div id="page_tcp'.$num.'" name="page_tcp'.$num.'">';

  InputControlCustom(_("Server Address"), 'server_addr'.$num, 'server_addr'.$num);

  InputControlCustom(_("Server Port"), 'server_port'.$num, 'server_port'.$num);

  InputControlCustom(_("Frame Interval"), 'tcp_frame_interval'.$num, 'tcp_frame_interval'.$num, _('ms'), 200);

  $tcp_proto = array('Modbus TCP', 'Transparent', 'S7', 'FX', 
                      'MC', 'ASCII', 'IEC104', 'OPCUA', 'DNP3', 
                      'BACnet/IP', 'Ethernet/IP', 'SNMP',
                      'DLMS', 'IEC61850');
  SelectControlCustom(_('Protocol'), 'tcp_proto'.$num, $tcp_proto, $tcp_proto[0], 'tcp_proto'.$num, null, "tcpProtocolChange($num)");

  echo '<div id="tcp_page_protocol_modbus'.$num.'" name="tcp_page_protocol_modbus'.$num.'">';
  InputControlCustom(_("Command Interval"), 'tcp_cmd_interval'.$num, 'tcp_cmd_interval'.$num, _('ms'), 10);
  echo '</div>';

  echo '<div id="tcp_page_protocol_transparent'.$num.'" name="tcp_page_protocol_transparent'.$num.'">';
  InputControlCustom(_("Reporting Center"), 'tcp_report_center'.$num, 'tcp_report_center'.$num, _('1-2-3-4-5'));
  echo '</div>';
  
  echo '<div id="tcp_page_protocol_plc'.$num.'" name="tcp_page_protocol_plc'.$num.'">';
  echo '<div id="tcp_page_protocol_s7'.$num.'" name="tcp_page_protocol_s7'.$num.'">';
  InputControlCustom(_("Rack"), 'rack'.$num, 'rack'.$num);
  echo '</div>';
  InputControlCustom(_("Slot"), 'slot'.$num, 'slot'.$num);
  echo '</div>';

  echo '<div id="tcp_page_protocol_opcua'.$num.'" name="tcp_page_protocol_opcua'.$num.'">';
  CheckboxControlCustom(_('Anonymous'), 'anonymous'.$num, 'anonymous'.$num, 'checked', null, "anonymousCheckTcp($num)");
  echo '<div id="page_anonymous'.$num.'" name="page_anonymous'.$num.'">';
  InputControlCustom(_('Username'), 'username'.$num, 'username'.$num);
  InputControlCustom(_('Password'), 'password'.$num, 'password'.$num);
  echo '</div>';
  
  $policy_list = [_('None'), 'basic128', 'basic256', 'basic256sha256'];
  SelectControlCustom(_('Security Policy'), 'security_policy'.$num, $policy_list, $policy_list[0], 'security_policy'.$num, null, "securityChangeTcp($num)");

  echo '<div id="page_security'.$num.'" name="page_security'.$num.'">';
  InputControlCustom(_('URI'), 'uri'.$num, 'uri'.$num, _('If left blank, it will be automatically filled in'));

  UploadFileControlCustom(_('Certificate'), 'cert_btn'.$num, 'cert_text'.$num, 'certificate'.$num, 'certificate'.$num, "certChangeTcp($num)");

  UploadFileControlCustom(_('Private Key'), 'key_btn'.$num, 'key_text'.$num, 'private_key'.$num, 'private_key'.$num, "keyChangeTcp($num)");

  UploadFileMultipleControlCustom(_('Trust Server Certificate'), 'trust_btn'.$num, 'trust_text'.$num, 'trust_crt'.$num.'[]', 'trust_crt'.$num, "trustChangeTcp($num)");
  echo '</div>';
  echo '</div>';

  echo '<div id="tcp_page_protocol_dnp3'.$num.'" name="tcp_page_protocol_dnp3'.$num.'">';
  InputControlCustom(_('Slave Address'), 'tcp_slave_address'.$num, 'tcp_slave_address'.$num, "0~65519");
  InputControlCustom(_('Master Address'), 'tcp_master_address'.$num, 'tcp_master_address'.$num, "0~65519");
  echo '</div>';

  echo '<div id="tcp_page_protocol_bacnet'.$num.'" name="tcp_page_protocol_bacnet'.$num.'">';
  exec("ip -o link show | awk -F': ' '{print $2}'", $interface_tmp);
  sort($interface_tmp);
  $interface_list = array();
  foreach ($interface_tmp as $value) {
      if ($value == 'eth1' || $value == 'docker0' ||  $value == 'lo' ||
          strstr($value, 'veth') != NULL || strstr($value, '@NONE') != NULL || 
          strstr($value, 'br-') != NULL)
          continue;

      $interface_list["$value"] = $value;
  }
  SelectControlCustom(_('Interface'), 'tcp_interface'.$num, $interface_list, $interface_list['eth0'], 'tcp_interface'.$num);
  $collect_mode = array('poll'=>'poll', 'cov'=>'cov');
  SelectControlCustom(_('Collect Mode'), 'tcp_collect_mode'.$num, $collect_mode, $collect_mode['poll'], 'tcp_collect_mode'.$num);
  echo '</div>';

  echo '<div id="tcp_page_protocol_snmp'.$num.'" name="tcp_page_protocol_snmp'.$num.'">';
  $snmp_version = [_('SNMPv2'), _('SNMPv3')];
  SelectControlCustom(_('SNMP Version'), 'snmp_version'.$num, $snmp_version, $snmp_version[0], 'snmp_version'.$num, null, "snmpVersionChangeTcp($num)");
  echo '<div id="tcp_page_snmpv2'.$num.'" name="tcp_page_snmpv2'.$num.'">';
  $community_string = ['public'=>'public', 'private'=>'private'];
  SelectControlCustom(_('Community String'), 'community_string'.$num, $community_string, $community_string[0], 'community_string'.$num);
  echo '</div>';
  echo '<div id="tcp_page_snmpv3'.$num.'" name="tcp_page_snmpv3'.$num.'">';
  InputControlCustom(_('Username'), 'snmp_username'.$num, 'snmp_username'.$num);
  $security_level = [_('noAuthNoPriv'), _('authNoPriv'), _('authPriv')];
  SelectControlCustom(_('Security Level'), 'security_level'.$num, $security_level, $security_level[0], 'security_level'.$num, null, "securityLevelChangeTcp($num)");
  echo '<div id="page_snmpv3_auth'.$num.'" name="page_snmpv3_auth'.$num.'">';
  $auth_protocol = ['MD5', 'SHA', 'SHA-224', 'SHA-256', 'SHA-384', 'SHA-512'];
  SelectControlCustom(_('Auth Protocol'), 'auth_protocol'.$num, $auth_protocol, $auth_protocol[0], 'auth_protocol'.$num);
  InputControlCustom(_('Auth Key'), 'auth_key'.$num, 'auth_key'.$num);
  echo '</div>';
  echo '<div id="page_snmpv3_privacy'.$num.'" name="page_snmpv3_privacy'.$num.'">';
  $priv_protocol = ['DES', 'AES'];
  SelectControlCustom(_('Priv Protocol'), 'priv_protocol'.$num, $priv_protocol, $priv_protocol[0], 'priv_protocol'.$num);
  InputControlCustom(_('Priv Key'), 'priv_key'.$num, 'priv_key'.$num);
  echo '</div>';
  echo '</div>';
  echo '</div>';

  echo '<div id="tcp_page_protocol_dlms'.$num.'" name="tcp_page_protocol_dlms'.$num.'">';
    InputControlCustom(_('Client Address'), 'tcp_dlms_client_address'.$num, 'tcp_dlms_client_address'.$num, "1~255");
    InputControlCustom(_('Server Address'), 'tcp_dlms_server_address'.$num, 'tcp_dlms_server_address'.$num, "1~255");
    $auth_list = [_('None'), 'Low', 'High', 'HighMd5', 'HighSha1', 'HighGmac', 'HighSha256'];
    SelectControlCustom(_('Authentication'), 'tcp_dlms_auth'.$num, $auth_list, $auth_list[0], 'tcp_dlms_auth'.$num, null, "dlmsAuthChangeTcp($num)");
    echo '<div id="tcp_page_dlms_password'.$num.'" name="tcp_page_dlms_password'.$num.'">';
      InputControlCustom(_('Password'), 'tcp_dlms_password'.$num, 'tcp_dlms_password'.$num);
    echo '</div>';
    echo '<div id="tcp_page_security_dlms'.$num.'" name="tcp_page_security_dlms'.$num.'">';
      $security_level = [_('None'), 'Authentication', 'Encryption', 'AuthenticationEncryption'];
      SelectControlCustom(_('Security Level'), 'tcp_dlms_security_level'.$num, $security_level, $security_level[0], 'tcp_dlms_security_level'.$num, null, "dlmsSecurityChangeTcp($num)");
      echo '<div id="tcp_page_authentication_dlms'.$num.'" name="tcp_page_authentication_dlms'.$num.'">';
        InputControlCustom(_('Authentication Key'), 'tcp_dlms_authentication_key'.$num, 'tcp_dlms_authentication_key'.$num);
      echo '</div>';
      echo '<div id="tcp_page_encrypted_dlms'.$num.'" name="tcp_page_encrypted_dlms'.$num.'">';
        InputControlCustom(_('Block Cipher Key'), 'tcp_dlms_cipher_Key'.$num, 'tcp_dlms_cipher_Key'.$num);
      echo '</div>';
      InputControlCustom(_('Client System Title'), 'tcp_dlms_client_title'.$num, 'tcp_dlms_client_title'.$num);
      InputControlCustom(_('Invocation counter'), 'tcp_dlms_invocation_counter'.$num, 'tcp_dlms_invocation_counter'.$num, "eg: 0.0.43.1.0.255");
    echo '</div>';
  echo '</div>';

  echo '<div id="tcp_page_protocol_iec61850'.$num.'" name="tcp_page_protocol_iec61850'.$num.'">';
    $iec61850_auth_list = [_('None'), 'Password'/*, 'TLS'*/];
    SelectControlCustom(_('Authentication'), 'tcp_iec61850_auth'.$num, $iec61850_auth_list, $iec61850_auth_list[0], 'tcp_iec61850_auth'.$num, null, "iec61850AuthChangeTcp($num)");
    echo '<div id="tcp_page_password_iec61850'.$num.'" name="tcp_page_password_iec61850'.$num.'">';
        InputControlCustom(_('Password'), 'tcp_iec61850_password'.$num, 'tcp_iec61850_password'.$num);
    echo '</div>';
    echo '<div id="tcp_page_tls_iec61850'.$num.'" name="tcp_page_tls_iec61850'.$num.'">';
      UploadFileControlCustom(_('Client Key'), 'iec61850_key_btn'.$num, 'iec61850_key_text'.$num, 'iec61850_key'.$num, 'iec61850_key'.$num, "iec61850KeyChangeTcp($num)");
      UploadFileControlCustom(_('Client Certificate'), 'iec61850_cert_btn'.$num, 'iec61850_cert_text'.$num, 'iec61850_cert'.$num, 'iec61850_cert'.$num, "iec61850CertChangeTcp($num)");
      UploadFileControlCustom(_('Root Certificate'), 'iec61850_root_cert_btn'.$num, 'iec61850_root_cert_text'.$num, 'iec61850_root_cert'.$num, 'iec61850_root_cert'.$num, "iec61850RootCertChangeTcp($num)");
    echo '</div>';
  echo '</div>';

  $count = $num - 1;
  exec("uci -P /var/state get dct.connection.tcp_status$count", $tmp);
  $status = $tmp[0] ?? '-';
  LabelControlCustom(_("Connection Status"), 'connect_status'.$num, 'connect_status'.$num, $status);

  echo '    </div><!-- /.page_tcp -->
          </div><!-- /.row -->
        </div><!-- /.tab-pane | basic tab -->';
}

function page_server($num)
{
  if ($num == 1)
    $active = "active";
  else
    $active = "fade";

echo "<div class=\"tab-pane $active\" id=\"server$num\">
        <div class=\"row\">
          <div class=\"cbi-value\">
            <label class=\"cbi-value-title\">"; echo _("Enabled"); echo "</label>
            <input class=\"cbi-input-radio\" id=\"enable$num\" name=\"enabled$num\" value=\"1\" type=\"radio\" checked onchange=\"enableServer(true, $num)\">
            <label >"; echo _("Enable"); echo "</label>

            <input class=\"cbi-input-radio\" id=\"disable$num\" name=\"enabled$num\" value=\"0\" type=\"radio\" onchange=\"enableServer(false, $num)\">
            <label >"; echo _("Disable"); echo "</label>
          </div>

          <div class=\"cbi-value\" id=\"page_server$num\" name=\"page_server$num\">
            <div class=\"cbi-value\">
              <label class=\"cbi-value-title\">"; echo _("Protocol"); echo "</label>
              <select id=\"proto$num\" name=\"proto$num\" class=\"cbi-input-select\" onchange=\"protocolChange($num)\">
                <option value=\"0\" selected=\"\">TCP</option>
                <option value=\"1\">UDP</option>
                <option value=\"2\">MQTT</option>
                <option value=\"3\">SparkPlugB</option>
                <option value=\"4\">HTTP</option>
              </select>
            </div>

            <div name=\"page_encap$num\" id=\"page_encap$num\">
              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Payload Format"); echo "</label>
                <select id=\"encap_type$num\" name=\"encap_type$num\" class=\"cbi-input-select\" onchange=\"encapChange($num)\">
                  <option value=\"0\">Transparent</option>
                  <option value=\"1\" selected=\"\">JSON</option>
                </select>
              </div>
            </div>

            <div name=\"page_json$num\" id=\"page_json$num\">
              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("JSON Format"); echo "</label>
                <select id=\"json_format$num\" name=\"json_format$num\" class=\"cbi-input-select\" onchange=\"jsonChange($num)\">
                  <option value=\"0\">base-format</option>
                  <option value=\"1\" selected=\"\">elastpro-format</option>
                  <option value=\"2\">array-format</option>
                </select>
                <i class=\"fas fa-question-circle\"
                  style=\"color:#17a2b8;cursor:pointer;margin-left:0.5rem;\"
                  title=''>
                </i>
              </div>
            </div>

            <div name=\"page_addr$num\" id=\"page_addr$num\">
              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Server Address"); echo "</label>
                <input type=\"text\" class=\"cbi-input-text\" name=\"server_addr$num\" id=\"server_addr$num\" />
              </div>
            </div>

            <div name=\"page_url$num\" id=\"page_url$num\">
              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Http URL"); echo "</label>
                <input type=\"text\" class=\"cbi-input-text\" name=\"http_url$num\" id=\"http_url$num\" />
              </div>
            </div>

            <div name=\"page_port$num\" id=\"page_port$num\">
              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Server Port"); echo "</label>
                <input type=\"text\" class=\"cbi-input-text\" name=\"server_port$num\" id=\"server_port$num\" />
              </div>
            </div>";

            exec("sudo uci get dct.basic.enabled", $has_cached); 
            exec("sudo uci get dct.basic.cache_enabled", $cache_enabled);

            if ($has_cached[0] == '1' && $cache_enabled[0] == '1') {
              echo "<div class=\"cbi-value\" name=\"page_cache$num\" id=\"page_cache$num\">
                      <label class=\"cbi-value-title\">"; echo _("Enable Cache"); echo "</label>
                      <input type=\"checkbox\" class=\"cbi-input-checkbox\" name=\"cache_enabled$num\" id=\"cache_enabled$num\" value=\"1\"/>
                      <label class=\"cbi-value-description\">"; echo _("Cache When Fails To Send"); echo "</label>
                    </div>";
            }
                
    echo   "<div name=\"page_tcp$num\" id=\"page_tcp$num\">
              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Register Packet"); echo "</label>
                <input type=\"text\" class=\"cbi-input-text\" name=\"register_packet$num\" id=\"register_packet$num\" />
                <label class=\"cbi-value-description\">"; echo _("Max 128 Bytes ASCII"); echo "</label>
              </div>

              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Use HEX Format"); echo "</label>
                <input type=\"checkbox\" class=\"cbi-input-checkbox\" name=\"register_packet_hex$num\" id=\"register_packet_hex$num\" value=\"1\"/>
                <label class=\"cbi-value-description\">"; echo _("Default is ASCII"); echo "</label>
              </div>

              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Heartbeat Packet"); echo "</label>
                <input type=\"text\" class=\"cbi-input-text\" name=\"heartbeat_packet$num\" id=\"heartbeat_packet$num\" />
                <label class=\"cbi-value-description\">"; echo _("Max 128 Bytes ASCII"); echo "</label>
              </div>

              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Use HEX Format"); echo "</label>
                <input type=\"checkbox\" class=\"cbi-input-checkbox\" name=\"heartbeat_packet_hex$num\" id=\"heartbeat_packet_hex$num\" value=\"1\"/>
                <label class=\"cbi-value-description\">"; echo _("Default is ASCII"); echo "</label>
              </div>

              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Heartbeat Interval"); echo "</label>
                <input type=\"text\" class=\"cbi-input-text\" name=\"heartbeat_interval$num\" id=\"heartbeat_interval$num\" />
                <label class=\"cbi-value-description\">"; echo _("Seconds, 0 means No Heartbeat"); echo "</label>
              </div>
            </div>  

            <div name=\"page_mqtt$num\" id=\"page_mqtt$num\">
              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Heartbeat Interval"); echo "</label>
                <input type=\"text\" class=\"cbi-input-text\" name=\"mqtt_heartbeat_interval$num\" id=\"mqtt_heartbeat_interval$num\" />
                <label class=\"cbi-value-description\">"; echo _("Seconds, 0 means Default Heartbeat"); echo "</label>
              </div>

              <div name=\"page_topic$num\" id=\"page_topic$num\">
                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("MQTT Publish Topic"); echo "</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"mqtt_pub_topic$num\" id=\"mqtt_pub_topic$num\" />
                </div>

                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("MQTT Subscribe Topic"); echo "</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"mqtt_sub_topic$num\" id=\"mqtt_sub_topic$num\" />
                </div>
              </div>

              <div name=\"page_sparkplug$num\" id=\"page_sparkplug$num\">
                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Group ID"); echo "</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"sparkplug_group_id$num\" id=\"sparkplug_group_id$num\" />
                </div>

                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Node ID"); echo "</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"sparkplug_node_id$num\" id=\"sparkplug_node_id$num\" />
                </div>

                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Device ID"); echo "</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"sparkplug_device_id$num\" id=\"sparkplug_device_id$num\" />
                </div>
              </div>

              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("MQTT Username"); echo "</label>
                <input type=\"text\" class=\"cbi-input-text\" name=\"mqtt_username$num\" id=\"mqtt_username$num\" />
              </div>

              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("MQTT Password"); echo "</label>
                <input type=\"text\" class=\"cbi-input-text\" name=\"mqtt_password$num\" id=\"mqtt_password$num\" />
              </div>

              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Client ID"); echo "</label>
                <input type=\"text\" class=\"cbi-input-text\" name=\"mqtt_client_id$num\" id=\"mqtt_client_id$num\" />
              </div>

              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Enable TLS/SSL"); echo "</label>
                <input type=\"checkbox\" class=\"cbi-input-checkbox\" name=\"mqtt_tls_enabled$num\" id=\"mqtt_tls_enabled$num\" onchange=\"enableTls($num)\" value=\"1\"/>
              </div>

              <div name=\"page_mqtt_tls$num\" id=\"page_mqtt_tls$num\">
                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Certificate Type"); echo "</label>
                  <select id=\"certificate_type$num\" name=\"certificate_type$num\" class=\"cbi-input-select\" onchange=\"cerChange($num)\">
                    <option value=\"0\">CA signed server certificate</option>
                    <option value=\"1\">One-way certification</option>
                    <option value=\"2\">Two-way certification</option>
                  </select>
                </div>

                <div name=\"page_one$num\" id=\"page_one$num\">
                  <div class=\"cbi-value\">
                    <label class=\"cbi-value-title\">"; echo _("CA"); echo "</label>
                    <label for=\"mqtt_ca$num\" class=\"cbi-file-lable\">
                        <input type=\"button\" class=\"cbi-file-btn\" id=\"ca_btn$num\" value=\""; echo _("Choose file"); echo "\">
                        <span id=\"ca_text$num\">"; echo _("No file chosen"); echo "</span>
                        <input type=\"file\" class=\"cbi-file\" name=\"mqtt_ca$num\" id=\"mqtt_ca$num\" onchange=\"caFileChange($num)\">
                    </label>
                  </div>
                </div>

                <div name=\"page_two$num\" id=\"page_two$num\">
                  <div class=\"cbi-value\">
                    <label class=\"cbi-value-title\">"; echo _("Public Certificate"); echo "</label>
                    <label class=\"cbi-file-lable\" for=\"mqtt_cert$num\">
                        <input type=\"button\" class=\"cbi-file-btn\" id=\"cer_btn$num\" value=\""; echo _("Choose file"); echo "\">
                        <span id=\"cer_text$num\">"; echo _("No file chosen"); echo "</span>
                        <input type=\"file\" class=\"cbi-file\" name=\"mqtt_cert$num\" id=\"mqtt_cert$num\"  onchange=\"cerFileChange($num)\">
                    </label>
                  </div>
                  <div class=\"cbi-value\">
                    <label class=\"cbi-value-title\">"; echo _("Private Key"); echo "</label>
                    <label class=\"cbi-file-lable\" for=\"mqtt_key$num\">
                        <input type=\"button\" class=\"cbi-file-btn\" id=\"key_btn$num\" value=\""; echo _("Choose file"); echo "\">
                        <span id=\"key_text$num\">"; echo _("No file chosen"); echo "</span>
                        <input type=\"file\" class=\"cbi-file\" name=\"mqtt_key$num\" id=\"mqtt_key$num\" onchange=\"keyFileChange($num)\">
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div name=\"page_http$num\" id=\"page_http$num\">
              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Enable Self Defined Header"); echo "</label>
                <input type=\"checkbox\" class=\"cbi-input-checkbox\" name=\"self_define_header$num\" id=\"self_define_header$num\" onchange=\"enableHeader($num)\" value=\"1\"/>
              </div>
              <div name=\"page_header$num\" id=\"page_header$num\">
                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Header Name"); echo "1</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"header_name1_$num\" id=\"header_name1_$num\" />
                </div>

                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Header Value"); echo "1</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"header_value1_$num\" id=\"header_value1_$num\" />
                </div>

                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Header Name"); echo "2</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"header_name2_$num\" id=\"header_name2_$num\" />
                </div>

                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Header Value"); echo "2</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"header_value2_$num\" id=\"header_value2_$num\" />
                </div>

                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Header Name"); echo "3</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"header_name3_$num\" id=\"header_name3_$num\" />
                </div>

                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Header Value"); echo "3</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"header_value3_$num\" id=\"header_value3_$num\" />
                </div>
              </div><!-- /.page_header1 -->
            </div>

            <div name=\"page_json$num\" id=\"page_json$num\">
              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Enable Self Defined Variable"); echo "</label>
                <input type=\"checkbox\" class=\"cbi-input-checkbox\" name=\"self_define_var$num\" id=\"self_define_var$num\" onchange=\"enableVar($num)\" value=\"1\"/>
              </div>

              <div name=\"page_var$num\" id=\"page_var$num\">
                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Variable Name"); echo "1</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"var_name1_$num\" id=\"var_name1_$num\" />
                </div>

                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Variable Value"); echo "1</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"var_value1_$num\" id=\"var_value1_$num\" />
                </div>

                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Variable Name"); echo "2</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"var_name2_$num\" id=\"var_name2_$num\" />
                </div>

                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Variable Value"); echo "2</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"var_value2_$num\" id=\"var_value2_$num\" />
                </div>

                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Variable Name"); echo "3</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"var_name3_$num\" id=\"var_name3_$num\" />
                </div>

                <div class=\"cbi-value\">
                  <label class=\"cbi-value-title\">"; echo _("Variable Value"); echo "3</label>
                  <input type=\"text\" class=\"cbi-input-text\" name=\"var_value3_$num\" id=\"var_value3_$num\" />
                </div>
              </div><!-- /.page_var1 -->
            </div><!-- /.page_json1 -->

            <div name=\"page_hj212_$num\" id=\"page_hj212_$num\">
              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("MN"); echo "</label>
                <input type=\"text\" class=\"cbi-input-text\" name=\"mn$num\" id=\"mn$num\" />
              </div>

              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("ST"); echo "</label>
                <input type=\"text\" class=\"cbi-input-text\" name=\"st$num\" id=\"st$num\" />
                <label class=\"cbi-value-description\">"; echo _("2 Bytes Length"); echo "</label>
              </div>

              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("PW"); echo "</label>
                <input type=\"text\" class=\"cbi-input-text\" name=\"pw$num\" id=\"pw$num\" />
                <label class=\"cbi-value-description\">"; echo _("6 Bytes Length"); echo "</label>
              </div>

            </div><!-- /.page_hj212_1 -->
            <div name=\"page_status$num\" id=\"page_status$num\">
              <div class=\"cbi-value\">
                <label class=\"cbi-value-title\">"; echo _("Connection Status"); echo "</label>";
                $count = $num - 1;
                exec("uci -P /var/state get dct.connection.status$count", $status);
                echo "<label id=\"connect_status$num\" name=\"connect_status$num\">"; echo _(empty($status[0]) ? "-" : $status[0]); echo "</label>
              </div>
            </div>
          </div><!-- /.page_server -->
        </div><!-- /.row -->
      </div><!-- /.tab-pane | basic tab -->";
}

function page_im_ex($conf_name) {
  $conf_name_lower = strtolower($conf_name);
  echo "<div id=\"confLayer\"></div>
  <div id=\"confBox\" style=\"overflow:auto\">
    <div style=\"margin-top: -1rem; margin-right: -1rem; text-align: right !important;\">
      <button class=\"conf-btn\" onclick=\"closeConfBox()\">";echo _("X");echo "</button>
    </div>
    </br>
    <div class=\"card\">
      <div class=\"card-header\">
        <h4 id=\"title\" >$conf_name "._("Configure Import Export")."</h4>
      </div>
      </br></br></br>
      <div class=\"cbi-value\">
        <label class=\"cbi-value-title\">";echo _("Configure Export"); echo "</label>
        <input type=\"submit\" class=\"btn btn-success\" value=\""; echo _("Export"); echo "\" name=\"export\" onclick=\"downloadFile('$conf_name')\">
      </div>
      </br></br>
      <form method=\"POST\" action=\"" . $conf_name_lower . "_conf\" enctype=\"multipart/form-data\" role=\"form\">";
      echo \ElastPro\Tokens\CSRF::hiddenField();
      echo "<div class=\"cbi-value\">
          <input hidden=\"hidden\" name=\"page_im_ex_name\" id=\"page_im_ex_name\" value=\"0\">
          <label class=\"cbi-value-title\">"; echo _("Configure Import"); echo "</label>
          <label for=\"upload\" class=\"cbi-file-lable\">
            <input type=\"file\" name=\"upload_file\" id=\"upload_file\">
            <input type=\"submit\" value=\""._("Upload")."\" name=\"upload\" data-toggle=\"modal\" data-target=\"#hostapdModal\">
          </label>
        </div>
      </form>
      </br></br></br>
    </div>
  </div>";
}

function save_import_file($section, $status, $file) {
  define('KB', 1024);
    $tmp_destdir = '/tmp/';
    $auth_flag = 0;

    try {
        // If undefined or multiple files, treat as invalid
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters');
        }

        $upload = \ElastPro\Uploader\FileUpload::factory('import', $tmp_destdir);
        $upload->set_max_file_size(2048*KB);
        $upload->set_allowed_mime_types(array('text/plain', 'application/octet-stream', 'text/csv'));
        $upload->file($file);
        $validation = new validation;
        $upload->callbacks($validation, array('check_name_length'));
        $results = $upload->upload();

        if (!empty($results['errors'])) {
            throw new RuntimeException($results['errors'][0]);
        }

        // Valid upload, get file contents
        $file_path = $results['full_path'];
        $new_file_path = '/tmp/config_import.csv';
        system("sudo mv $file_path $new_file_path");
        
        if (file_exists($new_file_path)) {
            $status->addMessage('file uploaded successfully', 'info');
            // 文件编码转换
            exec("sudo dos2unix $new_file_path");
            exec("sudo conf_im_ex import $section");
        } else {
            $status->addMessage('Fail to upload file', 'danger');
        }

        return $status;
    } catch (RuntimeException $e) {
        $status->addMessage($e->getMessage(), 'danger');
        return $status;
    }
}

function conf_im_ex($conf_name)
{
  echo "<input type=\"button\" class=\"cbi-button-add\" name=\"confBox\" value=\""._("Configure Import Export")."\" onclick=\"conf_im_ex('$conf_name')\">";
}

function page_table_title($section, $option_list) {
  echo '<input type="hidden" data-i18n="cur_value" value="'._("Current Value").'">';
  echo '<input type="hidden" data-i18n="write_value" value="'._("Write Value").'">';
  echo "<table class=\"table cbi-section-table\" name=\"table_$section\" id=\"table_$section\">
      <tr class=\"tr cbi-section-table-titles\">";
  $name_buf = '';
  $descr_buf = '';
  
  for ($i = 0; $i < count($option_list); $i++) {
    $style = strlen($option_list[$i]['style']) > 0 ? "style=\"". $option_list[$i]['style'] ."\"" : '';

    if ($option_list[$i]['name'] !== '') {
      $name = _($option_list[$i]['name']);
      $data_field = $option_list[$i]['data-field'] != '' ? "data-field="._($option_list[$i]['data-field']) : '';
      $name_buf .= "<th class=\"th cbi-section-table-cell\" $data_field $style>$name</th>";
    }

    $descr = $option_list[$i]['descr'] != '' ? _($option_list[$i]['descr']) : '';
    $descr_buf .= "<th class=\"th cbi-section-table-cell\" $style>$descr</th>";
    
    unset($name);
    unset($style);
    unset($descr);
  }
  echo $name_buf;
  echo "<th class=\"th cbi-section-table-cell cbi-section-actions\"></th>
        <th class=\"th cbi-section-table-cell cbi-section-actions\"></th>
      </tr>
      <tr class=\"tr cbi-section-table-descr\">";
  echo $descr_buf;
  echo "<th class=\"th cbi-section-table-cell cbi-section-actions\"></th>
        <th class=\"th cbi-section-table-cell cbi-section-actions\"></th>
      </tr>
    </table>";
  if ($section != 'adc' && $section != 'di' && $section != 'do' && $section != 'system_param' &&
      $section != 'modbus_slave_point' && $section != 'dnp3') {
    $section_t = ucfirst($section);
    echo "<div class=\"cbi-section-create\">
      <input type=\"button\" class=\"cbi-button-add\" name=\"popBox\" value="._("Add")." onclick=\"addData('$section')\">
      <input type=\"button\" class=\"cbi-button-add\" name=\"confBox\" value=\""._("Configure Import Export")."\" onclick=\"conf_im_ex('$section_t')\">
      </div>";
  }
}

function page_progressbar($title, $content) {
  echo '<div class="modal fade" id="hostapdModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div class="modal-title" id="ModalLabel"><i class="fas fa-sync-alt mr-2"></i>' . $title . '</div>
        </div>
        <div class="modal-body">
          <div class="col-md-12 mb-3 mt-1">'. $content . '...</div>
          <div class="progress" style="height: 20px;">
            <div class="progress-bar bg-info" role="progressbar" id="progressBar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="9"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline btn-primary" data-dismiss="modal">' . _("Close") .'</button>
        </div>
      </div>
    </div>
  </div>';
}

function dct_rules_common_add_fields_behind($arrOri) {
  $common = array(
    array("name"=>"Reporting Center",     "style"=>"", "descr"=>"Multiple Servers Are Separated By Minus", "ctl"=>"input"),
    // array("name"=>"Operator",             "style"=>"display:none", "descr"=>"0 + - * /", "ctl"=>"select"),
    // array("name"=>"Operation Expression", "style"=>"display:none", "descr"=>"", "ctl"=>"input"),
    // array("name"=>"Operand",              "style"=>"display:none", "descr"=>"", "ctl"=>"input"),
    // array("name"=>"Accuracy",             "style"=>"display:none", "descr"=>"0~6", "ctl"=>"select"),
    // array("name"=>"SMS&Email Reporting",  "style"=>"display:none", "descr"=>"", "ctl"=>""),
    // array("name"=>"Event Reporting Center","style"=>"display:none", "descr"=>"", "ctl"=>""),
    // array("name"=>"Report Type",          "style"=>"display:none", "descr"=>"", "ctl"=>""),
    // array("name"=>"Alarm Up Limit",       "style"=>"display:none", "descr"=>"", "ctl"=>""),
    // array("name"=>"Alarm Down Limit",     "style"=>"display:none", "descr"=>"", "ctl"=>""),
    // array("name"=>"Phone Number",         "style"=>"display:none", "descr"=>"", "ctl"=>""),
    // array("name"=>"Email",                "style"=>"display:none", "descr"=>"", "ctl"=>""),
    // array("name"=>"Interpreter",          "style"=>"display:none", "descr"=>"", "ctl"=>""),
    // array("name"=>"Contents",             "style"=>"display:none", "descr"=>"", "ctl"=>""),
    // array("name"=>"Event Reporting Center", "style"=>"display:none", "descr"=>"", "ctl"=>""),
    // array("name"=>"Timeout Count",              "style"=>"display:none", "descr"=>"", "ctl"=>""),
    // array("name"=>"Retry Interval",       "style"=>"display:none", "descr"=>"", "ctl"=>""),
    // array("name"=>"Again Interval",       "style"=>"display:none", "descr"=>"", "ctl"=>""),
    array("name"=>"Enable",               "style"=>"", "descr"=>"", "ctl"=>"check"),
  );

  return array_merge($arrOri, $common);
}

function dct_rules_common_add_fields($arrOri) {
  $common_pre = array(
    array("name"=>"Order",                "data-field" => "", "style"=>"", "descr"=>"", "ctl"=>"input"),
    array("name"=>"Device Name",          "data-field" => "", "style"=>"", "descr"=>"", "ctl"=>"input"),
    array("name"=>"Belonged Interface",   "data-field" => "", "style"=>"", "descr"=>"", "ctl"=>"select"),
    array("name"=>"Tag Name",             "data-field" => "factor_name", "style"=>"", "descr"=>"", "ctl"=>"input"),
  );

  $tmp_array = array_merge($common_pre, $arrOri);

  return dct_rules_common_add_fields_behind($tmp_array);
}

function dct_rules_common($table_name) {
  InputControlCustom(_('Reporting Center'), $table_name.'.server_center', $table_name.'.server_center', _('Multiple Servers Are Separated By Minus'));

  $operator_list = [_('None'), '+', '-', '*', '/', _('Expression')];
  SelectControlCustom(_('Operator (math)'), $table_name.'.operator', $operator_list, $operator_list[0], $table_name.'.operator', _('0 + - * /'), "selectOperator('$table_name')");

  echo '<div name="page_operand" id="page_operand">';
  InputControlCustom(_('Operand'), $table_name.'.operand', $table_name.'.operand');
  echo '</div>';

  echo '<div name="page_ex" id="page_ex">';
  InputControlCustom(_('Operation Expression'), $table_name.'.ex', $table_name.'.ex', _('(x + 10) * 10,  x is collected data'));
  echo '</div>';

  $accuracy_list = ['0', '1', '2', '3', '4', '5', '6'];
  SelectControlCustom(_('Accuracy'), $table_name.'.accuracy', $accuracy_list, $accuracy_list[0], $table_name.'.accuracy', _('0~6'));

  CheckboxControlCustom(_('Event Reporting'), $table_name.'.sms_reporting', $table_name.'.sms_reporting', null, null, "enableAlarm('$table_name')");

  echo '<div name="page_sms" id="page_sms">';
  $report_type = [_('Change reporting'), _('Alarm reporting')];
  SelectControlCustom(_('Report Type'), $table_name.'.report_type', $report_type, $report_type[0], $table_name.'.report_type', null, "selectReportType('$table_name')");
  
  echo '<div name="page_alarm" id="page_alarm">';
  InputControlCustom(_('Alarm Up Limit'), $table_name.'.alarm_up', $table_name.'.alarm_up');

  InputControlCustom(_('Alarm Down Limit'), $table_name.'.alarm_down', $table_name.'.alarm_down');
  echo '</div>';
  InputControlCustom(_('Phone Number'), $table_name.'.phone_num', $table_name.'.phone_num', _('Multiple Phones Are Separated By Comma'));

  InputControlCustom(_('Email'), $table_name.'.email', $table_name.'.email', _('Multiple emails Are Separated By Comma'));

  CheckboxControlCustom(_('Interpreter'), $table_name.'.interpreter', $table_name.'.interpreter');

  InputControlCustom(_('Contents'), $table_name.'.contents', $table_name.'.contents');

  InputControlCustom(_('Event Reporting Center'), $table_name.'.event_server_center', $table_name.'.event_server_center', _('Multiple Servers Are Separated By Minus'));

  InputControlCustom(_('Max Timeout Count'), $table_name.'.timeout_count', $table_name.'.timeout_count', _('The count of collect period'));

  InputControlCustom(_('Retry Interval'), $table_name.'.retry_interval', $table_name.'.retry_interval', _('Minutes, it must be a multiple of collect period'));

  InputControlCustom(_('Again Interval'), $table_name.'.again_interval', $table_name.'.again_interval', _('Minutes, it must be a multiple of collect period'));
  echo '</div>';

  CheckboxControlCustom(_('Enable'), $table_name.'.enabled', $table_name.'.enabled', 'checked');
}
