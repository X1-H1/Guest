<?php
session_start();
//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');
//定义个常量，用来指定本页的内容
define('SCRIPT','index');
//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快
//读取XML文件
$_html = _html(_get_xml('text.xml'));
//读取帖子列表
global $_pagenum,$_pagesize,$_system;
_page("SELECT a_id FROM tb_article WHERE a_reid=0",$_system['article']);
$_result = _query("SELECT a_id,a_type,a_title,a_readcount,a_commendcount FROM tb_article WHERE a_reid=0 ORDER BY a_date DESC LIMIT $_pagenum,$_pagesize");

//最新图片,找到时间点最后上传的那张图片，并且是非公开的
$_photo = _fetch_array("SELECT p_id AS id,p_name AS name,p_url AS url FROM tb_photo WHERE p_sid IN (SELECT d_id FROM tb_dir WHERE d_type=0) ORDER BY p_date DESC LIMIT 1");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>--><?php //echo $_system['webname']?><!----首页</title>-->
<?php
    require ROOT_PATH."includes/title.inc.php";
?>
    <script type="text/javascript" src="js/send_message.js"></script>
    <!--  此处的send_message.js相当于视频中的blog.js  -->
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/30 0030
 * Time: 12:10
 */
    require ROOT_PATH.'includes/header.inc.php';

?>


<div id="list">
    <h2>帖子列表</h2>
    <a href="post.php" class="post">发表帖子</a>
    <ul class="article">
        <?php
            $_htmllist = array();
            while($_rows = _fetch_array_list($_result)){
                $_htmllist['id'] = $_rows['a_id'];
                $_htmllist['type'] = $_rows['a_type'];
                $_htmllist['readcount'] = $_rows['a_readcount'];
                $_htmllist['commendcount'] = $_rows['a_commendcount'];
                $_htmllist['title'] = $_rows['a_title'];
                $_htmllist = _html($_htmllist);

                echo '<li class="icon'.$_htmllist['type'].'"><em>阅读数(<strong>'.$_htmllist['readcount'].'</strong>)评论数(<strong>'.$_htmllist['commendcount'].'</strong>)</em><a href="article.php?id='.$_htmllist['id'].'">'._title($_htmllist['title'],20).'</a> </li>';
            }
            _free_result($_result);
        ?>
    </ul>
    <?php
        //此处调用分页函数; 1：调用数字分页，2：调用文本分页
        _paging(2);
    ?>
</div>

<div id="user">
    <h2>新进会员</h2>
    <dl>
        <dd class="user"><?php echo $_html['username'];?>(<?php echo $_html['sex'];?>)</dd>
        <dt><img src="<?php echo $_html['face'];?>" alt="<?php echo $_html['username'];?>"></dt>
        <dd class="message"><a href="javascript:;" name="message" title="<?php echo $_html['id'];?>">发消息</a></dd>
        <dd class="friend"><a href="javascript:;" name="friend" title="<?php echo $_html['id'];?>">加好友</a></dd>
        <dd class="guest">写留言</dd>
        <dd class="flower"><a href="javascript:;" name="flower" title="<?php echo $_html['id'];?>">送花朵</a></dd>
        <dd class="email">邮件：<a href="mailto:<?php echo $_html['email'];?>"><?php echo $_html['email'];?></a></dd>
        <dd class="url">网址：<a href="<?php echo $_html['url'];?>" target="_blank"><?php echo $_html['url'];?></a></dd>
    </dl>
</div>

<div id="pics">
    <h2>最新图片 -- <?php echo $_photo['name']?></h2>
    <a href="photo_detail.php?id=<?php echo $_photo['id'];?>"><img src="thumb.php?filename=<?php echo $_photo['url']?>&percent=0.5" alt="<?php echo $_photo['name']?>"/></a>
</div>

<?php
    require ROOT_PATH.'includes/foot.inc.php';
?>




</body>
</html>

