<?php
/**
 * 跳转路由器
 * 处理短链接跳转请求
 */

// 获取短链接key
$shortKey = isset($_GET['key']) ? $_GET['key'] : '';

if (empty($shortKey)) {
    header('HTTP/1.1 404 Not Found');
    echo '链接无效';
    exit;
}

// 数据库连接
require_once __DIR__ . '/db_config.php';

try {
    $pdo = new PDO(
        "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4",
        $db_config['username'],
        $db_config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // 查询链接
    $stmt = $pdo->prepare("SELECT * FROM ylb_jump_links WHERE short_key = ?");
    $stmt->execute([$shortKey]);
    $link = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$link) {
        header('HTTP/1.1 404 Not Found');
        echo '链接不存在或已失效';
        exit;
    }
    
    // 检查是否过期
    if ($link['expire_time'] && strtotime($link['expire_time']) < time()) {
        header('HTTP/1.1 410 Gone');
        echo '链接已过期';
        exit;
    }
    
    // 更新访问计数
    $stmt = $pdo->prepare("UPDATE ylb_jump_links SET visit_count = visit_count + 1 WHERE id = ?");
    $stmt->execute([$link['id']]);
    
    // 跳转到scheme
    $scheme = $link['scheme'];
    
    // 输出跳转页面
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>跳转中...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            margin-bottom: 25px;
        }
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .countdown {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>' . ($link['type'] == 'wechat' ? '微信' : 'QQ') . '跳转</h1>
        <p>正在准备跳转，请点击下方按钮继续</p>
        <div class="countdown" id="countdown">3</div>
        <a href="' . htmlspecialchars($scheme) . '" class="btn" id="jumpBtn">立即跳转</a>
    </div>
    
    <script>
        // 倒计时自动跳转
        let count = 3;
        const countdownEl = document.getElementById("countdown");
        const jumpBtn = document.getElementById("jumpBtn");
        const scheme = "' . addslashes($scheme) . '";
        
        const timer = setInterval(() => {
            count--;
            countdownEl.textContent = count;
            
            if (count <= 0) {
                clearInterval(timer);
                window.location.href = scheme;
            }
        }, 1000);
        
        // 点击按钮立即跳转
        jumpBtn.addEventListener("click", function(e) {
            clearInterval(timer);
        });
    </script>
</body>
</html>';
    
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo '服务器错误: ' . $e->getMessage();
}