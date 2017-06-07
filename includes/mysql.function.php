<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/8 0008
 * Time: 23:40
 */
//防止恶意调用
if(!defined('XH')){
    exit('非法调用');
}
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PWD','');
define('DB_NAME','db_chat');


/**
 * _connection 此函数是用来链接数据库的
 * @access public
 *
 */
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PWD, DB_NAME);//在此处将$conn设置成全局变量，每个函数用到的时候，再引用
function _connection()
{
    global $conn;//用global来引用一下这个全局变量
    if(!$conn){
        exit('数据库链接失败！！！');
    }
}

/**
 *
 *
 */
function _set_charset(){
    global $conn;
    if(!mysqli_set_charset($conn,'utf8')){
        exit('字符集错误！！！');
    }
}

/**
 * @param $_sql
 * @return bool|mysqli_result
 *
 */
function _query($_sql){
    global $conn;
    if(!$_result = mysqli_query($conn,$_sql)){
        exit('SQL语句错误！！！');
    }
    return $_result;
}

/**
 * _fetch_array（）此函数只能获取一条数据组
 * @param $_sql
 * @return array|null
 */
function _fetch_array($_sql){
    return mysqli_fetch_array(_query($_sql),MYSQLI_ASSOC);
}

/**
 * 可以返回指定数据集的所有数据
 * @param $_result
 */
function _fetch_array_list($_result){
    return mysqli_fetch_array($_result,MYSQLI_ASSOC);
}

function _num_rows($_result){
    return mysqli_num_rows($_result);
}


/**
 * @param $_sql
 * @param $_info
 *
 */
function _is_repeat($_sql,$_info){
    if(_fetch_array($_sql)){
        _alert_back($_info);
    }
}

/**
 *
 *
 */
function _close(){
    global $conn;
    mysqli_close($conn);
}

/**
 * @return int
 *
 */
function _affected_rows(){
    global $conn;
    return mysqli_affected_rows($conn);
}


/**
 * 销毁结果集
 * @param $_result
 */
function _free_result($_result){
    mysqli_free_result($_result);
}

/**
 * @return int|string
 */
function _insert_id(){
    global $conn;
    return mysqli_insert_id($conn);
}
