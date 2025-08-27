FROM php:7.4-apache

# 安装PHP扩展和必要工具
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo_mysql zip

# 启用Apache模块
RUN a2enmod rewrite

# 设置工作目录
WORKDIR /var/www/html

# 复制项目文件
COPY . /var/www/html/

# 设置权限 - 确保所有必要的目录都有正确的权限
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod -R 777 /var/www/html/install/ \
    && chmod -R 777 /var/www/html/console/ \
    && chmod -R 777 /var/www/html/console/upload/ \
    && chmod -R 777 /var/www/html/common/ \
    && chmod -R 777 /var/www/html/s/ \
    && chmod -R 777 /var/www/html/static/upload/ \
    && mkdir -p /var/www/html/console/upload/ \
    && mkdir -p /var/www/html/static/upload/ \
    && chown -R www-data:www-data /var/www/html/console/upload/ \
    && chown -R www-data:www-data /var/www/html/static/upload/ \
    && chmod +x /var/www/html/docker-entrypoint.sh

# 暴露端口
EXPOSE 80

# 使用自定义入口点脚本
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
CMD ["apache2-foreground"]