<?php
// 安装脚本
// 检查是否已安装
$appJsonPath = __DIR__ . '/../app.json';
$appJson = json_decode(file_get_contents($appJsonPath), true);

if ($appJson['install'] == 2) {
    echo json_encode([
        'success' => false,
        'message' => '插件已安装'
    ]);
    exit;
}

// 创建数据库表
try {
    require_once __DIR__.'/../config.php';
    
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 创建表
    $pdo->exec("CREATE TABLE IF NOT EXISTS ylb_jump_links (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type ENUM('wechat','qq') NOT NULL,
        scheme VARCHAR(255) NOT NULL,
        short_key CHAR(8) UNIQUE,
        expire_time DATETIME NULL,
        visit_count INT DEFAULT 0
    )");
    
    // 更新安装状态
    $appJson['install'] = 2;
    file_put_contents($appJsonPath, json_encode($appJson, JSON_PRETTY_PRINT));
    
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