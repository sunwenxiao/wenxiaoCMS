<?php
defined('WENXIAOCMS') or exit('Access denied!');
//仅对mysql中的get_all,get_row,get_pages进行缓存,
class cache{
	static $cache_dir ;
	function __construct(){
		self::$cache_dir = CMS_ROOT.'/'.APPLICATION.'/caches/data/';
		if(!is_dir(self::$cache_dir)){
			mkdir(self::$cache_dir,0777,true);
			build_dir_secure(self::$cache_dir);
		}
		!is_writable( self::$cache_dir ) && exit('Data caches folder is not writable!');
	}
	
	//设置缓存
	function set_cache($sql = '' , $data = array()){
		$cache_file =  self::get_cache_name($sql);
		file_put_contents($cache_file,serialize($data));
		touch($cache_file,time());
	}
	
	//获取缓存
	function get_cache($sql = ''){
		$cache_file =  self::get_cache_name($sql);
		$cache_data = unserialize(file_get_contents($cache_file));
		return $cache_data;
	}
	
	//1.缓存文件存在,2.过期时间已到,3.数据表已改变
	function is_expired($sql = ''){
		$cache_file =  self::get_cache_name($sql);
		if(!file_exists($cache_file) or (DATA_CACHE_EXPIRED && (filemtime($cache_file) + DATA_CACHE_EXPIRED) < time()) or $this->tbl_changed($sql)){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	//数据表是否已改变
	function tbl_changed($sql = ''){
		//缓存数据时间
		$cache_data_file =  self::get_cache_name($sql);
		$cache_data_time = filemtime($cache_data_file);
		//缓存tbl
		$cache_tbl_file  = self::$cache_dir.'tables.php';
		if(!file_exists($cache_tbl_file )){file_put_contents($cache_tbl_file,serialize(array()));}
		$cache_tbl       = unserialize(file_get_contents($cache_tbl_file));		
		//用到的tbl
		$sql = strtolower($sql);
		$sql = str_replace(array("\t","\n","\r"),' ',$sql);
		preg_match_all('/'.DBPREFIX.'_[a-z_]*/',$sql,$tbls);
		$tbl = array_unique($tbls[0]);
		foreach($tbl as $v){
			if(!isset($cache_tbl[$v])){
				$this->cache_table($v,strtotime('-1 day',$cache_data_time));
			}else{
				if($cache_tbl[$v] > $cache_data_time){
					return TRUE;
					break;	
				}
			}
		}
		return FALSE;	
	}
	
	//记录数据表修改时间
	function cache_table($tbl = '' ,$time = 0){
		if($time == 0 ) $time = time();
		$cache_file = self::$cache_dir.'tables.php';
		if(file_exists($cache_file)){
			$cache_data = unserialize(file_get_contents($cache_file));
		}else{
			$cache_data = array();
		}
		$cache_data[$tbl] = $time;
		file_put_contents($cache_file,serialize($cache_data));
	}
	
	//删除缓存
	function del_cache(){
		del_dir_all(self::$cache_dir);
	}
	
	//更新缓存
	function update_cache(){
		$cache_file = self::$cache_dir.'tables.php';
		if(file_exists($cache_file)){
			$tbls = unserialize(file_get_contents($cache_file));
		}else{
			$tbls = array();
		}
		foreach($tbls as $k=>&$v){
			$tbls[$k] = time();
		}
		file_put_contents($cache_file,serialize($tbls));
	}
	
	//得到缓存文件名
	static function get_cache_name($sql = ''){
		$filename = md5(strtolower($sql)).'.php';
		$dir1     = self::$cache_dir.substr($filename,0,2);
		$dir2     = $dir1.'/'.substr($filename,2,2);
		if(!is_dir($dir1)){
			mkdir($dir1, 0777);
			build_dir_secure($dir1);
		}
		if(!is_dir($dir2)) {
			mkdir($dir2, 0777);
			build_dir_secure($dir2);
		}
		return $dir2.'/'.$filename;
	}
}