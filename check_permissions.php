<?php
// 权限检查脚本 - 部署后验证用
$dirs = [
    '/var/www/html/console',
    '/var/www/html/console/upload',
    '/var/www/html/static/upload',
    '/var/www/html/common',
    '/var/www/html/s',
    '/var/www/html/install'
];

echo "=== 权限检查报告 ===\n";
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $perms = fileperms($dir);
        $readable = is_readable($dir) ? '可读' : '不可读';
        $writable = is_writable($dir) ? '可写' : '不可写';
        
        echo "目录: $dir\n";
        echo "权限: " . substr(sprintf('%o', $perms), -4) . "\n";
        echo "状态: $readable, $writable\n";
        echo "---\n";
    } else {
        echo "目录不存在: $dir\n";
    }
}

// 测试上传功能
$testFile = '/var/www/html/console/upload/test.txt';
if (file_put_contents($testFile, "权限测试文件")) {
    echo "上传测试: 成功\n";
    unlink($testFile);
} else {
    echo "上传测试: 失败\n";
}
?>