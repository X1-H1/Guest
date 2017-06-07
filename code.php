<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/5 0005
 * Time: 22:39
 */
//开启SESSION
session_start();

//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');

//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快


//运行验证码函数
//验证码大小为：75*25，默认位数是4位，如果要6位，长度推荐125，如果要8位，推荐175以此类推
//第四个参数是否要边框，要的话为填true,默认为false
//可以通过数据库的方法，来设置验证码的各种属性
_code();

?>

