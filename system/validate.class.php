<?php
defined('WENXIAOCMS') or exit('Access denied!');
/**
类说明:
---------外部使用-------------
输入检查类,使用示例
$param = array('user_name','password','company','mobile',
'disabled','qq','add_time','salt','province','city','address');	
//字段验证
$check = validate::check($param,
   array(
	  'user_name'=>'required|alpha_numeric|range_length(3,20)|exist("bk_channal_member","user_name")',
	  'password'=>'required',
	  'confirm_password'=>'required|compare_password("password")',
	  'mobile'=>'valid_mobile',
	  'qq'=>'valid_qq',
	  'disabled'=>'required'
  )
);
----------规则---------------
required			 :必填项
min_length(2)		 :最小长度
max_length(10)		 :最大长度
exact_length(5)		 :精确长度
range_length(5,10)	 :长度范围
greater_than(5)		 :数值大于
less_than(5)		 :数值小于
between(5,10)		 :数值区间
alpha				 :只有字母
alpha_numeric		 :只字母和数字
alpha_dash			 :只字母/数字/下划线/破折号
numeric				 :只数字
integer				 :只整数(正负)
positive_integer	 :只正整数
decimal(2)			 :只2位小数
valid_base64		 :只允许base64
valid_email			 :email格式
valid_phone			 :电话格式
valid_mobile		 :手机格式
valid_400			 :400电话
valid_url			 :是否url地址,http开头
valid_emails		 :多个email地址检查
max_words			 :最大单词数
domain				 :主机地址
enum('1,2,3')		 :枚举或等于[1,2,3]
valid_qq			 :qq格式
valid_username		 :用户名是否合法只能以字母开头,长度在3-20之间
time				 :只时间
not_equal('1,2,3')	 :值不等于
compare_password('k'):检查两次输入密码是否一致
has_phone			 :是否含电话号码
regex				 :正则
valid_ip			 :只ip
_valid_ipv4			 :是否为ip4
_valid_ipv6			 :是否为ip6
hanzi				 :只汉字
identity_card		 :是否身份证
idcard_verify_number :内部函数
idcard_15to18		 :18位身份证校验码有效性检查
idcard_checksum18	 :是否为19位身份证
is_mainland_card	 :大陆身份证
is_hong_kong_card	 :香港身份证
is_taiwan_card		 :台湾
is_macao_card		 :澳门
is_passport			 :护照
exist('tbl','field') :数据是否存在
*/
class validate{
	static $msg = array();
	static $k;
	static $methods;
	public static function check($param = '', $check = '' ){		
		if($param == '' or $check == '') return array();
		self::$methods = get_class_methods('validate');		
		foreach ( $check as $index => $rul){
			self::$k = $index;
			$value = isset($param[$index]) ? $param[$index] : getp($index);
			$rules = explode('|',$rul);
			foreach($rules as $rule){
				$rule = trim($rule);
				if($rule !== '' && self::rule_exist($rule) && ( preg_match('/^required.*?/',$rul) or $value!=='') ){
					if(strpos($rule,'(') > 0){									
						$func = preg_replace('/\(([^\)]*?)\)/','($value,\\1)',$rule);
						$do = 'return self::'.$func.';';
						if(!eval($do)) break;
					}else{
						if(!self::$rule($value)) break;		
					}
				}
			}
		}
		if(count(self::$msg) ==  0) return array();
		return self::$msg;
	}
	
	//规则是否存在
	public static function rule_exist($rule){
		$rule = preg_replace('/\([^\)]*?\)/','',$rule);
		if(in_array($rule,self::$methods))return TRUE;
		return self::record('规则'.$rule.'不存在,请检查!');
	}
	
	//错误记录
	public static function record($error){
		if(isset(self::$msg[self::$k])){
			self::$msg[self::$k] .= ' AND '. $error;	
		}else{
			self::$msg[self::$k]=$error;
		}
		return FALSE;
	}
	
	//必填项
	public static function required($value){
		if((is_array($value) && count($value) > 0) or strlen(trim($value)) > 0)return TRUE;
		return self::record('字段为必填项,请检查!');
	}
	
	//最小长度
	public static function min_length($str,$val){
		$str = trim( $str );
		if (preg_match("/[^0-9]/", $val))return self::record('长度不是数字,请检查!');
		if (function_exists('mb_strlen')){
			return (mb_strlen($str,'utf-8') < $val) ? self::record('字符串长度不应该小于'.$val.',请检查!'): TRUE;
		}
		return (strlen($str) < $val) ? self::record('字符串长度不应该小于'.$val.',请检查!'): TRUE;
	}
	
	//最大长度
	public static function max_length($str, $val){
		$str = trim( $str );
		if (preg_match("/[^0-9]/", $val))return self::record('长度不是数字,请检查!');
		if (function_exists('mb_strlen')){
			return (mb_strlen($str,'utf-8') > $val) ? self::record('字符串长度不应该大于'.$val.',请检查!'): TRUE;
		}
		return (strlen($str) > $val) ? self::record('字符串长度不应该大于'.$val.',请检查!'): TRUE;
	}
	
	//精确长度
	public static function exact_length($str, $val){
		$str = trim( $str );
		if (preg_match("/[^0-9]/", $val))return self::record('长度不是数字,请检查!');
		if (function_exists('mb_strlen')){
			return (mb_strlen($str,'utf-8') != $val) ? self::record('字符串长度应该等于'.$val.',请检查!'): TRUE;
		}
		return (strlen($str) != $val) ? self::record('字符串长度应该等于'.$val.',请检查!'): TRUE;
	}
	
	//长度在指定之间
	public static function range_length($value,$min,$max){
		if( self::min_length($value,$min) && self::max_length($value,$max))
		return TRUE;
		return self::record('字符串长度应在'.$min.'-'.$max.'之间,请检查!');
	}
	
	//是否比指定值大
	public static function greater_than($str, $min){
		if ( ! is_numeric($str))return self::record('比较输入错误,请检查!');
		if($str > $min)return TRUE;
		return self::record('字段值应该大于'.$min.',请检查!');
	}
	
	//是否比指定值小
	public static function less_than($str, $max){
		if ( ! is_numeric($str))return self::record('比较输入错误,请检查!');
		if($str < $max)return TRUE;
		return self::record('字段值应该小于'.$max.',请检查!');
	}
	
	//数值大小在指定值之间
	public static function between($value,$min,$max){
		if ( ! is_numeric($value))return self::record('比较输入错误,请检查!');
		if (self::greater_than($value,$min) && self::less_than($value,$max))return TRUE;
		return self::record('字段值应该在'.$min.'-'.$max.'之间,请检查!');
	}
	
	//如果表单元素值中包含除字母以外的其他字符，则返回FALSE
	public static function alpha($str)	{
		return ( ! preg_match("/^([a-z])+$/i", $str)) ? self::record('字段只允许字母,请检查!'): TRUE;
	}
	
	//如果表单元素值中包含除字母和数字以外的其他字符，则返回FALSE
	public static function alpha_numeric($str)	{
		return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? self::record('字段只允许字母或数字,请检查!') : TRUE;
	}
	
	//如果表单元素值中包含除字母/数字/下划线/破折号以外的其他字符，则返回FALSE
	public static function alpha_dash($str){
		return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? self::record('字段只允许字母/数字/下划线/破折号,请检查!') : TRUE;
	}
	
	//如果表单元素值中包含除数字以外的字符，则返回 FALSE
	public static function numeric($str){
		if(preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str))return TRUE;
		return self::record('字段指只允许数字,请检查!');
	}
	
	//表单元素中包含除整数以外的字符，则返回FALSE
	public static function integer($str){
		if(preg_match('/^[\-+]?[0-9]+$/', $str))return TRUE;
		return self::record('字段只允许整数,请检查!');
	}
	
	//表单元素中包含除整数以外的字符，则返回FALSE
	public static function positive_integer($str){
		if(is_numeric($str) && intval($str) > 0 )return TRUE;
		return self::record('字段只允许正整数,请检查!');
	}
	
	//如果表单元素中输入（非小数）不完整的值，则返回FALSE
	public static function decimal($str,$unit = 1){
		if(preg_match('/^[\-+]?[0-9]+\.[0-9]{0,'.$unit.'}$/', $str))return TRUE;
		return self::record('字段要求'.$unit.'位以内小数,请检查!');
	}
	
	//如果表单元素的值包含除了base64 编码字符之外的其他字符，则返回FALSE。
	public static function valid_base64($str){
		if(preg_match('/[^a-zA-Z0-9\/\+=]/', $str))return TRUE;
		return self::record('字段只允许base64 编码字符,请检查!');
	}
	
	//是否为email
	public static function valid_email($str){
		if(preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str))
		return TRUE;
		return self::record('Email格式不正确,请检查!');
	}
	
	//电话
	public static function valid_phone($value){
		$rt=preg_match("/^0[0-9]{2,3}-[0-9]{7,8}(-[0-9]{1,6}){0,1}$/",$value);//座机号
		if($rt)return true;
		return self::record('电话格式不正确,请检查!');
	}
	
	//手机
	public static function valid_mobile($value){
		$rt=preg_match("/^1[3458][0-9]{9}$/",$value);//手机
		if($rt)return true;
		return self::record('手机格式不正确,请检查!');
	}
	
	//400电话
	public static function valid_400(){
		$rt=preg_match("/^[48]00-[0-9]{3}-[0-9]{4}$/",$value);//400 800
		if($rt)return true;
		return self::record('400/800电话格式不正确,请检查!');
	}
	
	//是否为url地址
	public static function valid_url($value){
	if(preg_match("/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i",$value)) return TRUE;
		return self::record('URL地址格式不正确,请检查!');
	}
	
	//如果表单元素值中任何一个值包含不合法的email地址（地址之间用英文逗号分割），则返回FALSE。
	public static function valid_emails($str){
		if (strpos($str, ',') === FALSE){
			return $this->valid_email(trim($str));
		}
		foreach (explode(',', $str) as $email){
			if (trim($email) != '' && $this->valid_email(trim($email)) === FALSE){
				return self::record('URL地址格式不正确,请检查!');
			}
		}
		return TRUE;
	}
	
	//单词数目数
	public static function max_words($value,$maxWord){
		return count(explode(',',$value))<=$maxWord;
	}
	
	//主机
	public static function domain($value){
		return self::url("http://".$value);
	}
	
	//枚举[1,2,3]
	public static function enum($value,$enum){
		$array = explode(',',$enum);
    	if (in_array($value,$array)) return TRUE;
		return self::record('字段值不在'.$enum.'范围内,请检查!');
  	}
	
	//QQ
	public static function valid_qq($value){
		$rt=preg_match("/^\d+\d{4,10}$/",$value);//座机号
		if($rt)return true;
		return self::record('QQ格式不正确,请检查!');
	}
	
	//用户名
	public static function valid_username($value){
		if(preg_match('/^[A-Za-z][_A-Za-z0-9]+$/',$value) && strlen($value) >3 && strlen($value) < 20)
		return TRUE;
		return self::record('只能以字母开头,长度在3-20之间,请检查!');
	}
	
	//是否是时间
	public static function time($value){
		$tmp=explode(":",$value);
		if(count($tmp)==3 && self::range($tmp[0],array(0,23)) && self::range($tmp[1],array(0,59)) && self::range($tmp[2],array(0,59)))return TRUE;
		return self::record('时间格式不正确,请检查!');
	}
		
	//值不能等于
	public static function not_equal($value,$notValues){
		$values=explode("|",$notValues);
		foreach($values as &$v){
			if(trim($v)==''){
				unset($v);
			}
		}
		return !in_array($value,$values);
	}
	
	//检查两次输入密码是否一致
	public static function compare_password($value,$str){
		$pwd2 = getp($str);
		if($value == $pwd2){
			return TRUE;
		}
		return self::record('两次密码输入不一致!');
	}
	
	//值不能含有电话号码
	public static function has_phone($value){
		$rt=preg_match("/^.*0[0-9]{2,3}-[0-9]{7,8}(-[0-9]{1,6}){0,1}.*$/",$value);//座机号
		if($rt)return self::record('不允许存在座机号,请检查!');
		$rt=preg_match("/^.*1[3458][0-9]{9}.*$/",$value);//手机
		if($rt)return self::record('不允许存在手机号码,请检查!');
		$rt=preg_match("/^.*[48]00-[0-9]{3}-[0-9]{4}.*$/",$value);//400 800
		if($rt)return self::record('不允许存在400/800电话,请检查!');
		return TRUE;   
	}
	
	//正则
	public static function regex($value,$regex){
		return (bool) preg_match($regex, $value);
	}
	
	//检查ip地址
	public static function valid_ip($ip, $which = ''){
		$which = strtolower($which);
		if (is_callable('filter_var')){
			switch ($which) {
				case 'ipv4':
					$flag = FILTER_FLAG_IPV4;
					break;
				case 'ipv6':
					$flag = FILTER_FLAG_IPV6;
					break;
				default:
					$flag = '';
					break;
			}
			if(filter_var($ip, FILTER_VALIDATE_IP, $flag)) return TRUE;
			return self::record('Ip格式不正确,请检查!');
			
		}
		if ($which !== 'ipv6' && $which !== 'ipv4'){
			if (strpos($ip, ':') !== FALSE){
				$which = 'ipv6';
			}elseif (strpos($ip, '.') !== FALSE)	{
				$which = 'ipv4';
			}else{
				return self::record('Ip格式不正确,请检查!');
			}
		}
		$func = '_valid_'.$which;
		if(self::$func($ip)) return TRUE;
		return self::record('IP格式不正确,请检查!');
	}
	
	//ipv4
	public static function _valid_ipv4($value){
		if (preg_match('/^([01]{8}.){3}[01]{8}$/i', $value)) {
            $value = bindec(substr($value, 0, 8)) . '.' . bindec(substr($value, 9, 8)) . '.'
                   . bindec(substr($value, 18, 8)) . '.' . bindec(substr($value, 27, 8));
        } elseif (preg_match('/^([0-9]{3}.){3}[0-9]{3}$/i', $value)) {
            $value = (int) substr($value, 0, 3) . '.' . (int) substr($value, 4, 3) . '.'
                   . (int) substr($value, 8, 3) . '.' . (int) substr($value, 12, 3);
        } elseif (preg_match('/^([0-9a-f]{2}.){3}[0-9a-f]{2}$/i', $value)) {
            $value = hexdec(substr($value, 0, 2)) . '.' . hexdec(substr($value, 3, 2)) . '.'
                   . hexdec(substr($value, 6, 2)) . '.' . hexdec(substr($value, 9, 2));
        }
        $ip2long = ip2long($value);
        if ($ip2long === false) {
            return false;
        }
        return ($value == long2ip($ip2long));
	}
	
	//ipv6检查
	public static function _valid_ipv6($value){
		if (strlen($value) < 3) {
            return $value == '::';
        }
        if (strpos($value, '.')) {
            $lastcolon = strrpos($value, ':');
            if (!($lastcolon && self::_valid_ipv4(substr($value, $lastcolon + 1)))) {
                return false;
            }
            $value = substr($value, 0, $lastcolon) . ':0:0';
        }
        if (strpos($value, '::') === false) {
            return preg_match('/\A(?:[a-f0-9]{1,4}:){7}[a-f0-9]{1,4}\z/i', $value);
        }
        $colonCount = substr_count($value, ':');
        if ($colonCount < 8) {
            return preg_match('/\A(?::|(?:[a-f0-9]{1,4}:)+):(?:(?:[a-f0-9]{1,4}:)*[a-f0-9]{1,4})?\z/i', $value);
        }
        if ($colonCount == 8) {
            return preg_match('/\A(?:::)?(?:[a-f0-9]{1,4}:){6}[a-f0-9]{1,4}(?:::)?\z/i', $value);
        }
        return false;
	}
	
	//只能输入汉字检查
	public static function hanzi($value){
		if(preg_match("/^[\x7f-\xff]+$/",$value)){
        	return TRUE;
		}
		return self::record('只能输入汉字!');
	}
	//----------------------------------------------------------
	//身份证检查
	//----------------------------------------------------------
	public static function identity_card($value){
		if(self::is_mainland_card($value) or self::is_hong_kong_card($value) or self::is_taiwan_card($value) or self::is_macao_card($value) or self::is_passport($value)){
			return TRUE;
		}else{
			return self::record('身份证格式不正确,请检查!');
		}
	}
	
	// 计算身份证校验码，根据国家标准GB 11643-1999 
	private static function idcard_verify_number($idcard_base){ 
		if(strlen($idcard_base) != 17){
			return false; 
		} 
		//加权因子 
		$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); 
		//校验码对应值 
		$verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); 
		$checksum = 0; 
		for ($i = 0; $i < strlen($idcard_base); $i++){ 
			$checksum += substr($idcard_base, $i, 1) * $factor[$i]; 
		} 
		$mod = $checksum % 11; 
		$verify_number = $verify_number_list[$mod]; 
		return $verify_number; 
	}
	// 将15位身份证升级到18位 
	private static function idcard_15to18($idcard){ 
		if (strlen($idcard) != 15){ 
			return false; 
		}else{
			// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码 
			if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false){ 
				$idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9); 
			}else{ 
				$idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9); 
			} 
		} 
		$idcard = $idcard . self::idcard_verify_number($idcard); 
		return $idcard; 
	}
	// 18位身份证校验码有效性检查 
	function idcard_checksum18($idcard){ 
		if (strlen($idcard) != 18){ return false; }
		$idcard_base = substr($idcard, 0, 17); 
		if (self::idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))){ 
			return false; 
		}else{ 
			return $idcard; 
		} 
	}
	/**
     * 是否中国大陆身份证号
     * @param string $card_no 身份证号
     * @return bool
     */
    public static function is_mainland_card($card_no){
        $card_no = strlen($card_no) == 15 ? self::idcard_15to18($card_no) : $card_no;
        return self::idcard_checksum18($card_no);
    }
	/**
     * 是否中国香港身份证号
     * @param string $card_no 身份证号
     * @return bool
     */
    public static function is_hong_kong_card($card_no){
        $card_no = str_replace(array('（', '）'), array('(', ')'), $card_no);
        $pattern = '/^[a-z]\d{2,7}\([\da]\)$/i';
        $match = preg_match($pattern, $card_no);
        if($match){
            $alpha = strtolower($card_no[0]);
            $first = ord($alpha) - 96;
            $sum = $first*8 + $card_no[1]*7 + $card_no[2]*6 + $card_no[3]*5 + $card_no[4]*4 + $card_no[5]*3 + $card_no[6]*2;
            if(intval($card_no[8]) == 11 - $sum % 11){
                return true;
            }
        }
        return false;
    }
    /**
     * 是否中国台湾身份证号
     * @param string $card_no 身份证号
     * @return bool
     */
    public function is_taiwan_card($card_no){
        $pattern = '/^[a-z]\d{9}$/i';
        $match = preg_match($pattern, $card_no);
        if($match){
            $sum = $card_no[1]*8 + $card_no[2]*7 + $card_no[3]*6 + $card_no[4]*5 + $card_no[5]*4 + $card_no[6]*3 + $card_no[7]*2 + $card_no[8]*1 + $card_no[9];
            if(intval($card_no[9]) == $sum % 10){
                return true;
            }
        }
        return false;
    }
    /**
     * 是否中国澳门身份证号
     * @param string $card_no 身份证号
     * @return bool
     */
    public function is_macao_card($card_no){
        $card_no = str_replace(array('（', '）'), array('(', ')'), $card_no);
        $pattern = '/^(?:1|5|7)\d{7}\(\d\)$/i';
        $match = preg_match($pattern, $card_no);
        return $match ? true : false;
    }
    /**
     * 是否护照号
     * @param string $card_no 身份证号
     * @return bool
     */
    public function is_passport($card_no){
        if(strlen($card_no) == 9){
            return true;
        }
        return false;
    }
	 /**
     * 是否存在
     */
    public function exist($value,$table,$field){
        if($table == '' or $field == '') return self::record('配置参数错误!');
		$db = new mysql();
		$r = $db->get_exist($table,"`$field` = '$value'");
		if(!$r) return TRUE;
		return self::record('字段值已存在!');
    }
    
}