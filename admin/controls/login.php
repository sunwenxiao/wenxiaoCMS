<?php
class login extends admincontroller{
	//登录页
	function on_index(){
	    $adminlogin = array();
	    $adminlogin['title'] = '欢迎使用文晓框架';
	    $adminlogin['heading'] = '登陆';
		$this->assign('adminlogin',$adminlogin);
		$this->display('admin_login');
	}
	//登入
	function on_in(){
		$name = getp('username');
		$pwd  = getp('password');
		$remember  = getp('remember');
		if(!$remember)
		{
			$remember = 0;
		}
		check_token();
		if (!empty($_COOKIE['m_ac_r']))
		{
		$cookie = unserialize(sys_auth($_COOKIE['m_ac_r'], 'DECODE', 'mobile_admin_AHJKED', 10800));
		if(!empty($cookie['remember'])){
			$strSql = "select count(id) as c from ".DBPREFIX."admins where login_name='$name' and password='".$pwd."' ";
			
			$member = $this->db->query($strSql,'Row');
			if($member['c']>=1) {
				echo json_encode(array(
					'isok' => 1,
					'message' => sprintf('欢迎 %s', $name),
					'jump_page' => '?m=systemc',
				));
			}
			else
			{
				echo json_encode(array(
					'isok' => 0,
					'message' =>'密码错误',
					'jump_page' => '?m=login',
				));
			}
			exit();
		}
		}

		$strSql = "select id,salt,password,type,role_id from ".DBPREFIX."admins where login_name='$name'";
	
		$member = $this->db->query($strSql,'Row');

		if(md5(md5($pwd).$member['salt']) != $member['password'])
		{
			echo json_encode(array(
				'isok' => 0,
				'message' =>'密码错误',
				'jump_page' => '?m=login',
			));
		}
		else
		{
			$jumpurl = "?m=login";
			$roid = $member['role_id'];
			if ($roid) {
				//查找对应角色跳转路径
				$aSql = "select action_url from ".DBPREFIX."admin_role where id=$roid";
				
				$aurl = $this->db->query($aSql,'Row');
				
				$jumpurl = $aurl['action_url'];
			}
			echo json_encode(array(
				'isok' => 1,
				'message' => sprintf('欢迎 %s', $name),
				'jump_page' => $jumpurl,
			));
		}
		set_session('uid',$member['id']);
		set_session('uname',$name);
		set_session('roleid',$member['role_id']);
		$this->set_cookie($member['id'], $name,$member['password'],$remember);

	}
	
	//验证
	function on_validate()
	{
		$valid = true;
		//用户名的验证
		if (getg('field')=='username') {
			$name = getp('username');
			$strSql = "select count(id) as c from ".DBPREFIX."admins where login_name='$name'";
			$member = $this->db->query($strSql,'Row');
			if ($member['c']== 0) {
				$valid = false;
			} 
		}
		//验证码的验证
		if (getg('field')=='code') {
			$code = getp('code');
			if (!check_code($code)) {
				$valid = false;
			} 
		}
		echo json_encode(array('valid' => $valid));
	
	}
	
	//退出
	function on_out(){
		csfr_check();
		$this->clear_cookie();
		session_destroy();
		msg('退出成功!','?m=login');
	}	
	
	//注册
	function on_register()
	{
	    $this->display('admin_register');
	}
}