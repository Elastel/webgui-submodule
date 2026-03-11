#!/bin/bash
# When wireless client AP or Bridge mode is enabled, this script handles starting
# up network services in a specific order and timing to avoid race conditions.

PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
CONFIGFILE="/etc/raspap/hostapd.ini"
DAEMONPATH="/lib/systemd/system/raspapd.service"
OPENVPNENABLED=$(pidof openvpn | wc -l)

WIFIENABLED=$(uci get wifi.wifi.enabled)
WIFICLIENTENABLED=$(uci get wifi.wifi_client.enabled)

positional=()
while [[ $# -gt 0 ]]
do
key="$1"

case $key in
    -i|--interface)
    interface="$2"
    shift # past argument
    shift # past value
    ;;
    -s|--seconds)
    seconds="$2"
    shift
    shift
    ;;
    -a|--action)
    action="$2"
    shift
    shift
    ;;
esac
done
set -- "${positional[@]}"

echo "Stopping network services..."
if [ $OPENVPNENABLED -eq 1 ]; then
    systemctl stop openvpn-client@client
fi
systemctl stop systemd-networkd
systemctl stop hostapd.service
systemctl stop dnsmasq.service
systemctl stop dhcpcd.service

if [ "${action}" = "stop" ]; then
    echo "Services stopped. Exiting."
    exit 0
fi

if [[ "$interface" == "br0" || "$interface" == "uap0" ]]; then
    lan_mac_conf=$(uci get network.lan.mac)
    cur_mac=$(cat /sys/class/net/br0/address)
    if [[ -n "$lan_mac_conf" ]]; then
        if [[ "$lan_mac_conf" != "$cur_mac" ]]; then
            ifconfig br0 down
            ifconfig br0 hw ether $lan_mac_conf
            ifconfig br0 up

            ifconfig eth1 down
            ifconfig eth1 hw ether $lan_mac_conf
            ifconfig eth1 up
        fi
    else
        eth0_mac=$(cat /sys/class/net/eth0/address)
        eth1_mac=$(echo $eth0_mac | awk -F: '{print $1":"$2":"$3":"$4":"$5":"$NF+1}')
        ifconfig br0 down
        ifconfig br0 hw ether $eth1_mac
        ifconfig br0 up

        ifconfig eth1 down
        ifconfig eth1 hw ether $eth1_mac
        ifconfig eth1 up
    fi
fi

if [ $interface = "uap0" ]; then
    wan_mac_conf=$(uci get network.wan.mac)
    cur_wan_mac=$(cat /sys/class/net/eth0/address)
    if [[ "$lan_mac_conf" != "$cur_mac" ]]; then
        ifconfig eth0 down
        ifconfig eth0 hw ether $wan_mac_conf
        ifconfig eth0 up
    fi
fi

# Start services, mitigating race conditions
echo "Starting network services..."
if [[ "$WIFICLIENTENABLED" == "1" ]]; then
    systemctl mask hostapd.service
    systemctl disable hostapd.service
    brctl delif br0 wlan0
    sudo sed -i "s/eth1 wlan0/eth1/g" /etc/dhcpcd.conf

    [ -n "$(pgrep wpa_supplicant)" ] || {
        wpa_supplicant -B -i wlan0 -c /etc/wpa_supplicant/wpa_supplicant.conf &> /dev/null
        # wpa_supplicant -Dwext -iwlan0 -c/etc/wpa_supplicant/wpa_supplicant.conf -B &> /dev/null
    }
else
    if [[ "$WIFIENABLED" == "1" ]]; then
        kill -9 $(pgrep wpa_supplicant)
        brctl addif br0 wlan0
        sudo sed -i "s/eth1 wlan0/eth1/g" /etc/dhcpcd.conf
        sudo sed -i "s/eth1/eth1 wlan0/g" /etc/dhcpcd.conf
        
        sleep 1
        systemctl unmask hostapd.service
        systemctl enable hostapd.service
        sleep 1
        systemctl start hostapd.service
    else
        systemctl mask hostapd.service
        systemctl disable hostapd.service
    fi
fi

sleep "${seconds:-1}"

# check br0 ip
BR0_IP=$(ip route | grep -c br0)
if [ $BR0_IP = "0" ]; then
    BR0_GATEWAY=$(uci get network.lan.ip)
    BR0_MAK=$(uci get network.lan.netmask)
    ifconfig br0 $BR0_GATEWAY netmask $BR0_MAK up
fi

echo "Stopping systemd-networkd"
systemctl stop systemd-networkd

echo "Restarting eth0 interface..."
ip link set down eth1
ip link set up eth1

echo "Removing uap0 interface..."
iw dev uap0 del

echo "Enabling systemd-networkd"
systemctl start systemd-networkd
systemctl enable systemd-networkd

systemctl start dhcpcd.service
sleep "${seconds:-1}"

systemctl start dnsmasq.service

if [ $OPENVPNENABLED -eq 1 ]; then
    systemctl start openvpn-client@client
fi

# @mp035 found that the wifi client interface would stop every 8 seconds
# for about 16 seconds. Reassociating seems to solve this
if [ "${config[WifiAPEnable]}" = 1 ]; then
    echo "Reassociating wifi client interface..."
    sleep "${seconds}"
    wpa_cli -i ${config[WifiManaged]} reassociate
fi

echo "RaspAP service start DONE"

