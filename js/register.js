/**
 * Created by Administrator on 2017/1/5 0005.
 */
//等待网页加载完毕再执行
window.onload = function () {
    code();
    var fag = document.getElementById('fag');
    if(fag != null){
        // window.alert(fag.src);
        fag.onclick = function () {
            //onclick="javascript:window.open('face.php','face','width=400,height=400,top=120px,left=482px')"
            window.open('face.php','face','width=400,height=400,top=120px,left=482px');
        };
    }
    //验证表单
    var fm = document.getElementsByTagName('form')[0];
    if(fm == undefined) return;
    fm.onsubmit = function () {
        //能用客户端的尽量用客户端，不能用的话才用服务器端
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
        if(fm.password.value != fm.notpassword.value){
            alert('两次密码输入不一致！');
            fm.notpassword.value = '';//清空文本框的内容
            fm.notpassword.focus();//获得鼠标的焦点
            return false;
        }
        //密码提示与回答
        if(fm.question.value.length < 2 || fm.question.value.length > 20){
            alert('密码提示长度应在2到20位之间！');
            fm.question.value = '';//清空文本框的内容
            fm.question.focus();//获得鼠标的焦点
            return false;
        }
        if(fm.answer.value.length < 2 || fm.answer.value.length > 20){
            alert('密码回答长度应在2到20位之间！');
            fm.answer.value = '';//清空文本框的内容
            fm.answer.focus();//获得鼠标的焦点
            return false;
        }
        if(fm.answer.value == fm.question.value){
            alert('密码提示与密码回答不能相同！');
            fm.answer.value = '';//清空文本框的内容
            fm.answer.focus();//获得鼠标的焦点
            return false;
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
    }
};