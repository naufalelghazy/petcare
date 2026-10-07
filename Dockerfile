FROM php:8.2-apache

# Install PDO MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Pastikan hanya mpm_prefork yang aktif (mencegah error AH00534: More than one MPM loaded)
RUN rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* 2>/dev/null || true && \
    a2enmod mpm_prefork 2>/dev/null || true

# Salin script entrypoint & hilangkan format Windows carriage return (\r)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

# Set direktori kerja & salin kode aplikasi
WORKDIR /var/www/html
COPY . /var/www/html/

# Atur hak akses file
RUN chown -R www-data:www-data /var/www/html

ENV PORT=80
EXPOSE ${PORT}

CMD ["/usr/local/bin/docker-entrypoint.sh"]

