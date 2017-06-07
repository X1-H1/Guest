/**
 * Created by Administrator on 2017/3/10 0010.
 */
window.onload = function () {
    var fm = document.getElementsByTagName('form')[0];
    var pass = document.getElementById('pass');


    fm[1].onclick = function () {
        pass.style.display = 'none';
    };
    fm[2].onclick = function () {
        pass.style.display = 'block';
    };



    fm.onsubmit = function () {
        if(fm.name.value.length < 2 || fm.name.value.length > 20){
            alert('相册名长度应在2到20位之间！');
            fm.name.value = '';//清空文本框的内容
            fm.name.focus();//获得鼠标的焦点
            return false;
        }
        if(fm[2].checked){
            if(fm.password.value.length < 6){
                alert('密码不得小于6位！');
                fm.password.value = '';//清空文本框的内容
                fm.password.focus();//获得鼠标的焦点
                return false;
            }
        }
        return true;
    }
};