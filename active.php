<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/9 0009
 * Time: 13:57
 */

//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');
//定义个常量，用来指定本页的内容
define('SCRIPT','active');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

if(!isset($_GET['active'])){
    _alert_back('非法操作！');
}

//开始激活处理
if(isset($_GET['action']) && isset($_GET['active']) && $_GET['action'] == 'ok'){
    $_active = _mysql_string($_GET['active']);
    if(_fetch_array("SELECT user_active FROM tb_user WHERE user_active='$_active' LIMIT 1")){
        //如果存在的话，就将数据库中的active设置为空
        _query("UPDATE tb_user SET user_active=NULL WHERE user_active='$_active' LIMIT 1");
        if(_affected_rows() == 1){
            _close();
            _location('激活账户成功！！！','login.php');
        }else{
            _close();
            _location('激活账户失败！！！','register.php');
        }
    }else{
        _alert_back('非法操作！');
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--激活</title>-->
    <?php
    require ROOT_PATH."includes/title.inc.php"
    ?>
    <script type="text/javascript" src="js/register.js"></script>
</head>

<body>
<?php
require ROOT_PATH.'includes/header.inc.php';
?>
<div id="active">
    <h2>用户账号激活</h2>
    <p>由于没有做邮件发送系统，本页面只是单纯的模拟完成发送邮件并激活账号的功能，点击一下超链接激活你的账号</p>
    <p><a href="active.php?action=ok&amp;active=<?php echo $_GET['active'];?>">激活</a></p>
</div>

<?php
require ROOT_PATH.'includes/foot.inc.php';

?>
</body>
</html>

