<?php
class wenxiaocms_header_model extends base_model{
    
    //得到所有头信息
    function get_all()
    {
        //将$wenxiaocms_header存入redis，并检查redis是否有值，有值读取，无值赋值
        //网站基础配置与读取，从数据库中读取到redis中。
        $existshs = $this->redis->EXISTS('wenxiaocms_header_jsoncode');
        $wenxiaocms_header = array();
        if($existshs)
        {
            //有值的时候讲值从redis里取出
            $wenxiaocms_header_jsoncode = $this->redis->get('wenxiaocms_header_jsoncode');
            $wenxiaocms_header = json_decode($wenxiaocms_header_jsoncode,true);
        }
        else 
        {
            //没有值的情况下将值设置到哈希表中
            $wenxiaocms_header = $this->db->query("select `key`,`value` from wenxiaocms_header");
            $wenxiaocms_header_jsoncode = json_encode($wenxiaocms_header);
            $this->redis->set('wenxiaocms_header_jsoncode',$wenxiaocms_header_jsoncode);
        }
        
        return $wenxiaocms_header;
    }
    
    //清除header
    function del_header()
    {
        $this->redis->del('wenxiaocms_header_jsoncode');
    }
    
}