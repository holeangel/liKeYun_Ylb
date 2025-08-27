#!/bin/bash
set -e

echo "Starting liKeYun_ylb application..."

# 生成数据库配置文件
if [ -f /var/www/html/generate-db-config.php ]; then
    echo "Generating database configuration..."
    php /var/www/html/generate-db-config.php
fi

# 启动Apache
exec apache2-foreground