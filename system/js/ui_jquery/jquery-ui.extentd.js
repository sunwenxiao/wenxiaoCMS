//对话框
function dialog(setting){
	if($( "#dialog-form" ).length>0)return ;
	h  = setting.height == undefined ? 300:setting.height;
	w  = setting.width  == undefined ? 500:setting.width;
	url=setting.url  == undefined ? '#':setting.url;
	title=setting.title == undefined ? '标题':setting.title;
	src    = $("script[src$='jquery-ui.extentd.js']").attr('src');
	imgurl = src.replace('jquery-ui.extentd.js','');
	str="\
	<div id='dialog-form' title='"+title+"'>\
	<iframe id='dialog-new-iframe' width='"+ w +"' height='"+h+"' frameborder='0' src='"+url+"' style='background: url(\""+imgurl+"images/loading.gif\") no-repeat center center' onload='this.style.background=\"none\"'></iframe>\
	</div>\
	";
	$("body").append(str);
	//ie6无法打开iframe链接,iframe加载后再链接重新加载;
	if(document.all){$("#dialog-new-iframe").attr('src',url)}
	$( "#dialog-form" ).dialog({resizable: false,closeOnEscape: true,width:w+30,modal:true,close:function(){dialog_remove();}});
}
function dialog_close(){
	dialog_remove();
}
function dialog_remove(){
	//ie下无法再次获得焦点需要,iframe冲突,应先移除
	if(document.all)$('#dialog-new-iframe').remove();
	$("#dialog-form").remove();
}
function echo_remove(){
	$("#dialog-msg-box").remove();
}
function confrm_remove(){
	$("#confrm-msg-box").remove();
}
var ui = {
	alert:function(msg,t){
		if($( "#dialog-msg-box" ).length>0)return false;
		msg = msg == undefined?'消息':msg;
		t = t == undefined ? 0:t;
		s= "class= 'ui-icon ui-icon-info'"
		if(t==1) s= "class= 'ui-icon ui-icon-circle-check'"
		if(t==2) s= "class= 'ui-icon ui-icon-circle-close'"
		if(t==3) s= "class= 'ui-icon ui-icon-alert'"
		w=300;
		d=3000;
		str="<div id='dialog-msg-box' title='消息提示' style='padding-top:10px;'>\
		<span "+s+" style='float:left;'></span>\
		<p style='margin:0px 0px 10px 20px;'>"+msg+"</p>\
		<p>该窗口将在3秒内自动关闭!</p>\
		</div>";
		$("body").append(str);
		$( "#dialog-msg-box" ).dialog({resizable: false,closeOnEscape: true,width:w,close:function(){echo_remove();}});
		setTimeout(echo_remove,d);
	},
	confirm:function(msg, func_yes,func_no){
		if($( "#confrm-msg-box" ).length>0) return false;
		w=300;
		str="<div id='confrm-msg-box' title='消息确认'><p style='margin-top:10px'>"+msg+"</p></div>";
		$("body").append(str);
		$( "#confrm-msg-box" ).dialog({
				resizable: false,
				closeOnEscape: false,
				width:w,
				modal:true,
				close:function(){
					confrm_remove();
				},
				buttons:{
					"确认": function() {
						func_yes();
						$( this ).dialog( "close" );
					},
					'取消': function() {
						if(func_no!=undefined) func_no();
						$( this ).dialog( "close" );
					}
				}
		});
	}
}
$(function(){
	src    = $("script[src$='jquery-ui.extentd.js']").attr('src');
	imgurl = src.replace('jquery-ui.extentd.js','');
	$('body').append('<img src="'+imgurl+'images/loading.gif" style="display:none" />');
})