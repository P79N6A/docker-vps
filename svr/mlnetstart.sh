#!/bin/bash

#chmod -R 777 /root/kod

#echo "=== starting sss ==="
#ssserver -d start -c /root/sss/config.json

#echo "=== starting apache ==="
#/opt/lampp/xampp startapache

#echo "=== starting deluge-web ==="
#start-stop-daemon --start --name deluge --background --exec /usr/bin/deluge-web -- -p 1926

echo "=== starting mlnet ==="
echo "start-stop-daemon -c xy --start --name mlnet --background --exec /usr/bin/mlnet"
start-stop-daemon -c xy --start --name mlnet --background --exec /usr/bin/mlnet
