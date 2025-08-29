// 微信QQ跳转插件前端逻辑
$(document).ready(function() {
    // 初始化
    loadLinksList();
    
    // 微信跳转表单提交
    $('#wechatForm').on('submit', function(e) {
        e.preventDefault();
        
        const appid = $('#appid').val();
        const path = $('#path').val();
        const expire = $('#wechatExpire').val();
        
        if (!appid || !path) {
            alert('请填写完整信息');
            return;
        }
        
        $.ajax({
            url: 'server/api.php',
            type: 'POST',
            data: {
                action: 'create',
                type: 'wechat',
                appid: appid,
                path: path,
                expire: expire
            },
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        showResult(data.shortUrl, data.scheme);
                        loadLinksList(); // 刷新列表
                    } else {
                        alert('生成失败: ' + data.message);
                    }
                } catch (e) {
                    alert('响应格式错误');
                    console.error(e);
                }
            },
            error: function() {
                alert('请求失败，请检查网络连接');
            }
        });
    });
    
    // QQ跳转表单提交
    $('#qqForm').on('submit', function(e) {
        e.preventDefault();
        
        const type = $('#qqType').val();
        const id = $('#qqId').val();
        const expire = $('#qqExpire').val();
        
        if (!id) {
            alert('请填写QQ号或群号');
            return;
        }
        
        $.ajax({
            url: 'server/api.php',
            type: 'POST',
            data: {
                action: 'create',
                type: 'qq',
                qqType: type,
                qqId: id,
                expire: expire
            },
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        showResult(data.shortUrl, data.scheme);
                        loadLinksList(); // 刷新列表
                    } else {
                        alert('生成失败: ' + data.message);
                    }
                } catch (e) {
                    alert('响应格式错误');
                    console.error(e);
                }
            },
            error: function() {
                alert('请求失败，请检查网络连接');
            }
        });
    });
    
    // 刷新链接列表
    $('#refreshList').on('click', function() {
        loadLinksList();
    });
    
    // 复制到剪贴板功能
    window.copyToClipboard = function(elementId) {
        const element = document.getElementById(elementId);
        element.select();
        document.execCommand('copy');
        alert('已复制到剪贴板');
    };
});

// 显示生成结果
function showResult(shortUrl, scheme) {
    $('#shortUrl').val(shortUrl);
    $('#schemeUrl').val(scheme);
    $('#resultArea').removeClass('d-none');
    
    // 生成二维码
    $('#qrcode').empty();
    new QRCode(document.getElementById('qrcode'), {
        text: shortUrl,
        width: 128,
        height: 128
    });
}

// 加载链接列表
function loadLinksList() {
    $.ajax({
        url: 'server/api.php',
        type: 'GET',
        data: { action: 'list' },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if (data.success) {
                    renderLinksList(data.links);
                } else {
                    alert('获取列表失败: ' + data.message);
                }
            } catch (e) {
                console.error('解析响应失败', e);
            }
        },
        error: function() {
            console.error('请求失败');
        }
    });
}

// 渲染链接列表
function renderLinksList(links) {
    const tbody = $('#linksList');
    tbody.empty();
    
    if (links.length === 0) {
        tbody.append('<tr><td colspan="5" class="text-center">暂无数据</td></tr>');
        return;
    }
    
    links.forEach(function(link) {
        const expireText = link.expire_time ? new Date(link.expire_time).toLocaleString() : '永久有效';
        const typeText = link.type === 'wechat' ? '微信' : 'QQ';
        
        const row = `
            <tr>
                <td>${typeText}</td>
                <td><a href="${link.short_url}" target="_blank">${link.short_key}</a></td>
                <td>${link.visit_count}</td>
                <td>${expireText}</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="deleteLink(${link.id})">删除</button>
                </td>
            </tr>
        `;
        
        tbody.append(row);
    });
}

// 删除链接
function deleteLink(id) {
    if (confirm('确定要删除此链接吗？')) {
        $.ajax({
            url: 'server/api.php',
            type: 'POST',
            data: {
                action: 'delete',
                id: id
            },
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        loadLinksList(); // 刷新列表
                    } else {
                        alert('删除失败: ' + data.message);
                    }
                } catch (e) {
                    alert('响应格式错误');
                }
            },
            error: function() {
                alert('请求失败，请检查网络连接');
            }
        });
    }
}
