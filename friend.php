<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/17 0017
 * Time: 14:45
 */
session_start();
//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');
//定义个常量，用来指定本页的内容
define('SCRIPT','friend');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

//判断是否登录
if(!isset($_COOKIE['username'])){
    _alert_close('请先登录！');
}

//添加好友
if(@$_GET['action'] == 'add'){
    _check_code($_SESSION['code'],$_POST['yzm']);
    if($_rows = _fetch_array("SELECT user_id FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")){
        _check_uni(@$_rows['user_uni'],@$_COOKIE['uniqid']);
        include ROOT_PATH."includes/check.func.php";
        $_clean = array();
        $_clean['touser'] = $_POST['touser'];
        $_clean['fromuser'] = $_COOKIE['username'];
        $_clean['content'] = _check_content($_POST['content']);
        $_clean = _mysql_second_string($_clean);

        if($_clean['touser'] == $_clean['fromuser']){
            _alert_close("请不要尝试添加自己为好友！");
        }
        //数据库验证好友是否已经添加
        if($_rows = _fetch_array("SELECT f_id FROM tb_friend WHERE (f_touser='{$_clean['touser']}' AND f_fromuser='{$_clean['fromuser']}')
                                                                OR (f_touser='{$_clean['fromuser']}' AND f_fromuser='{$_clean['touser']}') LIMIT 1")){
            _alert_close("你们已经是好友了！或者是未验证的好友！无需添加！");
        }else{
            //添加好友信息
            _query("INSERT INTO tb_friend(
                                          f_touser,
                                          f_fromuser,
                                          f_content,
                                          f_date) 
                                    VALUE (
                                          '{$_clean['touser']}',
                                          '{$_clean['fromuser']}',
                                          '{$_clean['content']}',
                                          now()
                                    )");
            //新增成功
            if(_affected_rows() == 1){
                //关闭数据库链接
                _close();
//                _session_destroy();
                _alert_close("添加好友成功！请等待验证！");
            }else{
                //关闭数据库链接
                _close();
//                _session_destroy();
                _alert_back("添加好友失败!");
            }
        }
    }else{
        _alert_close("非法操作！");
    }
        exit();
}

//获取数据
if(isset($_GET['id'])){
    if($_rows = _fetch_array("SELECT user_username FROM tb_user WHERE user_id='{$_GET['id']}' LIMIT 1")){
        $_html = array();
        $_html['touser'] = $_rows['user_username'];
    }else{
        _alert_close('不存在此用户！');
    }
}else{
    _alert_close('非法操作！');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--加好友</title>-->
    <?php
        require ROOT_PATH."includes/title.inc.php"
    ?>
    <script type="text/javascript" src="js/code.js"></script>
    <script type="text/javascript" src="js/message.js"></script>
    <!--    客户端验证失败   js 引入不成功 但验证码能正常使用,隔天重新打开程序后又可以正常使用js了，客户端验证启动 -->
</head>

<body>


<div id="message">
    <h3>加好友</h3>
    <form method="post" action="?action=add">
        <input type="hidden" value="<?php echo $_html['touser']?>" name="touser"/>
        <dl>
            <dd><input type="text" readonly="readonly" value="TO：<?php echo $_html['touser']?>" class="text"/></dd>
            <dd><textarea name="content" rows="5" cols="25" >我想和你交个朋友!!!</textarea></dd>
            <dd>验 证 码：<input type="text" name="yzm" class="text yzm"/>
                         <img id="code" src="code.php"/>
                         <input type="submit" class="submit" value="添加好友"/>
            </dd>
        </dl>
    </form>
</div>



</body>
</html>

