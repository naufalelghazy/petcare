FROM php:8.2-apache

# Install PDO MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Configure Apache to listen on the dynamic $PORT provided by Railway
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf && \
    sed -i 's/:80/:${PORT}/' /etc/apache2/sites-available/000-default.conf

# Set working directory & copy project files
WORKDIR /var/www/html
COPY . /var/www/html/

# Set file permissions
RUN chown -R www-data:www-data /var/www/html

# Default PORT fallback
ENV PORT=80

EXPOSE ${PORT}

CMD ["apache2-foreground"]
