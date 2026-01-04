FROM alpine:3.19

# Install packages
RUN apk add --no-cache \
    nginx \
    php82 \
    php82-fpm \
    php82-mysqli \
    php82-json \
    php82-opcache \
    php82-curl \
    php82-mbstring \
    php82-session \
    php82-xml \
    rsyslog \
    supervisor \
    bash

# Create required directories
RUN mkdir -p /var/log/remote /run/nginx /run/php /var/log/supervisor /var/www/html

# Copy configs
COPY ./src/nginx /etc/nginx/
COPY ./src/php-fpm/php-fpm.conf /etc/php82/php-fpm.d
COPY ./src/rsyslog/rsyslog.conf /etc/rsyslog.conf
COPY ./src/supervisord/supervisord.conf /etc
COPY ./src/html /var/www/html

# Permissions
RUN chown -R nginx:nginx /var/www/html
# RUN mkdir -p /var/log/remote /var/log/remote/php-fpm /var/log/remote/nginx /var/log/remote/rsyslog
RUN chmod 777 -R /var/log/remote


# Expose HTTP
EXPOSE 80 514/udp 514/tcp

# Start everything with supervisord
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]