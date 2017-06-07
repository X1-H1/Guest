<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/5 0005
 * Time: 13:50
 */
session_start();
//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');
//定义个常量，用来指定本页的内容
define('SCRIPT','post');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快
//登录后才可以发帖
if(!isset($_COOKIE['username'])){
    _location('发帖前，必须登录！','login.php');
}
//将帖子写入数据库
if(@$_GET['action'] == 'post'){
    //验证码验证
    _check_code($_SESSION['code'],$_POST['yzm']);
    if($_rows = _fetch_array("SELECT user_id,user_post_time FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")) {
        //为了防止cookie伪造，还要比对一下唯一标识符
        _check_uni(@$_rows['user_uni'], @$_COOKIE['uniqid']);//此处的COOKIE中的唯一编码uniqid虽然是变量但是要全写，否则会出错

        global $_system;
        //验证一下是否在规定的时间外发帖
        _timed(time(),$_rows['user_post_time'],$_system['post']);

        include ROOT_PATH."includes/check.func.php";
        //接收帖子的内容
        $_clean = array();
        $_clean['username'] = $_COOKIE['username'];
        $_clean['type'] = $_POST['type'];
        $_clean['title'] = _check_post_title($_POST['title'],2,40);
        $_clean['content'] = _check_post_content($_POST['content'],10);
        //$_clean['date'] = $_POST['date'];
        $_clean = _mysql_second_string($_clean);
        //写入数据库
        _query("INSERT INTO tb_article(a_username,a_title,a_type,a_content,a_date) VALUES('{$_clean['username']}','{$_clean['title']}','{$_clean['type']}','{$_clean['content']}',now())");
        if(_affected_rows() == 1){
            //获取刚刚新增的ID
            $_clean['id'] = _insert_id();
            //setcookie('post_time',time());
            $_clean['time'] = time();
            _query("UPDATE tb_user SET user_post_time='{$_clean['time']}' WHERE user_username='{$_COOKIE['username']}'");
            //关闭数据库链接
            _close();
//            _session_destroy();
            //成功写入数据库后，提示并跳转到指定的页面
            _location('帖子发表成功！','article.php?id='.$_clean['id']);
        }else{
            //关闭数据库链接
            _close();
//            _session_destroy();
            //成功写入数据库后，提示并跳转到指定的页面
            _alert_back('帖子发表失败！');
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--发表帖子</title>-->
<?php
    require ROOT_PATH."includes/title.inc.php"
?>
    <script type="text/javascript" src="js/code.js"></script>
    <script type="text/javascript" src="js/post.js"></script>
</head>
<body>
<?php
    require ROOT_PATH.'includes/header.inc.php';
?>
<div id="post">
    <h2>发表帖子</h2>
    <form method="post" name="post" action="?action=post">
        <dl>
            <dt>请填写一下内容</dt>
            <dd>类&nbsp;&nbsp;&nbsp;&nbsp;型：
                <?php
                    foreach (range(1,16) as $_num){
                        if($_num == 1){
                            echo '<label for="type'.$_num.'"><input id="type'.$_num.'" type="radio" name="type" value="'.$_num.'" checked="checked"/> ';
                        }else{
                            echo '<label for="type'.$_num.'"><input id="type'.$_num.'" type="radio" name="type" value="'.$_num.'"/> ';
                        }
                        echo '<img src="images/icon'.$_num.'.png" alt="类型"/></label> ';
                        if($_num == 8){
                            echo "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        }
                    }
                ?>
            </dd>
            <dd>标&nbsp;&nbsp;&nbsp;&nbsp;题：<input type="text" name="title" class="text"/>(*必填，2-40位)</dd>
            <dd id="q">贴&nbsp;&nbsp;&nbsp;&nbsp;图：&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;">Q图系列[1]</a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;">Q图系列[2]</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;">Q图系列[3]</a></dd>
            <dd>
                <?php include ROOT_PATH.'includes/ubb.inc.php'?>
                <textarea name="content" rows="15" cols=""></textarea>
            </dd>
            <dd>验 证 码：<input type="text" name="yzm" class="text yzm"/><img id="code" src="code.php"/>
                         <input type="submit" class="submit" value="发表帖子"/></dd>
        </dl>
    </form>
</div>
<?php
    require ROOT_PATH.'includes/foot.inc.php';
?>
</body>
</html>
