<?php
defined('WENXIAOCMS') or exit('Access denied!');
class lib_mysqli {
	protected $mysqli;							//mysqli实例对象
	public $sql;								//sql语句
	protected $rs;								//结果集
	protected $query_num	= 0;				//执行次数
	protected $fetch_mode	= MYSQLI_ASSOC;		//获取模式
	protected $cache;							//缓存类对象
	protected $reload     = false;				//是否重新载入
	protected $cache_mark = true;				//缓存标记

	//构造函数：主要用来返回一个mysqli对象
	public function  __construct($dbhost, $dbuser, $dbpass, $dbname, $dbport) {
		$this->mysqli    = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);
		if(mysqli_connect_errno()) {
			$this->mysqli    = false;
			echo '<h2>'.mysqli_connect_error().'</h2>';
			die();
		} else {
			$this->mysqli->set_charset("utf8");
		}
	}

	//缓存类对象：文件缓存、memcache键值对缓存
	public function cache_obj($cache) {
		$this->cache	= $cache;
	}

	//析构函数：主要用来释放结果集和关闭数据库连接
	public function  __destruct() {
		$this->free();
		$this->close();
	}

	//释放结果集所占资源
	protected function free() {
		@$this->rs->free();
	}

	//关闭数据库连接
	protected function close() {
		$this->mysqli->close();
	}

	//获取结果集
	protected function fetch() {
		return $this->rs->fetch_array($this->fetch_mode);
	}

	//获取查询的sql语句
	protected function get_query_sql($sql, $limit = null) {
		if (@preg_match("/[0-9]+(,[ ]?[0-9]+)?/is", $limit) && !preg_match("/ LIMIT [0-9]+(,[ ]?[0-9]+)?$/is", $sql)) {
			$sql .= " LIMIT " . $limit;
		}
		return $sql;
	}

	//从缓存中获取数据
	protected function get_cache($sql,$method) {
		$cache_file    = md5($sql.$method);
		$res    = $this->cache->get($cache_file);
		if(!$res) {							//如果缓存文件过期或不存在的话，返回false；如果缓存文件存在且未过期的话，则返回缓存数据
			$res    = $this->$method($sql);	//先从缓存中取数据，如果缓存中没数据，则从数据库中取数据
			if($res && $this->cache_mark && !$this->reload) {
				$this->cache->set($cache_file, $res);//如果缓存文件过期或不存在的话，将重新将从数据库中查询的数据放入缓存文件
			}
		}
		return $res;
	}
	
	//分页函数 返回为分页的数组,pages为分页代码,data为分页数据
	function get_pages($sql,$pc=15,$ppc=10){
		$data                = array();
		if(preg_match('/group by/',$sql)){
			$gby_str = substr($sql,stripos($sql,'group by')+8,strlen($sql));
			$gby_arr = explode(',', $gby_str);
			$distinct = 'distinct ';
			foreach ($gby_arr as $k=>$r){
				if($k){
					$distinct.= ','.$r;
				}else{
					$distinct.= $r;
				}
			}
			$count_sql = 'select count('.$distinct.') cnt '.substr($sql,strpos($sql,'from'));
			$dc    = $this->get_all($count_sql);//获取数据
			$count = 0;
			if($dc){
				foreach($dc as $v){
					$count += $v['cnt'];
				}
			}
		}else{
			$count_sql           = 'select count(*) cnt '.substr($sql,strpos($sql,'from'));
			$dc    				 = $this->get_row($count_sql);//获取数据
			$count               = $dc['cnt'];//获得总记录数
		}
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
			.page-box {height:30px;line-height:30px; float:right;margin-right:22px;}
			.page-box a{display:inline; margin:0px;padding:0px; margin-left:5px; padding:3px 6px; border: 1px solid #dcdddd; background-color:#fff}
			.page-box a:hover,.page-box a.current{ background-color:#327aee; color:#fff}
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
		$dt				= $this->get_all($sql.$page_limit);//分页数据
		$data['data']	= count( $dt ) > 0 ? $dt : array();
		return $data;
	}

	//获取查询次数
	public function query_num() {
		return $this->query_num;
	}

	//执行sql语句查询
	public function query($sql, $limit = null) {
		$sql    = $this->get_query_sql($sql, $limit);
		$this->sql[]    = $sql;
		$this->rs    = $this->mysqli->query($sql);
		if (!$this->rs) {
			echo "<p>error: ".$this->mysqli->error."</p>";
			echo "<p>sql: ".$sql."</p>";
			die();
		} else {
			$this->query_num++;
			return $this->rs;
		}
	}

	//返回单条记录的单个字段值
	public function get_one($sql) {
		$this->query($sql, 1);
		$this->fetch_mode    = MYSQLI_NUM;
		$row = $this->fetch();
		$this->free();
		return $row[0];
	}

	//缓存单个字段
	public function cache_one($sql, $reload = false) {
		$this->reload	= $reload;
		$sql    = $this->get_query_sql($sql, 1);
		return $this->get_cache($sql, 'get_one');
	}

	//获取单条记录
	public function get_row($sql, $fetch_mode = MYSQLI_ASSOC) {
		$this->query($sql, 1);
		$this->fetch_mode    = $fetch_mode;
		$row = $this->fetch();
		$this->free();
		return $row;
	}

	//缓存行
	public function cache_row($sql, $reload = false) {
		$this->reload	= $reload;
		$sql    = $this->get_query_sql($sql, 1);
		return $this->get_cache($sql, 'get_row');
	}

	//返回所有的结果集
	public function get_all($sql, $limit = null, $fetch_mode = MYSQLI_ASSOC) {
		$this->query($sql, $limit);
		$all_rows = array();
		$this->fetch_mode    = $fetch_mode;
		while($rows = $this->fetch()) {
			$all_rows[] = $rows;
		}
		$this->free();
		return $all_rows;
	}

	//缓存all
	public function cache_all($sql, $reload = false, $limit = null) {
		$this->reload	= $reload;
		$sql    = $this->get_query_sql($sql, $limit);
		return $this->get_cache($sql, 'get_all');
	}

	//返回前一次mysql操作所影响的记录行数
	public function affected_rows() {
		return $this->mysqli->affected_rows;
	}
	
	 /**
	 * 获取插入语句
     *
     * @param    string     $tbl_name   表名
     * @param    array      $info       数据
     */
    public function get_insert_db_sql($tbl_name,$info)
	{	
		//首先判断是否为数组，再判断数组是否为空
        if(is_array($info)&&!empty($info))
        {
            $i = 0;
            foreach($info as $key=>$val)
            {
                $fields[$i] = $key;	//将所有的键名放到一个$fields[]数组中
                $values[$i] = $val;	//将所有的值放到一个$values[]数组中
                $i++;
            }
            $s_fields = "(".implode(",",$fields).")";
            $s_values  = "('".implode("','",$values)."')";
            $sql = "INSERT INTO
                        $tbl_name
                        $s_fields
                    VALUES
                        $s_values";
            Return $sql;
        }
        else
        {
            Return false;
        }
    }
        
    /**
     * 获取替换语句:replace into是insert into的增强版
     * 区别：replace into跟insert功能类似，不同点在于：replace into 首先尝试插入数据到表中，如果发现表中
			 已经有此行数据(根据主键或唯一索引判断)，则先删除此行数据，然后插入新的数据，否则直接插入新数据
     * @param    string     $tbl_name   表名
     * @param    array      $info       数据
     */
    public function get_replace_db_sql($tbl_name,$info)
    {
        if(is_array($info)&&!empty($info))
        {
            $i = 0;
            foreach($info as $key=>$val)
            {
                $fields[$i] = $key;
                $values[$i] = $val;
                $i++;
            }
            $s_fields = "(".implode(",",$fields).")";
            $s_values  = "('".implode("','",$values)."')";
            $sql = "REPLACE INTO
                        $tbl_name
                        $s_fields
                    VALUES
                        $s_values";
            Return $sql;
        }
        else
        {
            Return false;
        }
    }
    
  /**
     * 获取更新SQL语句
     *
     * @param    string     $tbl_name   表名
     * @param    array      $info       数据
     * @param    array      $condition  条件
     */
    public function get_update_db_sql($tbl_name,$info,$condition)
    {
        $i = 0;
        $data = '';
        if(is_array($info)&&!empty($info))
        {
            foreach( $info as $key=>$val )
            {
                if(isset($val))
                {
                    $val = $val;
                    if($i==0&&$val!==null)
                    {
                        $data = $key."='".$val."'";	//第一次：如，update 表名 set username='admin'
                    }
                    else
                    {
                        $data .= ",".$key." = '".$val."'";//非第一次：如， ，password='123'
                    }
                    $i++;
                }
            }	
            $sql = "UPDATE ".$tbl_name." SET ".$data." WHERE ".$condition;
            return $sql;
        }
        else
        {
            Return false;
        }
    }
    
    /**
     * 取得数据库最后一个插入ID
     *
     * @return int
     */
    public function last_id() {
        return mysqli_insert_id($this->mysqli);
    }
    

    public function real_get($sql, $fetch_mode = MYSQLI_ASSOC) {
		$this->query($sql);
		$this->fetch_mode    = $fetch_mode;
		$row = $this->fetch();
		$this->free();
		return $row;
	}
}