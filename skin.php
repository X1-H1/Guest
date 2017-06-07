<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/19 0019
 * Time: 0:09
 */
//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');

//定义个常量，用来指定本页的内容
define('SCRIPT','article');

//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快
$_skinurl = $_SERVER['HTTP_REFERER'];
//必须从上一页点击过来，而且必须有ID
if(empty($_skinurl) || !isset($_GET['id'])){
    _alert_back('非法操作');
}else{
    //最好判断一下ID必须是123中的一个，否则不行
    //生成一个cookie，用来保存皮肤的种类
    setcookie('skin',$_GET['id']);
    _location(null,$_skinurl);
}

?>