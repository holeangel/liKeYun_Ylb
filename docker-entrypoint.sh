#!/bin/bash
set -e

echo "Starting liKeYun_ylb application..."

# 生成数据库配置文件
if [ -f /var/www/html/generate-db-config.php ]; then
    echo "Generating database configuration..."
    php /var/www/html/generate-db-config.php
fi

# 确保安装检测需要的目录有写入权限
echo "Setting up permissions for installation detection..."
chmod -R 777 /var/www/html/install/
chmod -R 777 /var/www/html/console/
chmod 777 /var/www/html/console/upload/

# 启动Apache
exec apache2-foreground
