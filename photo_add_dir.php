<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/10 0010
 * Time: 10:51
 */
session_start();
//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');

//定义个常量，用来指定本页的内容
define('SCRIPT','photo_add_dir');

//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

//此页面只有管理员才能登录
_manage_login();

//添加目录
if(@$_GET['action'] == 'adddir'){
    if($_rows = _fetch_array("SELECT user_id FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")) {
        //为了防止cookie伪造，还要比对一下唯一标识符
        _check_uni(@$_rows['user_uni'], @$_COOKIE['uniqid']);//此处的COOKIE中的唯一编码uniqid虽然是变量但是要全写，否则会出错

        //引入验证文件
        include ROOT_PATH."includes/check.func.php";
        //接收数据
        $_clean = array();
        $_clean['name'] = _check_dir_name($_POST['name'],2,20);
        $_clean['type'] = $_POST['type'];
        if(!empty($_clean['type'])){
            $_clean['password'] = _check_dir_password($_POST['password'],6);
        }
        $_clean['content'] = _check_dir_content($_POST['content'],40);//此处相对于视频中多添加了一个服务器端检测相册描述内容的函数
        $_clean['dir'] = time();
        $_clean = _mysql_second_string($_clean);

        //先检查一下主目录是否存在
        if(!is_dir('photo')){
            mkdir('photo',0777);
        }
        //主目录存在的时候，在其下接着创建一个你定义的目录
        if(!is_dir('photo/'.$_clean['dir'])){
            mkdir('photo/'.$_clean['dir']);
        }
        //把当前的目录信息写入数据库即可
        if(empty($_clean['type'])){
            _query("INSERT INTO tb_dir(d_name,d_type,d_content,d_dir,d_date) VALUES ('{$_clean['name']}','{$_clean['type']}','{$_clean['content']}','photo/{$_clean['dir']}',now())");
        }else{
            _query("INSERT INTO tb_dir(d_name,d_type,d_content,d_dir,d_date,d_password) VALUES ('{$_clean['name']}','{$_clean['type']}','{$_clean['content']}','photo/{$_clean['dir']}',now(),'{$_clean['password']}')");
        }
        //目录添加成功
        if(_affected_rows() == 1){
            _close();
            _location('目录添加成功！','photo.php');
        }else{
            _close();
            _alert_back('目录添加失败！');
        }

    }else{
        _alert_back("非法登录！");
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--好友</title>-->
    <?php
    require ROOT_PATH."includes/title.inc.php"
    ?>
    <script type="text/javascript" src="js/photo_add_dir.js"></script>
</head>

<body>
<?php
require ROOT_PATH.'includes/header.inc.php';
?>
<div id="photo">
    <h2>添加相册</h2>
    <form action="?action=adddir" method="post">
    <dl>
        <dd>相册名称：<input type="text" name="name" class="text"/></dd>
        <dd>相册类型：<input type="radio" name="type" value="0" checked="checked"/>公开 <input type="radio" name="type" value="1"/>私密</dd>
        <dd id="pass">相册密码：<input type="password" name="password" class="text"/></dd>
        <dd>相册描述：<textarea name="content"></textarea> </dd>
        <dd><input type="submit" class="submit" name="submit" value="创建相册"/></dd>
    </dl>
    </form>
</div>

<?php
require ROOT_PATH.'includes/foot.inc.php';

?>
</body>
</html>
