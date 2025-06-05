#!/bin/bash

DOMAIN="hexcub-api.zqdevs.my.id"
EMAIL="afn.happy@gmail.com"
WEBROOT="/var/www/app"

# Tunggu nginx ready
echo "🔄 Waiting for nginx to be ready..."
sleep 10

# Ambil sertifikat SSL menggunakan webroot
echo "🚀 Running certbot..."
certbot certonly --webroot -w "$WEBROOT" -d "$DOMAIN" --agree-tos --email "$EMAIL" --non-interactive || {
    echo "❌ Certbot failed"
    exit 1
}

# Symlink cert ke direktori nginx
ln -sf /etc/letsencrypt/live/$DOMAIN/fullchain.pem /etc/ssl/certs/ssl-cert.pem
ln -sf /etc/letsencrypt/live/$DOMAIN/privkey.pem /etc/ssl/private/ssl-cert.key

echo "✅ SSL installed."
