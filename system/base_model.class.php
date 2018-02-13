<?php
//define('REDIS_IP',		'10.162.80.234');
define('REDIS_IP',		'127.0.0.1');
define('REDIS_PORT', 	6379);
define('REDIS_DB',		7);
//////////////////////////////////////////////
class base_model{
	private $is_redis = false;
	//魔术方法
	function __get ($name) {
		static $_models = array();
        if (isset($_models[$name])) {
            return $_models[$name];
        }
		switch($name){
			case 'db':
				$_models['db'] = new mypdo();
				break;
			case 'db2':
				$_models['db2'] = new mypdo('', '', '', 'wenxiaocms_db2');
				break;
			case 'redis':
				$redis	= new Redis;
				$redis->connect(REDIS_IP,REDIS_PORT,3);
				$redis->select(REDIS_DB);
				$_models['redis'] = $redis;
				$this->is_redis = true;
				break;
			case 'syslog':
				$syslog	= new rsyslog;
				$_models['syslog'] = $syslog;
				break;
		}
		return $_models[$name];
	}
	//按照条件获取db
	function get_db(){
		if(1){
			return $this->db2;
		}else{
			return $this->db;
		}
	}
	//析构函数
	function __destruct(){
		if($this->is_redis){
			$this->redis->close();
		}		
	}	
}