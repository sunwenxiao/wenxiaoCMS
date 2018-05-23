<?php
defined('WENXIAOCMS') or exit('Access denied!');
define('USER_CORRECT', '1000');
define('USER_WARNING', '1001');
define('APP_WARNING', '1002');
define('IOS_PUBLICK_KEY', 'SadfhEGeroasdnAdfg123skL');
//得到用户自定义的头部
function getu($k){
	$var = &$_SERVER;
	$k = 'HTTP_'.$k;
	return isset($var[$k]) ? new_html_special_chars($var[$k]) : NULL;
}

//签名校验
function check_sign($str = '',$ver = ''){
	if($str == '' or $ver == '') return false;
	$str = base64_encode(strtoupper(md5($str)));
	return strrev(strtoupper(md5(substr($str,2,strlen($str)-3)."kh0002-KHWallS1")))== $ver;
}
//安卓签名校验
function check_android_sign($str = '',$ver = ''){
	if($str == '' or $ver == '') return false;
	$str = base64_encode(strtoupper(md5($str)));
	return strrev(strtoupper(md5(substr($str,2,strlen($str)-3)."PuBuWallState")))== $ver;
}
/**
 * 获取IOS的c3des对象
 */
function get_ios_private_c3des(){
	$key = getu('KX');
	if(isset($key)&&!empty($key)){
		$c3des_p 	= new c3des(IOS_PUBLICK_KEY);
    	$key_arr = explode('#', $c3des_p->decrypt($key));
    	$rand = $key_arr[0];
    	$open_udid = $key_arr[1];
    	$private_key = substr(strtoupper(md5($open_udid)),0,16).'KHWallDw';
    	return new c3des($private_key);
	}else{
		return FALSE;
	}
}
/**
 * 获取Android的c3des对象
 */
function get_android_private_c3des(){
	$key = getu('KX');
	if(isset($key)&&!empty($key)){
		$c3des_p 	= new c3des(IOS_PUBLICK_KEY);
    	$key_arr = explode('#', $c3des_p->decrypt($key));
    	$rand = $key_arr[0];
    	$open_udid = $key_arr[1];
    	$private_key = substr(strtoupper(md5($open_udid)),0,16).'PuBuWallAndroid';
		
    	return new c3des($private_key);
	}else{
		return FALSE;
	}
}

function sys_auth($string, $operation = 'ENCODE', $key = '', $expiry = 0) {
		$key_length = 4;
		$key = md5($key != '' ? $key : "asfwexaloq");
		$fixedkey = md5($key);
		$egiskeys = md5(substr($fixedkey, 16, 16));
		$runtokey = $key_length ? ($operation == 'ENCODE' ? substr(md5(microtime(true)), -$key_length) : substr($string, 0, $key_length)) : '';
		$keys = md5(substr($runtokey, 0, 16) . substr($fixedkey, 0, 16) . substr($runtokey, 16) . substr($fixedkey, 16));
		$string = $operation == 'ENCODE' ? sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$egiskeys), 0, 16) . $string : base64_decode(substr($string, $key_length));
	
		$i = 0; $result = '';
		$string_length = strlen($string);
		for ($i = 0; $i < $string_length; $i++){
			$result .= chr(ord($string{$i}) ^ ord($keys{$i % 32}));
		}
		if($operation == 'ENCODE') {
			return $runtokey . str_replace('=', '', base64_encode($result));
		} else {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$egiskeys), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		}
}

function extlog($lg){
	$sn = getu('SN')?getu('SN'):getg('SN');
	$ai = getu('AI')?getu('AI'):getg('AI');
	return $lg.'|'.$sn.'|'.$ai;
}

function trace($title,$data = array()){
	if(D_LOG) logd(array('title'=>$title.' =>','data'=>$data));
}

//获取时间索引
function get_timeindex(){
	return '1000'.( date("w") == 0 ? 7 : date("w") ).date('H');
}

//获取城市
function get_city(){
	return strtolower($_SERVER['GEOIP_CITY']) ? strtolower($_SERVER['GEOIP_CITY']) . 'shi' : 'beijingshi'; 
}

function encrypt($data,$key='mobile_admin_Advert'){
	
	return base64_encode(sys_auth(serialize($data), 'ENCODE', $key));
}

function decrypt($data,$key='mobile_admin_Advert'){
	return unserialize(sys_auth(base64_decode($data), 'DECODE', $key));
}

function get_servers($type){
	global $platform_thrift_servers,$api_thrift_servers;
	if($type == 'platform'){
		return $platform_thrift_servers[mt_rand(0,count($platform_thrift_servers) - 1)];
	}
	if($type == 'api'){
		return $api_thrift_servers[mt_rand(0,count($api_thrift_servers) - 1)];
	}
}

function get_head($url){
	$url = htmlspecialchars_decode($url);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, 1);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 1);
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; SeaPort/1.2; Windows NT 5.1; SV1; InfoPath.2)"); 
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($curl);
	curl_close($curl);
	return $data;
}

function is_apk($url){
	$url = htmlspecialchars_decode($url);
	$head = get_head($url);
	if($head){
		$info = explode("\n",$head);
		$val  = explode(" ",$info[0]);
		$code = $val[1];
		if($code == 200){
			$android_char = 'application/vnd.android.package-archive';
			return strpos($head,$android_char) ? array('url'=>$url): 'not apk';
		}
		if($code >= 400){
			return 'Bad url!';	
		}
		if($code == 301 or $code == 302){
			$redirect_url = '';
			foreach($info as $v){
				if(strpos($v,'Location:')!==false){
					$redirect_url = trim(substr($v,strpos($v,'Location:')+9));	
					break;
				}
			}
			if($redirect_url){
				return is_apk($redirect_url);
			}else{
				return 'Error url!';	
			}
		}
	}else{
		return 'Not valid url!';	
	}	
}
