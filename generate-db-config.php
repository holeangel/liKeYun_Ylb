<?php
// 数据库配置生成脚本
// 从环境变量读取数据库配置并生成Db.php文件

// 检查是否所有必要的环境变量都存在
$requiredEnvVars = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS'];
foreach ($requiredEnvVars as $var) {
    if (!getenv($var)) {
        error_log("Missing required environment variable: $var");
        exit(1);
    }
}

// 获取环境变量
$db_host = getenv('DB_HOST');
$db_port = getenv('DB_PORT') ?: '3306';
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');

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