<?php
defined('WENXIAOCMS') or exit('Access denied!');
header('Content-type:text/html;charset=utf-8');
define('LIBS_PATH_UP',dirname(__FILE__));
//上传类
//如果被外界得知上传地址如何??
class upload{
	var $uptypes       = array('image/jpeg'=>'.jpg','image/pjpeg'=>'.jpg','image/png'=>'.png',
						'image/x-png'=>'.png','image/gif'=>'.gif','image/wbmp'=>'.bmp');
	var $max_file_size = 3000000;		//3M
	var $upfolder      = '/upload';	//相对于根目录的路径
	var $max_w         = 1000;
	var $max_h         = 800;
	var $uped_folder   = NULL;
	var $app;

	//构造函数
	function __construct($app = APPLICATION){
		//上传应用
		$this->app = $app;
	}

	//上传动作
	function do_upload( $upname = 'upload'){
		$this->upfile	=	$_FILES[$upname];
		$this->set_home();
		$this->file_check();
		$this->file_upload();
	}

	//存储文件夹设置,以年/月建立文件夹
	function set_home(){
		$dir  = $this->app.$this->upfolder;
		$dir1 = $dir.'/'.date("Y");
		$dir2 = $dir1.'/'.date("m");
		$dir3 = $dir2.'/'.date("d");
		! is_dir ( $dir1 ) && mkdir ( $dir1 , 0777) && build_dir_secure($dir1);
		! is_dir ( $dir2 ) && mkdir ( $dir2 , 0777) && build_dir_secure($dir2);
		! is_dir ( $dir3 ) && mkdir ( $dir3 , 0777) && build_dir_secure($dir3);
		$this->upfolder = $dir3.'/';
		$this->uped_folder = $dir1 . '/' . $dir2.'/';
	}

	//文件上传检查
	function file_check(){
		$this->upfile['name'] == '' && $this->message('无文件!');
		!in_array( $this->upfile["type"], array_keys($this->uptypes)) && $this->message('只能上传图片文件:.png.jpg.gif');
		$this->max_file_size < $this->upfile["size"] && $this->message('文件太大,应小于3M！');
		//许可类型检查
		if(isset($_GET['type'])){
			if(in_array('.'.$_GET['type'], array_values($this->uptypes))){
				$suffix  = $this->uptypes[$this->upfile["type"]];
				$suffix !='.'.$_GET['type'] && $this->message('请上传'.$_GET['type'].'文件！');
			}else{
				$this->message('type 参数不正确!');
			}
		}
		global $size;
		$size = getimagesize($this->upfile["tmp_name"]);

		//图片精确尺寸控制,强制上传或自动处理
		if(isset($_GET['width'])  && $size[0] != intval($_GET['width'])){$wmsg  = intval($_GET['width']); }else{$wmsg  = '不限';}
		if(isset($_GET['height']) && $size[1] != intval($_GET['height'])){$hmsg = intval($_GET['height']);}else{$hmsg  = '不限';}
		if($wmsg!= '不限'  or $hmsg != '不限')
		$this->message('图片尺寸应为:长*高'.$wmsg.'*'.$hmsg.',请检查!');

		//图片尺寸限制,按原比例自动生成缩略图,如果小则不变
		if(isset($_GET['maxwidth'])) $this->max_w = intval($_GET['maxwidth']);
		if(isset($_GET['maxheight']))$this->max_h = intval($_GET['maxheight']);
	}
	//上传文件
	function file_upload($upname = ''){
		global $size;
		//防止重命名
		do{
			$upname = $upname=='' ? mktime().rand() : $upname;
			$upname = $upname.$this->uptypes[$this->upfile["type"]];
			$filename = $this->upfolder.$upname;
		} while(is_file($filename));
		//文件上传
		if(!move_uploaded_file($this->upfile["tmp_name"],$filename)){
			$this->message('移动文件出错！');
		}else{
			//缩略图处理
			if($size[0] > $this->max_w or $size[1] > $this->max_h){
				include 'image.class.php';
				$image = new image();
				$image->thumb($filename,'',$this->max_w,$this->max_h);
			}
			//回调函数处理
			if(isset($_GET['callback']) && !empty($_GET['callback'])){
				$upname   = $this->upfolder.$upname;
				$callback = $_GET['callback'];
				//调用窗口应该编写js upload_back()函数来获得上传后的文件路径
				echo "<script>alert('上传成功!');parent.{$callback}('$upname')</script>";
			}else{
				$this->message("上传成功!");
			}
			self::show_form();
		}
	}

	//消息函数
	function message($str){
		echo ('<script>alert(\''.$str.'\');</script>');
		self::show_form();
		exit;
	}

	//显示表单
	function show_form(){
		$form=
		<<<EOT
		<html>
		<form method="post" enctype="multipart/form-data">
		<input type="file" id="img_file" name="upload"  style="position:absolute;left:0;top:0;height:35px;width:200px;overflow: hidden;opacity:0"/>
		<input type="submit" id="up_img" name="_dosubmit"  value="上传" />
		</form>
		</body>
		</html>
EOT;
		$form .= <<<SCPT
		<script type="text/javascript" src="system/js/jquery.js"></script>
		<script type="text/javascript">
		$().ready(function(){
		    //选择图片后
		    $("#img_file").change(function(){
				$("#up_img").click();
		    });
		});
	    </script>
SCPT;
		echo $form;
	}
}