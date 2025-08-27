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

# 为所有需要写入权限的目录设置完全权限
chmod -R 777 /var/www/html/install/
chmod -R 777 /var/www/html/console/
chmod -R 777 /var/www/html/s/
chmod -R 777 /var/www/html/common/
chmod -R 777 /var/www/html/static/

# 特别为安装检测需要的目录设置权限
chmod -R 777 /var/www/html/console/upload/

# 递归设置所有子目录和文件的权限
find /var/www/html -name "*.php" -exec chmod 666 {} \;
find /var/www/html -name "*.html" -exec chmod 666 {} \;
find /var/www/html -name "*.js" -exec chmod 666 {} \;
find /var/www/html -name "*.css" -exec chmod 666 {} \;
find /var/www/html -type d -exec chmod 777 {} \;

# 启动Apache
exec apache2-foreground