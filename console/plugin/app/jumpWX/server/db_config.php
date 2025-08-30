<?php
/**
 * 数据库配置文件
 * 使用Railway数据库配置
 */

// 直接使用Railway数据库配置
$db_config = [
    'host' => 'yamabiko.proxy.rlwy.net',
    'dbname' => 'railway',
    'username' => 'root',
    'password' => 'beAvsMJdVOJoZKTtcvjhPACSXTmPqePr',
    'port' => '11142'
];

error_log("jumpWX插件使用数据库配置: " . json_encode([
    'host' => $db_config['host'],
    'port' => $db_config['port']
]));