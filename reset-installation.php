<?php
// 重置安装的PHP脚本
// 将此文件上传到您的Koyeb应用根目录，然后通过浏览器访问它

// 设置错误报告
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>安装重置工具</h1>";

// 检查文件是否存在并尝试删除
$files_to_delete = [
    '/var/www/html/install/install.lock',
    '/var/www/html/console/Db.php'
];

foreach ($files_to_delete as $file) {
    echo "<p>检查文件: $file ... ";
    
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "<strong style='color:green'>已删除</strong></p>";
        } else {
            echo "<strong style='color:red'>删除失败! 权限问题</strong></p>";
            echo "<p>尝试更改权限...</p>";
            
            chmod(dirname($file), 0777);
            
            if (unlink($file)) {
                echo "<p><strong style='color:green'>再次尝试删除成功!</strong></p>";
            } else {
                echo "<p><strong style='color:red'>仍然无法删除。请联系服务器管理员。</strong></p>";
            }
        }
    } else {
        echo "<strong style='color:blue'>文件不存在</strong></p>";
    }
}

// 检查目录权限
$dirs_to_check = [
    '/var/www/html/install',
    '/var/www/html/console',
    '/var/www/html/console/upload',
    '/var/www/html/static/upload'
];

echo "<h2>检查目录权限</h2>";

foreach ($dirs_to_check as $dir) {
    echo "<p>检查目录: $dir ... ";
    
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        echo "当前权限: $perms";
        
        if ($perms != '0777') {
            echo " - <span style='color:orange'>尝试设置为777</span>";
            if (chmod($dir, 0777)) {
                echo " - <strong style='color:green'>成功!</strong>";
            } else {
                echo " - <strong style='color:red'>失败!</strong>";
            }
        } else {
            echo " - <strong style='color:green'>已经是777</strong>";
        }
        
        echo "</p>";
    } else {
        echo "<strong style='color:red'>目录不存在!</strong> 尝试创建...</p>";
        
        if (mkdir($dir, 0777, true)) {
            echo "<p><strong style='color:green'>目录创建成功!</strong></p>";
        } else {
            echo "<p><strong style='color:red'>无法创建目录!</strong></p>";
        }
    }
}

echo "<h2>完成</h2>";
echo "<p>请尝试 <a href='/install/' style='color:blue;font-weight:bold'>重新安装</a> 应用程序。</p>";
?>