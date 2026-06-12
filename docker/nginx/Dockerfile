FROM nginx:1.17.8-alpine

RUN apk add --no-cache openssl \
    && mkdir -p /etc/nginx/certs \
    && openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout /etc/nginx/certs/local.key \
        -out /etc/nginx/certs/local.crt \
        -subj "/CN=localhost"

COPY . /app/
COPY ./docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf
