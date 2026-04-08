FROM php:8.2-apache

# Cài extension cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd mysqli

# Bật mod_rewrite cho .htaccess
RUN a2enmod rewrite

# Copy toàn bộ project vào /var/www/html
COPY . /var/www/html/

# Cấu hình Apache cho phép .htaccess
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/webbanhang.conf \
    && a2enconf webbanhang

# Phân quyền
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Render dùng port động
ENV PORT=80
EXPOSE 80

CMD ["apache2-foreground"]
