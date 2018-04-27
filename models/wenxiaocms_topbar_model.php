<?php
class wenxiaocms_topbar_model extends base_model{
    //得到得到父导航条
    function get_topbar_father()
    {
        
        //查询父ID列表是否存在redis中若不存在则写入
        $existshs = $this->redis->EXISTS('topbar_father_jsoncode');
        $topbar_father = array();
        if($existshs)
        {
            //有值的时候讲值从redis里取出
            $topbar_father = $this->redis->get('topbar_father_jsoncode');
            $topbar_father = json_decode($topbar_father,true);
        }
        else
        {
            //没有值的情况下将值设置到哈希表中
            $topbar_father  = $this->db->query("select * from wenxiaocms_topbar where `father_id`=0");
            $topbar_father_jsoncode = json_encode($topbar_father);
            $this->redis->set('topbar_father_jsoncode',$topbar_father_jsoncode);
        }
        return $topbar_father;
    }
    
    //得到单条导航条信息
    function get_one_topbar_info($id)
    {
       
        //查询父此条ID是否存在redis如果不存在则写入
        $existshs = $this->redis->EXISTS('topbar_info_jsoncode_'.$id);
        $topbar_father = array();
        if($existshs)
        {
            //有值的时候讲值从redis里取出
            $topbar_info_jsoncode = $this->redis->get('topbar_info_jsoncode_'.$id);
            $topbar_info = json_decode($topbar_info_jsoncode,true);
        }
        else
        {
            //没有值的情况下将值设置到哈希表中
            $topbar_info  = $this->db->query("select * from wenxiaocms_topbar where `id`=$id",'Row');
            $topbar_info_jsoncode = json_encode($topbar_info);
            $this->redis->set('topbar_info_jsoncode_'.$id,$topbar_info_jsoncode);
        }
        return $topbar_info;
    }
    
    //得到子导航条信息
    function get_son_topbar_list($id)
    {
        
        //得到子导航条信息是否存在redis如果不存在则写入
        $existshs = $this->redis->EXISTS('son_topbar_list_info_jsoncode_'.$id);
        $son_topbar_list_info = array();
        if($existshs)
        {
            //有值的时候讲值从redis里取出
            $son_topbar_list_info_jsoncode = $this->redis->get('son_topbar_list_info_jsoncode_'.$id);
            $son_topbar_list_info = json_decode($son_topbar_list_info_jsoncode,true);
        }
        else
        {
            //没有值的情况下将值设置到哈希表中
            $son_topbar_list_info = $this->db->query("select * from wenxiaocms_topbar where `father_id` = $id");
            $son_topbar_list_info_jsoncode = json_encode($son_topbar_list_info);
            $this->redis->set('son_topbar_list_info_jsoncode_'.$id,$son_topbar_list_info_jsoncode);
        }
        return $son_topbar_list_info;
    }
    //查询是否有子导航
    function is_hanve_son($id)
    {
        
        //得到子导航条数量信息是否存在redis如果不存在则写入
        $existshs = $this->redis->EXISTS('son_count_'.$id);
        $son_count = 0;
        if($existshs)
        {
            //有值的时候讲值从redis里取出
            $son_count = $this->redis->get('son_count_'.$id);
        }
        else
        {
            //没有值的情况下将值设置到哈希表中
            $wenxiaocms_topbar  = $this->db->query("select count(id) son_count from wenxiaocms_topbar where `father_id` = $id",'Row');
            $son_count = $wenxiaocms_topbar['son_count'];
            $this->redis->set('son_count_'.$id,$son_count);
        }
        return $son_count;
    }
    
  
}