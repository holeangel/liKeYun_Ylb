<?php
/**
 * 数据库配置文件
 * 集成到现有项目中
 */

// 尝试包含主项目的数据库配置
$main_config_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/config/db.php';

if (file_exists($main_config_path)) {
    // 如果主项目配置文件存在，使用主项目的数据库配置
    include_once $main_config_path;
    
    // 假设主项目配置中有 $db_config 或类似变量
    if (!isset($db_config) && isset($config['db'])) {
        $db_config = $config['db'];
    }
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