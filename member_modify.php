<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/13 0013
 * Time: 17:33
 */

session_start();

//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');

//定义个常量，用来指定本页的内容
define('SCRIPT','member_modify');

//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

//修改资料
if(@$_GET['action'] == 'modify'){
    _check_code($_SESSION['code'],$_POST['yzm']);
    if($_rows = _fetch_array("SELECT user_id FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")){
        //为了防止cookie伪造，还要比对一下唯一标识符
        _check_uni(@$_rows['user_uni'],@$_COOKIE['uniqid']);//此处的COOKIE中的唯一编码uniqid虽然是变量但是要全写，否则会出错
        include ROOT_PATH."includes/check.func.php";
        $_clean = array();
        $_clean['password'] = _check_modify_password($_POST['password']);
        $_clean['sex'] = _check_sex($_POST['sex']);
        $_clean['face'] = _check_face($_POST['face']);
        $_clean['switch'] = $_POST['switch'];
        $_clean['autograph'] = _check_autograph($_POST['autograph'],200);
        $_clean['email'] = _check_email($_POST['email'],6,40);
        $_clean['qq'] = _check_qq($_POST['qq']);
        $_clean['url'] = _check_url($_POST['url'],40);
        //修改资料
        if(empty($_clean['password'])){
            _query("UPDATE tb_user SET 
                                        user_sex = '{$_clean['sex']}',
                                        user_face = '{$_clean['face']}',
                                        user_switch = '{$_clean['switch']}',
                                        user_autograph = '{$_clean['autograph']}',
                                        user_email ='{$_clean['email']}',
                                        user_qq = '{$_clean['qq']}',
                                        user_url = '{$_clean['url']}'
                                  WHERE 
                                        user_username = '{$_COOKIE['username']}'
                                        ");
        }else{
            _query("UPDATE tb_user SET 
                                        user_password = '{$_clean['password']}',
                                        user_sex = '{$_clean['sex']}',
                                        user_face = '{$_clean['face']}',
                                        user_switch = '{$_clean['switch']}',
                                        user_autograph = '{$_clean['autograph']}',
                                        user_email ='{$_clean['email']}',
                                        user_qq = '{$_clean['qq']}',
                                        user_url = '{$_clean['url']}'
                                  WHERE 
                                        user_username = '{$_COOKIE['username']}'
                                        ");
        }
    }

    //判断是否修改成功
    if(_affected_rows() == 1){
        //关闭数据库链接
        _close();
//        _session_destroy();
        //成功写入数据库后，提示并跳转到指定的页面
        _location('修改成功！','member.php');
    }else{
        //关闭数据库链接
        _close();
//        _session_destroy();
        //成功写入数据库后，提示并跳转到指定的页面
        _location('请确认输入的数据是否有效','member_modify.php');
    }
}

//是否正常登陆
if(isset($_COOKIE['username'])){
    //获取数据
    $_rows = _fetch_array("SELECT user_username,user_sex,user_url,user_qq,user_face,user_switch,user_autograph,user_email FROM tb_user WHERE user_username='{$_COOKIE['username']}'");
    if($_rows){
        $_html = array();
        $_html['username'] = $_rows['user_username'];
        $_html['sex'] = $_rows['user_sex'];
        $_html['url'] = $_rows['user_url'];
        $_html['qq'] = $_rows['user_qq'];
        $_html['face'] = $_rows['user_face'];
        $_html['switch'] = $_rows['user_switch'];
        $_html['autograph'] = $_rows['user_autograph'];
        $_html['email'] = $_rows['user_email'];
        $_html = _html($_html);

        //性别选择
        if($_html['sex'] == '男'){
            $_html['sex_html'] = '<input type="radio" name="sex" value="男" checked="checked"/>男 <input type="radio" name="sex" value="女"/>女';
        }elseif ($_html['sex'] == '女'){
            $_html['sex_html'] = '<input type="radio" name="sex" value="男"/>男 <input type="radio" name="sex" value="女" checked="checked"/>女';
        }
        //头像选择
        $_html['face_html'] = '<select name="face">';
        foreach (range(1,83) as $num){
            if($_html['face'] == 'face/'.$num.'.png'){
                $_html['face_html'] .= '<option value="face/'.$num.'.png" selected="selected">face/'.$num.'.png</option>';
            }else{
                $_html['face_html'] .= '<option value="face/'.$num.'.png">face/'.$num.'.png</option>';
            }
        }
        $_html['face_html'] .='</select>';

        //签名开关
        if($_html['switch'] == 1){
            $_html['switch_html'] = '<input type="radio" name="switch" value="1" checked="checked"/>启用<input type="radio" name="switch" value="0"/>禁用';
        }elseif ($_html['switch'] == 0){
            $_html['switch_html'] = '<input type="radio" name="switch" value="1"/>启用<input type="radio" name="switch" value="0" checked="checked"/>禁用';
        }

    }else{
        _alert_back('此用户不存在');
    }
}else{
    _alert_back('非法登录！');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--个人中心</title>-->
    <?php
        require ROOT_PATH."includes/title.inc.php"
    ?>
    <script type="text/javascript" src="js/code.js"></script>
    <script type="text/javascript" src="js/member_modify.js"></script>
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
        <h2>会员管理中心</h2>
        <form action="?action=modify" method="post">
        <dl>
            <dd>用 户 名：<input type="text" name="username" value="<?php echo $_html['username'];?>"/></dd>
            <dd>密&nbsp;&nbsp;&nbsp;&nbsp;码：<input type="password" class="pwd" name="password"/> (不填则不修改)</dd>
            <dd>性&nbsp;&nbsp;&nbsp;&nbsp;别：<?php echo $_html['sex_html'];?></dd>
            <dd>头&nbsp;&nbsp;&nbsp;&nbsp;像：<?php echo $_html['face_html'];?></dd>
            <dd>电子邮件：<input class="text" type="text" name="email" value="<?php echo $_html['email'];?>"/></dd>
            <dd>主&nbsp;&nbsp;&nbsp;&nbsp;页：<input class="text" type="text" name="url" value="<?php echo $_html['url'];?>"/></dd>
            <dd>Q&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Q：<input class="text" type="text" name="qq" value="<?php echo $_html['qq'];?>"/></dd>
            <dd>个性签名：<?php echo $_html['switch_html']?>（可以使用UBB代码）
                <p><textarea name="autograph"><?php echo $_html['autograph']?></textarea></p></dd>
            <dd>验 证 码：<input type="text" name="yzm" class="text yzm"/><img id="code" src="code.php"/> <input class="submit" type="submit" value="修改资料"/></dd>
        </dl>
        </form>
    </div>
</div>

<?php
    require ROOT_PATH.'includes/foot.inc.php';
?>
</body>
</html>

