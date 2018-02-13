<?php
defined('WENXIAOCMS') or exit('Access denied!');
/**
 * 使用openssl实现非对称加密
 */
class rsa{
	public  $private_key;		//私钥   
	public  $public_key;			//公钥   
	/**
	 * 初始化配置
	 */
	public function __construct(){
		$this->configargs = array(
			//"config" => "F:\wamp\bin\php\php5.3.5\extras\openssl\openssl.cnf",//openssl.cnf文件的路径，这个是必须配置的，其他还有一些参数的设置，不填为默认值，可以从网上找到资料。
			"config" => "/etc/pki/tls/openssl.cnf",
			'digest_alg' => 'sha1', 
			'private_key_type' => OPENSSL_KEYTYPE_RSA, 
			'private_key_bits' => 512
		);
	}
	/**
     * 生成keys
     *
     * @param  array $opensslConfig
     * @return RsaOptions
     * @throws Rsa\Exception\RuntimeException
     */
	public function generate_keys(){
		$r=openssl_pkey_new($this->configargs);
		$result=openssl_pkey_export($r, $privKey,NULL,$this->configargs);
		$this->private_key=$privKey;
		$rp = openssl_pkey_get_details($r);
		$this->public_key = $rp['key'];
	}
	
    /**
     * 用公钥加密
	 * 每次加密的字节数，不能超过密钥的长度值减去11,而每次加密得到的密文长度，却恰恰是密钥的长度。所以，如果要加密较长的数据，可以采用数据截取的方法，分段加密
	 * 默认为50字符长度截取一次
     * @param  string $data
     * @return string
     */
    public function encrypt($data,$public_key,$cut = 50) {
        $encrypted = '';
		$length = strlen($data);
		$count = ceil($length/$cut);
		if($count>1){
			for($i=1;$i<=$count;$i++){
				$start = ($i-1)*$cut;
				$ministr = substr($data,$start,$cut);
				openssl_public_encrypt($ministr, $result, $public_key);
				$encrypted.= base64_encode($result);
				if($i<$count)
				$encrypted.= ':';
			}
		}else{
			openssl_public_encrypt($data, $result, $public_key);
			$encrypted = base64_encode($result);
		}
        return $encrypted;
    }
	/**
     * 用私钥解密
	 * 数据解密首先处理需要解密的字符串，默认已冒号分割，对每一组数据解密后拼接获取最终结果
     * @param  string $data
     * @return string
     */
    public function decrypt($data, $private_key) {
        $decrypted = '';
		$outarr = explode(':',$data);
		foreach($outarr as $key=>$val){
			openssl_private_decrypt(base64_decode($val), $result, $private_key);
			$decrypted .=$result;
		}
        return $decrypted;
    }
	/**
     * 签名
     * @param  string  $data
     * @return string
     */
    public function sign($data, $private_key){
        $signature = '';
        openssl_sign($data, $signature,$private_key);
        return base64_encode($signature);
    }
	/**
     * 签名验证
     * @param  string   $data
     * @return boole
     */
	public function verify($data,$signature, $public_key){
		$ok = openssl_verify($data,base64_decode($signature), $public_key);
		if (1 !== $ok) {
			return FALSE;	
		}else{
			return TRUE;	
		}
	}
}