// 微信QQ跳转插件入口文件
// 检查当前路径并重定向到主页面
(function() {
    // 获取当前路径
    var currentPath = window.location.pathname;
    
    // 如果当前路径是插件入口路径，则重定向到index.html
    if (currentPath.includes('/app/wxJump') && !currentPath.endsWith('/index.html')) {
        // 保留查询参数
        var searchParams = window.location.search;
        window.location.href = 'index.html' + searchParams;
    }
})();