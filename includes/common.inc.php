<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/5 0005
 * Time: 12:27
 */
//防止恶意调用
if(!defined('XH')){
    exit('非法调用');
}

//转换硬路径常量
define('ROOT_PATH',substr(dirname(__FILE__),0,-8));
//防止PHP版本太低
if(PHP_VERSION < '5.4.0'){
   exit('PHP版本太低，不能运行此程序');
}

//引入函数库
require ROOT_PATH.'includes/global.func.php';

require ROOT_PATH.'includes/mysql.function.php';

//创建一个自动转义状态的常量
//get_magic_quotes_gpc为关闭时返回 0，否则返回 1。在 PHP 5.4.O 起将始终返回 FALSE
define('GPC',get_magic_quotes_gpc());


//执行耗时

define('START_TIME',_runtime());
//也可以用超级全局变量：$GLOBAL['START_TIME'] = _runtime();在foot.inc.php总用$GLOBAL['START_TIME']来接收一下


//链接数据库
//define('DB_HOST','localhost');
//define('DB_USER','root');
//define('DB_PWD','');
//define('DB_NAME','db_chat');
//$conn = mysqli_connect(DB_HOST,DB_USER,DB_PWD,DB_NAME)or die("数据库链接失败！！！");//.mysqli_connect_error()
//选定字符集
//mysqli_set_charset($conn,'utf8')or die("字符集设置错误！！！");
_connection();//链接MySQL数据库以及具体数据库
_set_charset();//设置字符集

//此处为测试注册用户名是否重名问题
//$query = mysqli_query($conn,"SELECT user_username FROM tb_user WHERE user_username='一梦千年'");
//print_r(mysqli_fetch_array($query,MYSQLI_ASSOC));

//短信提醒
$_message = _fetch_array(@"SELECT COUNT(m_id) AS count FROM tb_message WHERE m_state=0 AND m_touser='{$_COOKIE['username']}'");
//print_r($_message['count']);
//下面的$_message_html变量在视频中用的是$GLOBALS['message']
if(empty($_message['count'])){
    $_message_html = '<strong class="read"><a href="member_message.php">(0)</a></strong>';
}else{
    $_message_html = '<strong class="noread"><a href="member_message.php">('.$_message['count'].')</a></strong>';
}

//网站系统设置初始化
if($_rows = _fetch_array("SELECT 
                                  s_webname,
                                  s_article,
                                  s_blog,
                                  s_photo, 
                                  s_skin, 
                                  s_string, 
                                  s_post, 
                                  s_re, 
                                  s_code,
                                  s_register                                  
                            FROM 
                                  tb_system 
                            WHERE 
                                  s_id=1 
                            LIMIT 1")){
    $_system = array();
    $_system['webname'] = $_rows['s_webname'];
    $_system['article'] = $_rows['s_article'];
    $_system['blog'] = $_rows['s_blog'];
    $_system['photo'] = $_rows['s_photo'];
    $_system['skin'] = $_rows['s_skin'];
    $_system['post'] = $_rows['s_post'];
    $_system['re'] = $_rows['s_re'];
    $_system['code'] = $_rows['s_code'];
    $_system['register'] = $_rows['s_register'];
    $_system['string'] = $_rows['s_string'];
    $_system = _html($_system);

    //如果有skin的cookie那么就替代系统数据库里的皮肤
    if(@$_COOKIE['skin']){
        $_system['skin'] = $_COOKIE['skin'];
    }
}else{
    exit("系统表异常！");
}




?>