<?php
 class main extends indexcontroller {
    function on_index(){
      $this->view->display();
    }
    function on_get_son_topbar()
    {
        $father_id = getG_P('father_id');
        if(!$father_id)
        {
            exit('请求有误');
        }
        //得到父导航条信息
        $father_info = M('wenxiaocms_topbar')->get_one_topbar_info($father_id);
        $father_type = $father_info['father_type'];
        //得到子导航的列表
        $son_topbar_list_info = M('wenxiaocms_topbar')->get_son_topbar_list($father_id);
        //如果是带图片背景的则把下一级也一块取出
        if($father_type==1)
        {
            foreach ($son_topbar_list_info as $k=>$son_info)
            {
                $son_topbar_list_info[$k]['son_son'] = M('wenxiaocms_topbar')->get_son_topbar_list($son_info['id']);
            }
        }
        //如果是列表方式的则查询下级是否有子导航
        if($father_type==2)
        {
            foreach ($son_topbar_list_info as $k=>$son_info)
            {
                $son_topbar_list_info[$k]['is_hanve_son'] = M('wenxiaocms_topbar')->is_hanve_son($son_info['id']);
            }
        }
        $this->assign('father_info',$father_info);
        $this->assign('son_topbar_list_info',$son_topbar_list_info);
        $this->display();
    }
    //清空包含头Redis
    function on_del_redis()
    {
        M('wenxiaocms_header')->del_header();
    }
 }