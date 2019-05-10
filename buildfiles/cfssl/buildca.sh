#!/bin/bash

cfssl gencert --ca ./conf/rootca.pem --ca-key ./conf/rootca-key.pem --config ./conf/wlst-gen-cert.json ./conf/client-csr.json | cfssljson --bare $1
