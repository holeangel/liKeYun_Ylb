# 引流宝(YLB)项目需求文档

## 项目概述

引流宝(YLB)是一个多功能营销引流工具平台，旨在帮助用户通过多种渠道进行引流、获客和转化。该平台集成了多种营销工具，包括短链接、客服码、群活码、渠道码、分享卡片等功能，以及插件扩展系统。
## 我的描述
我用github管理代码。用koyeb部署项目。用railway做数据库，mysql

我的数据库mysql://root:beAvsMJdVOJoZKTtcvjhPACSXTmPqePr@yamabiko.proxy.rlwy.net:11142/railway

我的项目github：https://github.com/holeangel/liKeYun_Ylb
我的本地项目：D:\project2025\liKeYun_ylb-main
我的koyeb项目的环境变量是：
RAILWAY_DB_HOST=yamabiko.proxy.rlwy.net
RAILWAY_DB_NAME=railway
RAILWAY_DB_PASSWORD=beAvsMJdVOJoZKTtcvjhPACSXTmPqePr
RAILWAY_DB_PORT=11142
RAILWAY_DB_USER=root
。我们的项目已经部署在koyeb上了。这是前端的地址https://wily-eleni-holeangel-ce7dc357.koyeb.app


## 核心功能模块

### 1. 短链接(DWZ)
- 将长链接转换为短链接，便于分享和传播
- 支持链接访问统计和分析
- 支持链接有效期设置
- 支持自定义短链接关键词

### 2. 客服码(KF)
- 生成带参数的客服二维码
- 支持多客服轮询分配
- 支持访问统计和分析
- 支持设置工作时间和非工作时间不同的应对策略

### 3. 群活码(QUN)
- 支持微信群二维码管理和轮询展示
- 支持设置群容量和自动切换
- 支持访问统计和分析
- 支持设置群满后的提示信息

### 4. 渠道码(CHANNEL)
- 生成不同渠道的推广二维码
- 支持渠道数据统计和分析
- 支持渠道参数自定义

### 5. 分享卡片(SHARECARD)
- 生成可分享的营销卡片
- 支持自定义卡片样式和内容
- 支持访问统计和分析

### 6. 素材管理(SUCAI)
- 上传和管理营销素材
- 支持素材分类和标签
- 支持素材快速调用

### 7. 淘宝客(TBK)
- 淘宝客链接生成和管理
- 支持佣金统计和分析
- 支持商品推广

### 8. 卡密系统(KAMI)
- 生成和管理卡密
- 支持卡密激活和验证
- 支持卡密统计和分析

### 9. 插件系统(PLUGIN)
- 支持第三方插件安装和管理
- 提供插件开发SDK
- 当前已有插件：
  - 微信跳转插件(wxJump)：实现微信内部跳转到外部链接
  - QQ跳转插件：实现QQ内部跳转到外部链接
  - 框架代理插件(FrameProxy)：实现网页框架代理
  - 插件SDK：用于开发新插件

### 10. 用户系统(USER)
- 用户注册和登录
- 用户权限管理
- 用户操作日志

### 11. 数据统计(INDEX)
- 访问量统计和分析
- IP统计和分析
- 各功能模块使用情况统计

## 技术架构

### 前端
- HTML5 + CSS3 + JavaScript
- jQuery + Bootstrap框架
- Chart.js用于数据可视化

### 后端
- PHP 7.4+
- MySQL数据库
- PDO数据库操作

### 服务器要求
- Apache/Nginx
- PHP 7.4+
- MySQL 5.6+
- 支持SSL证书

## 数据库结构

主要数据表包括：
- huoma_user：用户表
- huoma_dwz：短链接表
- huoma_kf：客服码表
- huoma_qun：群活码表
- huoma_channel：渠道码表
- huoma_sharecard：分享卡片表
- huoma_sucai：素材表
- huoma_tbk：淘宝客表
- huoma_kami：卡密表
- huoma_ip：IP统计表
- ylb_jump_links：微信跳转链接表(插件)

## 部署环境

当前项目部署在Koyeb平台，访问地址：
- https://wily-eleni-holeangel-ce7dc357.koyeb.app/

## 开发规范

### 代码规范
- 使用UTF-8编码
- PHP文件使用<?php开头
- 类名使用大驼峰命名法
- 方法和变量使用小驼峰命名法
- 常量使用全大写加下划线
- 注释清晰，关键功能必须有注释

### 目录结构
- console/：后台管理系统
- common/：前台展示页面
- static/：静态资源
- s/：短链接和跳转处理
- install/：安装程序
- FrameBridge/：框架桥接
- wailian/：外链处理

### 插件开发规范
- 插件放置在console/plugin/app/目录下
- 每个插件必须有app.json配置文件
- 插件必须有logo.png图标
- 插件安装脚本放在server/setup.php
- 插件必须实现标准接口

## 未来规划

1. 增强数据分析能力，提供更详细的数据报表
2. 优化移动端体验
3. 增加更多第三方平台集成
4. 提升系统安全性和稳定性
5. 开发更多实用插件
6. 优化用户界面和交互体验



## 项目目前的问题是
我们现在要进行插件开发wxJump，现在整个插件有问题，就是在点击安装插件后就没有反应了。

## 版本信息

当前版本：2.4.6