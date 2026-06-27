#!/bin/sh
set -eu

CERT_DIR=/etc/letsencrypt/live/simaggotbalkot.com

if [ ! -f "$CERT_DIR/fullchain.pem" ] || [ ! -f "$CERT_DIR/privkey.pem" ]; then
    mkdir -p "$CERT_DIR"
    openssl req -x509 -nodes -newkey rsa:2048 -days 1 \
        -keyout "$CERT_DIR/privkey.pem" \
        -out "$CERT_DIR/fullchain.pem" \
        -subj "/CN=simaggotbalkot.com" >/dev/null 2>&1
fi

exec /docker-entrypoint.sh "$@"
