<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/5 0005
 * Time: 13:02
 */

function _manage_login(){
    if((!isset($_COOKIE['username'])) || (!isset($_SESSION['admin']))){
        _alert_back("非法登录！");
    }
}

function _timed($_now_time,$_pre_time,$_second){
    if($_now_time - $_pre_time < $_second){
        _alert_back('操作过于频繁，请稍后！！！');
    }

}


function _remove_Dir($dirName){
    if(! is_dir($dirName)){
        return false;
    }
    $handle = @opendir($dirName);
    while (($file = @readdir($handle)) !== false){
        if($file != '.' && $file != '..'){
            $dir = $dirName .'/'.$file;
            is_dir($dir) ? _remove_Dir($dir):@unlink($dir);
        }
    }
    closedir($handle);
    return rmdir($dirName);
}


/**
 * @return mixed
 * _runtime()是用来获取执行耗时
 * @access public 表示函数对外公开
 * @return float 表示返回出来的是一个浮点型数字
 */
function _runtime(){
    $_stime = explode(' ',microtime());
    return $_stime[1] + $_stime[0];
}


/**
 *_alert_back() 表示JS弹窗
 * @access public
 * @param $_info
 * @return void 弹窗
 *
 */
function _alert_back($_info){
    echo "<script type='text/javascript'>alert('".$_info."');history.back()</script>";
    exit();
}

/**
 *_code()是验证码函数
 * @access public
 * @param int $_height 表示验证码的高度
 * @param int $_width 表示验证码的长度
 * @param int $_rand_num 表示验证的位数
 * @param bool $_flag 表示验证码是否需要边框
 * @return void 这个函数执行后产生一个验证码
 *
 */
function _code($_width = 75,$_height = 25,$_rand_num = 4,$_flag = false){
    //创建随机码
    $_rand = null;
    for($i=0;$i<$_rand_num;$i++){
        $_rand .= dechex(mt_rand(0,15));
    }

//保存随机数在SESSION中
    $_SESSION['code'] = $_rand;

//创建一张图像
    $_img = imagecreatetruecolor($_width,$_height);

//白色
    $_white = imagecolorallocate($_img,255,255,255);

//填充到背景上
    imagefill($_img,0,0,$_white);


    if($_flag){
//边框为随机色
        $_rc = imagecolorallocate($_img,rand(0,255),rand(0,255),rand(0,255));
        imagerectangle($_img,0,0,$_width-1,$_height-1,$_rc);
    }
//随机画出6个线条
    for ($i=0;$i<6;$i++){
        $_rnd_color = imagecolorallocate($_img,rand(0,255),rand(0,255),rand(0,255));
        imageline($_img,mt_rand(0,$_width),mt_rand(0,$_height),mt_rand(0,$_height),mt_rand(0,$_height),$_rnd_color);
    }
//随机雪花
    for ($i=1;$i<100;$i++)
    {
        $_rnd_color = imagecolorallocate($_img,rand(200,255),rand(200,255),rand(200,255));
        imagestring($_img,1,mt_rand(1,$_width),mt_rand(1,$_height),'*',$_rnd_color);
    }

//输出验证码
    for($i=0;$i<strlen($_SESSION['code']);$i++){
        $_rc = imagecolorallocate($_img,rand(0,100),rand(0,150),rand(0,200));
        imagestring($_img,mt_rand(3,5),$i*$_width/$_rand_num+mt_rand(1,10),mt_rand(1,$_height/2),$_SESSION['code'][$i],$_rc);
    }

//输出图像
    header("Content-Type:image/png");
    imagepng($_img);

//销毁
    imagedestroy($_img);

}

/**
 * _check_code 检验验证码
 * @access public
 * @param string $_first_code
 * @param string $_second_coed
 * @return void
 */
function _check_code($_first_code,$_second_coed){
    if(!($_POST['yzm'] == $_SESSION['code'])){
        _alert_back('验证码错误!');
    }
}


/**
 * _mysql_string 判断是否需要转义
 * @access public
 * @param string $_string
 * @return string $_string 返回转义后的字符串
 *
 */
function _mysql_string($_string){
    //get_magic_quotes_gpc()如果开启，就不需要转义,如果没开启就需要转义
    //get_magic_quotes_gpc为关闭时返回 0，否则返回 1。在 PHP 5.4.O 起将始终返回 FALSE
    //也就是说此处的GPC返回值是0;if条件语句不会执行
    //在其前面加一个！使其执行if条件语句
    if(!GPC){
        global $conn;
        #此处需要转义,需要调用此函数mysqli_real_escape_string();
        return mysqli_real_escape_string($conn,$_string);
    }
    return $_string;
}

/**
 * 加密唯一标识符
 * @return string
 */
function _sha1_uniqid(){
    return _mysql_string(sha1(uniqid(rand(),true)));
}


function _location($_info,$_url){
    if(!empty($_info)){
        echo "<script type='text/javascript'>alert('$_info');location.href ='$_url';</script>";
    }else{
        header('Location:'.$_url);
    }
}

/**
 *
 * 销毁生成的session
 */
function _session_destroy(){
    if(session_start()){
        session_destroy();
    }
}

/**
 * 销毁cookies
 */
function _unset_cookies(){
    setcookie('username','',time()-1);
    setcookie('uni','',time()-1);
    _session_destroy();
    _location("",'index.php');
}

/**
 * 防止在登录状态下，启动登录页面
 */
function _login_state(){
    if(isset($_COOKIE['username'])){
        _alert_back('登录状态无法进行此操作！');
    }
}

/**
 * 此函数完成分页的各项参数
 * @param $_sql
 * @param $_pagesize
 */
function _page($_sql,$_size){
    //将函数里的所有变量取出来，以供外部调用
    global $_page,$_pagenum,$_pagesize,$_pageabsolute,$_num;
    //分页模块 LIMIT 后面可以放两个参数第一个参数是从第几个数据开始，第二参数是显示的个数
    //page = 1 说明是第一页，表示1-10条数据 LIMIT 0，10 pagenum = 0
    //Page = 2 说明是第二页，表示11-20条数据 LIMIT 10，10 pagenum =10
    //page = 3 说明是第三页，表示21-30条数据 LIMIT 20，10 pagenum = 20
    if(isset($_GET['page'])){
        $_page = $_GET['page'];
        if(empty($_GET['page']) || $_GET['page'] <= 0 || !is_numeric($_GET['page']))
        {
            $_page = 1;
        }else{
            $_page = intval($_page);
        }
    }else{
        $_page = 1;
    }
    //首先要得到所有数据总和
    $_pagesize = $_size;
    $_num = _num_rows(_query($_sql));

    if($_num == 0){
        $_pageabsolute = 1;
    }else{
        //获得总页数
        $_pageabsolute = ceil($_num / $_pagesize);
    }

    if($_page > $_pageabsolute){
        $_page = $_pageabsolute;
    }

    $_pagenum = ($_page - 1) * $_pagesize;
}



/**
 * 此函数功能为分页，能实现两种分页方法，分别是数字分页，以及文本分页
 * @param $_type
 */
function _paging($_type){
    global $_pageabsolute,$_page,$_num,$_id;
    if($_type == 1){
        echo '<div id="page_num">';
        echo '<ul>';
        for($i=1;$i<=$_pageabsolute;$i++){
            if($_page == $i){
                echo '<li><a href="'.SCRIPT.'.php?'.$_id.'page='.$i.'" class="selected">'.$i.'</a> </li>';
            }else{
                echo '<li><a href="'.SCRIPT.'.php?'.$_id.'page='.$i.'">'.$i.'</a> </li>';
            }
        }
        echo '</ul>';
        echo '</div>';
    }elseif($_type == 2){
        echo '<div id="page_text">';
        echo '<ul>';
        echo '<li>'.$_page.'/'.$_pageabsolute.'页</li>';
        echo '<li>共有<stron>'.$_num.'</stron>条数据 |</li>';
        if($_page == 1){
            echo '<li>首页 |</li>';
            echo '<li>上一页 |</li>';
        }else{
            echo '<li><a href="'.SCRIPT.'.php">首页</a> |</li>';//此处可以用$_SERVER['SCRIPT_NAME']获取当前文件的系统目录，这样在将此段代码复制到其他程序时保证了一定的智能
            echo '<li><a href="'.SCRIPT.'.php?'.$_id.'page='.($_page-1).'">上一页</a> |</li>';
        }
        if($_page == $_pageabsolute){
            echo '<li>下一页 |</li>';
            echo '<li>尾页 |</li>';
        }else{
            echo '<li> <a href="'.SCRIPT.'.php?'.$_id.'page='.($_page+1).'">下一页</a> |</li>';
            echo '<li> <a href="'.SCRIPT.'.php?'.$_id.'page='.$_pageabsolute.'">尾页</a></li>';
        }
        echo '</ul>';
        echo '</div>';
    }else{
        _paging(2);
    }
}

/**
 * 此函苏表示对字符串进行HTML过滤显示，如果是数组按数组的方式过滤，如果是单独的字符串，那么就按单独的字符串过滤
 * @param $_string
 * @return array|string
 *
 */
function _html($_string){
    if(is_array($_string)){
        foreach ($_string as $_key => $_value)
            $_string[$_key] = _html($_value);//这里的调用方法采用了递归的方法
    }else{
        $_string = htmlspecialchars($_string);
    }
    return $_string;
}

/**
 * _mysql_string 判断是否需要转义
 * @access public
 * @param string $_string
 * @return string $_string 返回转义后的字符串
 *
 */
function _mysql_second_string($_string){
    //get_magic_quotes_gpc()如果开启，就不需要转义,如果没开启就需要转义
    //get_magic_quotes_gpc为关闭时返回 0，否则返回 1。在 PHP 5.4.O 起将始终返回 FALSE
    //也就是说此处的GPC返回值是0;if条件语句不会执行
    //在其前面加一个！使其执行if条件语句
//    if(!GPC){
        global $conn;
        #此处需要转义,需要调用此函数mysqli_real_escape_string();
//        return mysqli_real_escape_string($conn,$_string);
//    }
    if(is_array($_string)){
        foreach ($_string as $_key => $_value)
            $_string[$_key] = _mysql_second_string($_value);//这里的调用方法采用了递归的方法
    }else{
        $_string = mysqli_real_escape_string($conn,$_string);
    }
    return $_string;
}


/**
 * 检测唯一标识符
 * uniqid 此单词在函数名中全写，在变量中全部写为uni
 * @param $_mysql_uni
 * @param $_cookie_uni
 */
function _check_uni($_mysql_uni,$_cookie_uni){
    if($_mysql_uni != $_cookie_uni)
    {
        _alert_back('唯一标识符异常！');
    }
}

/**
 * 关闭函数
 * @param $_info
 */
function _alert_close($_info){
    echo "<script type='text/javascript'>alert('".$_info."');window.close()</script>";
    exit();
}

/**
 * 此函数是用于将发件内容显示限定指定长度
 * @param $_string
 * @return string
 */
function _title($_string,$_strlen){
    if(mb_strlen($_string,'utf8') > $_strlen){
        $_string = mb_substr($_string,0,$_strlen,'utf8').'...';
    }
    return $_string;
}

function _set_xml($_xmlfile,$_clean){
    $_fp = @fopen('text.xml','w');
    if(!$_fp){
        exit("系统错误，文件不存在！");
    }
    flock($_fp,LOCK_EX);

    $_string = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
    fwrite($_fp,$_string,strlen($_string));

    $_string = "<vip>\r\n";
    fwrite($_fp,$_string,strlen($_string));

    $_string = "\t<id>{$_clean['id']}</id>\r\n";
    fwrite($_fp,$_string,strlen($_string));

    $_string = "\t<username>{$_clean['username']}</username>\r\n";
    fwrite($_fp,$_string,strlen($_string));

    $_string = "\t<sex>{$_clean['sex']}</sex>\r\n";
    fwrite($_fp,$_string,strlen($_string));

    $_string = "\t<face>{$_clean['face']}</face>\r\n";
    fwrite($_fp,$_string,strlen($_string));

    $_string = "\t<email>{$_clean['email']}</email>\r\n";
    fwrite($_fp,$_string,strlen($_string));

    $_string = "\t<url>{$_clean['url']}</url>\r\n";
    fwrite($_fp,$_string,strlen($_string));

    $_string = "</vip>";
    fwrite($_fp,$_string,strlen($_string));


    flock($_fp,LOCK_UN);
    fclose($_fp);
}

function _get_xml($_xmlfile){
    $_html = array();
    if(file_exists($_xmlfile)){
        $_xml = file_get_contents($_xmlfile);
        preg_match_all('/<vip>(.*)<\/vip>/s',$_xml,$_dom);
        foreach ($_dom[1] as $_value) {
            preg_match_all('/<id>(.*)<\/id>/s',$_value,$_id);
            preg_match_all('/<username>(.*)<\/username>/s',$_value,$_username);
            preg_match_all('/<sex>(.*)<\/sex>/s',$_value,$_sex);
            preg_match_all('/<face>(.*)<\/face>/s',$_value,$_face);
            preg_match_all('/<email>(.*)<\/email>/s',$_value,$_email);
            preg_match_all('/<url>(.*)<\/url>/s',$_value,$_url);
            $_html['id'] = $_id[1][0];
            $_html['username'] = $_username[1][0];
            $_html['sex'] = $_sex[1][0];
            $_html['face'] = $_face[1][0];
            $_html['email'] = $_email[1][0];
            $_html['url'] = $_url[1][0];
        }
    }else{
        echo "文件不存在！";
    }
    return $_html;
}

function _ubb($_string){
    $_string = nl2br($_string);
    $_string = preg_replace('/\[b\](.*)\[\/b\]/U','<strong>\1</strong>',$_string);
    $_string = preg_replace('/\[size=(.*)\](.*)\[\/size\]/U','<span style="font-size:\1px">\2</span>',$_string);
    $_string = preg_replace('/\[i\](.*)\[\/i\]/U','<em>\1</em>',$_string);
    $_string = preg_replace('/\[u\](.*)\[\/u\]/U','<span style="text-decoration:underline">\1</span>',$_string);
    $_string = preg_replace('/\[s\](.*)\[\/s\]/U','<span style="text-decoration:line-through">\1</span>',$_string);
    $_string = preg_replace('/\[color=(.*)\](.*)\[\/color\]/U','<span style="color:\1">\2</span>',$_string);
    $_string = preg_replace('/\[url\](.*)\[\/url\]/U','<a href="=\1" target="_blank">\1</a>',$_string);
    $_string = preg_replace('/\[email\](.*)\[\/email\]/U','<a href="=mailto:\1">\1</a>',$_string);
    $_string = preg_replace('/\[img\](.*)\[\/img\]/U','<img src="\1" alt="图片"/>',$_string);
    $_string = preg_replace('/\[flash\](.*)\[\/flash\]/U','<embed style="width:480px;height:400px;" src="\1"/>',$_string);
    return $_string;
}


function _thumb($_filename,$_percent){
    //生成png标头文件
    header('Content-type:image/png');
    $_n = explode('.',$_filename);
    //获取文件信息，长和高
    list($_width,$_height) = getimagesize($_filename);
    //生成微缩的长和高
    $_new_width = $_width * $_percent;
    $_new_height = $_height * $_percent;
    //创建一个和微缩过后的长和高相等的画布
    $_new_image = imagecreatetruecolor($_new_width,$_new_height);
    //按照已有的图片创建一个画布
    switch ($_n[1]){
        case 'jpg': $_image = imagecreatefromjpeg($_filename);
            break;
        case 'png': $_image = imagecreatefrompng($_filename);
            break;
        case 'gif': $_image = imagecreatefromgif($_filename);
            break;
    }
    //将原图采集后重新复制到新图上，就缩略了
    imagecopyresampled($_new_image,$_image,0,0,0,0,$_new_width,$_new_height,$_width,$_height);
    imagepng($_new_image);
    imagedestroy($_new_image);
    imagedestroy($_image);
}
?>