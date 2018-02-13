<?php
defined('WENXIAOCMS') or exit('Access denied!');
class admincontroller extends controller {
    protected $admin = array();
    function __construct(){
        parent::__construct();
    	$this->admin = $this->login_check();
		if (empty($this->admin) && ROUTE_M != 'login') {
			header('Location:'.SCRIPT_NAME.'?m=login&a=index');
			exit;
		}
		
    }
    protected static $mysql;
    protected static function __getDBInstance($conf){
    
    	$mysql = new mysqli($conf['server_ip'],$conf['server_mysql_user'],$conf['server_mysql_pass'],$conf['server_mysql_db'],$conf['mysql_port']);
    	self::$mysql = $mysql ;
    	return self::$mysql;
    	 
    }
	//登陆检查
	protected function login_check () {
		if (empty($_COOKIE['m_ac_r']))
		{ 
			return false;
		}
		load_app_func('api',APPLICATION);
		$cookie = unserialize(sys_auth($_COOKIE['m_ac_r'], 'DECODE', 'mobile_admin_AHJKED', 10800));
		if ($cookie) {
			$admin =
			$this->db->query("select * from admins where id='" . intval($cookie['id']) . "' and login_name='" . $cookie['name'] . "'",'Row');
			if ($admin) {
				return $admin;
			}
		}
		return false;
	}

	protected function set_cookie ($id, $name) {
		load_app_func('api',APPLICATION);
		$value = array('id' => $id, 'name' => $name);
		$value = sys_auth(serialize($value), 'ENCODE', 'mobile_admin_AHJKED', 10800);
		setcookie('m_ac_r', $value, time()+10800,'/');
	}
	
	protected function clear_cookie () {
		setcookie('m_ac_r', '', -1, '/');
	}
	
	protected function optlog($operation = array()){
		if(!isset($this->admin)) return;
		$admin = $this->admin;
		$role = $this->db->get_one('name','admin_role','id = '.$admin['role_id']);
		if($this->admin['type'] == 101){
			$role = '超级管理员';
		}
		$param['ip'] 				= $_SERVER['REMOTE_ADDR'];
		$param['add_time'] 			= time();
		$param['mid']				= $admin['id'];
		$param['login_name'] 		= $admin['login_name'];
		$param['real_name'] 		= $admin['real_name'];
		$param['role_name'] 		= $role;
		$param['object'] 		    = isset($operation['obj'])		? $operation['obj'] :'';
		$param['operation'] 		= isset($operation['opt'])		? $operation['opt'] :'';
		$param['before'] 			= isset($operation['before'])	? $operation['before']:'';
		$param['after'] 			= isset($operation['after'])	? $operation['after']:'';
		$this->db->insert('admin_log',$param);
	}
	/* !CodeTemplates.overridecomment.nonjd!
	 * @see controller::on_index()
	 */
	public function on_index() {
		// TODO 自动生成的方法存根
		
	}

	
	
}

?>
