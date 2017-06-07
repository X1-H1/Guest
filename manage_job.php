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
define('SCRIPT','manage_job');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快
//必须是管理员才能登陆
_manage_login();

//添加管理员
if(@$_GET['action'] == 'add'){
    if($_rows = _fetch_array("SELECT user_id FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")) {
        //为了防止cookie伪造，还要比对一下唯一标识符
        _check_uni(@$_rows['user_uni'], @$_COOKIE['uniqid']);//此处的COOKIE中的唯一编码uniqid虽然是变量但是要全写，否则会出错
        $_clean = array();
        $_clean['username'] = $_POST['manage'];
        $_clean = _mysql_second_string($_clean);
        //添加管理员，实际上是修改数据中的level字段
        _query("UPDATE tb_user SET user_level=1 WHERE user_username='{$_clean['username']}'");
        if(_affected_rows() == 1){
            _close();
            _location('管理员添加成功！',SCRIPT.'.php');
        }else{
            _close();
            _alert_back("管理员添加失败！原因：不存在此用户或者为空");
        }
    }else{
        _alert_back("非法登录！");
    }
}


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
                    WHERE 
                          user_level=1
                ORDER BY 
                          user_reg_time DESC 
                    LIMIT 
                           $_pagenum,$_pagesize");

//辞职
if(@$_GET['action'] == 'job'){
    if($_rows = _fetch_array("SELECT user_id FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")) {
        //为了防止cookie伪造，还要比对一下唯一标识符
        _check_uni(@$_rows['user_uni'], @$_COOKIE['uniqid']);//此处的COOKIE中的唯一编码uniqid虽然是变量但是要全写，否则会出错
        //辞职
        _query("UPDATE tb_user SET user_level=0 WHERE user_username='{$_COOKIE['username']}' AND user_id='{$_GET['id']}'");
        if(_affected_rows() == 1){
            _close();
            _session_destroy();
            _location('辞职成功！','index.php');
        }else{
            _close();
            _alert_back("辞职失败！");
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
                    if($_COOKIE['username'] == $_html['username']){
                        $_html['job_html'] = '<a href="manage_job.php?action=job&id='.$_html['id'].'">辞职</a>';
                    }else{
                        $_html['job_html'] = '无权操作';
                    }
            ?>
            <tr>
                <td><?php echo $_html['id'];?></td>
                <td><?php echo $_html['username'];?></td>
                <td><?php echo $_html['email'];?></td>
                <td><?php echo $_html['reg_time'];?></td>
                <td><?php echo $_html['job_html'];?></td>
            </tr>
            <?php }?>
        </table>
        <form action="?action=add" method="post">
            <input type="text" name="manage" class="text"/>
            <input type="submit" value="添加管理员"/>
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

