<?php
/**
 * 数据库配置文件
 * 集成到现有项目中
 */

// 尝试包含主项目的数据库配置
$main_config_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/console/Db.php';

if (file_exists($main_config_path)) {
    // 包含主项目的数据库配置文件
    include_once $main_config_path;
    
    // 使用主项目的数据库配置
    $db_config = [
        'host' => $config['db_host'] ?? 'localhost',
        'dbname' => $config['db_name'] ?? 'ylb_db',
        'username' => $config['db_user'] ?? 'root',
        'password' => $config['db_pass'] ?? '',
        'port' => $config['db_port'] ?? '3306'
    ];
} else {
    // 如果找不到主项目配置，使用默认配置或环境变量
    $db_config = [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'dbname' => getenv('DB_NAME') ?: 'ylb_db',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'port' => getenv('DB_PORT') ?: '3306'
    ];
}
