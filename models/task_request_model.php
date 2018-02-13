<?php
class task_request_model extends base_model{	
	//获取taskid
	public function get_task($pc_name,$screen_x,$screen_y){
		if(!in_array($screen_y.$screen_x,array(19201080,1280720,800480))){
			$this->redis->hset('pc_error',$pc_name.'|x:'.$screen_x.'|y:'.$screen_y,1);
			return array();
		}
		$pc_id 		= M('pc')->get_id($pc_name);
		$hour 		= date('YmdH',$_SERVER['REQUEST_TIME']);
		$task_id 	= self::get_task_liucun_id($pc_id,$screen_x,$screen_y,$hour);
		$type 		= 'liucun';
		if(empty($task_id)){
			$task_id = self::get_task_register_id($hour);
			$type 	 = 'register';
		}
		if($task_id){
			$context					= self::get_context($task_id);
			$context['screen_index'] 	= $screen_y.$screen_x;
			return array(
				'type'		=> $type,
				'task_id'	=> $task_id,
				'context'	=> $context);	
		}else{
			return array();	
		}
	}
	//获取APP相关信息,获取上下文信息
	//app_id
	//control_id
	//register_id
	private function get_context($task_id){
		$key = 'task:info:'.$task_id;
		return $this->redis->hgetall($key);
	}
	
	//获取留存的taskid
	private function get_task_liucun_id($pc_id,$screen_x,$screen_y,$hour){
		$pc_key 			= 'task:liucun:pc:'.$pc_id.':hour:'.$hour;
		$screen_key 		= 'task:liucun:screen:'.$screen_y.$screen_x.':hour:'.$hour;
		$temp_key			= 'task:liucun:tmp:'.microtime(true).mt_rand(1000,9999);
		$disable_key 		= 'task_ids_stoped:'.$hour;
		$this->redis->sinterstore($temp_key,$pc_key,$screen_key);
		$task_liucun_ids 	= $this->redis->sdiff($temp_key,$disable_key);
		$task_liucun_id 	= 0;
		if($task_liucun_ids){
			$times 			= 0;
			while($times < 5){
			  $times++;
			  $rand_key = array_rand($task_liucun_ids);
			  $task_liucun_id = $task_liucun_ids[$rand_key];
			  $task_lock = 'task:lock:'.$task_liucun_id;
			  if($this->redis->setnx($task_lock,1)){
				  $this->redis->srem($pc_key,$task_liucun_id);
				  $this->redis->expire($task_lock,10);
				  break;
			  }else{
				 $task_liucun_id = 0; 
			  }
		  	}
		}
		$this->redis->del($temp_key);
		return $task_liucun_id;
	}
	
	//获取注册的taskid
	private function get_task_register_id($hour){
		$register_key 	= 'task:register:hour:'.$hour;
		$temp_key		= 'task:register:tmp:'.microtime(true).mt_rand(1000,9999);
		$disable_key 	= 'task_ids_stoped:'.$hour;
		$this->redis->sdiffstore($temp_key,$register_key,$disable_key);
		$times 			= 0;
		while($times < 5){
			$times++;
			$task_register_id 	= $this->redis->srandmember($temp_key);
			if($task_register_id){
				$task_lock = 'task:lock:'.$task_register_id;
				if($this->redis->setnx($task_lock,1)){
					$this->redis->srem($register_key,$task_register_id);
					$this->redis->expire($task_lock,10);
					break;
				}else{
					$task_register_id = 0;
				}
			}else{
				$task_register_id = 0;
				break;
			}
		}
		$this->redis->del($temp_key);
		return $task_register_id;
	}
}