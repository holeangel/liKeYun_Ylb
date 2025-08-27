# liKeYun_ylb 部署指南

## 项目概述
私域引流宝是一个PHP开发的私域流量获取与监控工具，包含微信活码系统、短网址生成、数据监控等功能。

## 部署到 Koyeb

### 前提条件
1. GitHub 账号
2. Koyeb 账号 (https://koyeb.com)
3. Railway 账号 (用于数据库，https://railway.app)

### 部署步骤

#### 1. 准备 GitHub 仓库
```bash
# 初始化Git仓库
git init
git add .
git commit -m "Initial commit for liKeYun_ylb deployment"

# 创建GitHub仓库并推送
git remote add origin https://github.com/your-username/liKeYun_ylb.git
git branch -M main
git push -u origin main
```

#### 2. 设置 Railway 数据库
1. 登录 Railway (https://railway.app)
2. 创建新项目 → 选择 MySQL
3. 记录数据库连接信息：
   - RAILWAY_DB_HOST
   - RAILWAY_DB_PORT 
   - RAILWAY_DB_NAME
   - RAILWAY_DB_USER
   - RAILWAY_DB_PASSWORD

#### 3. 部署到 Koyeb
1. 登录 Koyeb (https://koyeb.com)
2. 点击 "Create App"
3. 选择 "GitHub" 作为部署源
4. 选择你的 liKeYun_ylb 仓库
5. 配置环境变量：
   ```
   RAILWAY_DB_HOST=your_railway_db_host
   RAILWAY_DB_PORT=your_railway_db_port
   RAILWAY_DB_NAME=your_railway_db_name
   RAILWAY_DB_USER=your_railway_db_user
   RAILWAY_DB_PASSWORD=your_railway_db_password
   ```
6. 点击 "Deploy"

#### 4. 完成安装
1. 访问你的 Koyeb 应用域名
2. 进入安装页面：`/install/install.html`
3. 填写数据库信息（使用Railway提供的数据库信息）
4. 设置管理员账号信息
5. 完成安装

### 环境变量说明
| 变量名 | 说明 | 示例 |
|--------|------|------|
| RAILWAY_DB_HOST | Railway数据库主机 | railway-db-host.railway.app |
| RAILWAY_DB_PORT | Railway数据库端口 | 3306 |
| RAILWAY_DB_NAME | Railway数据库名 | railway |
| RAILWAY_DB_USER | Railway数据库用户 | root |
| RAILWAY_DB_PASSWORD | Railway数据库密码 | your_password |

### 文件说明
- `.gitignore` - 排除不需要上传的文件（README.md、LICENSE-MIT等）
- `Dockerfile` - Docker容器配置
- `koyeb.yaml` - Koyeb部署配置
- `generate-db-config.php` - 数据库配置生成脚本
- `docker-entrypoint.sh` - 容器启动脚本

### 注意事项
1. 首次访问需要完成网页安装流程
2. 确保Railway数据库已正确配置
3. 安装完成后不要删除 `console/Db.php` 文件
4. 定期备份数据库重要数据

## 故障排除
如果部署失败，检查：
1. 环境变量是否正确设置
2. Railway数据库是否可访问
3. 容器日志是否有错误信息

## 技术支持
如有问题，请参考项目文档或联系开发者。