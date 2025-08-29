FROM php:8.1-apache

# 安装PDO MySQL扩展
RUN docker-php-ext-install pdo pdo_mysql

# 启用Apache rewrite模块
RUN a2enmod rewrite

# 复制项目文件
COPY . /var/www/html/

# 设置工作目录
WORKDIR /var/www/html

# 创建.htaccess文件
RUN echo "RewriteEngine On\n\
RewriteRule ^jump/([a-z0-9]{8})$ /s/jumpRouter.php?key=\$1 [L]" > /var/www/html/.htaccess

# 设置权限
RUN chown -R www-data:www-data /var/www/html

# 暴露端口
EXPOSE 80

# 启动Apache
CMD ["apache2-foreground"]