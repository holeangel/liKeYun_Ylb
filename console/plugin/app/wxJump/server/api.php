<?php
require_once __DIR__.'/../config.php';
require_once __DIR__.'/generators/wechat.php';
require_once __DIR__.'/generators/qq.php';

// 设置响应头
header('Content-Type: application/json');

// 连接数据库
try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 确保表存在
    $pdo->exec("CREATE TABLE IF NOT EXISTS ylb_jump_links (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type ENUM('wechat','qq') NOT NULL,
        scheme VARCHAR(255) NOT NULL,
        short_key CHAR(8) UNIQUE,
        expire_time DATETIME NULL,
        visit_count INT DEFAULT 0
    )");
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => '数据库连接失败: ' . $e->getMessage()
    ]);
    exit;
}

// 获取当前域名
function getCurrentDomain() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    return $protocol . $_SERVER['HTTP_HOST'];
}

// 生成短链接密钥
function generateShortKey() {
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $key = '';
    for ($i = 0; $i < 8; $i++) {
        $key .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $key;
}

// 处理API请求
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'create':
        createLink();
        break;
    case 'list':
        listLinks();
        break;
    case 'delete':
        deleteLink();
        break;
    default:
        echo json_encode([
            'success' => false,
            'message' => '未知操作'
        ]);
}

// 创建链接
function createLink() {
    global $pdo;
    
    $type = $_POST['type'] ?? '';
    $scheme = '';
    $expireTime = null;
    
    // 计算过期时间
    if (!empty($_POST['expire']) && $_POST['expire'] > 0) {
        $expireDays = (int)$_POST['expire'];
        $expireTime = date('Y-m-d H:i:s', strtotime("+{$expireDays} days"));
    }
    
    // 根据类型生成scheme
    if ($type === 'wechat') {
        $appid = $_POST['appid'] ?? '';
        $path = $_POST['path'] ?? '';
        
        if (empty($appid) || empty($path)) {
            echo json_encode([
                'success' => false,
                'message' => '参数不完整'
            ]);
            return;
        }
        
        $scheme = generateWechatScheme($appid, $path);
    } elseif ($type === 'qq') {
        $qqType = $_POST['qqType'] ?? 'user';
        $qqId = $_POST['qqId'] ?? '';
        
        if (empty($qqId)) {
            echo json_encode([
                'success' => false,
                'message' => '参数不完整'
            ]);
            return;
        }
        
        $scheme = generateQQScheme($qqType, $qqId);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '不支持的类型'
        ]);
        return;
    }
    
    // 生成短链接密钥
    $shortKey = generateShortKey();
    
    // 检查短链接是否已存在
    $stmt = $pdo->prepare("SELECT id FROM ylb_jump_links WHERE short_key = ?");
    $stmt->execute([$shortKey]);
    
    if ($stmt->rowCount() > 0) {
        // 如果已存在，重新生成
        $shortKey = generateShortKey();
    }
    
    // 插入数据库
    try {
        $stmt = $pdo->prepare("INSERT INTO ylb_jump_links (type, scheme, short_key, expire_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$type, $scheme, $shortKey, $expireTime]);
        
        $domain = getCurrentDomain();
        $shortUrl = "{$domain}/jump/{$shortKey}";
        
        echo json_encode([
            'success' => true,
            'shortKey' => $shortKey,
            'shortUrl' => $shortUrl,
            'scheme' => $scheme
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => '保存失败: ' . $e->getMessage()
        ]);
    }
}

// 获取链接列表
function listLinks() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM ylb_jump_links ORDER BY id DESC");
        $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $domain = getCurrentDomain();
        foreach ($links as &$link) {
            $link['short_url'] = "{$domain}/jump/{$link['short_key']}";
        }
        
        echo json_encode([
            'success' => true,
            'links' => $links
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => '获取列表失败: ' . $e->getMessage()
        ]);
    }
}

// 删除链接
function deleteLink() {
    global $pdo;
    
    $id = $_POST['id'] ?? 0;
    
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => '参数不完整'
        ]);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM ylb_jump_links WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'success' => true,
            'message' => '删除成功'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => '删除失败: ' . $e->getMessage()
        ]);
    }
}