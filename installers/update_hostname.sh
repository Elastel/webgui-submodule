#!/bin/sh

NEW_HOSTNAME="$1"
HOSTS="/etc/hosts"

if [ -z "$NEW_HOSTNAME" ]; then
    echo "Usage: $0 <hostname>"
    exit 1
fi

echo "$NEW_HOSTNAME" > /etc/hostname
hostname "$NEW_HOSTNAME"

if grep -q "^127.0.0.1" "$HOSTS"; then

    LINE=$(grep "^127.0.0.1" "$HOSTS")

    case "$LINE" in
        *localhost*)
            sed -i "s/^127.0.0.1.*/127.0.0.1   localhost/" "$HOSTS"
            ;;
        *)
            sed -i "s/^127.0.0.1.*/127.0.0.1   localhost/" "$HOSTS"
            ;;
    esac

else
    echo "127.0.0.1   localhost" >> "$HOSTS"
fi

grep -q "^::1" "$HOSTS" || echo "::1     localhost" >> "$HOSTS"

if grep -q "^127.0.1.1" "$HOSTS"; then
    sed -i "s/^127.0.1.1.*/127.0.1.1   $NEW_HOSTNAME/" "$HOSTS"
else
    echo "127.0.1.1   $NEW_HOSTNAME" >> "$HOSTS"
fi

echo "Hostname updated to: $NEW_HOSTNAME"
