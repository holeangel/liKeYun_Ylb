<?php
// 简单测试脚本放在根目录
require_once __DIR__ . '/console/plugin/app/jumpWX/server/db_config.php';

try {
    $conn = new mysqli(
        $db_config['host'],
        $db_config['username'],
        $db_config['password'],
        $db_config['dbname'],
        $db_config['port']
    );
    
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }
    
    echo "数据库连接成功！";
    $conn->close();
} catch (Exception $e) {
    die("错误: " . $e->getMessage());
}