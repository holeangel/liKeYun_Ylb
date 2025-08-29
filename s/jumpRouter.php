<?php
/**
 * 跳转路由器入口
 * 用于处理短链接跳转请求
 */

// 获取短链接key
$shortKey = isset($_GET['key']) ? $_GET['key'] : '';

if (empty($shortKey)) {
    header('HTTP/1.1 404 Not Found');
    echo '链接无效';
    exit;
}

// 包含主路由器文件
require_once __DIR__ . '/../console/plugin/app/wxJump/server/router.php';