#!/bin/bash

script_home=/home/pi/python
script_name="$script_home/threads.py"
pid_file="$script_home/threads.pid"


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
    cd $script_home
    echo "starting $script_name"
    nohup python /home/pi/python/threads.py $1 &
    echo $! > "$pid_file"
}



stop() {
    # `kill -0 pid` returns successfully if the pid is running, but does not
    # actually kill it.
    # kill tcp
    fuser -k -n tcp 5000
    # turnOff GSS
    bash GPSstop.sh
    kill -15 $1 && kill $1
    pkill -f "$script_name"
    rm "$pid_file"
    rm "/app/storage/json/gps.json"


    echo "stopped"
}

read running pid < <(running)

case $1 in
    start)
        if $running; then
            echo "$script_name is already running with PID $pid"
        else
            if $2; then
                echo "taskID not found"

            else
                start $2
            fi
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
            echo "1"
        else
            echo "0"
        fi
        ;;
    *)  echo "usage: $0 <start|stop|restart|status>"
        exit
        ;;
esac
