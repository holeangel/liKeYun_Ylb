<?php
/**
 * API处理文件
 * 处理前端AJAX请求
 */

header('Content-Type: application/json');

// 获取操作类型
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// 根据操作类型处理请求
switch ($action) {
    case 'create':
        handleCreate();
        break;
    case 'list':
        handleList();
        break;
    case 'delete':
        handleDelete();
        break;
    default:
        echo json_encode([
            'success' => false,
            'message' => '未知操作'
        ]);
}

/**
 * 处理创建链接请求
 */
function handleCreate() {
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    
    if ($type === 'wechat') {
        $appid = isset($_POST['appid']) ? $_POST['appid'] : '';
        $secret = isset($_POST['secret']) ? $_POST['secret'] : '';
        $path = isset($_POST['path']) ? $_POST['path'] : '';
        $query = isset($_POST['query']) ? $_POST['query'] : '';
        $expire = isset($_POST['expire']) ? intval($_POST['expire']) : 0;
        $use_official_api = isset($_POST['use_official_api']) ? (bool)$_POST['use_official_api'] : true;
        
        if (empty($appid) || empty($path)) {
            echo json_encode([
                'success' => false,
                'message' => '参数不完整'
            ]);
            return;
        }
        
        require_once __DIR__ . '/generators/wechat.php';
        
        // 如果选择使用官方API但没有提供secret，返回错误
        if ($use_official_api && empty($secret)) {
            echo json_encode([
                'success' => false,
                'message' => '使用官方API需要提供AppSecret'
            ]);
            return;
        }
        
        // 创建链接
        $result = $use_official_api 
            ? createWechatLink($appid, $path, $expire, $secret, $query)
            : createWechatLink($appid, $path, $expire);
        
        if ($result['success']) {
            // 生成完整短链接URL
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $shortUrl = $protocol . $host . '/jump/' . $result['short_key'];
            
            echo json_encode([
                'success' => true,
                'scheme' => $result['scheme'],
                'shortUrl' => $shortUrl,
                'shortKey' => $result['short_key']
            ]);
        } else {
            echo json_encode($result);
        }
    } 
    else if ($type === 'qq') {
        $qqType = isset($_POST['qqType']) ? $_POST['qqType'] : '';
        $qqId = isset($_POST['qqId']) ? $_POST['qqId'] : '';
        $expire = isset($_POST['expire']) ? intval($_POST['expire']) : 0;
        
        if (empty($qqType) || empty($qqId)) {
            echo json_encode([
                'success' => false,
                'message' => '参数不完整'
            ]);
            return;
        }
        
        require_once __DIR__ . '/generators/qq.php';
        $result = createQQLink($qqType, $qqId, $expire);
        
        if ($result['success']) {
            // 生成完整短链接URL
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $shortUrl = $protocol . $host . '/jump/' . $result['short_key'];
            
            echo json_encode([
                'success' => true,
                'scheme' => $result['scheme'],
                'shortUrl' => $shortUrl,
                'shortKey' => $result['short_key']
            ]);
        } else {
            echo json_encode($result);
        }
    } 
    else {
        echo json_encode([
            'success' => false,
            'message' => '无效的链接类型'
        ]);
    }
}

/**
 * 处理获取链接列表请求
 */
function handleList() {
    require_once __DIR__ . '/db_config.php';
    
    try {
        $pdo = new PDO(
            "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4",
            $db_config['username'],
            $db_config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // 查询所有链接
        $stmt = $pdo->query("SELECT * FROM ylb_jump_links ORDER BY create_time DESC");
        $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 添加短链接URL
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        
        foreach ($links as &$link) {
            $link['short_url'] = $protocol . $host . '/jump/' . $link['short_key'];
        }
        
        echo json_encode([
            'success' => true,
            'links' => $links
        ]);
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => '数据库错误: ' . $e->getMessage()
        ]);
    }
}

/**
 * 处理删除链接请求
 */
function handleDelete() {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => '无效的ID'
        ]);
        return;
    }
    
    require_once __DIR__ . '/db_config.php';
    
    try {
        $pdo = new PDO(
            "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4",
            $db_config['username'],
            $db_config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // 删除链接
        $stmt = $pdo->prepare("DELETE FROM ylb_jump_links WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'success' => true,
            'message' => '删除成功'
        ]);
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => '数据库错误: ' . $e->getMessage()
        ]);
    }
}