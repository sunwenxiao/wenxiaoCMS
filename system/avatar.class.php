<?php
defined('WENXIAOCMS') or exit('Access denied!');
class avatar {
	var $avatar_dir;
	var $avatar_url;
	
	//构造函数
	function __construct($app = APPLICATION){
		//头像上传相关路径
		$this->avatar_dir = $app.'/upload/avatar/';//avatar保存地址		
	}
	
	//返回url
	function uploadavatar($uid,$filename = 'Filedata') {
		@header ( "Expires: 0" );
		@header ( "Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE );
		@header ( "Pragma: no-cache" );
		if (empty ( $uid )) return - 1;
		if (empty ( $_FILES [$filename] ))	return - 3;
		//路径设置等
		$avatardir = $this->avatar_dir;
		$tempdir   = $avatardir . 'tmp/';
		!is_dir($avatardir) && mkdir ($avatardir, 0777 );
		!is_dir($tempdir) 	&& mkdir ( $tempdir, 0777 );
		//图片类型检查
		list ( $width, $height, $type, $attr ) = getimagesize ( $_FILES [$filename] ['tmp_name'] );
		$imgtype  = array (1 => '.gif', 2 => '.jpg', 3 => '.png' );
		$filetype = $imgtype [$type];
		if (! $filetype) $filetype = '.jpg';

		//临时文件路径
		if($filename == 'Filedata') {
			$tmpavatar = $tempdir.'upload' . $uid . $filetype;
		}else{
			$tmpavatar = $this->get_home($uid).'/'.substr ( $uid, -2).'_avatar_big.png';
		}
		//上传
		file_exists ( $tmpavatar ) && @unlink ( $tmpavatar );
		if (@copy ( $_FILES [$filename] ['tmp_name'], $tmpavatar ) || @move_uploaded_file ( $_FILES [$filename] ['tmp_name'], $tmpavatar )) {
			@unlink ( $_FILES [$filename] ['tmp_name'] );
			list ( $width, $height, $type, $attr ) = getimagesize ( $tmpavatar );
			if ($width < 10 || $height < 10 || $type == 4) {
				@unlink ( $tmpavatar );
				return - 2;
			}
		} else {
			//删除缓存文件
			@unlink ( $_FILES [$filename] ['tmp_name'] );
			return - 4;
		}
		if($filename == 'Filedata') {
			$avatarurl = SITE_URL.$this->avatar_dir . 'tmp/upload' . $uid . $filetype;
		}else{
			$avatarurl = $this->get_dir($uid,2).'/'.substr ( $uid, -2).'_avatar_big.png';
		}
		return $avatarurl;
	}
	
	//接收数据
	function rectavatar($uid) {
		//准备头部
		@header ( "Expires: 0" );
		@header ( "Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE );
		@header ( "Pragma: no-cache" );
		header  ( "Content-type: application/xml; charset=gbk" );
		if (empty ( $uid )) {
			return '<root><message type="error" value="-1" /></root>';
		}
		//初始化
		
		$avatardir = $this->avatar_dir;
		$tempdir   = $avatardir . 'tmp/';

		//路径初始化
		$this->set_home ( $uid );
		$puid             = sprintf ( "%09d", $uid );
		$puid             = substr ( $puid, - 2 );
		$avatartype 	  = getg ( 'avatartype' ) == 'real' ? 'real' : 'virtual';
		$bigavatarfile    = $this->get_avatar ( $uid, 'big', $avatartype );
		$middleavatarfile = $this->get_avatar ( $uid, 'middle', $avatartype );
		$smallavatarfile  = $this->get_avatar ( $uid, 'small', $avatartype );
		$bigavatar        = $this->flashdata_decode ( getp ( 'avatar1') );
		$middleavatar     = $this->flashdata_decode ( getp ( 'avatar2') );
		$smallavatar      = $this->flashdata_decode ( getp ( 'avatar3') );
		if (! $bigavatar || ! $middleavatar || ! $smallavatar) {
			return '<root><message type="error" value="-2" /></root>';
		}
		$success = 1;
		$fp  = fopen ( $bigavatarfile, 'wb' );
		fwrite ( $fp, $bigavatar );
		fclose ( $fp );
		$fp  = fopen ( $middleavatarfile, 'wb' );
		fwrite ( $fp, $middleavatar );
		fclose ( $fp );
		$fp  = fopen ( $smallavatarfile, 'wb' );
		fwrite ( $fp, $smallavatar );
		fclose ( $fp );
		$biginfo    = @getimagesize ( $bigavatarfile );
		$middleinfo = @getimagesize ( $middleavatarfile );
		$smallinfo  = @getimagesize ( $smallavatarfile );
		if (! $biginfo || ! $middleinfo || ! $smallinfo || $biginfo [2] == 4 || $middleinfo [2] == 4 || $smallinfo [2] == 4) {
			file_exists ( $bigavatarfile )    && unlink ( $bigavatarfile );
			file_exists ( $middleavatarfile ) && unlink ( $middleavatarfile );
			file_exists ( $smallavatarfile )  && unlink ( $smallavatarfile );
			$success = 0;
		}
		//删除
		@unlink ( $tempdir.'/upload'. $uid .'.jpg');
		if ($success) {
			return '<?xml version="1.0" ?><root>  <face success="1"/></root>';
		} else {
			return '<?xml version="1.0" ?><root>  <face success="0"/></root>';
		}
	}
	
	function flashdata_decode($s) {
		$r = '';
		$l = strlen ( $s );
		for($i = 0; $i < $l; $i = $i + 2) {
			$k1 = ord ( $s [$i] ) - 48;
			$k1 -= $k1 > 9 ? 7 : 0;
			$k2 = ord ( $s [$i + 1] ) - 48;
			$k2 -= $k2 > 9 ? 7 : 0;
			$r .= chr ( $k1 << 4 | $k2 );
		}
		return $r;
	}

	//得到绝对路径
	function get_home($uid) {
		return $this->avatar_dir.$this->get_dir($uid,2);
	}
	
	//设置绝对路径
	function set_home($uid, $dir = '') {
		$dir4 = $this->avatar_dir .$this->get_dir($uid,2);
		if(is_dir($dir4 ))return;
		$dir  = $dir == ''? $this->avatar_dir :$dir;
		$dirs = $this->get_dir($uid);
		$dir1 = $dir .'/'.$dirs[0];
		$dir2 = $dir1.'/'.$dirs[1];
		$dir3 = $dir2.'/'.$dirs[2];
		! is_dir ( $dir1 ) && mkdir ( $dir1, 0777 );
		! is_dir ( $dir2 ) && mkdir ( $dir2, 0777 );
		! is_dir ( $dir3 ) && mkdir ( $dir3, 0777 );
		return $dir3;
	}
	
	//获取头像文件,返回相对路径
	function get_avatar($uid, $size = 'big', $type = 'jpg') {
		$size 	 = in_array ( $size, array ('big', 'middle', 'small' ) ) ? $size : 'big';
		$type  	 = in_array ( $type, array ('jpg', 'png')) ? $type : 'jpg';
		$dir  	 = $this->get_dir(abs(intval($uid)),2).'/'. substr ( $uid, - 2 );
		$fpath 	 = $dir."_avatar_$size.$type";
		return   $this->avatar_dir.$fpath;
	}
	
	//获得dir,返回相对路径
	function get_dir($uid , $t = 1){
		$dir  	= array();
		$uid  	= sprintf ( "%09d", $uid );
		$dir[0] = substr ( $uid, 0, 3 );
		$dir[1] = substr ( $uid, 3, 2 );
		$dir[2] = substr ( $uid, 5, 2 );
		return $t==1 ? $dir : implode('/',$dir);
	}
}
