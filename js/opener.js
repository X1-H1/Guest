/**
 * Created by Administrator on 2017/1/5 0005.
 */
window.onload = function () {
    var img = document.getElementsByTagName('img');
    for(i=0;i<img.length;i++){
        img[i].onclick = function(){
            _opener(this.alt);
        };
    }
};

function _opener(src){
    //opener表示父窗口.document表示文档//.src即父窗口的头像源文件，将在子窗口得到的新头像通过src赋值给父窗口
    var faceimg = opener.document.getElementById('fag').src = src;
    opener.document.register.face.value = src;
}