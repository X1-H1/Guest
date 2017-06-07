/**
 * Created by Administrator on 2017/1/22 0022.
 */
window.onload = function () {
    var ret = document.getElementById('return');
    var del = document.getElementById('delete');
    ret.onclick = function () {
        history.back();
        // history.go(-1);
        // self.location.reload();
    };
    del.onclick = function () {
        if(confirm("确定删除此短信？")){
            location.href="?action=delete&id="+this.name;
        }
    };
};