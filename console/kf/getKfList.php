<?php

	// 页面编码
	header("Content-type:application/json");
	
	// 判断登录状态
    session_start();
    if(isset($_SESSION["yinliubao"])){
        
        // 已登录
        // 接收参数
    	@$page = $_GET['p']?$_GET['p']:1;
    	
        // 当前登录的用户
        $LoginUser = $_SESSION["yinliubao"];
        
        // 数据库配置
    	include '../Db.php';
    
    	// 实例化类
    	$db = new DB_API($config);
    	
        // 2.4.5版本新增
        // 客服码去重
        // 加一个 kf_qc 字段
    	$checkExitsSQL = "SHOW COLUMNS FROM huoma_kf LIKE 'kf_qc'";
        $checkExits = $db->set_table('huoma_kf')->findSql($checkExitsSQL);
        if(!$checkExits) {
            
            // 不存在这个字段
            // 新增字段
            $Add_kf_qc = "ALTER TABLE huoma_kf ADD kf_qc int(1) DEFAULT '2' COMMENT '去重1开 2关'";
            $db->set_table('huoma_kf')->findSql($Add_kf_qc);
        }
        
        // 2.4.6版本新增
        // 长按次数记录
        // 加一个 longpress_num 字段
    	$checkExitsSQL_longpress_num = "SHOW COLUMNS FROM huoma_kf_zima LIKE 'longpress_num'";
        $checkExits_longpress_num = $db->set_table('huoma_kf_zima')->findSql($checkExitsSQL_longpress_num);
        if(!$checkExits_longpress_num) {
            
            // 不存在这个字段
            // 新增字段
            $Add_longpress_num = "ALTER TABLE huoma_kf_zima ADD longpress_num int(10) DEFAULT '0' COMMENT '长按次数'";
            $db->set_table('huoma_kf_zima')->findSql($Add_longpress_num);
        }
    
    	// 获取总数
    	$kfNum = $db->set_table('huoma_kf')->getCount(['kf_creat_user'=>$LoginUser]);
    
    	// 每页数量
    	$lenght = 10;
    
    	// 每页第一行
    	$offset = ($page-1)*$lenght;
    
    	// 总页码
    	$allpage = ceil($kfNum/$lenght);
    
    	// 上一页     
    	$prepage = $page-1;
    	if($page == 1){
    		$prepage=1;
    	}
    
    	// 下一页
    	$nextpage = $page+1;
    	if($page == $allpage){
    		$nextpage=$allpage;
    	}
    
    	// 获取当前登录用户创建的客服码，每页10个DESC排序
    	$getkfList = $db->set_table('huoma_kf')->findAll(
    	    $conditions = ['kf_creat_user' => $LoginUser],
    	    $order = 'ID DESC',
    	    $fields = null,
    	    $limit = ''.$offset.','.$lenght.''
    	);
    	
        // 判断获取结果
    	if($getkfList && $getkfList > 0){
    	    
    	    // 获取成功
    		$result = array(
    		    'kfList' => $getkfList,
    		    'kfNum' => $kfNum,
    		    'prepage' => $prepage,
    		    'nextpage' => $nextpage,
    		    'allpage' => $allpage,
    		    'page' => $page,
    		    'code' => 200,
    		    'msg' => '获取成功'
    		);
    	}else{
    	    
    	    // 获取失败
            $result = array(
                'code' => 204,
                'msg' => '暂无客服码'
            );
    	}
    }else{
        
        // 未登录
        $result = array(
			'code' => 201,
            'msg' => '未登录或登录过期'
		);
    }

	// 输出JSON
	echo json_encode($result,JSON_UNESCAPED_UNICODE);
	
?>