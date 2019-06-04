#!/bin/bash

docker run -dti --privileged -p 1920-1929:1920-1929 -p 6880-6888:6880-6888 -v svr:/root/svr $1 /bin/bash