<?php
/**
 * base.php             CMS框架入口文件
 * @copyright			(C) 2013-2015 alex
 * @lastmodify			2013-7-1
 */
defined('WENXIAOCMS') or exit('Access denied!');

//数据缓存开关
define('DATA_CACHE', 1);
//0:关闭时间缓存,大于0:开启时间缓存
define('DATA_CACHE_EXPIRED', 0);
//数据缓存模式,1:file,2:redis
define('DATA_CACHE_MODE', 1);

//请求默认参数
define('REQUEST_MODULE','m');
define('REQUEST_ACTION','a');

//调试跟踪模式
define('RUN_TRACE', 0);
if(RUN_TRACE){
	$start_time   = microtime(TRUE);
	$start_usage  = memory_get_usage();
	$read_times   = 0;
	$write_times  = 0;
}

//调试错误输出(输出代码运行和Mysql错误):1开,0关
define('D_BUG', 1);
D_BUG ? error_reporting(E_ALL) : error_reporting(0);

//初始参数设置
@set_magic_quotes_runtime(0);
@define('MAGIC_QUOTES_GPC' ,get_magic_quotes_gpc());

//session设置,0为关,大于0开启
define('SESSION_TIMEOUT' ,0);
define('V_HASH' ,mt_rand(10000,20000));
define('CMS_ROOT' , substr(dirname(__FILE__), 0, -7));
define('SYS_ROOT' , CMS_ROOT.'/system/');

//网站域名
define('SITE_URL' ,'http://'.$_SERVER['HTTP_HOST'].'/');
define('SCRIPT_NAME',basename($_SERVER['PHP_SELF']));
define('JS_PATH' ,'system/js');

//语言编码
define('CHARSET' ,'utf-8');
header('Content-type: text/html; charset='.CHARSET);

//开启session
session_start();
date_default_timezone_set('PRC');


//GIP开关,0关,1开
define('GIP',1);
if(GIP && function_exists('ob_gzhandler')) {
	ob_start('ob_gzhandler');
}else{
	ob_start();
}

//db.config 和 公共函数库 加载
include SYS_ROOT.'db.config.php';
include SYS_ROOT.'global.func.php';
unset($_ENV, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS);
class base{	

	//运行应用$app_name:应用名称,$request_m:控制器,默认为空
	static function run_application( $app_name = '' ,$request_m = '' ,$request_a = ''){
		//application 相关
		define('APPLICATION',		empty($app_name) ? 'application' : $app_name);
		if(APPLICATION == 'system') exit('Sorry, you can not create system application!');
		if(!is_dir(APPLICATION))    self::create_application(APPLICATION);
		
		//路径相关
		define('IMG_PATH',	APPLICATION.'/static/images');
		define('CSS_PATH',	APPLICATION.'/static/css');
		
		
		//控制器
		define('ROUTE_M',	$request_m == ''?(getg(REQUEST_MODULE) ? getg(REQUEST_MODULE):'main'):$request_m);
		define('ROUTE_A',   $request_a == ''?(getg(REQUEST_ACTION) ? getg(REQUEST_ACTION):'index'):$request_a);
	/*	$db             = new mypdo();
		$modules = $db->query("select m from admin_permission where appname='".$app_name."'");
		foreach ($modules as $k => $v)
		{
			$modules[$k] = $v['m'];
		}*/
		$filename    = APPLICATION.'/controls/'.ROUTE_M.'.php';
		//!in_array(ROUTE_M, $modules) && exit('Module not allowed!');
		!file_exists($filename)	     && exit('Module load error!');
		include CMS_ROOT.'/'.$filename;
	
		$classname =  ROUTE_M;
		$control   =  new $classname();
		$method    =  'on_'.ROUTE_A;
		if( method_exists ($control, $method)) {
			$control->$method();
		} else {
			exit('Action not found!');
		}
		self::run_trace();
		unset($GLOBALS, $_ENV, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS);
	}
	
	//创建应用
	static function create_application( $app_name = ''){
		mkdir ( $app_name , 0777) or exit('Dir is not writalbe!') ;
		build_dir_secure($app_name);
		$db = new mypdo();
		$arrayDataValue = array('appname'=>$app_name);
		$db->insert('admin_appname_list', $arrayDataValue);
		$arrayDataValue2 = array('appname'=>$app_name,'name'=>'主控','m'=>'main');
		$db->insert('admin_permission', $arrayDataValue2);
		$dirs  = array(
			'caches',
			'caches/data',
			'caches/templates',
			'caches/log',
			'config',
			'controls',
			'upload',
			'upload/avatar',
			'templates',
			'templates/default',
			'static',
			'static/js',
			'static/css',
			'static/images',
			'html',
			'lib',
		);
        foreach ($dirs as $dir){
			$dir = $app_name.'/'.$dir;
            if(!is_dir($dir)){
				mkdir($dir,0777,true);
				build_dir_secure($dir);
			}
        }
		$str = 
		'<Files *.html>
		Order Allow,Deny 
		Deny from all
		</Files>';
		file_put_contents($app_name.'/templates/.htaccess',str_replace("\t",'',$str));
		$str = 
		'<?php
		$modules = array(\'main\');';
		file_put_contents($app_name.'/config/modules.php',str_replace("\t",'',$str));
		$str = 
		'<?php
		 class main extends controller {
		    function on_index(){
		      $this->view->display();	
		    }
		 }';
		file_put_contents($app_name.'/controls/main.php',str_replace("\t",'',$str));
		$str=
		'<html>
		<body>
		hello world ^_^ !
		</body>
		</html>';
		file_put_contents($app_name.'/templates/default/main_index.html',str_replace("\t",'',$str));
	}
	
	//调试跟踪输出
	static function run_trace(){
		if(RUN_TRACE){
			global 	$read_times,$write_times,$start_usage,$start_time;
			$run_time       = number_format((microtime(TRUE) - $start_time),5);
			$usage          = number_format((memory_get_usage()-$start_usage)/1024);
			$peak_usage     = number_format((memory_get_peak_usage()-$start_usage)/1024);
			$ip             = get_ip();
			$city           = get_ip_city($ip);
			$db             = new mypdo();
			$db->insert(DBPREFIX.'run_log',
			array(
				'request_time'  => time(),
				'ip'            => $ip,
				'city'          => $city,
				'request_method'=> $_SERVER['REQUEST_METHOD'],
				'request_url'   => $_SERVER['REQUEST_URI'],
				'run_time'      => $run_time,
				'usage'         => $usage,
				'peak_usage'    => $peak_usage,
				'read_times'    => $read_times,
				'write_times'   => $write_times,
				'host'          => $_SERVER['HTTP_HOST'],
				'user'          => gets('uname') ? gets('uname'):'guest',
			)
			);
		}
	}	
}