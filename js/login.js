/**
 * Created by Administrator on 2017/1/9 0009.
 */
window.onload = function () {
    code();
    //登录表单验证
    var fm = document.getElementsByTagName('form')[0];
    fm.onsubmit = function () {
        //用户名验证
        if(fm.username.value.length < 2 || fm.username.value.length > 20){
            alert('用户名长度应在2到20位之间！');
            fm.username.value = '';//清空文本框的内容
            fm.username.focus();//获得鼠标的焦点
            return false;
        }
        if(/[<>\'\"\ ]/.test(fm.username.value)){
            alert('用户名不能包括非法字符！');
            fm.username.value = '';//清空文本框的内容
            fm.username.focus();//获得鼠标的焦点
            return false;
        }
        //密码验证
        if(fm.password.value.length < 6){
            alert('密码长度不能小于6位！');
            fm.password.value = '';//清空文本框的内容
            fm.password.focus();//获得鼠标的焦点
            return false;
        }
        //此处单纯的验证验证码的位数
        if(fm.yzm.value.length != 4){
            alert('验证码必须是4位！');
            fm.yzm.value = '';//清空文本框的内容
            fm.yzm.focus();//获得鼠标的焦点
            return false;
        }
    };
};