<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/10 0010
 * Time: 10:51
 */
session_start();
//定义一个常量，用来授权调用includes里面的文件
define('XH','bee');

//定义个常量，用来指定本页的内容
define('SCRIPT','photo_detail');

//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

//评论
if(@$_GET['action'] == 'rephoto'){
    if(!empty($_system['code'])){
        //验证码验证
        _check_code($_SESSION['code'],$_POST['yzm']);
    }

    if($_rows = _fetch_array("SELECT user_id FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")) {
        //为了防止cookie伪造，还要比对一下唯一标识符
        _check_uni(@$_rows['user_uni'], @$_COOKIE['uniqid']);
        //接收数据
        $_clean = array();
        $_clean['sid'] = $_POST['sid'];
        $_clean['title'] = $_POST['title'];
        $_clean['content'] = $_POST['content'];
        $_clean['username'] = $_COOKIE['username'];
        $_clean = _mysql_second_string($_clean);

        //写入数据库
        _query("INSERT INTO tb_photo_commend(pc_sid,pc_username,pc_title,pc_content,pc_date) VALUES ('{$_clean['sid']}','{$_clean['username']}','{$_clean['title']}','{$_clean['content']}',now())");
        if(_affected_rows() == 1){
            _query("UPDATE tb_photo SET p_commendcount=p_commendcount+1 WHERE p_id='{$_clean['sid']}'");
            _close();
            _location('评论成功！','photo_detail.php?id='.$_clean['sid']);
        }else{
            //关闭数据库链接
            _close();
            _alert_back('评论失败！');
        }
    }else{
        _alert_back("非法登录！");
    }
}

//取值
if(isset($_GET['id'])){
    if($_rows = _fetch_array("SELECT p_id,p_sid,p_name,p_url,p_username,p_readcount,p_commendcount,p_date,p_content FROM tb_photo WHERE p_id='{$_GET['id']}' LIMIT 1")) {

        //防止加密相册图片穿插访问,可以先取得这个图片的sid，也就是它的目录
        //然后在判断这个目录是否是加密的，如果是加密的，再判断是否有对应的cookie存在，并且对应相应的值
        //管理员不受这个限制
        if (!isset($_SESSION['admin'])) { //我自己动手做的时候，貌似不要添加这个最外层的循环都可以起到同样的效果
            if ($_dirs = _fetch_array("SELECT d_type,d_id,d_name FROM tb_dir WHERE d_id='{$_rows['p_sid']}'")) {
                if (!empty($_dirs['d_type']) && @$_COOKIE['photo' . $_dirs['d_id']] != $_dirs['d_name']) {
                    _alert_back("非法操作!");
                }
            } else {
                _alert_back("相册目录表出错了！");
            }
        }

        //累计阅读量
        _query("UPDATE tb_photo SET p_readcount=p_readcount+1 WHERE p_id='{$_GET['id']}'");

        $_html = array();
        $_html['id'] = $_rows['p_id'];
        $_html['sid'] = $_rows['p_sid'];
        $_html['name'] = $_rows['p_name'];
        $_html['url'] = $_rows['p_url'];
        $_html['username'] = $_rows['p_username'];
        $_html['readcount'] = $_rows['p_readcount'];
        $_html['commendcount'] = $_rows['p_commendcount'];
        $_html['date'] = $_rows['p_date'];
        $_html['content'] = $_rows['p_content'];
        $_html = _html($_html);

        //创建一个全局变量，做一个带参的分页
        global $_id;
        $_id = 'id='.$_html['id'].'&';

        //读取评论
        global $_pagenum,$_pagesize,$_page;
        _page("SELECT pc_id FROM tb_photo_commend WHERE pc_sid='{$_html['id']}'",10);
        $_result = _query("SELECT pc_username,pc_title,pc_content,pc_date FROM tb_photo_commend WHERE pc_sid='{$_html['id']}' ORDER BY pc_date ASC LIMIT $_pagenum,$_pagesize");

        //上一页，取得比自己大的ID中，最小的那个即可。
        $_html['preid'] = _fetch_array("SELECT min(p_id) AS id FROM tb_photo WHERE p_sid='{$_html['sid']}' AND p_id>'{$_html['id']}' LIMIT 1");

        if(!empty($_html['preid']['id'])){
            $_html['pre'] = '<a href="photo_detail.php?id='.$_html['preid']['id'].'#pre">上一张</a>';
        }else{
            $_html['pre'] = '<span>到头了</span>';
        }

        //下一页，取得比自己小的ID中，最大的那个即可。
        $_html['nextid'] = _fetch_array("SELECT max(p_id) AS id FROM tb_photo WHERE p_sid='{$_html['sid']}' AND p_id<'{$_html['id']}' LIMIT 1");

        if(!empty($_html['nextid']['id'])){
            $_html['next'] = '<a href="photo_detail.php?id='.$_html['nextid']['id'].'#next">下一张</a>';
        }else{
            $_html['next'] = '<span>到底了</span>';
        }

    }else{
        _alert_back("不存在此图片！");
    }
}else{
    _alert_back("非法操作！");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--好友</title>-->
    <?php
    require ROOT_PATH."includes/title.inc.php"
    ?>
    <script type="text/javascript" src="js/article.js"></script>
    <script type="text/javascript" src="js/code.js"></script>
</head>

<body>
<?php
require ROOT_PATH.'includes/header.inc.php';
?>
<div id="photo">
    <h2>图片详情</h2>
    <a name="pre"></a><a name="next"></a>
    <dl class="detail">
        <dd class="name"><?php echo $_html['name'];?></dd>
        <dt><?php echo $_html['pre'];?><img src="<?php echo $_html['url'];?>" alt="<?php echo $_html['name'];?>"/><?php echo $_html['next'];?></dt>
        <dd>【<a href="photo_show.php?id=<?php echo $_html['sid']?>">返回列表</a>】</dd>
        <dd>浏览量(<strong><?php echo $_html['readcount'];?></strong>) 评论量(<strong><?php echo $_html['commendcount'];?></strong>)上传者：【<?php echo $_html['username'];?>】发表于：<?php echo $_html['date'];?></dd>
        <dd>简介：<?php echo $_html['content'];?></dd>
    </dl>

    <?php
        $_i = 1;
        while($_rows = _fetch_array_list($_result)) {
            $_html['username'] = $_rows['pc_username'];
            $_html['retitle'] = $_rows['pc_title'];
            $_html['content'] = $_rows['pc_content'];
            $_html['date'] = $_rows['pc_date'];
            $_html = _html($_html);

        //拿出用户名，去查找用户名信息
        if($_rows = _fetch_array("SELECT
                                         user_id,
                                         user_sex,
                                         user_face,
                                         user_switch,
                                         user_autograph,
                                         user_email,
                                         user_url
                                    FROM
                                         tb_user
                                    WHERE 
                                         user_username='{$_html['username']}'")) {
            //提取用户信息
            $_html['userid'] = $_rows['user_id'];
            $_html['face'] = $_rows['user_face'];
            $_html['switch'] = $_rows['user_switch'];
            $_html['autograph'] = $_rows['user_autograph'];
            $_html['sex'] = $_rows['user_sex'];
            $_html['email'] = $_rows['user_email'];
            $_html['url'] = $_rows['user_url'];
            $_html = _html($_html);
        }else{
            //这个用户可能已经被删除了
        }

        ?>
    <p class="line"></p>
    <div class="re">
        <dl>
            <dd class="user"><?php echo $_html['username']; ?>(<?php echo $_html['sex']; ?>)</dd>
            <dt><img src="<?php echo $_html['face']; ?>" alt="<?php echo $_html['username']; ?>"></dt>
            <dd class="message"><a href="javascript:;" name="message" title="<?php echo $_html['userid']; ?>">发消息</a>
            </dd>
            <dd class="friend"><a href="javascript:;" name="friend"
                                  title="<?php echo $_html['userid']; ?>">加好友</a></dd>
            <dd class="guest">写留言</dd>
            <dd class="flower"><a href="javascript:;" name="flower"
                                  title="<?php echo $_html['userid']; ?>">送花朵</a></dd>
            <dd class="email">邮件：<a
                    href="mailto:<?php echo $_html['email'] ?>"><?php echo $_html['email'] ?></a></dd>
            <dd class="url">网址：<a href="<?php echo $_html['url'] ?>"
                                  target="_blank"><?php echo $_html['url'] ?></a></dd>
        </dl>
        <div class="content">
            <div class="user">
                <span><?php echo $_i + (($_page-1)*$_pagesize);?>#</span> <?php echo $_html['username']; ?>｜发表于：<?php echo $_html['date']; ?>
            </div>
            <h3>主题：<?php echo $_html['retitle']; ?></h3>
            <div class="detail">
                <?php echo _ubb($_html['content']);?>
                <?php
                //个性签名
                if($_html['switch'] == 1){
                    echo '<P class="autograph">'._ubb($_html['autograph']).'</P>';
                }
                ?>
            </div>
        </div>
    </div>

    <?php
        $_i++;
        }
        _free_result($_result);
        //此处调用分页函数; 1：调用数字分页，2：调用文本分页
        _paging(1);
    ?>

    <?php if(isset($_COOKIE['username'])){?>
        <p class="line"></p>
        <form method="post" action="?action=rephoto">
            <input type="hidden" name="sid" value="<?php echo $_html['id'];?>"/>
            <dl class="rephoto">
                <dd>标&nbsp;&nbsp;&nbsp;&nbsp;题：<input type="text" name="title" class="text" value="RE:<?php echo $_html['name'];?>"/>(*必填，2-40位)</dd>
                <dd id="q">贴&nbsp;&nbsp;&nbsp;&nbsp;图：&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;">Q图系列[1]</a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;">Q图系列[2]</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;">Q图系列[3]</a></dd>
                <dd>
                    <?php include ROOT_PATH.'includes/ubb.inc.php'?>
                    <textarea name="content" rows="15" cols=""></textarea>
                </dd>
                <dd>
                    <?php if(!empty($_system['code'])){?>
                        验 证 码：<input type="text" name="yzm" class="text yzm"/><img id="code" src="code.php"/>
                    <?php }?>
                    <input type="submit" class="submit" value="发表回复"/></dd>
            </dl>
        </form>
    <?php }?>
</div>

<?php
require ROOT_PATH.'includes/foot.inc.php';

?>
</body>
</html>
