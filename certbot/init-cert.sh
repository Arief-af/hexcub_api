#!/bin/bash

DOMAIN="hexcubapi.zqdevs.my.id"
EMAIL="afn.happy@gmail.com"
WEBROOT="/var/www/html"  # Gunakan webroot ini karena certbot pakai ini dalam volume

# Tunggu nginx siap (lebih aman pakai loop)
echo "🔄 Waiting for nginx to be ready..."
until curl -s http://hexcubapi.zqdevs.my.id > /dev/null; do
    sleep 3
done

echo "🚀 Running certbot..."
certbot certonly --webroot -w "$WEBROOT" -d "$DOMAIN" --agree-tos --email "$EMAIL" --non-interactive || {
    echo "❌ Certbot failed"
    exit 1
}

# Symlink cert ke direktori nginx SSL (pastikan path sesuai)
ln -sf /etc/letsencrypt/live/$DOMAIN/fullchain.pem /etc/ssl/cert.pem
ln -sf /etc/letsencrypt/live/$DOMAIN/privkey.pem /etc/ssl/key.pem

echo "✅ SSL installed."
