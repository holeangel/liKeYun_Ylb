<?php
/**
 * 微信QQ跳转插件安装脚本
 * 创建数据库表并更新安装状态
 */

// 检查是否已安装
$appJsonPath = dirname(__DIR__) . '/app.json';
$appConfig = json_decode(file_get_contents($appJsonPath), true);

if ($appConfig['install'] == 2) {
    die('插件已安装，请勿重复安装');
}

// 数据库连接
require_once __DIR__ . '/db_config.php';

try {
    $pdo = new PDO(
        "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4",
        $db_config['username'],
        $db_config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // 创建跳转链接表
    $sql = "CREATE TABLE IF NOT EXISTS `ylb_jump_links` (
        `id` INT AUTO_INCREMENT,
        `type` ENUM('wechat','qq') NOT NULL,
        `scheme` VARCHAR(255) NOT NULL,
        `short_key` CHAR(8) UNIQUE,
        `expire_time` DATETIME NULL,
        `visit_count` INT DEFAULT 0,
        `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);
    
    // 更新安装状态
    $appConfig['install'] = 2;
    file_put_contents($appJsonPath, json_encode($appConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo json_encode([
        'success' => true,
        'message' => '插件安装成功'
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => '数据库错误: ' . $e->getMessage()
    ]);
}