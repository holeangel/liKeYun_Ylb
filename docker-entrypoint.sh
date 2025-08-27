#!/bin/bash
set -e

echo "Starting liKeYun_ylb application..."

# 生成数据库配置文件
if [ -f /var/www/html/generate-db-config.php ]; then
    echo "Generating database configuration..."
    php /var/www/html/generate-db-config.php
fi

# 设置文件权限（简化版本）
chown -R www-data:www-data /var/www/html
find /var/www/html -type d -exec chmod 755 {} \;
find /var/www/html -type f -exec chmod 644 {} \;

# 为需要写入的目录设置特殊权限
chmod -R 777 /var/www/html/install/
chmod -R 777 /var/www/html/console/
chmod -R 777 /var/www/html/console/upload/

# 启动Apache
exec apache2-foreground