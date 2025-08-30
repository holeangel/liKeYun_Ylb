<?php
/**
 * 数据库配置文件
 * 集成到现有项目中
 */

// 调试：输出当前环境变量
error_log("RAILWAY_DB_HOST: " . getenv('RAILWAY_DB_HOST'));
error_log("RAILWAY_DB_NAME: " . getenv('RAILWAY_DB_NAME'));
error_log("RAILWAY_DB_USER: " . getenv('RAILWAY_DB_USER'));
error_log("RAILWAY_DB_PORT: " . getenv('RAILWAY_DB_PORT'));

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
        'host' => getenv('RAILWAY_DB_HOST') ?: 'localhost',
        'dbname' => getenv('RAILWAY_DB_NAME') ?: 'ylb_db',
        'username' => getenv('RAILWAY_DB_USER') ?: 'root',
        'password' => getenv('RAILWAY_DB_PASSWORD') ?: '',
        'port' => getenv('RAILWAY_DB_PORT') ?: '3306'
    ];
    
    // 调试：输出最终使用的数据库配置
    error_log("最终数据库配置: " . json_encode([
        'host' => $db_config['host'],
        'dbname' => $db_config['dbname'],
        'username' => $db_config['username'],
        'port' => $db_config['port']
    ]));
}
