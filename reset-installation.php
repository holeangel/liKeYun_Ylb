<?php
// 重置安装的PHP脚本
// 将此文件上传到您的Koyeb应用根目录，然后通过浏览器访问它

// 设置错误报告
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>安装重置工具</h1>";

// 使用系统命令直接删除文件（更强大的方法）
echo "<h2>尝试使用系统命令删除文件</h2>";

$commands = [
    'rm -f /var/www/html/install/install.lock',
    'rm -f /var/www/html/console/Db.php',
    'touch /var/www/html/install/test_write_permission.txt',
    'touch /var/www/html/console/test_write_permission.txt'
];

foreach ($commands as $command) {
    echo "<p>执行: <code>$command</code> ... ";
    $output = [];
    $return_var = 0;
    exec($command, $output, $return_var);
    
    if ($return_var === 0) {
        echo "<strong style='color:green'>成功</strong></p>";
    } else {
        echo "<strong style='color:red'>失败 (代码: $return_var)</strong></p>";
        echo "<p>输出: " . implode("<br>", $output) . "</p>";
    }
}

// 检查文件是否存在并尝试删除（PHP方法）
echo "<h2>使用PHP方法检查和删除文件</h2>";

$files_to_delete = [
    '/var/www/html/install/install.lock',
    '/var/www/html/console/Db.php'
];

foreach ($files_to_delete as $file) {
    echo "<p>检查文件: $file ... ";
    
    if (file_exists($file)) {
        echo "<strong style='color:red'>文件仍然存在!</strong> 尝试删除... ";
        
        if (unlink($file)) {
            echo "<strong style='color:green'>已删除</strong></p>";
        } else {
            echo "<strong style='color:red'>删除失败! 权限问题</strong></p>";
            echo "<p>尝试更改权限...</p>";
            
            chmod(dirname($file), 0777);
            
            if (unlink($file)) {
                echo "<p><strong style='color:green'>再次尝试删除成功!</strong></p>";
            } else {
                echo "<p><strong style='color:red'>仍然无法删除。</strong></p>";
            }
        }
    } else {
        echo "<strong style='color:green'>文件不存在 (已成功删除或从未存在)</strong></p>";
    }
}

// 检查和设置目录权限
echo "<h2>检查和设置目录权限</h2>";

// 使用系统命令设置权限
$permission_commands = [
    'chmod -R 777 /var/www/html/install',
    'chmod -R 777 /var/www/html/console',
    'chmod -R 777 /var/www/html/console/upload',
    'chmod -R 777 /var/www/html/static/upload',
    'mkdir -p /var/www/html/console/upload',
    'mkdir -p /var/www/html/static/upload',
    'chmod -R 777 /var/www/html/console/upload',
    'chmod -R 777 /var/www/html/static/upload'
];

foreach ($permission_commands as $command) {
    echo "<p>执行: <code>$command</code> ... ";
    $output = [];
    $return_var = 0;
    exec($command, $output, $return_var);
    
    if ($return_var === 0) {
        echo "<strong style='color:green'>成功</strong></p>";
    } else {
        echo "<strong style='color:red'>失败 (代码: $return_var)</strong></p>";
        echo "<p>输出: " . implode("<br>", $output) . "</p>";
    }
}

// 检查目录权限（PHP方法）
$dirs_to_check = [
    '/var/www/html/install',
    '/var/www/html/console',
    '/var/www/html/console/upload',
    '/var/www/html/static/upload'
];

echo "<h2>检查目录权限（PHP方法）</h2>";

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

// 检查文件是否可写
echo "<h2>检查文件写入权限</h2>";
$test_files = [
    '/var/www/html/install/test_write_permission.txt',
    '/var/www/html/console/test_write_permission.txt'
];

foreach ($test_files as $file) {
    echo "<p>检查文件: $file ... ";
    if (file_exists($file)) {
        if (is_writable($file)) {
            echo "<strong style='color:green'>可写</strong></p>";
        } else {
            echo "<strong style='color:red'>不可写</strong></p>";
        }
    } else {
        echo "<strong style='color:red'>文件不存在</strong></p>";
    }
}

echo "<h2>完成</h2>";
echo "<p>请尝试 <a href='/install/' style='color:blue;font-weight:bold'>重新安装</a> 应用程序。</p>";
echo "<p>如果仍然遇到问题，请尝试重启Koyeb应用。</p>";
?>