#!/bin/bash

# Ganti dengan domainmu ya
DOMAIN="hexcub-api.zqdevs.my.id"
EMAIL="afn.happy@gmail.com"
WEBROOT="/var/www/app/public"

# Tunggu nginx ready
echo "🔄 Waiting for nginx to be ready..."
sleep 10

# Ambil sertifikat SSL menggunakan webroot
echo "🚀 Running certbot..."
certbot certonly --webroot -w "$WEBROOT" -d "$DOMAIN" --agree-tos --email "$EMAIL" --non-interactive

# Symlink cert ke direktori nginx default
ln -sf /etc/letsencrypt/live/$DOMAIN/fullchain.pem /etc/ssl/certs/ssl-cert.pem
ln -sf /etc/letsencrypt/live/$DOMAIN/privkey.pem /etc/ssl/private/ssl-cert.key

# Reload nginx (kalau perlu, misalnya mount nginx.sock)
echo "✅ SSL installed. Now reload nginx manually or via volume/socket."
