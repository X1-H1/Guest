/**
 * Created by Administrator on 2017/3/13 0013.
 */
window.onload = function () {
    var up = document.getElementById('up');
    up.onclick = function () {
        centerWindow('upimg.php?dir='+this.title,'up','150','400');
    };
    var fm = document.getElementsByTagName('form')[0];
    fm.onsubmit = function () {
        if(fm.name.value.length < 2 || fm.name.value.length > 20){
            alert('图片名称长度应在2到20位之间！');
            fm.name.value = '';//清空文本框的内容
            fm.name.focus();//获得鼠标的焦点
            return false;
        }
        if(fm.url.value == ''){
            alert('地址不得为空！');
            fm.url.focus();//获得鼠标的焦点
            return false;
        }
        return true;
    }
};


function centerWindow(url,name,height,width){
    var top = (screen.height - height) / 2;
    var left = (screen.width - width) / 2;
    window.open(url,name,'height='+height+',width='+width+',top='+top+',left='+left);
}