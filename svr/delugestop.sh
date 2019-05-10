#!/bin/bash

#chmod -R 777 /root/kod

echo "=== starting sss ==="
ssserver -d start -c /root/sss/config.json

echo "=== starting apache ==="
/opt/lampp/xampp startapache

echo "=== stop deluge-web ==="
echo "start-stop-daemon --stop --name deluge-web"
start-stop-daemon --stop --name deluge-web

#echo "=== stop mlnet ==="
#start-stop-daemon -c xy --start --name mlnet --background --exec /usr/bin/mlnet
