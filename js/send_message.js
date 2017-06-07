/**
 * Created by Administrator on 2017/1/17 0017.
 */
window.onload = function () {
    var message = document.getElementsByName('message');
    var friend = document.getElementsByName('friend');
    var flower = document.getElementsByName('flower');
    for(var i=0;i<message.length;i++){
        message[i].onclick = function () {
            centerWindow('message.php?id='+this.title,'message',300,500);
        };
    }
    for(var j=0;j<friend.length;j++){
        friend[j].onclick = function () {
            centerWindow('friend.php?id='+this.title,'friend',300,500);
        };
    }
    for(var k=0;k<flower.length;k++){
        flower[k].onclick = function () {
            centerWindow('flower.php?id='+this.title,'flower',300,500);
        };
    }
};
function centerWindow(url,name,height,width){
    var top = (screen.height - height) / 2;
    var left = (screen.width - width) / 2;
    window.open(url,name,'height='+height+',width='+width+',top='+top+',left='+left);
}