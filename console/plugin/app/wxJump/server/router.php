<?php
require_once __DIR__.'/../config.php';
$key = $_GET['key'] ?? '';

// 查询数据库
$pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
$stmt = $pdo->prepare("SELECT scheme FROM ylb_jump_links WHERE short_key = ? AND (expire_time > NOW() OR expire_time IS NULL)");
$stmt->execute([$key]);
$result = $stmt->fetch();

if ($result) {
    // 更新访问计数
    $pdo->prepare("UPDATE ylb_jump_links SET visit_count = visit_count + 1 WHERE short_key = ?")->execute([$key]);
    // 执行跳转
    header("Location: {$result['scheme']}");
    exit;
} else {
    http_response_code(404);
    echo "链接不存在或已过期";
}
