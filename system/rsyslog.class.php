<?php
class rsyslog {
	//RequestLog,请求日志
	function request($msg = ''){
		$this->do_log($msg, LOG_LOCAL1);
	}
	
	//FilledRequestLog,有效请求日志
	function filled_request($msg = ''){
		$this->do_log($msg, LOG_LOCAL2);
	}
	
	//ResponseLog,广告创意日志
	function response($msg = ''){
		$this->do_log($msg, LOG_LOCAL3);
	}
	
	//ClickLog,点击日志
	function click($msg = ''){
		$this->do_log($msg, LOG_LOCAL4);
	}
	
	//ConversionLog,转化日志
	function conversion($msg = ''){
		$this->do_log($msg, LOG_LOCAL5);
	}
	
	//作弊和扣量等报表,转化日志
	function extra_conversion($msg = ''){
		$this->do_log_debug($msg, LOG_LOCAL5);
	}
	
	//ConversionLog,转化日志
	function userapplist($msg = ''){
		$this->do_log($msg, LOG_LOCAL6);
	}
	
	//ImpressionLog,展示日志
	function impression($msg = ''){
		$this->do_log($msg, LOG_LOCAL7);
	}
	
	//执行记录log
	function do_log($msg,$level){
		$prefix = date('Y-m-d H:i:s').'|'.($_SERVER['HTTP_X_REAL_IP']?$_SERVER['HTTP_X_REAL_IP']:$_SERVER['REMOTE_ADDR']).'|';
		openlog(NULL,NULL, $level );
		syslog(LOG_NOTICE, $prefix.$msg);
		closelog();		
	}
}