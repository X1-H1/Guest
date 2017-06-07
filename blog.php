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
define('SCRIPT','blog');

//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

//分页模块
global $_pagenum,$_pagesize,$_system;
_page("SELECT user_id FROM tb_user ",$_system['blog']);//第一个参数是获得总的数据，第二个参数是每页显示的数据量

//从数据库中提取数据
//每次是重新读取结果集，而不是每次重新读取数据，如果调用之前封装好的读取数据库文件会在下面的while循环造成死循环
//此时我们需要修改之前封装好的函数，或者重新写一个读取数据库的函数在这里采取第二种方法即重新写一个函数
$_result = _query("SELECT user_id,user_username,user_sex,user_face FROM tb_user ORDER BY user_reg_time DESC LIMIT $_pagenum,$_pagesize");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--好友</title>-->
    <?php
    require ROOT_PATH."includes/title.inc.php"
    ?>
    <script type="text/javascript" src="js/send_message.js"></script>
</head>

<body>
<?php
require ROOT_PATH.'includes/header.inc.php';
?>
<div id="blog">
    <h2>好友列表</h2>
    <?php
        $_html = array();
        while($_rows = _fetch_array_list($_result)){
            $_html['username'] = $_rows['user_username'];
            $_html['face'] = $_rows['user_face'];
            $_html['sex'] = $_rows['user_sex'];
            $_html['id'] = $_rows['user_id'];
            $_html = _html($_html);
    ?>
    <dl>
        <dd class="user"><?php echo $_html['username'];?>(<?php echo $_html['sex'];?>)</dd>
        <dt><img src="<?php echo $_html['face'];?>" alt="<?php echo $_html['user_username'];?>"></dt>
        <dd class="message"><a href="javascript:;" name="message" title="<?php echo $_html['id'];?>">发消息</a></dd>
        <dd class="friend"><a href="javascript:;" name="friend" title="<?php echo $_html['id'];?>">加好友</a></dd>
        <dd class="guest">写留言</dd>
        <dd class="flower"><a href="javascript:;" name="flower" title="<?php echo $_html['id'];?>">送花朵</a></dd>
    </dl>
    <?php }
    _free_result($_result);
    //此处调用分页函数; 1：调用数字分页，2：调用文本分页
    _paging(1);
    ?>
</div>

<?php
require ROOT_PATH.'includes/foot.inc.php';

?>
</body>
</html>
