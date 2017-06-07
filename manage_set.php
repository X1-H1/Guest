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
define('SCRIPT','manage_set');

//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

//必须是管理员才能登陆
_manage_login();


//修改系统表
if(@$_GET['action'] == 'set'){
    if($_rows = _fetch_array("SELECT user_id FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")){
        _check_uni(@$_rows['user_uni'],@$_COOKIE['uniqid']);//此处的COOKIE中的唯一编码uniqid虽然是变量但是要全写，否则会出错
        $_clean = array();
        $_clean['webname'] = $_POST['webname'];
        $_clean['article'] = $_POST['article'];
        $_clean['blog'] = $_POST['blog'];
        $_clean['photo'] = $_POST['photo'];
        $_clean['skin'] = $_POST['skin'];
        $_clean['string'] = $_POST['string'];
        $_clean['post'] = $_POST['post'];
        $_clean['re'] = $_POST['re'];
        $_clean['code'] = $_POST['code'];
        $_clean['register'] = $_POST['register'];
        $_clean = _mysql_second_string($_clean);

        //写入数据库
        _query("UPDATE
                       tb_system
                  SET
                       s_webname='{$_clean['webname']}',
                       s_article='{$_clean['article']}',
                       s_blog='{$_clean['blog']}',
                       s_photo='{$_clean['photo']}',
                       s_skin='{$_clean['skin']}',
                       s_string='{$_clean['string']}',
                       s_post='{$_clean['post']}',
                       s_re='{$_clean['re']}',
                       s_code='{$_clean['code']}',
                       s_register='{$_clean['register']}'
                  WHERE 
                       s_id=1
                  LIMIT
                       1");
        if(_affected_rows() == 1){
            _close();
            _location('修改成功！','manage_set.php');
        }else{
            _close();
            _location('请确认输入数据的正确性！','manage_set.php');
        }
    }else{
        _alert_back("异常！");
    }
}

//读取系统表
if($_rows = _fetch_array("SELECT 
                                  s_webname,
                                  s_article,
                                  s_blog,
                                  s_photo, 
                                  s_skin, 
                                  s_string, 
                                  s_post, 
                                  s_re, 
                                  s_code,
                                  s_register                                  
                            FROM 
                                  tb_system 
                            WHERE 
                                  s_id=1 
                            LIMIT 1")){
    $_html = array();
    $_html['webname'] = $_rows['s_webname'];
    $_html['article'] = $_rows['s_article'];
    $_html['blog'] = $_rows['s_blog'];
    $_html['photo'] = $_rows['s_photo'];
    $_html['skin'] = $_rows['s_skin'];
    $_html['string'] = $_rows['s_string'];
    $_html['post'] = $_rows['s_post'];
    $_html['re'] = $_rows['s_re'];
    $_html['code'] = $_rows['s_code'];
    $_html['register'] = $_rows['s_register'];
    $_html = _html($_html);


    //文章
    if($_html['article'] == 10){
        $_html['article_html'] = '<select name="article"><option value="10" selected="selected">每页10篇</option><option value="15">每页15篇</option></select>';
    }elseif ($_html['article'] == 15){
        $_html['article_html'] = '<select name="article"><option value="10">每页10篇</option><option value="15" selected="selected">每页15篇</option></select>';
    }

    //博友
    if($_html['blog'] == 15){
        $_html['blog_html'] = '<select name="blog"><option value="15" selected="selected">每页15人</option><option value="20">每页20人</option><option value="25">每页25人</option></select>';
    }elseif ($_html['blog'] == 20){
        $_html['blog_html'] = '<select name="blog"><option value="15">每页15人</option><option value="20" selected="selected">每页20人</option><option value="25">每页25人</option></select>';
    }elseif ($_html['blog'] == 25){
        $_html['blog_html'] = '<select name="blog"><option value="15">每页15人</option><option value="20">每页20人</option><option value="25" selected="selected">每页25人</option></select>';
    }

    //相册
    if($_html['photo'] == 8){
        $_html['photo_html'] = '<select name="photo"><option value="8" selected="selected">每页8张</option><option value="12">每页12张</option></select>';
    }elseif ($_html['photo'] == 12){
        $_html['photo_html'] = '<select name="photo"><option value="8">每页8张</option><option value="12" selected="selected">每页12张</option></select>';
    }

    //皮肤
    if($_html['skin'] == 1){
        $_html['skin_html'] = '<select name="skin"><option value="1" selected="selected">一套皮肤</option><option value="2">二套皮肤</option><option value="3">三套皮肤</option></select>';
    }elseif ($_html['skin'] == 2){
        $_html['skin_html'] = '<select name="skin"><option value="1">一套皮肤</option><option value="2" selected="selected">二套皮肤</option><option value="3">三套皮肤</option></select>';
    }elseif ($_html['skin'] == 3){
        $_html['skin_html'] = '<select name="skin"><option value="1">一套皮肤</option><option value="2">二套皮肤</option><option value="3" selected="selected">三套皮肤</option></select>';
    }

    //发帖限制
    if($_html['post'] == 30){
        $_html['post_html'] = '<input type="radio" name="post" value="30" checked="checked"/>30秒 <input type="radio" name="post" value="60"/>1分钟 <input type="radio" name="post" value="180"/>3分钟';
    }elseif ($_html['post'] == 60){
        $_html['post_html'] = '<input type="radio" name="post" value="30"/>30秒 <input type="radio" name="post" value="60" checked="checked"/>1分钟 <input type="radio" name="post" value="180"/>3分钟';
    }elseif ($_html['post'] == 180){
        $_html['post_html'] = '<input type="radio" name="post" value="30"/>30秒 <input type="radio" name="post" value="60"/>1分钟 <input type="radio" name="post" value="180" checked="checked"/>3分钟';
    }

    //回帖
    if($_html['re'] == 15){
        $_html['re_html'] = '<input type="radio" name="re" value="15" checked="checked"/>15秒 <input type="radio" name="re" value="30"/>30秒 <input type="radio" name="re" value="45"/>45秒';
    }elseif ($_html['re'] == 30){
        $_html['re_html'] = '<input type="radio" name="re" value="15"/>15秒 <input type="radio" name="re" value="30" checked="checked"/>30秒 <input type="radio" name="re" value="45"/>45秒';
    }elseif ($_html['re'] == 45){
        $_html['re_html'] = '<input type="radio" name="re" value="15"/>15秒 <input type="radio" name="re" value="30"/>30秒 <input type="radio" name="re" value="45" checked="checked"/>45秒';
    }

    //验证码
    if($_html['code'] == 1){
        $_html['code_html'] = '<input type="radio" name="code" value="1" checked="checked"/>启用  <input type="radio" value="0" name="code"/>禁用';
    }else{
        $_html['code_html'] = '<input type="radio" name="code" value="1"/>启用  <input type="radio" name="code" value="0" checked="checked"/>禁用';
    }

    //注册开关
    if($_html['register']){
        $_html['register_html'] = '<input type="radio" name="register" value="1" checked="checked"/>启用  <input type="radio" name="register" value="0"/>禁用';
    }else{
        $_html['register_html'] = '<input type="radio" name="register" value="1"/>启用  <input type="radio" name="register" value="0" checked="checked"/>禁用';
    }

}else{
    _alert_back("系统表读取错误！请联系管理员检查！！！");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--后台管理中心</title>-->
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
    require ROOT_PATH."includes/manage.inc.php";
    ?>
    <div id="member_main">
        <h2>后台管理中心</h2>
        <form method="post" action="?action=set">
        <dl>
            <dd>·网&nbsp;站&nbsp;名&nbsp;称：<input type="text" class="text" name="webname" value="<?php echo $_html['webname'];?>"/></dd>
            <dd>·文章每页列表数：<?php echo $_html['article_html'];?></dd>
            <dd>·博客每页列表数：<?php echo $_html['blog_html'];?></dd>
            <dd>·相册每页列表数：<?php echo $_html['photo_html'];?></dd>
            <dd>·站点默认皮肤：<?php echo $_html['skin_html'];?></dd>
            <dd>·非法字符过滤：<input name="string" class="text" value="<?php echo $_html['string'];?>"/>（*请用 | 线隔开）</dd>
            <dd>·每次发帖限制：<?php echo $_html['post_html'];?></dd>
            <dd>·每次回帖限制：<?php echo $_html['re_html'];?></dd>
            <dd>·是否启用验证码：<?php echo $_html['code_html'];?></dd>
            <dd>·是否开放注册：<?php echo $_html['register_html'];?></dd>
            <dd><input type="submit" value="修改系统设置" class="submit"/></dd>
        </dl>
        </form>
    </div>
</div>

<?php
require ROOT_PATH.'includes/foot.inc.php';

?>
</body>
</html>

