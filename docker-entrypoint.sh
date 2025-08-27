#!/bin/bash
set -e

echo "Starting liKeYun_ylb application..."

# 生成数据库配置文件
if [ -f /var/www/html/generate-db-config.php ]; then
    echo "Generating database configuration..."
    php /var/www/html/generate-db-config.php
fi

# 设置文件权限
chown -R www-data:www-data /var/www/html
find /var/www/html -type d -exec chmod 755 {} \;
find /var/www/html -type f -exec chmod 644 {} \;

# 为需要写入的目录设置特殊权限
chmod -R 777 /var/www/html/install/
chmod -R 777 /var/www/html/console/

# 确保upload目录存在并设置权限
if [ -d "/var/www/html/console/upload" ]; then
    chmod -R 777 /var/www/html/console/upload/
else
    echo "Warning: /var/www/html/console/upload/ directory does not exist"
fi

# 启动Apache
exec apache2-foreground