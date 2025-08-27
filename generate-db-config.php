<?php
// 数据库配置生成脚本
// 从环境变量读取数据库配置并生成Db.php文件

// 检查是否所有必要的环境变量都存在
// 支持两种环境变量命名方式：RAILWAY_ 前缀和直接DB_前缀
$db_host = getenv('RAILWAY_DB_HOST') ?: getenv('DB_HOST');
$db_port = getenv('RAILWAY_DB_PORT') ?: getenv('DB_PORT') ?: '3306';
$db_name = getenv('RAILWAY_DB_NAME') ?: getenv('DB_NAME');
$db_user = getenv('RAILWAY_DB_USER') ?: getenv('DB_USER');
$db_pass = getenv('RAILWAY_DB_PASSWORD') ?: getenv('DB_PASS');

// 验证必要的环境变量
if (!$db_host || !$db_name || !$db_user || !$db_pass) {
    error_log("Missing required database environment variables");
    error_log("DB_HOST: " . ($db_host ? "set" : "missing"));
    error_log("DB_NAME: " . ($db_name ? "set" : "missing"));
    error_log("DB_USER: " . ($db_user ? "set" : "missing"));
    error_log("DB_PASS: " . ($db_pass ? "set" : "missing"));
    exit(1);
}

// 数据库配置数组
$Db_config = [
    'db_host' => $db_host,
    'db_port' => $db_port,
    'db_name' => $db_name,
    'db_user' => $db_user,
    'db_pass' => $db_pass,
    'db_prefix' => '',
    'folderNum' => '1', // 默认使用根目录级别
    'version' => '2.4.6'
];

// 生成Db.php文件内容
$fileContent = "<?php\n\n";
$fileContent .= "// 数据库操作类\n";
$fileContent .= "include 'DbClass.php';\n\n";
$fileContent .= "// 数据库配置\n";
$fileContent .= '$config = ' . var_export($Db_config, true) . ";\n";
$fileContent .= "?>";

// 写入Db.php文件
$filePath = __DIR__ . '/console/Db.php';
if (file_put_contents($filePath, $fileContent)) {
    echo "Database configuration generated successfully at: $filePath\n";
} else {
    error_log("Failed to write database configuration file: $filePath");
    exit(1);
}

// 设置文件权限
chmod($filePath, 0644);

echo "Database configuration setup completed.\n";
?>