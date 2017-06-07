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
define('SCRIPT','photo');

//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

//删除目录
if(@$_GET['action'] == 'delete' && isset($_GET['id'])){
    if($_rows = _fetch_array("SELECT user_id FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")) {
        //为了防止cookie伪造，还要比对一下唯一标识符
        _check_uni(@$_rows['user_uni'], @$_COOKIE['uniqid']);
        //删除目录
        //获取目录的物理地址
        if($_rows = _fetch_array("SELECT d_dir FROM tb_dir WHERE d_id='{$_GET['id']}' LIMIT 1")){
            $_html = array();
            $_html['dir'] = $_rows['d_dir'];
            $_html = _html($_html);

            //3.删除磁盘的目录
            if(file_exists($_html['dir'])){
                if(_remove_Dir($_html['dir'])){
                    //1.删除目录里的数据库图片
                    _query("DELETE FROM tb_photo WHERE p_sid='{$_GET['id']}'");
                    //2.删除这个目录的数据库
                    _query("DELETE FROM tb_dir WHERE d_id='{$_GET['id']}'");
                    _close();
                    _location('目录删除成功！','photo.php');
                }else{
                    _close();
                    _alert_back("目录删除失败！");
                }
            }
        }else{
            _alert_back("不存在此目录！");
        }


    }else{
        _alert_back("非法登录！");
    }
}


//读取数据
global $_pagenum,$_pagesize,$_system;
_page("SELECT d_id FROM tb_dir ",$_system['photo']);//第一个参数是获得总的数据，第二个参数是每页显示的数据量

$_result = _query("SELECT d_id,d_name,d_type,d_face FROM tb_dir ORDER BY d_date DESC LIMIT $_pagenum,$_pagesize");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--好友</title>-->
    <?php
    require ROOT_PATH."includes/title.inc.php"
    ?>
</head>

<body>
<?php
require ROOT_PATH.'includes/header.inc.php';
?>
<div id="photo">
    <h2>相册列表</h2>
    <?php
        $_html = array();
        while($_rows = _fetch_array_list($_result)){
            $_html['name'] = $_rows['d_name'];
            $_html['id'] = $_rows['d_id'];
            $_html['type'] = $_rows['d_type'];
            $_html['face'] = $_rows['d_face'];
            $_html = _html($_html);
            if(empty($_html['type'])){
                $_html['type_html'] = '(公开)';
            }else{
                $_html['type_html'] = '(私密)';
            }
            if(empty($_html['face'])){
                $_html['face_html'] = '';
            }else{
                $_html['face_html'] = '<img src="'.$_html['face'].'" alt="'.$_html['name'].'"/>';
            }

            //获取相册中的图片数
            $_html['photo'] = _fetch_array("SELECT COUNT(*) AS count FROM tb_photo WHERE p_sid='{$_html['id']}'");
    ?>
    <dl>
        <dt><a href="photo_show.php?id=<?php echo $_html['id'];?>"><?php echo $_html['face_html'];?></a></dt>
        <dd><a href="photo_show.php?id=<?php echo $_html['id'];?>"><?php echo $_html['name'];?><?php echo '['.$_html['photo']['count'].']'.$_html['type_html'];?></a></dd>
        <?php if(isset($_SESSION['admin']) && isset($_COOKIE['username'])){?>
        <dd>[<a href="photo_dir_modify.php?id=<?php echo $_html['id']?>">修改</a>] [<a href="photo.php?action=delete&id=<?php echo $_html['id'];?>" >删除</a>]</dd>
        <?php }?>
    </dl>
    <?php }?>
    <?php if(isset($_SESSION['admin']) && isset($_COOKIE['username'])){?>
    <p><a href="photo_add_dir.php">添加目录</a></p>
    <?php }?>
</div>

<?php
require ROOT_PATH.'includes/foot.inc.php';

?>
</body>
</html>
