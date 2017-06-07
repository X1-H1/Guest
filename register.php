<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/5 0005
 * Time: 13:50
 */

session_start();
//本页面不需要在此处显示验证码
//echo $_SESSION['code'];
//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');
//定义个常量，用来指定本页的内容
define('SCRIPT','register');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快
//登录状态
_login_state();
//开始处理提交的表单数据
if(@$_GET['action'] == 'register'){
    if(empty($_system['register'])){
        exit("请不要尝试非法注册！");
    }
    //为了防止恶意注册，跨站攻击
    _check_code($_SESSION['code'],$_POST['yzm']);
    //引入验证文件，此处由于在if条件语句中引入文件用include最合适
    include ROOT_PATH."includes/check.func.php";
    //创建一个空数组，用来存放提交过来的合法数据
    $_clean = array();
    //可以通过唯一标识符来防止恶意注册，伪装表单跨站攻击等。
    //这个存放汝数据库的唯一标识符还有第二个用处，就是登录cookies验证
    $_clean['uni'] = _check_uniqid($_POST['uni'],$_SESSION['uni']);
    //也是一个唯一标识符，用来刚注册的用户激活处理
    $_clean['active'] = _sha1_uniqid();
    $_clean['username'] = _check_username($_POST['username'],2,20);
    $_clean['password'] = _check_password($_POST['password'],$_POST['notpassword'],6);
    $_clean['question'] = _check_question($_POST['question'],2,20);
    $_clean['answer'] = _check_answer($_POST['question'],$_POST['answer'],2,20);
    $_clean['sex'] = _check_sex($_POST['sex']);
    $_clean['face'] = _check_face($_POST['face']);
    $_clean['email'] = _check_email($_POST['email'],6,40);
    $_clean['qq'] = _check_qq($_POST['qq']);
    $_clean['url'] = _check_url($_POST['url'],40);

    //在新增用户之前要判断用户名是否重复
//    if(_fetch_array("SELECT user_username FROM tb_user WHERE user_username='{$_clean['username']}'")){
//        _alert_back('此用户名已被注册，请重新注册用户名！');
//    }
    _is_repeat("SELECT user_username FROM tb_user WHERE user_username='{$_clean['username']}' LIMIT 1",'此用户名已被注册，请重新注册用户名！');
    //测试写入数据库中,在双引号里，直接放变量是可以的，比如$_username，但是如果是数组，就必须加上{},比如{$_clean['username']}，
    _query("INSERT INTO tb_user(
                                              user_uni,
                                              user_active,
                                              user_username,
                                              user_password,
                                              user_question,
                                              user_answer,
                                              user_email,
                                              user_qq,
                                              user_url,
                                              user_sex,
                                              user_face,
                                              user_reg_time,
                                              user_last_time,
                                              user_last_ip
                                ) 
                        VALUES (
                                              '{$_clean['uni']}',
                                              '{$_clean['active']}',
                                              '{$_clean['username']}',
                                              '{$_clean['password']}',
                                              '{$_clean['question']}',
                                              '{$_clean['answer']}',
                                              '{$_clean['email']}',
                                              '{$_clean['qq']}',
                                              '{$_clean['url']}',
                                              '{$_clean['sex']}',
                                              '{$_clean['face']}',
                                              now(),
                                              now(),
                                              '{$_SERVER["REMOTE_ADDR"]}'
                               )"
    );
    if(_affected_rows() == 1){
        //获取刚刚新增的ID
        $_clean['id'] = _insert_id();
        //关闭数据库链接
        _close();
//        _session_destroy();
        //生成XML
        _set_xml('text.xml',$_clean);
        //成功写入数据库后，提示并跳转到指定的页面
        _location('注册成功！','active.php?active='.$_clean['active']);
    }else{
        //关闭数据库链接
        _close();
//        _session_destroy();
        //成功写入数据库后，提示并跳转到指定的页面
        _location('注册失败！','register.php');
    }
}
    //唯一标识符，每台电脑都不会产生相同的唯一标识符
    $_SESSION['uni'] = $_uni = _sha1_uniqid();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--注册</title>-->
<?php
    require ROOT_PATH."includes/title.inc.php"
?>
    <script type="text/javascript" src="js/code.js"></script>
    <script type="text/javascript" src="js/register.js"></script>
</head>

<body>
<?php
    require ROOT_PATH.'includes/header.inc.php';
?>
<div id="register">
    <h2>用户注册</h2>
    <?php if(!empty($_system['register'])){?>
    <form method="post" name="register" action="register.php?action=register">
        <input type="hidden" name="uni" value="<?php echo $_uni;?>"/>
        <dl>
            <dt>请填写一下内容</dt>
            <dd>用 户 名：<input type="text" name="username" class="text"/>(*必填，至少三位)</dd>
            <dd>密&nbsp;&nbsp;&nbsp;&nbsp;码：<input type="password" name="password" class="text"/>(*必填，至少六位)</dd>
            <dd>确认密码：<input type="password" name="notpassword" class="text"/>(*必填，至少六位)</dd>
            <dd>密码提示：<input type="text" name="question" class="text"/>(*必填，至少三位)</dd>
            <dd>密码回答：<input type="text" name="answer" class="text"/>(*必填，至少三位)</dd>
            <dd>性&nbsp;&nbsp;&nbsp;&nbsp;别：<input type="radio" name="sex" value="男" checked/>男
                         <input type="radio" name="sex" value="女"/>女</dd>
            <dd class="face"><input type="hidden" name="face" value="face/22.png"/><img src="face/22.png" alt="选择头像" id="fag"/></dd>
            <dd>电子邮件：<input type="text" name="email" class="text"/>(*必填，激活账户)</dd>
            <dd>&nbsp;&nbsp;Q&nbsp;&nbsp;Q&nbsp;&nbsp;：<input type="text" name="qq" class="text"/></dd>
            <dd>主页地址：<input type="text" name="url" class="text" value="http://"/></dd>
            <dd>验 证 码：<input type="text" name="yzm" class="text yzm"/><img id="code" src="code.php"/></dd>
            <dd><input type="submit" class="submit" value="注册"/></dd>
        </dl>
    </form>
    <?php }else{
        echo '<h4 style="text-align: center;padding: 20px;">本站点关闭了注册功能！</h4>';
    }?>
</div>
<?php
    require ROOT_PATH.'includes/foot.inc.php';
?>
</body>
</html>
