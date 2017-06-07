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
define('SCRIPT','photo_add_img');

//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

//此页面登录的等级必须是会员
if(@!$_COOKIE['username']){
    _alert_back("非法登录！");
}
//保存图片信息如表
if(@$_GET['action'] == 'addimg'){
    if($_rows = _fetch_array("SELECT user_id FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")) {
        //为了防止cookie伪造，还要比对一下唯一标识符
        _check_uni(@$_rows['user_uni'], @$_COOKIE['uniqid']);//此处的COOKIE中的唯一编码uniqid虽然是变量但是要全写，否则会出错
        include "includes/check.func.php";
        //接收数据
        $_clean = array();
        $_clean['name'] = _check_dir_name($_POST['name'],2,20);
        $_clean['url']  = _check_photo_url($_POST['url']);
        $_clean['content']  = $_POST['content'];
        $_clean['sid']  = $_POST['sid'];
        $_clean = _mysql_second_string($_clean);
        //写入数据库
        _query("INSERT INTO tb_photo(
                                        p_name,
                                        p_url,
                                        p_content,
                                        p_sid,
                                        p_username,
                                        p_date
                                    ) 
                            VALUES (
                                        '{$_clean['name']}',
                                        '{$_clean['url']}',
                                        '{$_clean['content']}',
                                        '{$_clean['sid']}',
                                        '{$_COOKIE['username']}',
                                        now()
        )");
        if(_affected_rows() == 1){
            _close();
            _location('图片添加成功！','photo_show.php?id='.$_clean['sid']);
        }else{
            _close();
            _alert_back('图片添加失败！');
        }
    }else{
        _alert_back("非法登录！");
    }

}
//取值
if(isset($_GET['id'])){
    if($_rows = _fetch_array("SELECT d_id,d_dir FROM tb_dir WHERE d_id='{$_GET['id']}' LIMIT 1")){
        $_html = array();
        $_html['id'] = $_rows['d_id'];
        $_html['dir'] = $_rows['d_dir'];
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
    <script type="text/javascript" src="js/photo_add_img.js"></script>
</head>

<body>
<?php
require ROOT_PATH.'includes/header.inc.php';
?>
<div id="photo">
    <h2>上传图片</h2>
    <form action="?action=addimg" method="post">
        <input type="hidden" name="sid" value="<?php echo $_html['id'];?>"/>
    <dl>
        <dd>图片名称：<input type="text" name="name" class="text"/></dd>
        <dd>图片地止：<input type="text" name="url" id="url" readonly="readonly" class="text"/> <a href="javascript:;" title="<?php echo $_html['dir'];?>" id="up">上传</a></dd>
        <dd>图片描述：<textarea name="content"></textarea> </dd>
        <dd><input type="submit" class="submit" name="submit" value="添加图片"/></dd>
    </dl>
    </form>
</div>

<?php
require ROOT_PATH.'includes/foot.inc.php';

?>
</body>
</html>
