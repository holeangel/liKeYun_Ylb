<?php
/**
 * 微信跳转链接生成器
 * 使用官方API方式实现，更安全可靠
 */

/**
 * 获取微信小程序 access_token
 * 
 * @param string $appid 小程序AppID
 * @param string $secret 小程序AppSecret
 * @return string|null 获取到的access_token或null
 */
function getAccessToken($appid, $secret) {
    $cacheFile = __DIR__ . '/../cache/access_token_' . $appid . '.php';
    
    // 确保缓存目录存在
    if (!file_exists(__DIR__ . '/../cache')) {
        mkdir(__DIR__ . '/../cache', 0755, true);
    }

    // 检查缓存文件是否存在且有效
    if (file_exists($cacheFile)) {
        $accessTokenData = include $cacheFile;
        if ($accessTokenData['expires_in'] > time()) {
            return $accessTokenData['access_token'];
        }
    }

    // 如果缓存无效或已过期，获取新的token
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";

    // 初始化cURL会话
    $ch = curl_init($url);

    // 设置cURL选项
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // 执行cURL请求并获取响应
    $response = curl_exec($ch);

    // 关闭cURL会话
    curl_close($ch);

    // 解码JSON响应
    $data = json_decode($response, true);

    // 检查是否成功获取access_token
    if (isset($data['access_token'])) {
        // 将access_token和过期时间存储在PHP文件中
        $accessTokenData = [
            'access_token' => $data['access_token'],
            'expires_in' => time() + $data['expires_in']
        ];
        file_put_contents($cacheFile, '<?php return ' . var_export($accessTokenData, true) . ';');
        return $data['access_token'];
    } else {
        // 获取access_token失败
        error_log("获取access_token失败: " . json_encode($data));
        return null;
    }
}

/**
 * 使用官方API生成微信小程序跳转链接
 * 
 * @param string $appid 小程序AppID
 * @param string $path 小程序路径
 * @param string $query 查询参数
 * @param string $env_version 环境版本
 * @return string|null 生成的scheme或null
 */
function generateWechatScheme($appid, $secret, $path, $query = "", $env_version = "release") {
    $accessToken = getAccessToken($appid, $secret);

    if ($accessToken !== null) {
        // 包含access_token的API端点URL
        $url = "https://api.weixin.qq.com/wxa/generatescheme?access_token=$accessToken";

        // POST请求的数据
        $data = [
            "jump_wxa" => [
                "path" => $path,
                "query" => $query,
                "env_version" => $env_version
            ]
        ];

        // 将数据编码为JSON格式
        $jsonData = json_encode($data);

        // 初始化cURL会话
        $ch = curl_init($url);

        // 设置cURL选项
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);

        // 执行cURL请求并获取响应
        $response = curl_exec($ch);

        // 关闭cURL会话
        curl_close($ch);

        // 解析响应
        $result = json_decode($response, true);
        
        if (isset($result['errcode']) && $result['errcode'] == 0 && isset($result['openlink'])) {
            return $result['openlink'];
        } else {
            error_log("生成scheme失败: " . $response);
            return null;
        }
    } else {
        return null;
    }
}

/**
 * 创建微信跳转链接
 * 
 * @param string $appid 小程序AppID
 * @param string $path 小程序路径
 * @param int $expire 有效期（天数，0表示永久有效）
 * @return array 包含scheme和short_key的数组
 */
function createWechatLink($appid, $path, $expire = 0, $secret = null, $query = "") {
    require_once __DIR__ . '/../db_config.php';
    
    try {
        $pdo = new PDO(
            "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4",
            $db_config['username'],
            $db_config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // 生成scheme
        if ($secret) {
            // 使用官方API生成scheme
            $scheme = generateWechatScheme($appid, $secret, $path, $query);
            if (!$scheme) {
                // 如果官方API失败，回退到URL Scheme方式
                $scheme = "weixin://dl/business?" . http_build_query([
                    'appid' => $appid,
                    'path' => urlencode($path),
                    't' => substr(md5(uniqid()), 0, 8)
                ]);
            }
        } else {
            // 使用URL Scheme方式
            $scheme = "weixin://dl/business?" . http_build_query([
                'appid' => $appid,
                'path' => urlencode($path),
                't' => substr(md5(uniqid()), 0, 8)
            ]);
        }
        
        // 生成短链接key
        $shortKey = substr(md5(uniqid() . $appid . $path), 0, 8);
        
        // 计算过期时间
        $expireTime = null;
        if ($expire > 0) {
            $expireTime = date('Y-m-d H:i:s', strtotime("+{$expire} days"));
        }
        
        // 插入数据库
        $stmt = $pdo->prepare("INSERT INTO ylb_jump_links (type, scheme, short_key, expire_time) VALUES (?, ?, ?, ?)");
        $stmt->execute(['wechat', $scheme, $shortKey, $expireTime]);
        
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