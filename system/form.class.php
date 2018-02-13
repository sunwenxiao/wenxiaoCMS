<?php
defined('WENXIAOCMS') or exit('Access denied!');
/**
 * 表单类
 * @author Alex
 *
 */
class form {
	/**
	 * 日期时间控件
	 * 通过id区分,多个必须指定不同的id
	 * {$form::date(array('isdatetime'=>1))}
	 * @param $name 控件name，id
	 * @param $value 选中值
	 * @param $isdatetime 是否显示时间
	 * @param $loadjs 是否重复加载js，防止页面程序加载不规则导致的控件无法显示
	 * @param $showweek 是否显示周，使用，true | false
	 */
	public static function date($option = array()) {
		if(!is_array($option) or empty($option)) return 'Date config error,option needs an array!';
		
		$name 	    = isset( $option['name'] ) 		? $option['name'] 		: 'inputdate';
		$id   	    = isset( $option['id'] ) 		? $option['id'] 		: 'inputdate';
		$value 	    = isset( $option['value'] )		? $option['value'] 		: '';
		$isdatetime = isset( $option['isdatetime'] )? $option['isdatetime']	: 0;
		$loadjs     = isset( $option['loadjs'] )	? $option['loadjs'] 	: 0;
		$showweek   = isset( $option['showweek'] )	? $option['showweek']	: 'true';
		$timesystem = isset( $option['timesystem'] )? $option['timesystem']	: 1;
		$miao = isset( $option['miao'] )? $option['miao']	: '00';
			
		//时间格式
		if($isdatetime) {
			$size = 21;
			$format = '%Y-%m-%d %H:%M:'.$miao;
			$dformat = 'Y-m-d H:m:s';
			if($timesystem){
				$showsTime = 'true';
			} else {
				$showsTime = '12';
			}
		} else {
			$size 		= 10;
			$dformat 	= 'Y-m-d';
			$format = '%Y-%m-%d';
			$showsTime 	= 'false';
		}
		$str = '';

		$value = $value && intval($value)!= 0 ? ((strstr($value, ':') || preg_match( '/^\d-/',$value)) ? $value : date($dformat,strtotime($value))):'';

		if($loadjs or !defined('CALENDAR_INIT')) {
			define('CALENDAR_INIT', 1);
			$str .= '<link rel="stylesheet" type="text/css" href="'.JS_PATH.'/calendar/jscal2.css"/>
			<link rel="stylesheet" type="text/css" href="'.JS_PATH.'/calendar/win2k.css"/>
			<script type="text/javascript" src="'.JS_PATH.'/calendar/calendar.js"></script>
			<script type="text/javascript" src="'.JS_PATH.'/calendar/lang/en.js"></script>';
		}
		
		$str .= '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" size="'.$size.'" class="date form-control" readonly placeholder="请选择时间" >';
		$str .= '<script type="text/javascript">
			Calendar.setup({
			weekNumbers: '.$showweek.',
		    inputField : "'.$id.'",
		    trigger    : "'.$id.'",
		    dateFormat: "'.$format.'",
		    showTime: '.$showsTime.',
		    minuteStep: 1,
		    onSelect   : function() {this.hide();ribian();}
			});
        </script>';
		return $str;
	}

	/**
	 * 下拉选择框
	 * {$form::select('name= \'category_id\' require',$cates,$data['category_id'])}
	 * str:name id以及require等
	 * $current 为当前值
	 * $id 为option 键的数组索引
	 * $title 为值得数组索引
	 * 假如传入一维数组,则key为键,val为值
	 */
	public static function select($str = '', $data = array(), $current = '-1' ,$id ='id',$title ='title' ) {
		if($str == '') return 'Select attr error, it needs a string!';
		if(empty($data)) $data = array();
		$string = '<select '.$str.'><option value=""></option>';		
		foreach($data as $key => $row) {
			if (is_array($row)) {
				$selected = $row[$id] == $current ? 'selected = \'selected\'':'';
				$string .= '<option value="'.$row[$id].'" '.$selected.' >'.$row[$title].'</option>';
			}else{
				$selected = $key == $current ? 'selected = \'selected\'':'';
				$string .= '<option value="'.$key.'" '.$selected.' >'.$row.'</option>';
			}
		}
		$string .= '</select>';
		return $string;
	} 
	
	/**
	 * 
	 * 调用方法:{$form::upload(array('callback'=>1,'type'=>'png','width'=>200,'height'=>200))}
	 * 上传插件类
	 * @param $callback 是否回调,回调需要在父窗口填写 upload_back()函数
	 * @param $type png限制
	 * @param $width ,$height 宽度和高度限制
	 */
	public static function upload($option = array()){
		if(!is_array($option)) return 'Upload config error!';
		$param = '';
		isset($option['callback']) 	&& $param .='&callback='.$option['callback'];
		isset($option['type']) 		&& $param .='&type='.$option['type'];
		isset($option['width']) 	&& $param .='&width='.$option['width'];
		isset($option['height']) 	&& $param .='&height='.$option['height'];
		isset($option['maxheight']) && $param .='&maxheight='.$option['maxheight'];
		isset($option['maxwidth']) 	&& $param .='&maxwidth='.$option['maxwidth'];
		$str    = isset($option['str'])? $option['str']:'' ;
		$string = '<iframe src="?m=api&a=upload'.$param.'" id="upload_iframe" frameborder=0 height=45 width=320 scrolling="no" '.$str.' ></iframe>';		
		return $string;
	}
	/**
	 * 调用方法:{$form::button(array('submit'=>'提交','button'=>'取消'))}
	 * 上传插件类
	 * @param $submit 提交表单值
	 * @param $button 返回表单值
	 */
	public static function button($option = array()){
		if(!is_array($option)) return '配置参数错误!';
		$submit      = isset( $option['submit'])?$option['submit']:'保存';
		$button      = isset( $option['button'])?$option['button']:'返回';
		$position    = isset( $option['pos'])   ?$option['pos']:'left';
		if(isset( $option['back']) ){
			if($option['back'] == -1){
				$back = get_back();
			}else{
				$back = $option['back'];
			}
			$click = 'onclick="window.location.href=\''.$back .'\'"';
		}else{
			$click = 'onclick="parent.dialog_close()"';
		}
		$string = 
				'<div style="margin:10px 0px; text-align:'.$position.'">
				<input type="submit" value="'.$submit.'" name="do_submit" style="width:auto" />
            	<input type="button" value="'.$button.'" '.$click.' style="width:auto" >
				</div>';
		return $string;
	}
	
	//调用方法:{$form::radio($name,array('title'=>'value'),$current)}
	public static function radio($str = '' , $data = array(), $current = '' ){
		if($str == '') return 'Radio attr error, it needs a string!';
		if(!is_array($data) or empty($data)) return 'Data needs an array!';
		$string = '';
		if(!empty($data)){
			foreach($data as $value => $title){
				$checked = $value == $current ? 'checked = "checked"':'';
				$string .= '<label><input '.$str.' type="radio" value="'.$value.'" '.$checked.' style="width:auto;" >'.$title.'</label>';
			}
		}
		return $string;
	}
	
	/**
	 * 复选框
	 * {$form::checkbox('name= \'category_id\' require',$data,$data['checked'])}
	 * str:name id以及require等
	 * $current 为当前值,string as '|a|b|c|'
	 * $id 为option 键的数组索引
	 * $title 为值得数组索引
	 */
	public static function checkbox($str = '', $data = array(), $current = '-1' ,$id ='id',$title ='title' ) {
		if($str == '') return 'Checkbox attr error, it needs a string!';
		if(!is_array($data) or empty($data)) return 'Data needs an array!';
		$current = explode('|',$current);
		$string = '<div><ul>';		
		foreach($data as $key => $row) {
			if (is_array($row)) {
			$selected = in_array($row[$id],$current) ? 'checked = \'checked\'':'';
			$string .='<li>
			<label><input type="checkbox" '.$str.' class="checkbox" value="'.$row[$id].'" '.$selected.' >'.$row[$title].'</label>
			</li>';
			}else{
			$selected = in_array($key,$current) ? 'checked = \'checked\'':'';
			$string .='<li>
			<label><input type="checkbox" '.$str.' class="checkbox" value="'.$key.'" '.$selected.' >'.$row.'</label>
			</li>';
			}
		}
		$string .= '</ul></div>';
		return $string;
	}
	
	/**
	 * 编辑器方法
	 * 调用方法:{$form::editor(array())}
	 * @param $name 编辑器名
	 */
	public static function editor($option = array()){
		if(!is_array($option) ) return 'Editor config error!';
		$name    = 	isset($option['name']) ? $option['name']:'editor_content';
		$id      = 	isset($option['id'])   ? $option['id']:'editor1';
		$type 	 = 	isset($option['type']) && in_array($option['type'],array('basic','full')) ? $option['type']:'basic';
		$height  = 	isset($option['height'])? $option['height']:300;
		$value   = 	isset($option['value']) ? $option['value']:'';
		$string  =	'<textarea id="'.$id.'" name="'.$name.'" style="display:none" ></textarea>
		<script type="text/javascript" src="'.JS_PATH.'/ckeditor/ckeditor.js"></script>
		<script type="text/javascript">
			//<![CDATA[
			 CKEDITOR.replace( \''.$id.'\',{
				 toolbar:\''.$type.'\',
				 height:\''.$height.'px\'
				 }
			 );		
			//]]>
		function upload_back(img){
			CKEDITOR.tools.callFunction(2,img);	
		}';
		$string .="</script>\n";	
		if($value !== ''){
			$string .= '
			<div id="editor_temp_content" style="display:none">'.stripslashes(htmlspecialchars_decode($value)).'</div>
			<script type="text/javascript">
			var oEditor = CKEDITOR.instances.'.$id.';
			oEditor.setData(document.getElementById("editor_temp_content").innerHTML);
			</script>';
		}
		return str_replace("\t",'',$string);
	}
	
	/**
	 * tab标签
	 * 调用方法:{$form::tab($data))}
	 * $data 传入的值,需要$title 为键,$url 为值
	 *  
	 */
	public static function tab($data = array()){
		if( !is_array($data)) return 'Data error, it needs an array as ("title"=>"url")!';
		$string ='<link rel="stylesheet" type="text/css" href="'.JS_PATH.'/tab/tab.css"/>';			
		$string.='<div class="tab_wrapper"><ul>';
		$first  = 'class = \'first\'';
		foreach( $data as $tile => $url){
			$url = $url == ''? '#':$url;
			$selected = str_exists (get_url(),$url)?'class =\'selected\'':'';
			if($first !== '' && $selected !== '') {
				$first = 'class = \'first selected \'';
				$selected = '';
			}
			$string .='<li '.$first.$selected.' ><a href="'.$url.'">'.$tile.'</a></li>';
			$first = '';
		}
		$string .= '</ul></div>';
		return $string;
	}
	/**
	 * 弹出窗,配合js dialog插件
	 * 调用方法:{$form::dialog()},必须的直接用默认值的方式,带参数的传参用"
	 */
	public static function dialog($title = '链接' ,$url = '#',$w = 550,$h =200){
		$string =' <a onclick=dialog({url:\''.$url.'\',title:\''.$title.'\',width:'.$w.',height:'.$h.'}) ';
		$string.=' href="javascript:;">'.$title.'</a>';	
		return $string;
	}
	
	/**
	 * 加载一个弹出框
	 */
	public static function mdialog($mybody = 'mybody'){
		$string ='            <!-- Modal -->
<div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div id='.$mybody.' class="modal-body ">
      </div>
    </div>
  </div>
</div>';
	
		return $string;
	}
	/**
	 * 加载一个信息确认框
	 */
	public static function mdel($id = 'delcfmModel' ,$info = '您确认要删除吗？',$url=''){
		$string =' <!-- 信息删除确认 -->  
<div class="modal fade" id="'.$id.'">  
  <div class="modal-dialog">  
    <div class="modal-content message_align">  
      <div class="modal-header">  
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>  
        <h4 class="modal-title">提示信息</h4>  
      </div>  
      <div class="modal-body">  
        <p>'.$info.'</p>  
        		<input type="hidden" id = "'.$id.'didv"/>
      </div>  
      <div class="modal-footer">  
         <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>  
         <a  onclick="'.$id.'urlSubmit()" class="btn btn-success" data-dismiss="modal">确定</a>  
      </div>  
    </div><!-- /.modal-content -->  
  </div><!-- /.modal-dialog -->  
</div><!-- /.modal -->  ';
		
		$string = $string.' <script type="text/javascript">
				      function '.$id.'(didv) {
				$("#'.$id.'didv").val(didv);//给会话中的隐藏属性URL赋值
          $("#'.$id.'").modal();
      }
				  function '.$id.'urlSubmit(){
          var didv=$.trim($("#'.$id.'didv").val());//获取会话中的隐藏属性URL
        	$.get("'.$url.'"+didv,function(data){
    			if(data=="success"){
    				window.location.reload();
    			}else{
    				alert(data);
    			}
    		})
      }
				  </script>';
		return $string;
	}
	
	/**
	 * 加载一个单向信息确认框
	 */
	public static function onlymdel($id = 'delcfmModel' ,$info = '您确认要删除吗？',$acfunction=''){
	    $string =' <!-- 信息删除确认 -->
<div class="modal fade" id="'.$id.'">
  <div class="modal-dialog">
    <div class="modal-content message_align">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">提示信息</h4>
      </div>
      <div class="modal-body">
        <p>'.$info.'</p>
        		<input type="hidden" id = "'.$id.'didv"/>
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
         <a  onclick="acfunction()" class="btn btn-success" data-dismiss="modal">确定</a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->  ';
	
	    $string = $string.' <script type="text/javascript">
				      function '.$id.'(didv) {
				$("#'.$id.'didv").val(didv);//给会话中的隐藏属性URL赋值
          $("#'.$id.'").modal();
      }
				  function acfunction(){
              '.$acfunction.';
                    }
				  </script>';
	    return $string;
	}
	
	/**
	 * 表格列表
	 * 调用方法:{$form::table()}
	 */
	public static function table($header = array() , $data = array(), $template = array()){
		$string ='
			<table class="listTable" width="100%" cellspacing="0" cellpadding="0" border="0">
				<tr class="repeterHeader">';
		foreach($header as $index => $title){
			$string .= '<td>'.$title.'</td>';
		}
		$string .='</tr>';
		foreach($data as $row ){
			global $rowdata;
			$rowdata = $row ;
			$string .='<tr class="repeterItems">';
			foreach($header as $index => $title){
				if(!isset($template[$index])){
					$string .= '<td>'.$row[$index].'</td>';
				}else{
					$str = preg_replace_callback('/\$row\[(.*?)\]/','self::tbl_parse',$template[$index]);
					$string .= '<td>'.$str.'</td>';
				}
			}
			$string .='</tr>';
		}
		$string .='</table>';
		return $string;
	}
	
	//正则匹配编译函数
	function tbl_parse($matches){
		global $rowdata;
		return $rowdata[$matches[1]];
	}
	
	/**
	 * 头像控件
 	 * 调用方法:{$form::avatar()}
	 * $callback 回调的js函数名
	 * $uid 上传头像的用户uid
	 */
	public static function avatar($uid = '',$callback = ''){
		if(empty($uid)) return 'Avatar uid error!';
		$flashpath = JS_PATH.'/avatar/avatar.swf?input='.$uid.'&appid=1&avatartype=virtual&ucapi=';
		$flashpath .= urlencode(SITE_URL.SCRIPT_NAME);//此处为头像上传接口
		$string = 
		'<object width="520" height="280" align="middle" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">
          <param value="always" name="allowScriptAccess">
          <param value="exactfit" name="scale">
          <param value="transparent" name="wmode">
          <param value="high" name="quality">
          <param value="#ffffff" name="bgcolor">
          <param value="'.$flashpath.'" name="movie">
          <param value="false" name="menu">
          <embed width="520" height="280" align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="transparent" scale="exactfit" allowfullscreen="false" allowscriptaccess="always" bgcolor="#ffffff" quality="high" src="'.$flashpath.'">
        </object>';
		if($callback != ''){
			$string .=	
			'<script type="text/javascript">
				function updateavatar(){
					'.$callback.'('.$uid.');
				}
			</script>';
		}
		return str_replace("\t",'',$string);
	}
	
	//UI组件,调用方法:{$form::load_jquery_ui($type)}
	public static function load_jquery_ui($t = 1){
		$theme = $t == 2 ?'flick':($t == 3 ? 'redmond' : 'smoothness');
		$string ='<link href="'.JS_PATH.'/ui_jquery/jquery-ui-1.9.2.min.css" rel="stylesheet" type="text/css" >
		<link href="'.JS_PATH.'/ui_jquery/'.$theme.'/jquery.ui.theme.css" rel="stylesheet" type="text/css" >
		<script type="text/javascript" src="'.JS_PATH.'/ui_jquery/jquery-ui-1.9.2.min.js"></script>
		<script type="text/javascript" src="'.JS_PATH.'/ui_jquery/jquery-ui.extentd.js"></script>
		';
		return str_replace("\t",'',$string);
	}

}

