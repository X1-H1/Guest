<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/13 0013
 * Time: 17:33
 */
session_start();
//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');

//定义个常量，用来指定本页的内容
define('SCRIPT','member');

//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

//是否正常登陆
if(isset($_COOKIE['username'])){
    $_rows = _fetch_array("SELECT user_username,user_sex,user_url,user_qq,user_reg_time,user_face,user_level,user_email FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1");
    if($_rows){
        $_html = array();
        $_html['username'] = $_rows['user_username'];
        $_html['sex'] = $_rows['user_sex'];
        $_html['url'] = $_rows['user_url'];
        $_html['qq'] = $_rows['user_qq'];
        $_html['reg_time'] = $_rows['user_reg_time'];
        $_html['face'] = $_rows['user_face'];
        $_html['email'] = $_rows['user_email'];
        switch ($_rows['user_level']){
            case 0:
                $_html['level'] = "普通会员";
                break;
            case 1:
                $_html['level'] = "管理员";
                break;
            default:
                $_html['level'] = "操作有误";
        }
        $_html = _html($_html);
    }else{
        _alert_back('此用户不存在');
    }
}else{
    _alert_back('非法登录！');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--个人中心</title>-->
    <?php
    require ROOT_PATH."includes/title.inc.php"
    ?>
</head>

<body>
<?php
require ROOT_PATH.'includes/header.inc.php';
?>

<div id="member">
    <?php
    require ROOT_PATH."includes/member.inc.php";
    ?>
    <div id="member_main">
        <h2>会员管理中心</h2>
        <dl>
            <dd>用 户 名：<?php echo $_html['username'];?></dd>
            <dd>性&nbsp;&nbsp;&nbsp;&nbsp;别：<?php echo $_html['sex'];?></dd>
            <dd>头&nbsp;&nbsp;&nbsp;&nbsp;像：<?php echo $_html['face'];?></dd>
            <dd>电子邮件：<?php echo $_html['email'];?></dd>
            <dd>主&nbsp;&nbsp;&nbsp;&nbsp;页：<?php echo $_html['url'];?></dd>
            <dd>&nbsp;&nbsp;Q&nbsp;&nbsp;Q：<?php echo $_html['qq'];?></dd>
            <dd>注册时间：<?php echo $_html['reg_time'];?></dd>
            <dd>身&nbsp;&nbsp;&nbsp;&nbsp;份：<?php echo $_html['level'];?></dd>
        </dl>
    </div>
</div>

<?php
require ROOT_PATH.'includes/foot.inc.php';

?>
</body>
</html>

