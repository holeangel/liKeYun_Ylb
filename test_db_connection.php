<?php
// 测试数据库连接
require_once 'console/plugin/app/wxJump/server/db_config.php';

echo "数据库配置信息:\n";
print_r($db_config);

try {
    $pdo = new PDO(
        "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4",
        $db_config['username'],
        $db_config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "数据库连接成功!\n";
} catch (PDOException $e) {
    echo "数据库连接失败: " . $e->getMessage() . "\n";
}
?>