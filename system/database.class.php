<?php
defined('WENXIAOCMS') or exit('Access denied!');
/**
  *数据库备份与恢复类
  *@author alex
  */
@set_time_limit(0);
class database{
	public $link  = null;
	public $dbname= null;
	function __construct() {
		$this->connect();
	}
	/**
	 * 开启数据库连接
	 * 			
	 * @return void
	 */
	private function connect( $db = DBNAME) {
		$con = mysql_connect(DBHOST,DBUSER,DBPW);
		mysql_query("SET NAMES ".DBCHARSET,$con);
		$this->dbname=$db;
		mysql_select_db($db,$con);
		$this->link=$con;
	}
	/**
	 * 备份文件下载
	 */
	public function public_down() {

	}
	
	/**
	 * 数据库修复、优化
	 */
	public function public_repair() {
		
	}
	
	/**
	 * 备份文件删除
	 */
	public function delete() {
		
	}
	/**
	 * 获得数据库中的表
	 */
	public function get_tables() {
		$tables=array();
		$data=$this->query("show tables",MYSQL_NUM);
		$i=0;
		foreach($data as $row){
			$tblname=$row[0];
			$tables[$i]['tblname']=$tblname;
			$q = $this->query("SHOW TABLE STATUS LIKE '".$tblname."'");
			$tables[$i]['records']=$q[0]['Rows'];
			$tables[$i]['size']=$q[0]['Data_length'];
			$i++;
		}
		return $tables;
	}
	
	/**
	 * 数据库备份
	 * @param unknown_type $tables 数据表数据组
	 * @param unknown_type $sizelimit 卷大小
	 * @param unknown_type $fileid 卷标
	 */
	public function export_database($tables,$devide_size=2048) {
		$sql='';
		$vol=1;
		foreach($tables as $tblname){
			//create
			$create=$this->query("show create table $tblname");	
			$create_sql  = 'DROP TABLE IF EXISTS `'.$create[0]['Table']."`;\n";
			$create_sql .= $create[0]['Create Table'].";\n";
			if(strlen($sql.$create_sql) > $devide_size*1000){
				$this->output_sql($sql,$vol);
				$vol++;
				$sql='';	
			}
			$sql .= $create_sql;
			//data
			$tbl_data = mysql_query("select * from $tblname",$this->link);
			while($row= mysql_fetch_array($tbl_data,MYSQL_NUM)){
				$row_sql = "INSERT INTO `$tblname` VALUES (";
				foreach($row as $v){
					$row_sql .="'".mysql_real_escape_string($v)."',";
				}
				$row_sql = rtrim($row_sql,",").");\n";
				if(strlen($sql.$row_sql) > $devide_size*1000){
					$this->output_sql($sql,$vol);
					$vol++;
					$sql='';	
				}
				$sql .= $row_sql;
			}
		}
		$this->output_sql($sql,$vol);
		return TRUE;
	}
	
	function import_tables($tbl = '',$file = ''){
		$str = mb_convert_encoding( file_get_contents($file),'utf-8', 'gb2312');
		$arr = explode("\n",$str);
		foreach($arr as $row){
			$value = "'".str_replace(array("\t",'NULL'),array("','",''),$row)."'";
			$this->db->execute_query("insert into `$tbl` values($value)");	
		}
	}
	
	
	/**
	 * sql文件输出
	 * @param unknown_type $sql 要输出的sql语句
	 * @param unknown_type $vol 卷标
	 */
	public function output_sql($sql,$vol) {
		$random = mt_rand(1000, 9999);
		$outdir= DB_BACK_PATH;
		$file_name=$this->dbname.'_'.date("Ymd").'_'.$random.'_'.$vol.'.sql';
		$outfile=$outdir.'/'.$file_name;
		$size=file_put_contents($outfile,$sql);
		$size=sizecount($size);
		$time=time();
		$this->query("insert into gc_data_backup (file_name , file_size ,backup_time, vol) values ('$file_name','$size','$time','$vol')");
		
	}
	/**
	 * 数据库恢复
	 * @param string $filename
	 */
	public function import_database($filename) {
		if($filename && fileext($filename)=='sql') {
			$filepath = '../data/backup/default/'.$filename;
			if(!file_exists($filepath)) msg(" $filepath 不存在请检查!");
			$sql = file_get_contents($filepath);
			$this->sql_execute($sql);
		}
	}
	
	/**
	 * 单句执行SQL
	 * @param unknown_type $sql
	 */
 	private function query($sql,$type=MYSQL_ASSOC) {
		if(preg_match('/^[(show)(select)(SHOW)(SELECT)]/', $sql)){
			$r=array();
			$result = mysql_query($sql,$this->link);
			if( mysql_errno() && D_BUG == 1){
				exit ('<b>Mysql Error:</b><br/>'.mysql_error().'<br/><b>SQL:</b><br/>'.$sql);	
			}
			if($result){
				while($data= mysql_fetch_array($result,$type)){
					$r[]=$data;
				}
			}else{
				$r=NULL;
			}
			return $r;
		}else{
			mysql_query($sql,$this->link);
			if( mysql_errno() && D_BUG == 1){
				exit ('<b>Mysql Error:</b><br/>'.mysql_error().'<br/><b>SQL:</b><br/>'.$sql);	
			}	
			return mysql_affected_rows();
		}
	}
	
	/**
	 * 执行SQL
	 * @param unknown_type $sql
	 */
 	private function sql_execute($sql) {
	    $sqls = $this->sql_split($sql);
		if(is_array($sqls)) {
			foreach($sqls as $sql) {
				if(trim($sql) != '') {
					$this->query($sql);
				}
			}
		} else {
			$this->query($sqls);
		}
		return true;
	}
	
	/**
	 * sql 文本拆分为数组
	 * @param unknown_type $sql
	 */
	private function sql_split($sql) {
		if(DBCHARSET) {
			$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=".DBCHARSET,$sql);
		}
		$sql = str_replace("\r", "\n", $sql);
		$ret = array();
		$num = 0;
		$queriesarray = explode(";\n", trim($sql));
		unset($sql);
		foreach($queriesarray as $query) {
			$ret[$num] = '';
			$queries = explode("\n", trim($query));
			$queries = array_filter($queries);
			foreach($queries as $query) {
				$str1 = substr($query, 0, 1);
				if($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
			}
			$num++;
		}
		return($ret);
	}
	
	function __destruct(){
		mysql_close($this->link);	
	}		
}
 