<?php
defined('WENXIAOCMS') or exit('Access denied!');
abstract class controller {
	abstract function on_index();
	function __construct(){
	}	
	function __get ($name) {
		static $_models = array();
        if (isset($_models[$name])) {
            return $_models[$name];
        }
		switch($name){
			case 'db':
				$_models['db'] = new mypdo();
				break;
			case 'excel':
				$_models['excel'] =  new excel();
				break;
			case 'view':
				$_models['view'] = new template();
				break;
		}
		return $_models[$name];
	}
	function assign($k = '', $v = ''){
		$this->view->assign($k , $v);
	}
	function display($filename = ''){
		$this->view->display($filename);
	}
	function render($data = array() ,$tpl = ''){
		$this->view->render($data,$tpl);
	}	
}