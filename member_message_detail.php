<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/22 0022
 * Time: 16:06
 */
session_start();
    //定义一个常量，用来授权调用includes里面的文件
    define('XH','bee');
    //定义个常量，用来指定本页的内容
    define('SCRIPT','member_message_detail');
    //引入公共文件
    require dirname(__FILE__).'/includes/common.inc.php';//转换成硬路径，速度更快

    //判断是否登录
    if(!isset($_COOKIE['username'])){
        _alert_close('请先登录！');
    }

    //删除短信模块
    if(@$_GET['action'] == 'delete' && $_GET['id']){
        //这是验证短信是否合法
        if($_rows = _fetch_array("SELECT m_id FROM tb_message WHERE m_id='{$_GET['id']}' LIMIT 1")){
            //当在进行危险操作的时候，要对唯一标识符进行验证
            if($_rows2 = _fetch_array("SELECT user_id FROM tb_user WHERE user_username='{$_COOKIE['username']}' LIMIT 1")) {
                //为了防止cookie伪造，还要比对一下唯一标识符
                _check_uni(@$_rows2['user_uni'], @$_COOKIE['uniqid']);//此处的COOKIE中的唯一编码uniqid虽然是变量但是要全写，否则会出错
                //执行删除短信的操作
                _query("DELETE FROM tb_message WHERE m_id='{$_GET['id']}' LIMIT 1");
                if(_affected_rows() == 1){
                    //关闭数据库链接
                    _close();
//                    _session_destroy();
                    //成功写入数据库后，提示并跳转到指定的页面
                    _location('删除成功！','member_message.php');
                }else{
                    //关闭数据库链接
                    _close();
//                    _session_destroy();
                    _alert_back('删除失败！');
                }
            }else{
                _alert_back("非法登录！");
            }
        }else{
            _alert_back("此短信不存在！");
        }
    }
    //分页模块
    global $_pagenum,$_pagesize;
    _page("SELECT m_id FROM tb_message ",15);//第一个参数是获得总的数据，第二个参数是每页显示的数据量
    $_result = _query("SELECT m_id,m_fromuser,m_content,m_date FROM tb_message ORDER BY m_date DESC LIMIT $_pagenum,$_pagesize");

    //处理id
    if(isset($_GET['id'])){
        $_rows = _fetch_array("SELECT m_id,m_fromuser,m_date,m_state,m_content FROM tb_message WHERE m_id='{$_GET['id']}' LIMIT 1");
        if($_rows){
            //在此页面将已读信息的state状态设置为1即可
            if(empty($_rows['m_state'])){
                _query("UPDATE tb_message SET m_state=1 WHERE m_id='{$_GET['id']}' LIMIT 1");
                if(!_affected_rows()){
                    _alert_back("操作异常！！！");
                }
            }
            $_html = array();
            $_html['id'] = $_rows['m_id'];
            $_html['fromuser'] = $_rows['m_fromuser'];
            $_html['content'] = $_rows['m_content'];
            $_html['date'] = $_rows['m_date'];
            $_html = _html($_html);
        }else{
            _alert_back("此短信内容有误！");
        }
    }else{
        _alert_back("非法登录！");
    }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--    <title>多用户留言系统--短信列表</title>-->
    <?php
    require ROOT_PATH."includes/title.inc.php"
    ?>
    <script type="text/javascript" src="js/member_message_detail.js"></script>
</head>

<body>
<?php
require ROOT_PATH.'includes/header.inc.php';
?>

<div id="member">
    <?php
        require ROOT_PATH."includes/member.inc.php";
    ?>
    <div id="member_main">
        <h2>信息详情</h2>
        <dl>
            <dd>发 信 人：<?php echo $_html['fromuser']?></dd>
            <dd>发送时间：<?php echo $_html['date']?></dd>
            <dd>信息内容：<strong><?php echo $_html['content']?></strong></dd>
            <dd class="button"> <input type="button" value="返回列表" id="return""/>
                                <input type="submit" value="删除短信" name="<?php echo $_html['id']?>" id="delete"/>
            </dd>
        </dl>
    </div>
</div>

    <?php
    require ROOT_PATH.'includes/foot.inc.php';
    ?>
</body>
</html>
