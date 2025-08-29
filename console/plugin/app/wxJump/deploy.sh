#!/bin/bash

# 检查是否安装了koyeb CLI
if ! command -v koyeb &> /dev/null; then
    echo "Koyeb CLI未安装，正在安装..."
    curl -fsSL https://cli.koyeb.com/install.sh | sh
fi

# 检查是否有.env文件
if [ ! -f .env ]; then
    echo "未找到.env文件，请先创建.env文件"
    echo "可以复制.env.example并填写Railway数据库信息"
    exit 1
fi

# 加载环境变量
source .env

# 登录Koyeb（如果未登录）
echo "请确保已登录Koyeb CLI，如未登录请运行 'koyeb login'"

# 创建Koyeb应用
echo "正在创建Koyeb应用..."
koyeb app create wx-jump

# 部署服务
echo "正在部署服务..."
koyeb service create \
    --app wx-jump \
    --name web \
    --type web \
    --ports 80:http \
    --env DB_HOST=$DB_HOST \
    --env DB_NAME=$DB_NAME \
    --env DB_USER=$DB_USER \
    --env DB_PASS=$DB_PASS \
    --env DB_PORT=$DB_PORT \
    --docker .

echo "部署完成！"
echo "请访问以下URL查看您的应用："
koyeb service get web -a wx-jump