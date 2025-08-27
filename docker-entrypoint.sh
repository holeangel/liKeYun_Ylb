#!/bin/bash
set -e

echo "Starting liKeYun_ylb application..."

# 生成数据库配置文件
if [ -f /var/www/html/generate-db-config.php ]; then
    echo "Generating database configuration..."
    php /var/www/html/generate-db-config.php
else
    echo "Warning: Database configuration generator not found"
fi

# 检查是否已安装，如果未安装则创建安装锁文件占位
if [ ! -f /var/www/html/install/install.lock ] && [ ! -f /var/www/html/console/Db.php ]; then
    echo "Application not installed yet. Please run the installation process."
    # 创建空的安装锁文件占位
    touch /var/www/html/install/install.lock
    chown www-data:www-data /var/www/html/install/install.lock
fi

# 设置文件权限
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html

# 启动Apache
exec apache2-foreground