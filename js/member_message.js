/**
 * Created by Administrator on 2017/1/25 0025.
 */
window.onload =function () {
  var all = document.getElementById("all");
  var form = document.getElementsByTagName("form")[0];
  all.onclick = function () {
      //form.elements是获取表单内的表格的所有行数
      //checked表示已选
      for(var i=0;i < form.elements.length;i++){
          if(form.elements[i].name!="chkall"){
              form.elements[i].checked = form.chkall.checked;
          }
      }
  };
  form.onsubmit = function () {
      //此处的下划线出现的莫名其妙，无奈，程序能够正常运行
      if(confirm("确定删除这批数据吗？")){
          return true;
      }else {
          return false;
      }
  };
};