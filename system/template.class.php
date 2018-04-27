<?php
defined('WENXIAOCMS') or exit('Access denied!');

//模版解析类
class template{
	
	var $vars = array();
	
	//变量加载,可字符串,可数组,可类
	function assign($k = '', $v = ''){
		if(is_array($k)) {
            $this->vars   =  array_merge($this->vars,$k);
        }elseif(is_object($k)){
            foreach($k as $key =>$val)
                $this->vars[$key] = $val;
        }else{
            $this->vars[$k] = $v;
        }
	}
	
	//模版显示
	function display($filename = ''){
		$filename = $filename === '' ? ROUTE_M.'_'.ROUTE_A:$filename;
		$this->vars && extract($this->vars, EXTR_SKIP);
		include $this->template_cache($filename);
		unset($this->vars);
	}
	
	//渲染模版
	function render($k = '',$filename = ''){
		if(!empty($k)) $this->assign($k);
		$this->display($filename);
	}
	//生成html文件
	function create_html(){
		
	}
	
	//删除模版缓存
	function del_cache(){
		$cache_dir  = CMS_ROOT.'/'.APPLICATION.'/caches/templates/';
		del_dir_file($cache_dir );
	}
	
	//解析模版文件
	function template_cache($filename){
		$cache_dir  = CMS_ROOT.'/'.APPLICATION.'/caches/templates/';
		!is_writable( $cache_dir) && exit('Caches folder is not writable!');		
		$cache_file = $cache_dir.$filename.".php";
		$tpl_file   = CMS_ROOT.'/'.APPLICATION.'/templates/default/'."$filename.html";
		if(!file_exists($tpl_file)){
			file_exists($cache_file) && @unlink($cache_file);
			//如果没有模板文件则创建
			$str=
			'<html>
		<body>
		模板文件生成好了赶快来吧你的HTML放进来吧^_^ !
		</body>
		</html>';
			file_put_contents($tpl_file,str_replace("\t",'',$str));
		}
		if(!file_exists($cache_file) or filemtime($cache_file) < filemtime($tpl_file) or filesize($cache_file) < filesize($tpl_file)){
			$cache_content = file_get_contents($tpl_file);
			$cache_content = $this->template_parse($cache_content);
			file_put_contents( $cache_file,$cache_content);
			@chmod($cache_file,0777);
		}
		return $cache_file;
	}

	//解析模版内容,返回字符串
	function template_parse( $str ) {
		//清除注释
		$str = preg_replace('/<!--.+?-->/s','',$str);
		//模版
		$str = preg_replace ( "/\{template\s+(.+)\}/", "<?php include template(\\1); ?>", $str );
		//包含
		$str = preg_replace ( "/\{include\s+(.+)\}/", "<?php include \\1; ?>", $str );
		//php语句
		/*
		 $str = preg_replace ( "/\{php\s+(.+)\}/", "<?php \\1?>", $str );
		 */
		$str = preg_replace ( "/\{php\s+([^\}]+?)\}/is", "<?php \\1?>", $str );
		//条件判断
		$str = preg_replace ( "/\{if\s+(.+?)\}/", "<?php if(\\1) { ?>", $str );
		$str = preg_replace ( "/\{else\}/", "<?php } else { ?>", $str );
		$str = preg_replace ( "/\{elseif\s+(.+?)\}/", "<?php } elseif (\\1) { ?>", $str );
		$str = preg_replace ( "/\{else if\s+(.+?)\}/", "<?php } elseif (\\1) { ?>", $str );
		$str = preg_replace ( "/\{\/if\}/", "<?php } ?>", $str );
		//for 循环
		$str = preg_replace("/\{for\s+(.+?)\}/","<?php for(\\1) { ?>",$str);
		$str = preg_replace("/\{\/for\}/","<?php } ?>",$str);
		$str = preg_replace("/\{\+\+(.+?)\}/","<?php ++\\1; ?>",$str);
		$str = preg_replace("/\{\-\-(.+?)\}/","<?php ++\\1; ?>",$str);
		$str = preg_replace("/\{(.+?)\+\+\}/","<?php \\1++; ?>",$str);
		$str = preg_replace("/\{(.+?)\-\-\}/","<?php \\1--; ?>",$str);
		$str = preg_replace ( "/\{loop\s+(\S+)\s+(\S+)\}/", "<?php \$n=1;if(is_array(\\1)) foreach(\\1 AS \\2) { ?>", $str );
		$str = preg_replace ( "/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/", "<?php \$n=1; if(is_array(\\1)) foreach(\\1 AS \\2 => \\3) { ?>", $str );
		$str = preg_replace ( "/\{\/loop\}/", "<?php \$n++;}unset(\$n); ?>", $str );
		//显示
		//常量
		$str = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/s","<?php echo \\1;?>", $str );
		$str = preg_replace ( "/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $str );
		//类常量
		$str = preg_replace ( "/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $str );
		//变量
		$str = preg_replace ( "/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1;?>", $str );
		//数组
		$str = preg_replace_callback("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/",
				function($matches){
					return  $this->addquote('<?php echo '.$matches[1].';?>');
				}
				,$str);
		/**$str = preg_replace("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/es", "\$this->addquote('<?php echo \\1;?>')",$str);*/
		$str = preg_replace ( "/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>", $str );
		//认证信息
		$str = "<?php defined('WENXIAOCMS') or exit('Access denied!'); ?>\n" . $str;
		return $str;
	}

	function addquote($var) {
		return str_replace ( "\\\"", "\"", preg_replace ( "/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var ) );
	}
}