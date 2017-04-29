#!/bin/bash

script_home=/home/pi/python
script_name="$script_home/readGPS.py"
pid_file="$script_home/readGPS.pid"

# returns a boolean and optionally the pid
running() {
    local status=false
    if [[ -f $pid_file ]]; then
        # check to see it corresponds to the running script
        local pid=$(< "$pid_file")
        local cmdline=/proc/$pid/cmdline
        # you may need to adjust the regexp in the grep command
        if [[ -f $cmdline ]] && grep -q "$script_name" $cmdline; then
            status="true $pid"
        fi
    fi
    echo $status
}

start() {
    echo "starting $script_name"
    nohup python "$script_name" &
    echo $! > "$pid_file"
}

stop() {
    # `kill -0 pid` returns successfully if the pid is running, but does not
    # actually kill it.
    kill -15 $1 && kill $1
    rm "$pid_file"
    echo "stopped"
}

read running pid < <(running)

case $1 in 
    start)
        if $running; then
            echo "$script_name is already running with PID $pid"
        else
            start
        fi
        ;;
    stop)
        stop $pid
        ;;
    restart)
        stop $pid
        start
        ;;
    status)
        if $running; then
            echo "$script_name is running with PID $pid"
        else
            echo "$script_name is not running"
        fi
        ;;
    *)  echo "usage: $0 <start|stop|restart|status>"
        exit
        ;;
esac
