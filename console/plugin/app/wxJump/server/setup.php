<?php
/**
 * 微信QQ跳转插件安装脚本
 * 创建数据库表并更新安装状态
 */

// 启用错误显示
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 检查是否已安装
$appJsonPath = dirname(__DIR__) . '/app.json';
$appConfig = json_decode(file_get_contents($appJsonPath), true);

if ($appConfig['install'] == 2) {
    die(json_encode([
        'code' => 400,
        'msg' => '插件已安装，请勿重复安装'
    ]));
}

// 数据库连接
require_once __DIR__ . '/db_config.php';

// 输出数据库配置信息（仅用于调试，生产环境应移除）
$debug_config = [
    'host' => $db_config['host'],
    'dbname' => $db_config['dbname'],
    'username' => $db_config['username'],
    'port' => $db_config['port'],
    // 不输出密码，安全考虑
];

try {
    // 记录连接尝试
    error_log("尝试连接数据库: {$db_config['host']}:{$db_config['port']}, 数据库: {$db_config['dbname']}");
    
    $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['dbname']};charset=utf8mb4";
    
    $pdo = new PDO(
        $dsn,
        $db_config['username'],
        $db_config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    error_log("数据库连接成功");

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
    error_log("数据表创建成功");

    // 更新安装状态
    $appConfig['install'] = 2;
    file_put_contents($appJsonPath, json_encode($appConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    error_log("插件状态更新成功");

    echo json_encode([
        'code' => 200,
        'msg' => '插件安装成功',
        'debug' => $debug_config
    ]);

} catch (PDOException $e) {
    error_log("数据库错误: " . $e->getMessage());
    echo json_encode([
        'code' => 500,
        'msg' => '数据库错误: ' . $e->getMessage(),
        'debug' => $debug_config
    ]);
}