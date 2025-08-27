# Koyeb部署权限问题解决方案

## 问题描述
部署到Koyeb后，安装环境检测提示"没有上传权限"，需要修改/console目录及其子目录的权限。

## 解决方案

### 1. 已更新的Dockerfile
已修改Dockerfile，添加了完整的权限设置：
- 设置console目录及其子目录权限为777
- 创建必要的upload目录
- 确保所有上传相关的目录都有正确权限

### 2. 重新部署步骤

1. **提交更改到GitHub**
   ```bash
   git add .
   git commit -m "Fix upload permissions for Koyeb deployment"
   git push origin main
   ```

2. **在Koyeb重新部署**
   - 登录Koyeb控制台
   - 找到你的likeyun-ylb服务
   - 点击"Redeploy"重新部署

3. **验证部署**
   - 部署完成后访问应用
   - 进入安装页面检查上传权限是否已解决

### 3. 目录结构确认
确保以下目录存在并设置了正确权限：
- `/var/www/html/console/` - 777权限
- `/var/www/html/console/upload/` - 777权限
- `/var/www/html/static/upload/` - 777权限

## 注意事项
- Koyeb使用容器化部署，权限设置在Dockerfile中完成
- 每次代码更新后需要重新部署才能生效
- 如果问题仍然存在，请检查Koyeb的部署日志