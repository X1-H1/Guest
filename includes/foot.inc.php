<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/5 0005
 * Time: 12:14
 */
//防止恶意调用
if(!defined('XH')){
    exit('非法调用');
}
_close();
?>
<div id="foot">
    <p>本程序耗时：<?php echo round(_runtime() - START_TIME,4);?>秒</p>
    <p>版权所有，翻版必究！</p>
    <p>本程序有<span>雪痕</span>参照视频而写</p>
</div>

