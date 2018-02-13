<?php
defined('WENXIAOCMS') or exit('Access denied!');

//msg函数
function msg( $m , $refer = '', $t = ''){
	$timeout = 2;
	$refer == '' && $refer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'#';
	$back  =  '<meta http-equiv="refresh" content="'.$timeout.';url='.$refer.'"/>';
	$back2 =  'href="'.$refer.'"';
	if($t != ''){
		$back  = '<script>setTimeout(function(){window.history.go(-1)},2000)</script>';
		$back2 = 'href="javascript:window.history.go(-1)"';
	}
	$imgpath=IMG_PATH;
	echo <<<EOT
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>提示信息</title>
	$back
	<style>
	table, tr, td, body, h1, h3, h4, div, p { margin:0px; padding:0px; font-size:12px; }
	body{height:100%;width:100%}
	table { width:100%; height:460px; font-family:"Times New Roman", Times, serif; text-align:center; background: #fff; color: #666;position:relative;top:50%;margin-top:-300px; }
	a { color:#333; text-decoration:none; }
	img { border:0; }
	h3 { width:500px; margin:10px auto 0 auto; background:#cecece; color:#fff; line-height:25px; }
	h4 { padding: 15px 0; color:#c00; font-weight:normal; }
	</style>
	</head>
	<body>
	<table>
	  <tr>
		<td><h1><a $back2><img src="$imgpath/android_53.889014722537px_1202540_easyicon.net.png" alt="WENXIAOcms" height="60" /></a></h1>
		  <h3>提示信息</h3>
		  <h4>$m</h4>
	<p> ^-^ <a $back2>如果您的浏览器没有自动跳转，请点击这里。</a> !!! </p>
	</td>
	</tr>
	</table>
	</body>
</html>
EOT;
	exit;
}

//返回$_GET
function getg($k){
	$var = &$_GET;
	inject_check($var,'getfilter');
	return isset($var[$k]) ? new_html_special_chars($var[$k]) : NULL;
}
//返回$_POST
function getp($k){
	$var = &$_POST;
	inject_check($var,'postfilter');
	return isset($var[$k]) ? new_html_special_chars($var[$k]) : NULL;
}

//返回$_REQUEST
function getr($k){
	$var = &$_REQUEST;
	inject_check($var,'postfilter');
	inject_check($var,'getfilter');
	return isset($var[$k]) ? new_html_special_chars($var[$k]) : NULL;
}

//返回cookie
function getc($k){
	$var = &$_POST;
	inject_check($var,'cookiefilter');
	return isset($var[$k]) ? new_html_special_chars($var[$k]) : NULL;
}

//数字化get参数
function getd($k){
	return  is_null(getg($k))? NULL : intval(getg($k));
}
//智能化获取参数
function getG_P($k){
	return  is_null(getg($k))? getp($k) : getg($k);
}

//获取session
function gets($key = '',$app = APPLICATION){
	if($key === '') return NULL;
	$key = APPLICATION.'_'.$key;
	return isset($_SESSION[$key])?$_SESSION[$key]:NULL;
}

//设置session
function set_session($key = '',$val = '' ,$app = APPLICATION){
	if($key === '' or $val === '') return FALSE;
	$key = APPLICATION.'_'.$key;
	$_SESSION[$key] = $val;
	return TRUE;
}




//删除session
function del_session($key = '' ,$app = APPLICATION){
	if($key === '') return FALSE;
	$key = APPLICATION."_".$key;
	if(isset($_SESSION[$key])){
		unset($_SESSION[$key]);
		return TRUE;
	}else{
		return FALSE;
	}
}

//sql注入检查
function StopAttack($StrFiltValue,$ArrFiltReq){
	if(is_array($StrFiltValue)) {
		foreach($StrFiltValue as $v){
			StopAttack($v,$ArrFiltReq);
			return true;
		}
	}
// 	if (preg_match('/'.$ArrFiltReq.'/is',$StrFiltValue)==1) {
// 		echo 'Websec notice:Illegal operation!' ;
// 		exit();
// 	}
}

//csfr攻击检测
function csfr_check(){
	if(empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])){
		return ;
	}else{
		exit('Illegal request!');
	}
}

//sql注入辅助函数
function inject_check($v,$filter){
	$getfilter="'|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?select|Update.+?SET|Insert\\s+INTO.+?VALUES|(Select|Delete).+?FROM|(Create|Alter|Drop|TRUNCATE)\\s+(TABLE|DATABASE)" ;
	$postfilter="\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?select|Update.+?SET|Insert\\s+INTO.+?VALUES|(Select|Delete).+?FROM|(Create|Alter|Drop|TRUNCATE)\\s+(TABLE|DATABASE)" ;
	$cookiefilter="\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?Select|Update.+?SET|Insert\\s+INTO.+?VALUES|(Select|Delete).+?FROM|(Create|Alter|Drop|TRUNCATE)\\s+(TABLE|DATABASE)" ;
	foreach($v as $key=>$value){
		StopAttack($value,$$filter);
	}
}

//
function new_html_special_chars($string) {
	if(!is_array($string)) return trim(htmlspecialchars($string));
	foreach($string as $key => $val) $string[$key] = new_html_special_chars($val);
	return $string;
}

//获取当前页面完整URL地址
function get_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
	$path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

//设置页面返回地址
function set_back() {
	return urlencode(base64_encode(get_url()));
}

//获取页面返回地址
function get_back() {
	if(getg('back')){
		$back = base64_decode(urldecode(getg('back')));
	}else{
		$back =  isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?';
	} 
	return $back;
}

//字符串是否存在
function str_exists($string, $find) {
	return !(strpos($string, $find) === FALSE);
}

//获取客户端ip
function get_ip() {
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$ip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}

//ip138查询城市
function get_ip_city($queryIP){
	$url    = 'http://www.ip138.com/ips138.asp?ip='.$queryIP;
	$result = file_get_contents($url);
	preg_match('@<ul class="ul1"><li>(.*)</li>@iU',$result,$ipArray);
	$loc    = mb_convert_encoding($ipArray[1], 'utf-8', 'gb2312');
	$loc    = explode(' ',$loc);
	$city   = explode('：',$loc[0]);	
	return $city[1];
}

//类自动加载函数
function __autoload($class_name) {
	include_once $class_name . '.class.php';
}

//模版函数
function template($filename){
	$template = new template();
	return $template->template_cache($filename);
}

//无限级分类函数,输入数据需带pid,与id
function tree($data,$pid = 0){
	global $tree_data,$tree_level;
	$tree_level	=	0;
	$tree_data	=	array();
	if(!empty($data)){
		get_tree($data,$pid);	
		return $tree_data;
	}
	return NULL;
}

//无限级遍历函数
function get_tree($array,$pid=0){
	global $tree_data,$tree_level;
	$tree_level++;
	foreach($array as $v){
		if($v['pid'] == $pid){
			$v['level'] = $tree_level;
			$tree_data[]=$v;
			get_tree($array,$v['id']);
			$tree_level--;
		}
	}
}

//无限级分类函数
function del_tree($table,$data,$pid){
	global $dtree;
	$dtree = array($pid);
	d_tree($data,$pid);
	$ids   = implode(',',$dtree);
	$db    = new mysql();
	return $db->delete($table,'id in( '.$ids.' )');
}

//无限级遍历函数
function d_tree($array,$pid=0){
	global $dtree;
	foreach($array as $v){
		if($v['pid']==$pid){
			$dtree[]=$v['id'];
			d_tree($array,$v['id']);
		}
	}
}

//字符串截取
function str_cut($string, $length, $dot = ' ...') {
	if(strlen($string) <= $length) {
		return $string;
	}
	$pre = chr(1);
	$end = chr(1);
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);
	$strcut = '';
	if(strtolower('utf-8') == 'utf-8') {
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) {
				break;
			}
		}
		if($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
	} else {
		for($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}
	$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
	$pos = strrpos($strcut, chr(1));
	if($pos !== false) {
		$strcut = substr($strcut,0,$pos);
	}
	return $strcut.$dot;
}

//获得随机字符串
function random($length, $numeric = 0) {
	  PHP_VERSION < '4.2.0' && mt_srand((double) microtime() * 1000000);
	  if ($numeric) {
		  $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
	  } else {
		  $hash = '';
		  $chars = '0123456789';
		  $max = strlen($chars) - 1;
		  for ($i = 0; $i < $length; $i++) {
			  $hash.=$chars[mt_rand(0, $max)];
		  }
	  }
	  return $hash;
}

//返回时间信息	
function get_timemessage() {
		$timenow=time();
		$str='';
		$morentime1 = strtotime('1:00');		
		$morentime2 = strtotime('6:00');
		$morentime3 = strtotime('12:00');
		$morentime4 = strtotime('13:00');
		$morentime5 = strtotime('18:00');
		$morentime6 = strtotime('23:00');
		if ($timenow < $morentime1) {
			$str = '凌晨了，请注意休息！';
		} elseif ($timenow < $morentime2) {
			$str = '早上好！';
		} elseif ($timenow < $morentime3) {
			$str = '上午好！';
		} elseif ($timenow < $morentime4) {
			$str = '中午好！';
		} elseif ($timenow < $morentime5) {
			$str = '下午好！';
		} elseif ($timenow < $morentime6) {
			$str = '晚上好！';
		} else{
			$str = '深夜了，请注意休息！';
		}
		return $str;
}

//取得文件扩展
function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}

//返回经stripslashes处理过的字符串或数组
function new_stripslashes($string) {
	if(!is_array($string)) return stripslashes($string);
	foreach($string as $key => $val) $string[$key] = new_stripslashes($val);
	return $string;
}

//将字符串转换为数组
function string2array($data) {
	if($data == '') return array();
	@eval("\$array = $data;");
	return $array;
}

//将数组转换为字符串
function array2string($data, $isformdata = 1) {
	if($data == '') return '';
	if($isformdata) $data = new_stripslashes($data);
	return addslashes(var_export($data, TRUE));
}

//IE浏览器判断
function is_ie() {
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) return false;
	if(strpos($useragent, 'msie ') !== false) return true;
	return false;
}

//转义 javascript 代码标记
 function trim_script($str) {
	if(is_array($str)){
		foreach ($str as $key => $val){
			$str[$key] = trim_script($val);
		}
 	}else{
 		$str = preg_replace ( '/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str );
		$str = preg_replace ( '/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str );
		$str = preg_replace ( '/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str );
		$str = preg_replace ( '/]]\>/si', ']] >', $str );
 	}
	return $str;
}

//格式化文本域内容
function trim_textarea($string) {
	$string = nl2br ( str_replace ( ' ', '&nbsp;', $string ) );
	return $string;
}

//安全过滤函数
function safe_replace($string) {
	$string = str_replace('%20','',$string);
	$string = str_replace('%27','',$string);
	$string = str_replace('%2527','',$string);
	$string = str_replace('*','',$string);
	$string = str_replace('"','&quot;',$string);
	$string = str_replace("'",'',$string);
	$string = str_replace('"','',$string);
	$string = str_replace(';','',$string);
	$string = str_replace('<','&lt;',$string);
	$string = str_replace('>','&gt;',$string);
	$string = str_replace("{",'',$string);
	$string = str_replace('}','',$string);
	$string = str_replace('\\','',$string);
	return $string;
}

//xss漏洞检测
function _xss_check() {
	$temp = strtoupper(urldecode(urldecode($_SERVER['REQUEST_URI'])));
	if(strpos($temp, '<') !== false || strpos($temp, '"') !== false || strpos($temp, 'CONTENT-TRANSFER-ENCODING') !== false) {
		exit('request_tainting');
	}
	return true;
}

// 兼容linux
function daddslashes($string, $force = 0, $strip = FALSE) {
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force, $strip);
			}
		} else {
			$string = addslashes($strip ? stripslashes($string) : $string);
		}
	}
	return $string;
}

//密码函数
function password($pwd){
	$salt = substr(uniqid(rand()), -6);
	return array('pwd'=>md5(md5($pwd).$salt),'salt'=>$salt);
}


//加载系统配置文件
function load_sys_config(){
}

//加载应用配置文件
function load_app_config($file_name = '',$config_name = '',$key = '',$app = APPLICATION){
	if( $file_name == '') 	exit('配置参数 file_name 不存在,请检查!');
	if( $config_name == '') exit('配置参数 config_name 不存在,请检查!');
	$config_file = $app.'/config/'.$file_name.'.php';
	if(!file_exists($config_file)) exit('配置文件:'.$config_file.'不存在,请检查!');
	include $config_file;
	if(!isset($$config_name)) exit('配置参数:'.$config_name.'不正确,请检查!');
	$c = $$config_name;
	return $key === '' ? $c :( isset($c[$key]) ? $c[$key]:'');
}

//加载系统js
function load_sys_js($file){
	$jsfile 	= JS_PATH.'/'.$file.'.js';
	if(!file_exists($jsfile)){
		$jsfile = JS_PATH.'/'.$file.'/'.$file.'.js';
	}
	$js 		= file_exists($jsfile) ? '<script type="text/javascript" src="'.$jsfile.'"></script>'."\n":'';
	return $js;
}

//加载应用js
function load_app_js($file){
	$jsfile = APPLICATION.'/static/js/'.$file.'.js';
	$js 	= file_exists($jsfile) ? '<script type="text/javascript" src="'.$jsfile.'"></script>'."\n":'';
	return $js;
}
function load_app_static(){
	$static = APPLICATION.'/static/';
	return $static;
}


//加载css文件
function load_css($file){
	$cssfile = CSS_PATH.'/'.$file.'.css';
	$css     = file_exists($cssfile) ? '<link href="'.$cssfile.'" rel="stylesheet" type="text/css">'."\n":'';
	return $css;
}

//加载 验证码
function load_checkcode(){
return '<img src="system/checkcode.php" alt="验证码" style="cursor:pointer;width:90px;" title="换一张" onclick="this.src=\'system/checkcode.php?t=\'+Math.random()">';
}


//加载app之class,仅load
function load_app_class($classname = '',$app = APPLICATION){
	$class_file = $app.'/lib/'.$classname.'.class.php';
	if( $classname != '' && file_exists($class_file)){
		include_once $class_file;
	}else{
		exit('Load ['.$class_file.'] error!');	
	}
}

//加载app之function
function load_app_func($funcname = '',$app = APPLICATION){
	$func_file = $app.'/lib/'.$funcname.'.func.php';	
	if( $funcname != '' && file_exists($func_file)){
		include_once $func_file;
	}else{
		exit('Load ['.$func_file.'] error!');		
	}
}

//加载system之function
function load_sys_func($funcname = ''){
	$func_file = 'system/'.$funcname.'.func.php';	
	if( $funcname != '' && file_exists($func_file)){
		include_once $func_file;
	}else{
		exit('Load ['.$func_file.'] error!');		
	}
}

//权限判断
function auth_check() {
	login_check();
	$navs    = get_navs();
	$modules = array();
	foreach($navs as $row){
		$modules[] = $row['m'];
	}
	if(!in_array(ROUTE_M,$modules)){
		session_destroy();
		msg('无此模块操作权限,请检查!','?m=login');
	}
}

//登录检测
function login_check(){
		if(isset($_COOKIE['uid'])&&isset($_COOKIE['uname'])&&isset($_COOKIE['p'])&&is_null(gets('uid'))){
		//两周自动登录
		$db = new mysql();
    	$password = $db->get_one('password','user',"id={$_COOKIE['uid']}");
 
    	if($password == $_COOKIE['p']){
    		set_session('uid',$_COOKIE['uid']);
			set_session('uname',$_COOKIE['uname']);
    	}
	}
	if(is_null(gets('uid'))) header('Location:'.SCRIPT_NAME.'');
	session_timeout_check();
}

//前台登录检测
function loginqiantaicheck(){

	if(isset($_COOKIE['uid'])&&isset($_COOKIE['uname'])&&isset($_COOKIE['p'])&&is_null(gets('uid'))){
		//两周自动登录
		$db = new mypdo();
    	$password = $db->get_one('password','user',"id={$_COOKIE['uid']}");
 
    	if($password == $_COOKIE['p']){
    		set_session('uid',$_COOKIE['uid']);
			set_session('uname',$_COOKIE['uname']);
    	}
	}
	if(is_null(gets('uid'))) header('Location:'.SCRIPT_NAME.'');
	session_timeout_check();
}

//session过期检测
function session_timeout_check(){
	if(SESSION_TIMEOUT == 0) return ;
	if(!isset($_SESSION['start_time']))	$_SESSION['start_time'] = time();
	$start = $_SESSION['start_time'];
	$now   = time();
	if($now > $start + SESSION_TIMEOUT){
		session_destroy();
		msg('登录时间已经过期,请重新登录!',SCRIPT_NAME.'/');	
	} else {
		$_SESSION['start_time'] = time();
	}
}

//初始化头部导航
function get_navs(){
	$db      = new mypdo();
	$uid     = gets('uid');
	$member  = $db->query('select role_id,type from '.DBPREFIX.'admins where id = '.$uid,'Row');
	$type    = $member['type'];
	$rid     = $member['role_id'];
	if($type == 1){
		$moudles[0]['m']='systemc';
		$moudles[0]['title']='系统管理';		
	}else{
		$moudles = $db->query('select m ,name title ,iconname
				from '.DBPREFIX.'admin_permission 
		        where id in (select permission_id from '.DBPREFIX.'admin_control where role_id = '.$rid.') and disabled = 0  order by display_order
				'
		);
	}
	return $moudles;
}	

//初始化侧边导航
function get_left_navs($m)
{
	$db      = new mypdo();
	$uid     = gets('uid');
	$member  = $db->query('select role_id,type from '.DBPREFIX.'admins where id = '.$uid,'Row');
	$rid     = $member['role_id'];
	$admin_permission_sons = $db->query("select CONCAT('?m=',m,'&a=',a,additional)as url,title,m,a,iconname from admin_permission_sons where m = '$m' and disabled = 0 and id in(select permission_sons_id from admin_control_sons where role_id = '$rid') order by display_order");
	return $admin_permission_sons;
}

//url 重定向
function redirect($url){
	header('Location:'.$url);	
}

//ajax输出
function ajax_out($str = ''){
	echo $str;
	exit;
}

//jsout
function js($msg= ''){
	return '<script>'.$msg.'</script>';
}

//获取头像
function avatar($path = '',$big = 'big',$type = 1){
	if(!in_array($big,array('big','middle','small'))) return NULL;
	$file = empty($path)?JS_PATH.'/avatar/noavatar_'.$big.'.gif':str_replace('big',$big,$path);
	return $type == 1?'<img class="avatar" src="'.$file.'" >':$file;
}

//日期格式
function dformat($time ,$type = 1){
	if(!empty($time)) {
		$time = (strstr($time, ':') || strstr($time, '-')) ? strtotime($time) : $time;
		if($type == 2){ 
			$t = "Y-m-d";
		}elseif($type == 3){
			$t = "H:i:s";
		}else{
			$t = "Y-m-d H:i:s";
		}
		return date($t,$time);
	}else{
		return '-';
	}
}

//判断提交是否正确
function submit_check($var = 'do_submit') {
	if(!empty($_POST[$var]) 
		&& $_SERVER['REQUEST_METHOD'] == 'POST'
		&&(empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST']))) {
		return true;
	} else {
		return false;
	}
}
//表单令牌
function build_token() {
	$tokenName  = '__hash__';
	$tokenType  = 'md5';
	if(!isset($_SESSION[$tokenName])) {
		$_SESSION[$tokenName]  = array();
	}
	// 标识当前页面唯一性
	$tokenKey   =  $tokenType($_SERVER['REQUEST_URI']);
	if(isset($_SESSION[$tokenName][$tokenKey])) {// 相同页面不重复生成session
		$tokenValue = $_SESSION[$tokenName][$tokenKey];
	}else{
		$tokenValue = $tokenType(microtime(TRUE));
		$_SESSION[$tokenName][$tokenKey]   =  $tokenValue;
	}
	$token      =  '<input type="hidden" name="'.$tokenName.'" value="'.$tokenKey.'_'.$tokenValue.'" />';
	return $token;
}

//检查表单令牌
function check_token() {
	$name   = '__hash__';
	if(is_null(getp($name)) || !isset($_SESSION[$name]) || $_SERVER['REQUEST_METHOD'] != 'POST'
		|| (empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) != preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST']))) { // 令牌数据无效
		msg('提交参数错误!');
	}
	// 令牌验证
	list($key,$value)  =  explode('_',getp($name));
	if($value && isset($_SESSION[$name][$key]) && $_SESSION[$name][$key] === $value) { // 防止重复提交
		unset($_SESSION[$name][$key]); // 验证完成销毁session
		return true;
	}
	// 开启TOKEN重置
	unset($_SESSION[$name][$key]);
	msg('提交参数错误!');
}


//产生formgeturl
function form_url_vars(){
	return 
	'<input type="hidden" name="m" value="'.ROUTE_M.'" />
	 <input type="hidden" name="a" value="'.ROUTE_A.'" />'
	.(getg('op')?'<input type="hidden" name="op" value="'.getg('op').'" />':'');
}

//调试输出函数
function dump($var = null, $vardump = false) {
	echo '<pre>';
	$vardump = empty($var) ? true : $vardump;
	if($vardump) {
		var_dump($var);
	} else {
		print_r($var);
	}
}


//生成目录安全文件
function build_dir_secure($dir = '') {
	file_put_contents($dir.'/index.html','');
}

//输出validate之js错误
function validate_errors($msg = array()){
	$script= '';
	foreach($msg as $key=>$val){
		$script .= "$(\"[name='$key']\").nextAll('.error').html('".$val."');";
	}
	$str  = '<script type="text/javascript">$(function(){'.$script.'})</script>';
	return $str;
}

//检测验证码,并清空session
function check_code($code = ''){
	if($code && isset($_SESSION['__code__']) && strtolower($code) == strtolower($_SESSION['__code__'])){
		return TRUE;
	}else{
		return FALSE;
	}
}

//检查是否为ajax
function check_ajax(){
	if(isset($_GET['callback']) or !isset($_SERVER['HTTP_X_REQUESTED_WITH'])or !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		exit("No permission!");
	}
}

//循环删除目录和文件函数  
function del_dir_all( $dirName ){  
	if ( is_dir($dirName) && $handle = opendir( $dirName) ) {  
	   while ( false !== ( $item = readdir( $handle ) ) ) {  
			if ( $item != "." && $item != ".." ) {  
				if ( is_dir( "$dirName/$item" ) ) {  
					del_dir_all( "$dirName/$item" );  
				} else {  
					unlink( "$dirName/$item" );  
				}  
			}  
		}  
		closedir( $handle );  
		rmdir($dirName);
	}else{
		 echo "目录无法找到： $dirName<br />\n";
	}
} 

//仅删除指定目录下的文件，不删除目录文件夹。
function del_dir_file( $dirName = '' ){ 
	if ( is_dir($dirName) && $handle = opendir( $dirName) ) {  
		while ( false !== ( $item = readdir( $handle ) ) ) {  
			if ( $item != "." && $item != ".." ) {  
				if ( is_dir( "$dirName/$item" ) ) {  
					del_dir_file( "$dirName/$item" );  
				} else {  
					unlink( "$dirName/$item" );  
				}  
			}  
		}  
		closedir( $handle );  
	}else{
		 echo "目录无法找到： $dirName<br />\n";
	}
}

//数字填充
function pad_str($str,$len,$pad){
	if(strlen($str) > abs($len) ){
		return substr($str,$len);	
	}else{
		$r = str_repeat($pad,abs($len) - strlen($str));
		return $len > 0 ? $r.$str  :$str.$r;
	}
}

//log函数
function logdx($str = '',$file = 'log'){
	$log  = ' ['.date('m-d H:i:s',$_SERVER['REQUEST_TIME']).']';
	$log .= pad_str(' ['.$_SERVER['REMOTE_ADDR'].']',-18,"&nbsp;");
	$log .= pad_str(' ['.$_SERVER['REQUEST_METHOD'].']',-6,"&nbsp;");
	$log .= pad_str(' ['.$_SERVER['SCRIPT_NAME'].'] ',-25,"&nbsp;");
	$log .= strlen($_SERVER['REQUEST_URI'])>100?'':' ['.$_SERVER['REQUEST_URI'].'] ';
	$log .= $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST) ?' ['.var_export($_POST,1).']':'';	
	if($str != ''){
		ob_start();
		if(is_array($str)){
			print_r($str);	
		}else{
			echo $str;	
		}
		$str = ob_get_contents();
		ob_end_clean();
		$log .= ' ['.$str.']';
	}
	$log .= "\r\n";
	if($file == 'log')$file = date('Ymd').'.log';
	file_put_contents('api/caches/log/'.$file.'.txt',$log,FILE_APPEND);
}

//log函数
function get_logs($file = 'log'){
	if($file == 'log')$file = date('Ymd').'.log';
	return file_exists('api/caches/log/'.$file.'.txt')?str_replace("\r\n",'<br/>',file_get_contents(APPLICATION.'/caches/log/'.$file.'.txt')):'';
}

//输出json字符串
function json_out($data = array()){
	echo empty($data)?json_encode(array()):json_encode($data);
	exit;
}

//打印url参数
function put_url_param($filename='test.txt',$addcontent,$isappend=true)
{
	$queryString = $_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]."---".file_get_contents("php://input");
	$content = date('Y-m-d h:i:s',time())."\n".$_SERVER['REMOTE_ADDR']."\n".var_export($_POST,1)."\n".$queryString."\n";
	if($isappend)
	{
	file_put_contents($filename,$content.$addcontent,FILE_APPEND);
	}
	else 
	{
	file_put_contents($filename,$content.$addcontent);
	}
}

//curl post
function curl_post($url = '',$data = array(),$headers = array()){
	
	//构造post数据
	$postfield = $s = '';
	foreach($data as $k=>$v){
		$postfield .= $s.$k.'='.$v;
		$s = '&';
	}
	//构造header
	$headerArr = array(); 
	foreach( $headers as $n => $v ) { 
		$headerArr[] = $n .':' . $v;  
	}
	$curl = curl_init(); 
	curl_setopt($curl, CURLOPT_URL,$url);
	curl_setopt($curl, CURLOPT_HEADER, false); 
	curl_setopt($curl, CURLOPT_POST, 1);//启用POST提交
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postfield); //设置POST提交的字符串
	curl_setopt($curl, CURLOPT_HTTPHEADER , $headerArr ); 
	curl_setopt($curl, CURLOPT_TIMEOUT, 25); // 超时时间 
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; SeaPort/1.2; Windows NT 5.1; SV1; InfoPath.2)");  
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);   
	$value = curl_exec($curl);   
	curl_close($curl);
	return $value;	
}

//取代json_encode,有中文的用这个
function encode_json($str) { 
	return urldecode(json_encode(url_encode($str))); 
} 

//urlencode编码
function url_encode($str) { 
	if(is_array($str)) { 
		foreach($str as $key=>$value) { 
			$str[urlencode($key)] = url_encode($value); 
		} 
	} else { 
		$str = urlencode($str); 
	} 
	return $str; 
}

function on_click($data)
{
	error_reporting(E_ALL);
	set_time_limit(0);
	//echo "<h2>TCP/IP Connection</h2>\n";

	$service_port = 10000;
	$address = "115.28.80.234";

	#整个网络通信过程
	#socket_create
	#socket_connect
	#socket_write
	#socket_read
	#socket_close

	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

	if ($socket < 0)
	{
//	echo "socket_create() failed: reason: " . socket_strerror($socket) . "\n";
	}
	else
	{
		//echo "OK.\n";
	}

		//echo "试图连接 '$address' 端口 '$service_port'...<br>";
		$result = socket_connect($socket, $address, $service_port);
		//echo "------".var_dump($result)."-------------";

		if ($result < 0)
		{
		//echo "socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "\n";
		}
		else
		{
		//echo "连接OK<br>";
	}
			$in  = msgpack_pack($data);

			$out = '';
			$out1='';


			if(!socket_write($socket, $in, strlen($in)))
			{
		//	echo "socket_write() failed: reason: " . socket_strerror($socket) . "\n";
			}
			else
			{
			//echo "发送到服务器信息成功！<br>";
			//echo "发送的内容为:<font color='red'>$in</font> <br>";
			}


			//echo "关闭SOCKET...<br>";
			socket_close($socket);
			//echo "关闭OK<br>";
			
	}
function _parse_name($name){
	$name = str_replace('\\','/',$name);
	if(strpos($name,'://') !== false){
		$parse 	= explode('://',$name);
		return 	array('app'=>$parse[0],'item'=>$parse[1]);
	}else{
		return array('app'=>APPLICATION,'item'=>$name);
	}
}
function require_cache($filename) {
    static $_import_files = array();
    if (!isset($_import_files[$filename])) {
        if (file_exists($filename)) {
            include $filename;
            $_import_files[$filename] = 1;
        } else {
            exit($filename.' does not exist!');
        }
    }
}
function M($name,$dir = '/models'){
	static $_models = array();
	$items = _parse_name($name);
	$modelclass = (strpos($items['item'],'/') !== false?substr(strrchr($items['item'],'/'),1):$items['item']).'_model';
	if(!isset($_models[$modelclass])){
		if(strpos($name,'://') !== false){	
			$model_dir = $items['app'].'/models/';
		}else{
			$model_dir = 
			$dir ? trim($dir,'/').'/' : (defined('MODEL_DIR') ? trim(MODEL_DIR,'/').'/' : $items['app'].'/models/');
		}
		if(!class_exists($modelclass,false))
			require_cache(CMS_ROOT.'/'.$model_dir.$items['item'].'_model.php');
		$_models[$modelclass] = new $modelclass();
	}
	return $_models[$modelclass];
}