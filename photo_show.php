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
define('SCRIPT','photo_show');

//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

//删除相片
if(@$_GET['action'] == 'delete' && isset($_GET['id'])){
    if($_rows = _fetch_array("SELECT user_id FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")) {
        //为了防止cookie伪造，还要比对一下唯一标识符
        _check_uni(@$_rows['user_uni'], @$_COOKIE['uniqid']);
        //取得这张图片的发布者
        if($_rows = _fetch_array("SELECT p_username,p_url,p_id,p_sid FROM tb_photo WHERE p_id='{$_GET['id']}' LIMIT 1")){
            $_html = array();
            $_html['username'] = $_rows['p_username'];
            $_html['id'] = $_rows['p_id'];
            $_html['sid'] = $_rows['p_sid'];
            $_html['url'] = $_rows['p_url'];

            $_html = _html($_html);
            //判断删除图片的身份是否合法
            if($_html['username'] == $_COOKIE['username'] || isset($_SESSION['admin'])){
                //首先删除图片的数据库信息
                _query("DELETE FROM tb_photo WHERE p_id='{$_html['id']}'");

                if(_affected_rows() == 1){
                    //删除图片的物理地址
                    if(file_exists($_html['url'])){
                        unlink($_html['url']);
                    }else{
                        _alert_back("磁盘里已不存在此图！");
                    }
                    _close();
                    _location('删除成功！','photo_show.php?id='.$_html['sid']);
                }else{
                    _close();
                    _alert_back('删除失败！');
                }


            }else{
                _alert_back("非法操作！");
            }
        }else{
            _alert_back("不存在此图片！");
        }

    }else{
        _alert_back("非法登录！");
    }
}



//取值
if(isset($_GET['id'])){
    if($_rows = _fetch_array("SELECT d_id,d_name,d_type FROM tb_dir WHERE d_id='{$_GET['id']}' LIMIT 1")){
        $_dirhtml = array();
        $_dirhtml['id'] = $_rows['d_id'];
        $_dirhtml['name'] = $_rows['d_name'];
        $_dirhtml['type'] = $_rows['d_type'];
        $_dirhtml = _html($_dirhtml);

        //对比加密相册的验证信息
        if(@$_POST['password']){
            if($_rows = _fetch_array("SELECT d_id FROM tb_dir WHERE d_password='".sha1($_POST['password'])."' LIMIT 1")){
                //生成cookie
                setcookie('photo'.$_dirhtml['id'],$_dirhtml['name']);
                //重定向
                _location(null,'photo_show.php?id='.$_dirhtml['id']);
            }else{
                _alert_back("相册密码不正确！");
            }
        }

    }else{
        _alert_back("不存在此相册！");
    }
}else{
    _alert_back("非法操作！");
}
//$_filename = 'photo/1489154165/1489396970.jpg';
$_percent = 0.3;
global $_pagenum,$_pagesize,$_system,$_id;
$_id = 'id='.$_dirhtml['id'].'&';
_page("SELECT p_id FROM tb_photo WHERE p_sid='{$_dirhtml['id']}'",$_system['photo']);//第一个参数是获得总的数据，第二个参数是每页显示的数据量
$_result = _query("SELECT p_id,p_username,p_name,p_url,p_content,p_readcount,p_commendcount FROM tb_photo WHERE p_sid='{$_dirhtml['id']}' ORDER BY p_date DESC LIMIT $_pagenum,$_pagesize");

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
    <h2><?php echo $_dirhtml['name'];?></h2>
    <?php
    if(empty($_dirhtml['type']) || @$_COOKIE['photo'.$_dirhtml['id']] == $_dirhtml['name'] || isset($_SESSION['admin'])){
        $_html = array();
        while($_rows = _fetch_array_list($_result)){
            $_html['username'] = $_rows['p_username'];
            $_html['name'] = $_rows['p_name'];
            $_html['url'] = $_rows['p_url'];
            $_html['id'] = $_rows['p_id'];
            $_html['content'] = $_rows['p_content'];
            $_html['readcount'] = $_rows['p_readcount'];
            $_html['commendcount'] = $_rows['p_commendcount'];
            $_html = _html($_html);
    ?>
    <dl>
        <dt><a href="photo_detail.php?id=<?php echo $_html['id']?>"><img src="thumb.php?filename=<?php echo $_html['url']?>&percent=<?php echo $_percent?>" alt="<?php echo $_html['content']?>"/></a></dt>
        <dd><a href="photo_detail.php?id=<?php echo $_html['id']?>">名称：<?php echo $_html['name'];?></a></dd>
        <dd>阅(<strong><?php echo $_html['readcount']; ?></strong>) 评(<strong><?php echo $_html['commendcount']; ?></strong>) 上传者：[<?php echo $_html['username']; ?>]</dd>
        <?php
            if($_html['username'] == @$_COOKIE['username'] || isset($_SESSION['admin'])){
        ?>
        <dd>[<a href="photo_show.php?action=delete&id=<?php echo $_html['id']?>">删除</a>]</dd>
        <?php
            }
        ?>
    </dl>
    <?php
        }
        _free_result($_result);
        //此处调用分页函数; 1：调用数字分页，2：调用文本分页
        _paging(1);
    ?>
    <p><a href="photo_add_img.php?id=<?php echo $_dirhtml['id'];?>">上传图片</a></p>
    <?php
    }else{
        echo '<form method="post" action="photo_show.php?id='.$_dirhtml['id'].'">';
        echo '<p>请输入密码：<input type="password" name="password"/><input type="submit" value="确认"></p>';
        echo '</form>';
    }
    ?>
</div>

<?php
require ROOT_PATH.'includes/foot.inc.php';

?>
</body>
</html>
