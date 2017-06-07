<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/14 0014
 * Time: 9:01
 */
//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');

//定义个常量，用来指定本页的内容
define('SCRIPT','thumb');

//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快
//缩略图
if(isset($_GET['filename']) && isset($_GET['percent'])){
    _thumb($_GET['filename'],$_GET['percent']);
}

?>