/**
 * Created by Administrator on 2017/1/9 0009.
 */
function code(){
    var code = document.getElementById('code');
    //在火狐浏览器上会出现code is null解决方法有两种
    //1:
    // if(code != null){
    //     code.onclick = function () {
    //         this.src='code.php?tm='+Math.random();
    //     };
    // }
    //2:
    if(code == null){
        return;
    }
    code.onclick = function () {
        this.src='code.php?tm='+Math.random();
    };

}