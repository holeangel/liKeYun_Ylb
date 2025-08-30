<?php
require_once __DIR__ . '/db_config.php';

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