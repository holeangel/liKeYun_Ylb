#!/bin/bash
set -e

echo "Starting liKeYun_ylb application..."

# 确保必要的目录存在并设置正确权限
echo "Setting up permissions..."
mkdir -p /var/www/html/console/upload/
mkdir -p /var/www/html/static/upload/

# 设置文件和目录权限
chown -R www-data:www-data /var/www/html/
find /var/www/html -type d -exec chmod 755 {} \;
find /var/www/html -type f -exec chmod 644 {} \;

# 设置需要写入权限的目录
chmod -R 777 /var/www/html/install/
chmod -R 777 /var/www/html/console/
chmod -R 777 /var/www/html/console/upload/
chmod -R 777 /var/www/html/static/upload/
chmod -R 777 /var/www/html/common/
chmod -R 777 /var/www/html/s/

# 生成数据库配置文件
if [ -f /var/www/html/generate-db-config.php ]; then
    echo "Generating database configuration..."
    php /var/www/html/generate-db-config.php
fi

# 启动Apache
exec apache2-foreground