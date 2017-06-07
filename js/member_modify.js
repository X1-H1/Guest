/**
 * Created by Administrator on 2017/1/15 0015.
 */
window.onload = function(){
    code();
    var fm = document.getElementsByTagName('form')[0];
    fm.onsubmit = function (){
        //密码验证
        if(fm.password.value != ''){
            if(fm.password.value.length < 6){
                alert('密码长度不能小于6位！');
                fm.password.value = '';//清空文本框的内容
                fm.password.focus();//获得鼠标的焦点
                return false;
            }
        }
        //邮箱验证
        if(!/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/.test(fm.email.value)){
            alert('请填写正确的邮件格式！');
            fm.email.value = '';//清空文本框的内容
            fm.email.focus();//获得鼠标的焦点
            return false;
        }
        //QQ号验证
        if(fm.qq.value != ''){
            if(!/^[1-9]{1}[\d]{4,9}/.test(fm.qq.value)){
                alert('QQ号格式不正确！');
                fm.qq.value = '';//清空文本框的内容,此处下划线出现的莫名其妙，程序能够正常运行
                fm.qq.focus();//获得鼠标的焦点
                return false;
            }
        }
        //网址验证
        if(fm.url.value != '') {
            if (!/^(http(s)?:\/\/)?(www\.)?[\w-]+\.\w{2,4}(\/)?$/.test(fm.url.value)) {
                alert('网页地址格式不正确！');
                fm.url.value = '';//清空文本框的内容
                fm.url.focus();//获得鼠标的焦点
                return false;
            }
        }
        //此处单纯的验证验证码的位数
        if(fm.yzm.value.length != 4){
            alert('验证码必须是4位！');
            fm.yzm.value = '';//清空文本框的内容
            fm.yzm.focus();//获得鼠标的焦点
            return false;
        }
        return true;
    };
};