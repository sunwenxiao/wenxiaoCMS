<?php
defined('WENXIAOCMS') or exit('Access denied!');
class indexcontroller extends controller {
    protected $admin = array();
    function __construct(){
        parent::__construct();
        //初始化前台头信息和导航条信息
        //初始化头信息
        $wenxiaocms_header = M('wenxiaocms_header')->get_all();
        foreach($wenxiaocms_header as $header)
        {
            $this->assign($header['key'],$header['value']);
        }
        //初始化导航条信息
        $topbar_father = M('wenxiaocms_topbar')->get_topbar_father();
        $this->assign('topbar_father',$topbar_father);
      
    }
    protected static $mysql;
    protected static function __getDBInstance($conf){
    	$mysql = new mysqli($conf['server_ip'],$conf['server_mysql_user'],$conf['server_mysql_pass'],$conf['server_mysql_db'],$conf['mysql_port']);
    	self::$mysql = $mysql ;
    	return self::$mysql;
    	 
    }

	/* !CodeTemplates.overridecomment.nonjd!
	 * @see controller::on_index()
	 */
	public function on_index() {
		// TODO 自动生成的方法存根
		
	}

	
	
}

?>
