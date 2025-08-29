<?php
/**
 * 测试wxJump插件安装
 */

echo "=== wxJump插件安装测试 ===\n\n";

// 1. 检查数据库配置
echo "1. 检查数据库配置...\n";
require_once 'console/plugin/app/wxJump/server/db_config.php';
print_r($db_config);
echo "\n";

// 2. 测试数据库连接
echo "2. 测试数据库连接...\n";
try {
    $pdo = new PDO(
        "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4",
        $db_config['username'],
        $db_config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✓ 数据库连接成功\n";
} catch (PDOException $e) {
    echo "✗ 数据库连接失败: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. 检查安装状态
echo "3. 检查安装状态...\n";
$appJsonPath = 'console/plugin/app/wxJump/app.json';
$appConfig = json_decode(file_get_contents($appJsonPath), true);
echo "当前安装状态: " . $appConfig['install'] . " (1=未安装, 2=已安装)\n";

// 4. 测试安装脚本
echo "4. 测试安装脚本...\n";
ob_start();
include 'console/plugin/app/wxJump/server/setup.php';
$output = ob_get_clean();

$result = json_decode($output, true);
if ($result && isset($result['success'])) {
    if ($result['success']) {
        echo "✓ 安装脚本执行成功: " . $result['message'] . "\n";
    } else {
        echo "✗ 安装脚本执行失败: " . $result['message'] . "\n";
    }
} else {
    echo "✗ 安装脚本输出格式错误\n";
    echo "原始输出: " . $output . "\n";
}

echo "\n=== 测试完成 ===\n";
?>