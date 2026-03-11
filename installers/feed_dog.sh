#!/bin/bash

if [ $1 == "0" ]; then
    model=$(cat /etc/fw_model)
    if [[ $model == "EG510" ]]; then
        while true ;do
            gpioset gpiochip0 5=1
            sleep 0.02
            gpioset gpiochip0 5=0
            sleep 2
        done
    else
        while true ;do
            echo 1 > /sys/class/gpio/gpio25/value
            sleep 0.02
            echo 0 > /sys/class/gpio/gpio25/value
            sleep 2
        done
    fi
else
	pids=$(fuser /dev/watchdog 2>/dev/null)

    if [ -n "$pids" ]; then
        echo "Processes using /dev/watchdog: $pids"

        for pid in $pids; do
            echo "Killing process with PID: $pid"
            kill -9 $pid
        done
    else
        echo "No process is using /dev/watchdog."
    fi

	sleep 1
	echo 'V' > /dev/watchdog
fi

