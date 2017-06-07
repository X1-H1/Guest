/**
 * Created by Administrator on 2017/1/17 0017.
 */
window.onload = function () {
  code();
  var fm = document.getElementsByTagName("form")[0];
  fm.onsubmit = function () {
    //此处单纯的验证验证码的位数
    if(fm.yzm.value.length != 4){
      alert('验证码必须是4位！');
        fm.yzm.focus();//获得鼠标的焦点
      return false;
    }
    if(fm.content.value.length < 10 || fm.content.value.length > 600){
      alert('信息内容不能小于10位，大于600位!');
      fm.content.focus();//获得鼠标的焦点
      return false;
    }
    return true;
  }
};