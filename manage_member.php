<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/22 0022
 * Time: 12:32
 */
session_start();
//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');
//定义个常量，用来指定本页的内容
define('SCRIPT','manage_member');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快
//必须是管理员才能登陆
_manage_login();

//分页模块
global $_pagenum,$_pagesize;
_page("SELECT user_id FROM tb_user ",15);//第一个参数是获得总的数据，第二个参数是每页显示的数据量

$_result = _query("SELECT 
                          user_id,
                          user_username,
                          user_email,
                          user_reg_time 
                     FROM 
                          tb_user 
                ORDER BY 
                          user_reg_time DESC 
                    LIMIT 
                           $_pagenum,$_pagesize");

//删除
//if($_GET['action'] == 'del'){
//
//}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--短信列表</title>-->
    <?php
        require ROOT_PATH."includes/title.inc.php"
    ?>
    <script type="text/javascript" src="js/member_message.js"></script>
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
        <h2>会员列表中心</h2>
        <form method="post" action="?action=delete">
        <table cellspacing="1">
            <tr>
                <th>ID号</th>
                <th>会员名</th>
                <th>邮件</th>
                <th>注册时间</th>
                <th>操作</th>
            </tr>
            <?php
                $_html = array();
                while($_rows = _fetch_array_list($_result)){
                    $_html['username'] = $_rows['user_username'];
                    $_html['email'] = $_rows['user_email'];
                    $_html['reg_time'] = $_rows['user_reg_time'];
                    $_html['id'] = $_rows['user_id'];
                    $_html = _html($_html);
            ?>
            <tr>
                <td><?php echo $_html['id'];?></td>
                <td><?php echo $_html['username'];?></td>
                <td><?php echo $_html['email'];?></td>
                <td><?php echo $_html['reg_time'];?></td>
                <td>【<a href="?action=del&id=<?php echo $_html['id']?>">删除</a>】【<a href="###?action=mdf&id=<?php echo $_html['id']?>">修改</a>】</td>
            </tr>
            <?php }?>
        </table>
        </form>
        <?php
            _free_result($_result);
            _paging(2);
        ?>
    </div>
</div>

<?php
    require ROOT_PATH.'includes/foot.inc.php';
?>
</body>
</html>

