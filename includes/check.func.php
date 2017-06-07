<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/6 0006
 * Time: 13:41
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
    global $_system;
    //去掉两边的空格
    $_string = trim($_string);

    //名字的长度小于3位或者大于20位都不可以
    if(mb_strlen($_string,'utf-8') < $_min_num || mb_strlen($_string,'utf-8') > $_max_num){
        _alert_back("用户名必须大于".$_min_num."小于".$_max_num."位!");
    }

    //限制敏感字符
    $_char_pattern = '/[<>\'\"\ ]/';
    if(preg_match($_char_pattern,$_string)){
        _alert_back("用户名不得包括敏感字符！");
    }
    //限制敏感用户名
    $_mg = explode("|",$_system['string']);
    //告诉用户那些名字就不能注册
    $_mz_string = null;
    foreach ($_mg as $value){
        $_mz_string .= '['.$value.']'.'  ';
    }
    //这里采用绝对匹配
    if(in_array($_string,$_mg)){
        _alert_back($_mz_string.'\n以上敏感用户名不得注册');
    }
    //将字符串转义输入
    #下面这个函数缺少一个连接数据库的资源句柄mysqli_real_escape_string(,)
    return _mysql_string($_string);
}


/**
 * _check_password验证密码
 * @access public
 * @param string $_first_pass
 * @param string $_second_pass
 * @param int $_min_num
 * @return string $_fist_num 返回一个加密后的密码
 */
function _check_password($_first_pass,$_second_pass,$_min_num = 6){
    //判断密码
    if(strlen($_first_pass) < $_min_num){
        _alert_back('密码不得少于'.$_min_num.'位');
    }
    if($_first_pass != $_second_pass){
        _alert_back('两次密码不一致');
    }
    return sha1($_first_pass);
}

/**
 * 验证修改页面时提交的修改后的密码
 * @param $_string
 * @param int $_min_num
 * @return mixed
 */
function _check_modify_password($_string,$_min_num = 6){
    //判断密码
    if(!empty($_string)){
        if(strlen($_string) < $_min_num){
            _alert_back('密码不得少于'.$_min_num.'位');
        }
    }else{
        return null;
    }
    return sha1($_string);
}

/**
 * _check_question返回密码提示
 * @access public
 * @param string $_q
 * @param int $_min_num
 * @param int $_max_num
 * @return string $_q 返回密码提示
 */
function _check_question($_q,$_min_num = 2,$_max_num = 20){
    //去掉两边的空格
    $_q = trim($_q);

    if(mb_strlen($_q,'utf-8') < $_min_num || mb_strlen($_q,'utf-8') > $_max_num){
        _alert_back("密码提示必须大于".$_min_num."小于".$_max_num."位!");
    }

    //返回的是密码提示
    #此处写入数据库需要转义
    return _mysql_string($_q);
}

/**
 * _check_answer 验证密码回答
 * @param $_ques
 * @param $_answer
 * @param int $_min_num
 * @param int $_max_num
 * @return string $_answer 返回一个加密的密码回答
 */
function _check_answer($_ques,$_answer,$_min_num = 2,$_max_num = 20){
    $_answer = trim($_answer);
    if(mb_strlen($_answer,'utf-8') < $_min_num || mb_strlen($_answer,'utf-8') > $_max_num){
        _alert_back("密码回答必须大于".$_min_num."小于".$_max_num."位!");
    }

    //密码提示与密码回答不能一致
    if($_ques == $_answer){
        _alert_back('密码提示与回答不能一致');
    }

    //将密码回答加密返回
    return _mysql_string(sha1($_answer));
}


/**
 * _check_email检测邮箱是否合法
 * @access public
 * @param string $_email 提交的邮箱地址
 * @return string $_email 验证后的邮箱地址
 *
 */
function _check_email($_email,$_min_num,$_max_num){

    //可以参考1926900127@qq.com
    //[a-zA-Z0-9_] => \w
    if(!preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/',$_email)){
        _alert_back('邮件格式不正确');
    }
    if(strlen($_email) < $_min_num || strlen($_email) > $_max_num){
        _alert_back('邮件长度不合法！');
    }
    return _mysql_string($_email);
}

/**
 * _check_qq验证QQ
 * @access public
 * @param int $_qq
 * @return int $_qq返回一个验证后的QQ
 */
function _check_qq($_qq){
    if(empty($_qq)){
        return null;
    }else{
        if(!preg_match('/^[1-9]{1}[\d]{4,9}/',$_qq)){
            _alert_back('QQ号码不正确！');
        }
    }
    return _mysql_string($_qq);
}


/**
 * _check_url 网址验证
 * @access public
 * @param string $_url
 * @return string $_url 返回验证后的网址
 *
 */
function _check_url($_url,$_max_num){
    if(empty($_url) || $_url == 'http://'){
        return null;
    }else{
        if(!preg_match('/^https?:\/\/(\w+\.)?[\w-\.]+(\.\w+)+$/',$_url)){
            _alert_back('主页地址不正确！');
        }
        if(strlen($_url) > $_max_num){
            _alert_back('网址长度唱过本站要求');
        }
    }
    return _mysql_string($_url);
}

/**
 * _check_uniqid 检测本机产生的唯一标识符是否唯一
 * @param string $_first_uni
 * @param string $_second_uni
 * @return string $_first_uni
 *
 */
function _check_uniqid($_first_uni,$_second_uni){
    if(strlen($_first_uni) != 40 || $_first_uni != $_second_uni){
        _alert_back('唯一标识符有误！');
    }
    return _mysql_string($_first_uni);
}

/**
 * _check_sex 性别
 * @param string $_string
 * @return string $_string
 *
 */
function _check_sex($_string){
    return _mysql_string($_string);
}

/**
 * _check_face 头像
 * @param string $_string
 * @return string $_string
 *
 */
function _check_face($_string){
    return _mysql_string($_string);
}

/**
 *
 *
 */
function _check_switch(){

}



function _check_autograph($_string,$_num){
    if(mb_strlen($_string,'utf8') > $_num){
        _alert_back("个性签名不能大于'.$_num.'个字符");
    }
    return $_string;
}
/**
 * 检测短信长度是否合法
 * @param $_string
 * @return mixed
 */
function _check_content($_string){
    if(mb_strlen($_string,'utf8') <10 || mb_strlen($_string,'utf8') > 600){
        _alert_back("短信内容不能少于10个字符或者大于600个字符");
    }
    return $_string;
}

/**
 * 检查帖子标题长度是否有效
 * @param $_string
 * @param $_min
 * @param $_max
 * @return mixed
 */
function _check_post_title($_string,$_min,$_max){
    if(mb_strlen($_string,'utf8') < $_min || mb_strlen($_string,'utf8') > $_max){
        _alert_back("帖子标题不能少于".$_min."个字符或者大于".$_max."个字符");
    }
    return $_string;
}


function _check_post_content($_string,$_min){
    if(mb_strlen($_string,'utf8') < $_min){
        _alert_back("帖子内容不能少于".$_min."位字符");
    }
    return $_string;
}

/**
 * 检查创建的相册的目录名
 * @param $_string
 * @param $_min
 * @param $_max
 * @return mixed
 */
function _check_dir_name($_string,$_min,$_max){
    if(mb_strlen($_string,'utf8') < $_min || mb_strlen($_string,'utf8') > $_max){
        _alert_back("名称不能少于".$_min."位或者不能大于".$_max."位！");
    }
    return $_string;
}

/**
 * 检测创建相册的密码
 * @param $_string
 * @param $_min_num
 * @return string
 */
function _check_dir_password($_string,$_min_num){
        if(strlen($_string) < $_min_num){
            _alert_back('密码不得少于'.$_min_num.'位');
        }
    return sha1($_string);
}

/**
 * 检测相册描述内容
 * @param $_string
 * @param $_max
 * @return mixed
 */
function _check_dir_content($_string,$_max){
    if(mb_strlen($_string,'utf8') > $_max){
        _alert_back("内容描述不得大于".$_max."位！");
    }
    return $_string;
}


function _check_photo_url($_string){
    if(empty($_string)){
        _alert_back("地址不能为空！");
    }
    return $_string;
}
?>