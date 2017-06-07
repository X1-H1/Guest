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
define('SCRIPT','member_flower');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

//判断是否登录
if(!isset($_COOKIE['username'])){
    _alert_close('请先登录！');
}

//批删除花朵模块
if(@$_GET['action'] == 'delete' && isset($_POST['ids'])){
    $_clean = array();
    $_clean['ids'] = _mysql_string(implode(",",$_POST['ids']));
    //当在进行危险操作的时候，要对唯一标识符进行验证
    if($_rows2 = _fetch_array("SELECT user_id FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")) {
        //为了防止cookie伪造，还要比对一下唯一标识符
        _check_uni(@$_rows2['user_uni'], @$_COOKIE['uniqid']);//此处的COOKIE中的唯一编码uniqid虽然是变量但是要全写，否则会出错
        //执行删除短信的操作
        _query("DELETE FROM tb_flower WHERE f_id IN ({$_clean['ids']})");
        if(_affected_rows()){
            //关闭数据库链接
            _close();
//            _session_destroy();
            //成功写入数据库后，提示并跳转到指定的页面
            _location('删除成功！','member_flower.php');
        }else{
            //关闭数据库链接
            _close();
//            _session_destroy();
            _alert_back('删除失败！');
        }
    }else{
        _alert_back("非法登录！");
    }
}

//分页模块
global $_pagenum,$_pagesize;
_page("SELECT f_id FROM tb_flower WHERE f_touser='{$_COOKIE['username']}'",15);//第一个参数是获得总的数据，第二个参数是每页显示的数据量
$_result = _query("SELECT f_id,f_fromuser,f_content,f_count,f_date FROM tb_flower WHERE f_touser='{$_COOKIE['username']}' ORDER BY f_date DESC LIMIT $_pagenum,$_pagesize");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--花朵列表</title>-->
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
    require ROOT_PATH."includes/member.inc.php";
    ?>
    <div id="member_main">
        <h2>花朵管理中心</h2>
        <form method="post" action="?action=delete">
        <table cellspacing="1">
            <tr>
                <th>送花人</th>
                <th>鲜花数量</th>
                <th>送花感言</th>
                <th>时间</th>
                <th>操作</th>
            </tr>
            <?php
                $_html = array();
                while($_rows = _fetch_array_list($_result)){
                    $_html['id'] = $_rows['f_id'];
                    $_html['fromuser'] = $_rows['f_fromuser'];
                    $_html['content'] = $_rows['f_content'];
                    $_html['count'] = $_rows['f_count'];
                    $_html['date'] = $_rows['f_date'];
                    $_html = _html($_html);
                    @$_html['sum'] += $_html['count'];
//                    if (empty($_rows['m_state'])){
//                        $_html['state'] = "<img src='images/noread.png' alt='未读' title='未读'>";
//                        $_html['content_xh'] = "<strong>"._title($_html['content'])."</strong>";
//                    }else{
//                        $_html['state'] = "<img src='images/read.png' alt='已读' title='已读'>";
//                        $_html['content_xh'] = _title($_html['content']);
//                    }
            ?>
            <tr>
                <td><?php echo $_html['fromuser'] ?></td>
                <td><img src="images/b4.png" alt="花朵"/> x <?php echo $_html['count']?>朵</td>
                <td><span title="<?php echo $_html['content']?>"><?php echo _title($_html['content'],14)?></span></td>
                <td><?php echo $_html['date'] ?></td>
                <td><input name="ids[]" value="<?php echo $_html['id']?>" type="checkbox"/></td>
            </tr>
            <?php
                }
                _free_result($_result);
            ?>
            <tr>
                <td colspan="5">共<strong><?php echo @$_html['sum']?></strong>朵花</td>
            </tr>
            <tr>
                <td colspan="5"><label for="all">全选<input type="checkbox" name="chkall" id="all"/></label><input type="submit" value="批删除"/></td>
            </tr>
        </table>
        </form>
        <?php
            _paging(2);
        ?>
    </div>
</div>

<?php
    require ROOT_PATH.'includes/foot.inc.php';
?>
</body>
</html>

