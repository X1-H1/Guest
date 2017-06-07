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
define('SCRIPT','article');

//引入公共文件
require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

//处理精华帖
if(@$_GET['action'] == 'nice' && isset($_GET['id']) && isset($_GET['on'])){
    if($_rows = _fetch_array("SELECT user_id FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")) {
        //为了防止cookie伪造，还要比对一下唯一标识符
        _check_uni(@$_rows['user_uni'], @$_COOKIE['uniqid']);//此处的COOKIE中的唯一编码uniqid虽然是变量但是要全写，否则会出错

        //设置精华帖，或者取消精华帖
        _query("UPDATE tb_article SET a_nice='{$_GET['on']}' WHERE a_id='{$_GET['id']}'");

        if(_affected_rows() == 1){
            _close();
            _location('精华帖操作成功！','article.php?id='.$_GET['id']);
        }else{
            _close();
            _alert_back('精华帖操作失败！');
        }
    }else{
        _alert_back('非法登录！');
    }
}

//处理回帖
if(@$_GET['action'] == 'rearticle'){

    if(!empty($_system['code'])){
        //验证码验证
        _check_code($_SESSION['code'],$_POST['yzm']);
    }

    if($_rows = _fetch_array("SELECT user_id,user_article_time FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")) {
        //为了防止cookie伪造，还要比对一下唯一标识符
        _check_uni(@$_rows['user_uni'], @$_COOKIE['uniqid']);//此处的COOKIE中的唯一编码uniqid虽然是变量但是要全写，否则会出错

        global $_system;
        _timed(time(),$_rows['user_article_time'],$_system['re']);

        //接受数据
        $_clean = array();
        $_clean['reid'] = $_POST['reid'];

        //此处为了解决下面显示的非法登录问题自己尝试解决,尝试失败，自己的想法是可能是下面的读取数据的时候$_GET['id']（63行），在跳转的时候（49行）没有得到值
        //$_clean['id'] = $_POST['reid'];

        $_clean['type'] = $_POST['type'];
        $_clean['title'] = $_POST['title'];
        $_clean['content'] = $_POST['content'];
        $_clean['username'] = $_COOKIE['username'];
        $_clean = _mysql_second_string($_clean);

        //写入数据库
        _query("INSERT INTO tb_article(a_reid,a_username,a_title,a_type,a_content,a_date) VALUES ('{$_clean['reid']}','{$_clean['username']}','{$_clean['title']}','{$_clean['type']}','{$_clean['content']}',now())");

        if(_affected_rows() == 1){
            $_clean['time'] = time();
            _query("UPDATE tb_user SET user_article_time='{$_clean['time']}' WHERE user_username='{$_COOKIE['username']}'");
            _query("UPDATE tb_article SET a_commendcount=a_commendcount+1 WHERE a_reid=0 AND a_id='{$_clean['reid']}'");
            _close();
            _location('回复成功！','article.php?id='.$_clean['reid']);
        }else{
            _close();
            _alert_back('回复失败！');
        }
    }else{
        _alert_back('非法登录！');
    }
}

//读出数据
if(isset($_GET['id'])){
    if($_rows = _fetch_array("SELECT 
                                      a_id,
                                      a_username,
                                      a_type,
                                      a_title,
                                      a_content,
                                      a_readcount,
                                      a_commendcount,
                                      a_nice,
                                      a_date,
                                      a_last_modify_date
                                FROM 
                                      tb_article 
                                WHERE 
                                      a_reid=0
                                  AND 
                                      a_id='{$_GET['id']}'")){
        //阅读量
        _query("UPDATE tb_article SET a_readcount=a_readcount+1 WHERE a_id='{$_GET['id']}'");

        //存在
        $_html = array();
        $_html['reid'] = $_rows['a_id'];
        $_html['username_subject'] = $_rows['a_username'];
        $_html['type'] = $_rows['a_type'];
        $_html['title'] = $_rows['a_title'];
        $_html['content'] = $_rows['a_content'];
        $_html['readcount'] = $_rows['a_readcount'];
        $_html['commendcount'] = $_rows['a_commendcount'];
        $_html['nice'] = $_rows['a_nice'];
        $_html['last_modify_date'] = $_rows['a_last_modify_date'];
        $_html['date'] = $_rows['a_date'];


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
                                         user_username='{$_html['username_subject']}'")){
            //提取用户信息
            $_html['userid'] = $_rows['user_id'];
            $_html['face'] = $_rows['user_face'];
            $_html['switch'] = $_rows['user_switch'];
            $_html['autograph'] = $_rows['user_autograph'];
            $_html['sex'] = $_rows['user_sex'];
            $_html['email'] = $_rows['user_email'];
            $_html['url'] = $_rows['user_url'];
            $_html= _html($_html);

            //创建一个全局变量，做一个带参的分页
            global $_id;
            $_id = 'id='.$_html['reid'].'&';

            //主题帖修改
            if($_html['username_subject'] == @$_COOKIE['username'] || isset($_SESSION['admin'])){
                $_html['subject_modify'] = '【<a href="article_modify.php?id='.$_html['reid'].'">修改</a>】';
            }else{

            }

            //读取最后修改信息
            if($_html['last_modify_date'] != '0000-00-00 00:00:00'){
                $_html['last_modify_date_string'] = '本帖已由['.$_html['username_subject'].']于'.$_html['last_modify_date'].'修改过！';
            }

            //给楼主回复
            if(@$_COOKIE['username']){
                $_html['re2'] = '<span>【<a href="#re" name="ree" title="回复1楼的'.$_html['username_subject'].'">回复</a>】</span>';
            }


            //个性签名
            if($_html['switch'] == 1){
                $_html['autograph_html'] = '<P class="autograph">'.@$_html['autograph'].'</P>';
            }

            //读取回帖
            global $_pagenum,$_pagesize,$_page;
            _page("SELECT a_id FROM tb_article WHERE a_reid='{$_html['reid']}'",10);
            $_result = _query("SELECT a_username,a_type,a_title,a_content,a_date FROM tb_article WHERE a_reid='{$_html['reid']}' ORDER BY a_date ASC LIMIT $_pagenum,$_pagesize");

        }else{
            //这个用户已被删除
        }
    }else{
        _alert_back("不存在这个主题！");
    }
}else{
    //帖子回复发表的时候除了会弹出回复成功外，还会弹出下面的非法登录！！！
    _alert_back("非法登录！！！");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--帖子详情</title>-->
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
<div id="article">
    <h2>帖子详情</h2>

    <?php
        //浏览量达到300，并且评论量达到15即可为热帖
        if($_html['readcount'] >= 300 && $_html['commendcount'] >=15){
    ?>
<!--            <img src="images/hot.png" alt="热帖" name="hot"/>-->
    <?php
        }
        if(!empty($_html['nice'])){
    ?>
<!--            <img src="images/nice.png" alt="精华帖" name="nice"/>-->
    <?php
        }
        if( $_page == 1){
    ?>

    <div id="subject">
        <dl>
            <dd class="user"><?php echo $_html['username_subject'];?>(<?php echo $_html['sex'];?>)【楼主】</dd>
            <dt><img src="<?php echo $_html['face'];?>" alt="<?php echo $_html['username_subject'];?>"></dt>
            <dd class="message"><a href="javascript:;" name="message" title="<?php echo $_html['userid'];?>">发消息</a></dd>
            <dd class="friend"><a href="javascript:;" name="friend" title="<?php echo $_html['userid'];?>">加好友</a></dd>
            <dd class="guest">写留言</dd>
            <dd class="flower"><a href="javascript:;" name="flower" title="<?php echo $_html['userid'];?>">送花朵</a></dd>
            <dd class="email">邮件：<a href="mailto:<?php echo $_html['email']?>"><?php echo $_html['email']?></a></dd>
            <dd class="url">网址：<a href="<?php echo $_html['url']?>" target="_blank"><?php echo $_html['url']?></a></dd>
        </dl>
        <div class="content">
            <div class="user">
                <span>
                    <?php
                        if(isset($_SESSION['admin'])){
                            if(empty($_html['nice'])){
                    ?>
                                [<a href="article.php?action=nice&on=1&id=<?php echo $_html['reid']?>">设置精华帖</a>]
                    <?php
                            }else{
                    ?>
                                [<a href="article.php?action=nice&on=0&id=<?php echo $_html['reid']?>">取消精华帖</a>]
                    <?php
                            }
                        }
                    ?>
                    <?php echo @$_html['subject_modify'];?> 1#
                </span> <?php echo $_html['username_subject'];?>｜发表于：<?php echo $_html['date'];?>
            </div>
            <h3>主题：<?php echo $_html['title'];?><img src="images/icon<?php echo $_html['type'];?>.png" alt="icon<?php echo $_html['type'];?>"/><?php echo @$_html['re2'];?></h3>
            <div class="detail">
                <?php echo _ubb($_html['content']);?>
                <?php echo @$_html['autograph_html'];?>
            </div>
            <div class="read">
                <p><?php echo @$_html['last_modify_date_string'];?></p>
                阅读量：（<?php echo $_html['readcount'];?>） 评论量：（<?php echo $_html['commendcount'];?>）
            </div>
        </div>
    </div>

    <?php
        }
    ?>

    <p class="line"></p>

    <?php
        $_i = 2;
        while($_rows = _fetch_array_list($_result)) {
            $_html['username'] = $_rows['a_username'];
            $_html['type'] = $_rows['a_type'];
            $_html['retitle'] = $_rows['a_title'];
            $_html['content'] = $_rows['a_content'];
            $_html['date'] = $_rows['a_date'];
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


                //楼层
                if($_page == 1 && $_i == 2){
                    if($_html['username'] == $_html['username_subject']){
                        @$_html['username_html'] = $_html['username'].'(楼主)';
                    }else{
                        @$_html['username_html'] = $_html['username'].'(沙发)';
                    }
                }else{
                    $_html['username_html'] = $_html['username'];
                }
            }else{
                //这个用户可能已经被删除了
            }


            //跟帖回复
            if(@$_COOKIE['username']) {
                $_html['re'] = '<span>【<a href="#re" name="ree" title="回复'.($_i + (($_page - 1) * $_pagesize)).'楼的'.$_html['username'].'">回复</a>】</span>';
            }
            ?>
            <div class="re">
                <dl>
                    <dd class="user"><?php echo $_html['username_html']; ?>(<?php echo $_html['sex']; ?>)</dd>
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
                    <h3>主题：<?php echo $_html['retitle']; ?><img src="images/icon<?php echo $_html['type']; ?>.png"
                                                              alt="icon<?php echo $_html['type']; ?>"/><?php echo @$_html['re'] ?></h3>
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
            <p class="line"></p>
            <?php
            $_i ++;
        }
        _free_result($_result);
        //此处调用分页函数; 1：调用数字分页，2：调用文本分页
        _paging(1);
    ?>

    <?php if(isset($_COOKIE['username'])){?>
    <a name="re"></a>
    <form method="post" action="?action=rearticle">
        <input type="hidden" name="reid" value="<?php echo $_html['reid'];?>"/>
        <input type="hidden" name="type" value="<?php echo $_html['type'];?>"/>
        <dl>
            <dd>标&nbsp;&nbsp;&nbsp;&nbsp;题：<input type="text" name="title" class="text" value="RE:<?php echo $_html['title'];?>"/>(*必填，2-40位)</dd>
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
