<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/9 0009
 * Time: 18:04
 */
//防止恶意调用
if(!defined('XH')){
    exit('非法调用');
}
if(!function_exists('_alert_back')){
    exit('_alert_back()函数不存在！请检查。');
}
if(!function_exists('_mysql_string')){
    exit('_mysql_string()函数不存在！请检查。');
}

/**
 *_check_username表示检测并过滤用户名
 * @access public
 * @param string $_string 受污染的用户名
 * @param int $_min_num 最小位数
 * @param int $_max_num 最大位数
 * @return string 过滤后的用户名
 *
 */
function _check_username($_string,$_min_num = 2,$_max_num = 20){
    //去掉两边的空格
    $_string = trim($_string);

    //名字的长度小于3位或者大于20位都不可以
    if(mb_strlen($_string,'utf-8') < $_min_num || mb_strlen($_string,'utf-8') > $_max_num){
        _alert_back("用户名必须大于".$_min_num."小于".$_max_num."位!");
    }

    //限制敏感字符
    $_char_pattern = '/[<>\'\"\ \   ]/';
    if(preg_match($_char_pattern,$_string)){
        _alert_back("用户名不得包括敏感字符！");
    }

    //将字符串转义输入
    #下面这个函数缺少一个连接数据库的资源句柄mysqli_real_escape_string(,)
    return _mysql_string($_string);
}

/**
 * _check_password验证密码
 * @access public
 * @param string $_first_pass
 * @param int $_min_num
 * @return string $_fist_num 返回一个加密后的密码
 */
function _check_password($_first_pass,$_min_num = 6){
    //判断密码
    if(strlen($_first_pass) < $_min_num){
        _alert_back('密码不得少于'.$_min_num.'位');
    }
    return sha1($_first_pass);
}

function _check_time($_string){
    $_time = array('0','1','2','3');
    if(!in_array($_string,$_time)){
        _alert_back('保留方式错误！');
    }
    return _mysql_string($_string);
}

/**
 * 生成登录cookies
 * @param $_username
 * @param $_uni
 *
 */
function _set_cookies($_username,$_uni,$_time){
    switch ($_time){
        case '0'://浏览器进程
            setcookie('username',$_username);
            setcookie('uni',$_uni);
            break;
        case '1'://一天有效期
            setcookie('username',$_username,time()+86400);
            setcookie('uni',$_uni,time()+86400);
            break;
        case '2'://一周有效期
            setcookie('username',$_username,time()+604800);
            setcookie('uni',$_uni,time()+604800);
            break;
        case '3'://一个月有效期
            setcookie('username',$_username,time()+2592000);
            setcookie('uni',$_uni,time()+2592000);
            break;
    }
}

?>