<?php
function generateWechatScheme($appid, $path) {
    return "weixin://dl/business?".http_build_query([
        'appid' => $appid,
        'path' => urlencode($path),
        't' => substr(md5(uniqid()), 0, 8)
    ]);
}