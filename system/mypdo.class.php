<?php
defined('WENXIAOCMS') or exit('Access denied!');
/**
 * MyPDO
 * @author Jason.Wei <jasonwei06@hotmail.com>
 * @license http://www.sunbloger.com/
 * @version 5.0 utf8
 */
class mypdo
{
    protected static $_instance = null;
    protected $dbName = '';
    protected $dsn;
    protected $dbh;
    protected $dbhost;
    protected $dbUser;
    protected $dbPasswd;
    protected $dbCharset;
    
    /**
     * 构造
     * 
     * @return MyPDO
     */
     function __construct($dbHost='', $dbUser='', $dbPasswd='', $dbName='', $dbCharset='')
    {
    	$this->dbhost = $dbHost ? $dbHost : DBHOST;
    	$this->dbuser = $dbUser ? $dbUser : DBUSER;
    	$this->dbPasswd  = $dbPasswd  ? $dbPasswd  : DBPW;
    	$this->dbName = $dbName   ? $dbName   : DBNAME;
    	$this->dbCharset = $dbCharset   ? $dbCharset : DBCHARSET;
        try {
            $this->dsn = 'mysql:host='.$this->dbhost.';dbname='.$this->dbName;
            if($this->dbh==null)
            {
            	
	            if(class_exists('PDO', false)){
	            	$this->dbh = new PDO($this->dsn, $this->dbuser, $this->dbPasswd,array(PDO::ATTR_PERSISTENT =>false));
	            	$this->dbh->exec('SET character_set_connection='.$this->dbCharset.', character_set_results='.$this->dbCharset.', character_set_client=binary');
	            }
            }
            
        } catch (PDOException $e) {
            $this->outputError($e->getMessage());
        }
    }
    
    /**
     * 防止克隆
     * 
     */
    private function __clone() {}
    
    /**
     * Singleton instance
     * 
     * @return Object
     */
    public static function getInstance($dbHost, $dbUser, $dbPasswd, $dbName, $dbCharset)
    {
        if (self::$_instance === null) {
            self::$_instance = new self($dbHost, $dbUser, $dbPasswd, $dbName, $dbCharset);
        }
        return self::$_instance;
    }
    
    /**
     * Query 查询
     *
     * @param String $strSql SQL语句
     * @param String $queryMode 查询方式(All or Row)
     * @param Boolean $debug
     * @return Array
     */
    public function query($strSql, $queryMode = 'All', $debug = false)
    {
    
        if ($debug === true) $this->debug($strSql);
        $recordset = $this->dbh->query($strSql);

		if(defined('TRACE_SQL')){
			logd($strSql);	
		}
        $this->getPDOError();
        if ($recordset) {
            $recordset->setFetchMode(PDO::FETCH_ASSOC);
            if ($queryMode == 'All') {
                $result = $recordset->fetchAll();
            } elseif ($queryMode == 'Row') {
                $result = $recordset->fetch();
            }
        } else {
            $result = null;
        }
        return $result;
    }
    
    /**
     * Update 更新
     *
     * @param String $table 表名
     * @param Array $arrayDataValue 字段与值
     * @param String $where 条件
     * @param Boolean $debug
     * @return Int
     */
    public function update($table, $arrayDataValue, $where = '', $debug = false)
    {
        $this->checkFields($table, $arrayDataValue);
        if ($where) {
            $strSql = '';
            foreach ($arrayDataValue as $key => $value) {
                $strSql .= ", `$key`='$value'";
            }
            $strSql = substr($strSql, 1);
            $strSql = "UPDATE `$table` SET $strSql WHERE $where";
        } else {
            $strSql = "REPLACE INTO `$table` (`".implode('`,`', array_keys($arrayDataValue))."`) VALUES ('".implode("','", $arrayDataValue)."')";
        }
     
	 	if(defined('TRACE_SQL')){
			logd($strSql);	
		}
        if ($debug === true) $this->debug($strSql);
        $result = $this->dbh->exec($strSql);
        $this->getPDOError();
        return $result;
    }
    
    /**
     * Insert 插入
     *
     * @param String $table 表名
     * @param Array $arrayDataValue 字段与值
     * @param Boolean $debug
     * @return Int
     */
    public function insert($table, $arrayDataValue, $debug = false)
    {
        $this->checkFields($table, $arrayDataValue);
        $strSql = "INSERT INTO `$table` (`".implode('`,`', array_keys($arrayDataValue))."`) VALUES ('".implode("','", $arrayDataValue)."')";
		if(defined('TRACE_SQL')){
			logd($strSql);	
		}
        if ($debug === true) $this->debug($strSql);
        $result = $this->dbh->exec($strSql);
        $this->getPDOError();
        return $this->dbh->lastInsertId();
    }
    
   
    
    /**
     * Replace 覆盖方式插入
     *
     * @param String $table 表名
     * @param Array $arrayDataValue 字段与值
     * @param Boolean $debug
     * @return Int
     */
    public function replace($table, $arrayDataValue, $debug = false)
    {
        $this->checkFields($table, $arrayDataValue);
        $strSql = "REPLACE INTO `$table`(`".implode('`,`', array_keys($arrayDataValue))."`) VALUES ('".implode("','", $arrayDataValue)."')";
        if ($debug === true) $this->debug($strSql);
        $result = $this->dbh->exec($strSql);
        $this->getPDOError();
        return $result;
    }
    
    /**
     * Delete 删除
     *
     * @param String $table 表名
     * @param String $where 条件
     * @param Boolean $debug
     * @return Int
     */
    public function delete($table, $where = '', $debug = false)
    {
        if ($where == '') {
            $this->outputError("'WHERE' is Null");
        } else {
            $strSql = "DELETE FROM `$table` WHERE $where";
            if ($debug === true) $this->debug($strSql);
			if(defined('TRACE_SQL')){
				logd($strSql);	
			}
            $result = $this->dbh->exec($strSql);
            $this->getPDOError();
            return $result;
        }
    }
    
    /**
     * execSql 执行SQL语句
     *
     * @param String $strSql
     * @param Boolean $debug
     * @return Int
     */
    public function execSql($strSql, $debug = false)
    {
        if ($debug === true) $this->debug($strSql);
        $result = $this->dbh->exec($strSql);
        $this->getPDOError();
        return $result;
    }
    
    /**
     * 获取字段最大值
     * 
     * @param string $table 表名
     * @param string $field_name 字段名
     * @param string $where 条件
     */
    public function getMaxValue($table, $field_name, $where = '', $debug = false)
    {
        $strSql = "SELECT MAX(".$field_name.") AS MAX_VALUE FROM $table";
        if ($where != '') $strSql .= " WHERE $where";
        if ($debug === true) $this->debug($strSql);
        $arrTemp = $this->query($strSql, 'Row');
        $maxValue = $arrTemp["MAX_VALUE"];
        if ($maxValue == "" || $maxValue == null) {
            $maxValue = 0;
        }
        return $maxValue;
    }
    
    /**
     * 获取指定列的数量
     * 
     * @param string $table
     * @param string $field_name
     * @param string $where
     * @param bool $debug
     * @return int
     */
    public function getCount($table, $field_name, $where = '', $debug = false)
    {
        $strSql = "SELECT COUNT($field_name) AS NUM FROM $table";
        if ($where != '') $strSql .= " WHERE $where";
        if ($debug === true) $this->debug($strSql);
        $arrTemp = $this->query($strSql, 'Row');
        return $arrTemp['NUM'];
    }
    
    /**
     * 查询数据是否存在
     * 
     * @param string $table
     * @param string $field_name
     * @param string $where
     * @return boolean
     */
    
	public function getExist($table = '' , $field_name = '',$where='')
	{
    	if($table == '' or $where == '')return FALSE;
    	return $this->getCount($table,$field_name,$where) > 0 ? TRUE : FALSE;
    }
    
    /**
     * 分页函数 返回为分页的数组,pages为分页代码,data为分页数据
     * @param string $sql
     * @param int $pc
     * @param int $ppc
     * @param int $is_cache
     * @return array
     */
    function getPages($sql,$pc=15,$ppc=10){
    	$data                = array();
    	$count_sql           = 'select count(*) cnt '.substr($sql,strpos($sql,'from'));
    	$dc                  = $this->query($count_sql,'Row');//获取数据
    	$count               = $dc['cnt'];//获得总记录数
    	$data['count']       = $count;//记录数赋值
    	$page_count			 = $pc;//分页记录数
    	$ppage_count         = $ppc;//分页页数
    	$page_left           = $count%$page_count;//最后一页剩余
    	$pages               = ($count-$page_left)/$page_count;
    	if($page_left) $pages++; //分页数计算
    	$data['pages_count'] = $pages;//分页数赋值
    	//地址改造
    	$url = preg_replace('/[&\?]p=[^&]+/','', str_replace($_SERVER['PHP_SELF'],'', $_SERVER["REQUEST_URI"]));
    	$url = empty($url)?$url.'?':$url.'&';
    	if(isset($_GET['p'])) $page_current = intval($_GET['p']);else $page_current=1;//首页记录处理
    	if($page_current<1) $page_current = 1;//首页记录处理
    	if($pages<$page_current)$page_current = $pages;
    	$data['page_current']=$page_current;//分页数
    	$page_prev=$page_current-1;//前一页
    	$page_next=$page_current+1;//下一页
    	$ppage_current= ceil($page_current/$ppage_count);//当前页分页
    	$page_start=($ppage_current-1)*$ppage_count+1;//分页开始
    	$page_start=$page_start<0?0:$page_start;
    	$page_end=$ppage_current*$ppage_count+1;//分页结束
    	if($pages<$page_end)$page_end=$pages+1;//分页结束处理
    	$pages_="<style>
			.page-box {height:30px;line-height:30px; float:right}
			.page-box a{display:inline; margin:0px;padding:0px; margin-left:5px; padding:3px 6px; border: 1px solid #dcdddd; background-color:#fff}
			.page-box a:hover,.page-box a.current{ background-color:#004499; color:#fff}
			</style>\n";
    	$pages_.=/*"共".$count."条 ".$pages."页*/"<div class=\"page-box\"><a href='{$url}p=$page_prev' ><span>上一页</span></a>";
    	if(1<$page_start)$pages_.="<a href='{$url}p=1'>1</a><a href='{$url}p=".($page_start-1)."'>...</a>";
    	for($i=$page_start;$i<$page_end;$i++){
    		if($i==$page_current) $css="class='current'" ;else $css='';
    		$pages_.= "<a href='{$url}p=$i' $css>$i</a>";
    	}
    	if($page_end!=$pages+1)
    		$pages_.="<a href='{$url}p=".$page_end."'>...</a><a href='{$url}p=".$pages."'>$pages</a>";
    	$pages_		   .= "<a href='{$url}p=$page_next'><span>下一页</span></a></div>";
    	$pages_			= $pages > 1?$pages_:'';//"共".$count."条 ".$pages."页";
    	$data['pages'] 	= str_replace("\t",'',$pages_);//分页代码
    	$floor			= ($page_current-1)*$page_count;
    	$floor			= $floor<0?0:$floor;
    	$page_limit		= " limit $floor,$page_count";
    
    	$dt				= $this->query($sql.$page_limit);//分页数据
    
    	$data['data']	= count( $dt ) > 0 ? $dt : array();
    	return $data;
    }
    
     /**
     * 查询一个数据
     * @param string $field
     * @param string $table
     * @param string $where
     * @return array
     */
    function get_one($field = '' ,$table = '', $where = ''){
    	if($table == '' or $field=='' or $where=='') return NULL;
    	$data = $this->query("SELECT $field FROM `$table` WHERE $where LIMIT 1",'Row');
    	return $data ? $data[$field] : array();
    }
    
    /**
     * 获取表引擎
     * 
     * @param String $dbName 库名
     * @param String $tableName 表名
     * @param Boolean $debug
     * @return String
     */
    public function getTableEngine($dbName, $tableName)
    {
        $strSql = "SHOW TABLE STATUS FROM $dbName WHERE Name='".$tableName."'";
        $arrayTableInfo = $this->query($strSql);
        $this->getPDOError();
        return $arrayTableInfo[0]['Engine'];
    }
    
    /**
     * beginTransaction 事务开始
     */
    private function beginTransaction()
    {
        $this->dbh->beginTransaction();
    }
    
    /**
     * commit 事务提交
     */
    private function commit()
    {
        $this->dbh->commit();
    }
    
    /**
     * rollback 事务回滚
     */
    private function rollback()
    {
        $this->dbh->rollback();
    }
    
    /**
     * transaction 通过事务处理多条SQL语句
     * 调用前需通过getTableEngine判断表引擎是否支持事务
     *
     * @param array $arraySql
     * @return Boolean
     */
    public function execTransaction($arraySql)
    {
        $retval = 1;
        $this->beginTransaction();
        foreach ($arraySql as $strSql) {
            if ($this->execSql($strSql) == 0) $retval = 0;
        }
        if ($retval == 0) {
            $this->rollback();
            return false;
        } else {
            $this->commit();
            return true;
        }
    }

    /**
     * checkFields 检查指定字段是否在指定数据表中存在
     *
     * @param String $table
     * @param array $arrayField
     */
    private function checkFields($table, $arrayFields)
    {
		return ;
        $fields = $this->getFields($table);
        foreach ($arrayFields as $key => $value) {
            if (!in_array($key, $fields)) {
                $this->outputError("Unknown column `$key` in field list.");
            }
        }
    }
    
    /**
     * getFields 获取指定数据表中的全部字段名
     *
     * @param String $table 表名
     * @return array
     */
    private function getFields($table)
    {
        $fields = array();
        $recordset = $this->dbh->query("SHOW COLUMNS FROM $table");
        $this->getPDOError();
        $recordset->setFetchMode(PDO::FETCH_ASSOC);
        $result = $recordset->fetchAll();
        foreach ($result as $rows) {
            $fields[] = $rows['Field'];
        }
        return $fields;
    }
    
    /**
     * getPDOError 捕获PDO错误信息
     */
    private function getPDOError()
    {
        if ($this->dbh->errorCode() != '00000') {
            $arrayError = $this->dbh->errorInfo();
            $this->outputError($arrayError[2]);
        }
    }
    
    /**
     * debug
     * 
     * @param mixed $debuginfo
     */
    private function debug($debuginfo)
    {
        var_dump($debuginfo);
        exit();
    }
    
    /**
     * 输出错误信息
     * 
     * @param String $strErrMsg
     */
    private function outputError($strErrMsg)
    {
        throw new Exception('MySQL Error: '.$strErrMsg);
    }
    
    /**
     * destruct 关闭数据库连接
     */
    public function destruct()
    {
        $this->dbh = null;
    }
}
?>