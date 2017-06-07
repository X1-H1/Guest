<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/9 0009
 * Time: 16:17
 */
session_start();
//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');
//定义个常量，用来指定本页的内容
define('SCRIPT','login');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快
//登录状态
_login_state();
//开始处理登录信息
if(@$_GET['action'] == 'login')
{
    if(!empty($_system['code'])){
        //为了防止恶意注册，跨站攻击
        _check_code($_SESSION['code'],$_POST['yzm']);
    }
    //引入验证文件，此处由于在if条件语句中引入文件用include最合适
    include ROOT_PATH."includes/login.check.php";
    $_clean = array();
    $_clean['username'] = _check_username($_POST['username']);
    $_clean['password'] = _check_password($_POST['password']);
    $_clean['time'] = _check_time($_POST['time']);
    //到数据库中去验证
    if($_rows = _fetch_array("SELECT user_username,user_uni,user_level FROM tb_user WHERE user_username='{$_clean['username']}' AND user_password='{$_clean['password']}' AND user_active='' LIMIT 1")){
        //登录成功后，记录登录信息
        _query("UPDATE tb_user SET user_last_time=now(),
                                   user_last_ip='{$_SERVER["REMOTE_ADDR"]}',
                                   user_login_count=user_login_count + 1
                              WHERE 
                                   user_username='{$_rows['user_username']}'
                                   ");
//        _session_destroy();
        _set_cookies($_rows['user_username'],$_rows['user_uni'],$_clean['time']);
        if($_rows['user_level'] == 1){
            $_SESSION['admin'] = $_rows['user_username'];
        }
        _close();
        _location('','member.php');
    }else{
        _close();
//        _session_destroy();
        _location('用户名密码错误还有可能此用户名未被注册激活！','login.php');
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--登录</title>-->
    <?php
    require ROOT_PATH."includes/title.inc.php"
    ?>
    <script type="text/javascript" src="js/code.js"></script>
    <script type="text/javascript" src="js/login.js"></script>
</head>

<body>
<?php
require ROOT_PATH.'includes/header.inc.php';
?>
<div id="login">
    <h2>登录</h2>
    <form method="post" name="login" action="login.php?action=login">
        <dl>
            <dd></dd>
            <dd>用 户 名：<input type="text" name="username" class="text"/></dd>
            <dd>密&nbsp;&nbsp;&nbsp;&nbsp;码：<input type="password" name="password" class="text"/></dd>
            <dd>信息保存：<input type="radio" name="time" value="0" checked/>不保存
                         <input type="radio" name="time" value="1"/>一天
                         <input type="radio" name="time" value="2"/>一周
                         <input type="radio" name="time" value="3"/>一个月</dd>
            <?php if(!empty($_system['code'])){?>
            <dd>验 证 码：<input type="text" name="yzm" class="text yzm"/><img id="code" src="code.php"/></dd>
            <?php }?>
            <dd><input type="submit" value="登录" class="button"/>    <input type="button" value="注册" id="reg" class="button reg"/></dd>
        </dl>
    </form>
</div>

<?php
require ROOT_PATH.'includes/foot.inc.php';

?>
</body>
</html>

