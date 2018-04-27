<?php
defined('WENXIAOCMS') or exit('Access denied!');
abstract class controller {
	var $view;
	var $db;
	var $cache;
	var $excel;

	abstract function on_index();
	
	function __construct(){
		$this->secure_check();
		$this->init_view();
		$this->init_db();
		$this->init_excel();
	}
	//安全检测
	function secure_check(){
// 		_xss_check();
// 		if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) exit('Access Error');
	}
	
	//初始化数据库
	function init_db(){
		$this->db = new mypdo();
	}
	//初始化excel
	function init_excel(){
		$this->excel = new excel();
	}
	
	//初始化视图
	function init_view(){
		$this->view = new template();	
	}
	
	//控制器方法内置视图编译变量
	function assign($k = '', $v = ''){
		$this->view->assign($k , $v);
	}
	
	//控制器方法内置视图显示
	function display($filename = ''){
		$this->view->display($filename);
	}
	
	//控制器方法内置视图渲染输出
	function render($data = array() ,$tpl = ''){
		$this->view->render($data,$tpl);
	}	
	
	
}