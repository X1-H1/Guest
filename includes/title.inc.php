<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/5 0005
 * Time: 17:32
 */
//防止恶意调用
if(!defined('XH')){
    exit('非法调用');
}
//防止非HTML页面调用
if(!defined('SCRIPT')){
    exit('Script Error!');
}
global $_system;
?>
<title><?php echo $_system['webname'];?></title>
<link rel="shortcut icon" href="flag.ico"/>
<link rel="stylesheet" type="text/css" href="styles/<?php echo $_system['skin']?>/first.css"/>
<link rel="stylesheet" type="text/css" href="styles/<?php echo $_system['skin']?>/<?php echo SCRIPT;?>.css"/>
