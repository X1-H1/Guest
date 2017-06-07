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
//修改
if(@$_GET['action'] == 'modify'){
    if($_rows = _fetch_array("SELECT user_id,user_article_time FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")) {
        //为了防止cookie伪造，还要比对一下唯一标识符
        _check_uni(@$_rows['user_uni'], @$_COOKIE['uniqid']);
        //引入验证文件
        include ROOT_PATH."includes/check.func.php";
        //接收数据
        $_clean = array();
        $_clean['id'] = $_POST['id'];
        $_clean['name'] = _check_dir_name($_POST['name'],2,20);
        $_clean['type'] = $_POST['type'];
        //此处仅仅是屏蔽了服务器端的密码验证，但还是有客户端的密码验证没处理，索性就不屏蔽了，管他的，修改的时候强制性的要求修改密码
        if(!empty($_clean['type'])){
            $_clean['password'] = _check_dir_password($_POST['password'],6);
        }
        $_clean['face'] = $_POST['face'];//理论上是需要验证的，此处暂时不写等日后再说
        $_clean['content'] = _check_dir_content($_POST['content'],40);//此处相对于视频中多添加了一个服务器端检测相册描述内容的函数
        $_clean = _mysql_second_string($_clean);

        //修改目录
        if(empty($_clean['type'])){
            _query("UPDATE 
                            tb_dir 
                      SET 
                            d_name='{$_clean['name']}',
                            d_type='{$_clean['type']}',
                            d_password=NULL,
                            d_face='{$_clean['face']}',
                            d_content='{$_clean['content']}' 
                      WHERE 
                            d_id='{$_clean['id']}' 
                      LIMIT 
                            1");
        }else{
            //而且这种情况在修改的时候密码是可以为空的
            //如果是相册是私密的type=1，但是点击修改的时候不填写密码，密码还为原来的值，这里要做一下判断
            //下面这种情况是点击修改的时候修改了密码的
            _query("UPDATE 
                            tb_dir 
                      SET 
                            d_name='{$_clean['name']}',
                            d_type='{$_clean['type']}',
                            d_password='{$_clean['password']}',
                            d_face='{$_clean['face']}',
                            d_content='{$_clean['content']}' 
                      WHERE 
                            d_id='{$_clean['id']}' 
                      LIMIT 
                            1");
        }
        if(_affected_rows() == 1){
            _close();
            _location('目录修改成功！','photo.php');
        }else{
            _close();
            _alert_back('目录修改失败！');
        }
    }else{
        _alert_back("非法登录！");
    }
}

//读出数据
if(isset($_GET['id'])){
    if($_rows = _fetch_array("SELECT d_id,d_name,d_type,d_password,d_face,d_content FROM tb_dir WHERE d_id='{$_GET['id']}' LIMIT 1")){
        $_html =array();
        $_html['id'] = $_rows['d_id'];
        $_html['name'] = $_rows['d_name'];
        $_html['type'] = $_rows['d_type'];
        $_html['face'] = $_rows['d_face'];
        $_html['content'] = $_rows['d_content'];
        $_html = _html($_html);
    }else{
        _alert_back("不存在此相册！");
    }
}else{
    _alert_back("非法操作！");
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
    <h2>修改相册目录</h2>
    <form action="?action=modify" method="post">
    <dl>
        <dd>相册名称：<input type="text" name="name" value="<?php echo $_html['name'];?>" class="text"/></dd>
        <dd>相册类型：<input type="radio" name="type" value="0" <?php if($_html['type'] == 0) echo 'checked="checked"';?>/>公开 <input type="radio" name="type" value="1" <?php if($_html['type'] == 1) echo 'checked="checked"';?>/>私密</dd>
        <dd id="pass" <?php if($_html['type'] == 1) echo 'style="display:block;"'?>>相册密码：<input type="password" name="password" class="text"/></dd>
        <dd>相册封面：<input type="text" name="face" value="<?php echo $_html['face'];?>" class="text"/></dd>
        <dd>相册描述：<textarea name="content" ><?php echo $_html['content'];?></textarea> </dd>
        <dd><input type="submit" class="submit" name="submit" value="修改目录"/></dd>
    </dl>
        <input type="hidden" value="<?php echo $_html['id'];?>" name="id"/>
    </form>
</div>

<?php
require ROOT_PATH.'includes/foot.inc.php';

?>
</body>
</html>
