#!/bin/bash

$HOME/.acme.sh/acme.sh --issue -d $1 -w /var/www/html --ecc --keylength ec-256