<?php
/**
 * QQ跳转链接生成器
 */

/**
 * 生成QQ群跳转Scheme
 * 
 * @param string $groupId QQ群号
 * @return string 生成的scheme
 */
function generateQQGroupScheme($groupId) {
    return "qq://group/join?key={$groupId}";
}

/**
 * 生成QQ个人聊天跳转Scheme
 * 
 * @param string $qqId QQ号
 * @return string 生成的scheme
 */
function generateQQChatScheme($qqId) {
    return "qq://im/msg?uin={$qqId}";
}

/**
 * 创建QQ跳转链接
 * 
 * @param string $type 类型：group(群)或user(个人)
 * @param string $id QQ群号或QQ号
 * @param int $expire 有效期（天数，0表示永久有效）
 * @return array 包含scheme和short_key的数组
 */
function createQQLink($type, $id, $expire = 0) {
    require_once __DIR__ . '/../db_config.php';
    
    try {
        $pdo = new PDO(
            "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4",
            $db_config['username'],
            $db_config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // 生成scheme
        $scheme = $type === 'group' ? generateQQGroupScheme($id) : generateQQChatScheme($id);
        
        // 生成短链接key
        $shortKey = substr(md5(uniqid() . $type . $id), 0, 8);
        
        // 计算过期时间
        $expireTime = null;
        if ($expire > 0) {
            $expireTime = date('Y-m-d H:i:s', strtotime("+{$expire} days"));
        }
        
        // 插入数据库
        $stmt = $pdo->prepare("INSERT INTO ylb_jump_links (type, scheme, short_key, expire_time) VALUES (?, ?, ?, ?)");
        $stmt->execute(['qq', $scheme, $shortKey, $expireTime]);
        
        return [
            'success' => true,
            'scheme' => $scheme,
            'short_key' => $shortKey
        ];
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => '数据库错误: ' . $e->getMessage()
        ];
    }
}