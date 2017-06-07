/**
 * Created by Administrator on 2017/2/23 0023.
 */
window.onload = function () {
    code();
    var ubb = document.getElementById('ubb');
    var fm = document.getElementsByTagName('form')[0];

    var font = document.getElementById('font');
    var color = document.getElementById('color');
    var html = document.getElementsByTagName('html')[0];

    if(fm != undefined) {
        fm.onsubmit = function () {
            //标题验证
            if (fm.title.value.length < 2 || fm.title.value.length > 40) {
                alert('标题长度应在2到40位之间！');
                fm.title.value = '';//清空文本框的内容
                fm.title.focus();//获得鼠标的焦点
                return false;
            }

            //回复内容验证
            if (fm.content.value.length < 10) {
                alert('回复内容不能小于10个字符！');
                fm.content.value = '';//清空文本框的内容
                fm.content.focus();//获得鼠标的焦点
                return false;
            }

            //此处单纯的验证验证码的位数
            if (fm.yzm.value.length != 4) {
                alert('验证码必须是4位！');
                fm.yzm.value = '';//清空文本框的内容
                fm.yzm.focus();//获得鼠标的焦点
                return false;
            }
            return true;
        };
    }


    var q = document.getElementById('q');

    if(q != null) {
        var qa = q.getElementsByTagName('a');
        qa[0].onclick = function () {
            window.open('q.php?num=48&path=qpic/1/', 'q', 'width=400,height=400,scrollbars=1');
        };
        qa[1].onclick = function () {
            window.open('q.php?num=38&path=qpic/2/', 'q', 'width=400,height=400,scrollbars=1');
        };
        qa[2].onclick = function () {
            window.open('q.php?num=28&path=qpic/3/', 'q', 'width=400,height=400,scrollbars=1');
        };
    }

    if(font != null) {
        html.onmouseup = function () {
            font.style.display = 'none';
            color.style.display = 'none';
        };
    }


    // fm.t.focus();
    if(ubb != null) {
        var ubbimg = ubb.getElementsByTagName('img');
        ubbimg[0].onclick = function () {
            font.style.display = 'block';
        };
        ubbimg[2].onclick = function () {
            content('[b][/b]');
        };
        ubbimg[3].onclick = function () {
            content('[i][/i]');
        };
        ubbimg[4].onclick = function () {
            content('[u][/u]');
        };
        ubbimg[5].onclick = function () {
            content('[s][/s]');
        };
        ubbimg[7].onclick = function () {
            color.style.display = 'block';
            fm.t.focus();
        };

        ubbimg[8].onclick = function () {
            var url = prompt('请输入网址：', 'http://');
            if (url) {
                if (/^(http(s)?:\/\/)?(www\.)?[\w-]+\.\w{2,4}(\/)?$/.test(url)) {
                    content('[url]' + url + '[/url]');
                } else {
                    alert("网址有误！")
                }
            }
        };
        ubbimg[9].onclick = function () {
            var email = prompt('请输入电子邮件：', '@');
            if (email) {
                if (/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/.test(email)) {
                    content('[email]' + email + '[/email]');
                } else {
                    alert("电子邮件有误！")
                }
            }
        };
        ubbimg[10].onclick = function () {
            var img = prompt('请输入图片地址：', '');
            if (img) {
                content('[img]' + img + '[/img]');
            }
        };
        ubbimg[11].onclick = function () {
            var flash = prompt('请输入视频flash：', 'http://');
            if (flash) {
                //此处的flash正则表达式不知是否正确
                //"/^https?:\/\/(/w+\.)?[\w\-\.]+(\.\w+)+/" 这里是视频中的正则表达式
                if (/^(http(s)?:\/\/)?(www\.)?[\w-]+\.\w{2,4}(\/)?$/.test(flash)) {
                    content('[flash]' + flash + '[/flash]');
                } else {
                    alert("视频地址有误！");
                }
            }
        };
        ubbimg[18].onclick = function () {
            fm.content.rows += 2;
        };
        ubbimg[19].onclick = function () {
            fm.content.rows -= 2;
        };
    }

    function content(string) {
        fm.content.value += string;
    }
    if(fm != undefined) {
        fm.t.onclick = function () {
            showcolor(this.value);
        };
    }


    var message = document.getElementsByName('message');
    var friend = document.getElementsByName('friend');
    var flower = document.getElementsByName('flower');
    var ree = document.getElementsByName('ree');


    for(var z=0;z<ree.length;z++){
        ree[z].onclick = function () {
            document.getElementsByTagName('form')[0].title.value = this.title;
        };
    }
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

function font(size){
    document.getElementsByTagName('form')[0].content.value += '[size='+size+'][/size]';
}
function showcolor(value) {
    document.getElementsByTagName('form')[0].content.value += '[color='+value+'][/color]';
}