FROM python:3.12-alpine

RUN apk add --no-cache nginx curl openssl bash

# Install certbot dan plugin nginx
RUN pip install certbot certbot-nginx

COPY init-cert.sh /init-cert.sh
RUN chmod +x /init-cert.sh

CMD ["/init-cert.sh"]