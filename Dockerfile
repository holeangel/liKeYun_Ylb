# 使用官方的PHP Apache镜像
FROM php:8.2-apache

# 设置工作目录
WORKDIR /var/www/html

# 安装必要的扩展
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli zip

# 启用Apache重写模块
RUN a2enmod rewrite

# 复制项目文件到容器中
COPY . .

# 设置Apache文档根目录权限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 添加启动脚本
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# 暴露端口
EXPOSE 80

# 使用自定义启动脚本
ENTRYPOINT ["docker-entrypoint.sh"]
