//消息
var msg = {
	email:'Email格式不正确,请检查!',
	name:'以字母开头,长度3-20',
	pwd:'密码长度6-20',
	pwd2:'两个密码不一致',
	checkcode:'验证码不正确,请检查!',
	mobile:'手机格式不正确,请检查!',
	telephone:'电话格式不正确,请检查!',
	qq:'QQ号码格式不正确',
	int:'只能填写正整数!',
	idcard:'身份证号码不正确,请检查!'
}
//空值检查
function check_blank(target){
	if($(target).val()==""){
		$(target).nextAll('.error').addClass('notice').removeClass('ok').html('请填写此字段!');
		return true;
	}else{
		h=$(target).nextAll('.error').html();//html判断
		if(h.indexOf("...")==-1 && h.indexOf("已存在")==-1)
		$(target).nextAll('.error').removeClass('notice').addClass('ok').html("");
		return false;
	}	
}
//合法性检查
function check_legal(target){
	rule=$(target).attr('check');
	if(rule==null)return true;
	val=$(target).val();
	m=0;
	switch(rule){
		case 'name':if (val.search(/^[A-Za-z][A-Za-z0-9]+$/) ==-1||val.length<3||val.length>20) m=1;break;
		case 'email':if (val.search(/^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.(?:com|cn|net)$/)== -1)m=1;break;
		case 'checkcode':if (val.search(/^[A-Za-z0-9]+$/) ==-1||val.length!=4)m=1;break;
		case 'pwd':	if (val.length<6||val.length>20)m=1;break;
		case 'pwd2':if ($('input[check="pwd"]').val()!=$('input[check="pwd2"]').val())m=1;break;
		case 'mobile':if (val.search(/^(((18[0-9]{1})|(13[0-9]{1})|(15[0-9]{1}))+\d{8})$/) == -1)m=1;break;
		case 'telephone':if (val.search(/^[-0-9]+$/) ==-1)m=1;break;
		case 'qq':if (val.search(/^\d+\d{4,10}$/) ==-1)m=1;break;
		case 'int':if (val.search(/^\d+$/) ==-1)m=1;break;
		case 'idcard':if(!check_id_card(val))m=1;break;
	}
	if(m==1){
		$(target).nextAll('.error').addClass('warn').removeClass('ok').html(msg[rule]);
		return false;
	}else{
		h = $(target).nextAll('.error').html();//html判断
		if(h.indexOf("...")==-1&&h.indexOf("已存在")==-1)
		$(target).nextAll('.error').removeClass('warn').addClass('ok').html("");
		return true;
	}
}
//提交验证
function submit_check(){
	$("[require],[check],[check='pwd'],[check_exist]").blur();
	m=0;
	$("span.error").each(function(){
		if($(this).html()!=""){
			m=1;
			return false;
		}
	})
	if(m)return false;
}
//验证初始化
function check_init(){
	src    = $("script[src$='validate.js']").attr('src');
	imgurl = src.replace('validate.js','');
	css    = "<style>\
	span.error{display:inline-block;*zoom:1;*display:inline;font-size: 12px; \
	color:#999; height:17px;vertical-align:middle}span.need{color:red; margin-right:5px;}\
	span.error.notice,span.error.warn,span.error.ok{ background-image:url('"+imgurl+"msg_bg.png');\
	padding-left:20px; background-repeat:no-repeat}\
	span.error.warn{ background-position: 0px -50px;}\
	span.error.notice{background-position: 0px -150px;}\
	span.error.ok{background-position: 0px -250px; }\
	</style>";
	$("head").append(css);	
	$('body').append('<img src="'+imgurl+'msg_bg.png" style="display:none" />');
	need_span  = "<span class='need'>*</span>";
	error_span = "<span class='error'></span>";
	$("[require],[check]").each(function(){
		$(this).after(error_span);
	})
	$("[require]").each(function(){
		$(this).after(need_span);
	})
	$("[check_exist]").each(function(){
		if($(this).val()!==''){
			$(this).attr('init',$(this).val());	
		}
	})
	$('form').submit(function(){
		return 	submit_check();
	})
}

//程序初始化
$(function(){
	check_init();
	$("[require]").blur(function(){
		check_blank(this);
	})
	$("[check]").blur(function(){
		if($(this).val()!=""){
			check_legal(this);
		}else{
			$(this).nextAll('.error').html('');
		}
	})
	$("[check][require]").blur(function(){
		if(!check_blank(this)){
			check_legal(this);
		}
	})
	$("input[check='pwd']").blur(function (){
		target=$("input[check='pwd2']")
		if(target.val()!==undefined&&target.val()!==""){//判断为空及合法性
			check_legal(target);
		}
	})	
	$("[check_exist]").blur(function(){
		if(!check_blank(this) && check_legal(this)){
			e = $(this).attr('check_exist');
			i = $(this).attr('init');
			if( i == undefined || i != $(this).val()){
				check_exist(e,this);
			}else{
				$(this).nextAll('.error').addClass('warn').addClass('ok').html("");
			}
		}
	})
	
})

//检查是否存在
function check_exist(e,tar){
	v   = $(tar).val();
	//$(tar).nextAll('.error').removeClass('ok').html("检测中...");
	$.post('?m='+getv('m')+'&a=ajax_exist&op='+e,{val:v},function(data){
		//对象得重新定位,异步请求无法通过全局传参
		out = $("[check_exist='"+e+"']").nextAll('.error'); 
		if(data == 0){
			out.addClass('warn').addClass('ok').html("");
		}else{
			out.addClass('warn').removeClass('ok').html("已存在!");		  
		}
	})
}

//js之get函数
function getv(name) {
	var reg = new RegExp("(^|\\?|&)"+ name +"=([^&]*)(\\s|&|$)", "i");
	if (reg.test(location.href)) return unescape(RegExp.$2.replace(/\+/g, " "));
	return "";
}

//身份证号码验证
function check_id_card(num){ 
        num = num.toUpperCase(); 
        if (!(/(^\d{15}$)|(^\d{17}([0-9]|X)$)/.test(num))){
            msg['idcard'] = '身份证号码位数不正确!';
            return false;
        }
		var len, re;
        len = num.length;
		//15位
        if (len == 15){
            re = new RegExp(/^(\d{6})(\d{2})(\d{2})(\d{2})(\d{3})$/);
            var arrSplit = num.match(re);
            var dtmBirth = new Date('19' + arrSplit[2] + '/' + arrSplit[3] + '/' + arrSplit[4]);
            var bGoodDay;
            bGoodDay = (dtmBirth.getYear() == Number(arrSplit[2])) 
			&& ((dtmBirth.getMonth() + 1) == Number(arrSplit[3])) && (dtmBirth.getDate() == Number(arrSplit[4]));
            if (!bGoodDay){
                msg['idcard']='身份证号出生日期不正确';  
                return false;
            }else{
				var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
				var arrCh  = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
				var nTemp  = 0, i;  
				num = num.substr(0, 6) + '19' + num.substr(6, num.length - 6);
				for(i = 0; i < 17; i ++){
					nTemp += num.substr(i, 1) * arrInt[i];
				}
				num += arrCh[nTemp % 11];  
				return true;  
            }  
        }
		//18位
        if (len == 18){
            re = new RegExp(/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/);
            var arrSplit = num.match(re);
            var dtmBirth = new Date(arrSplit[2] + "/" + arrSplit[3] + "/" + arrSplit[4]);
            var bGoodDay;
            bGoodDay = (dtmBirth.getFullYear() == Number(arrSplit[2])) 
			&& ((dtmBirth.getMonth() + 1) == Number(arrSplit[3])) && (dtmBirth.getDate() == Number(arrSplit[4]));
            if (!bGoodDay){
                msg['idcard'] = '输入的身份证号里出生日期不对！';
                return false;
            }else{
				var valnum;
				var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
				var arrCh  = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
				var nTemp  = 0, i;
				for(i = 0; i < 17; i ++){
					nTemp += num.substr(i, 1) * arrInt[i];
				}
				valnum = arrCh[nTemp % 11];
				if (valnum != num.substr(17, 1)){
					msg['idcard'] = '18位身份证的校验码不正确！应该为：' + valnum;
					return false;
				}
				return true;
        }
        }
        return false;
}   
