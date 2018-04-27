<?php defined('WENXIAOCMS') or exit('Access denied!'); ?>
  
        
        
        <div class="topbar">
            <div class="header6">
                <div class="container po-relative">
                    <nav class="navbar navbar-expand-lg h6-nav-bar">
                        <a href="javascript:void(0)" class="navbar-brand"><img src="<?php echo load_app_static();?><?php echo $logo_path;?>" alt="<?php echo $logo_alt;?>" /></a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#h6-info" aria-controls="h6-info" aria-expanded="false" aria-label="Toggle navigation"><span class="ti-menu"></span></button>
                        <div class="collapse navbar-collapse hover-dropdown font-14 ml-auto" id="h6-info">
                            <ul class="navbar-nav ml-auto">
                            	<?php $n=1;if(is_array($topbar_father)) foreach($topbar_father AS $v) { ?>
                            	<?php if($v['father_type']==0) { ?>
                            	<li class="nav-item">
                            	<a href="<?php echo $v['href'];?>" target="<?php echo $v['target'];?>" class="nav-link">Documentation</a>
                            	</li>
                            	<?php } elseif ($v['father_type']==1 ) { ?>
                                <li class="nav-item dropdown mega-dropdown" onmouseenter="get_son_topbar(<?php echo $v['id'];?>)"> 
                                <a class="nav-link dropdown-toggle" href="<?php echo $v['href'];?>" target="<?php echo $v['target'];?>"  id="h6-mega-dropdown<?php echo $v['id'];?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                  <?php echo $v['name'];?> <i class="fa fa-angle-down m-l-5"></i>
                                </a>
                                <div class="dropdown-menu b-none font-14 animated fadeInUp" aria-labelledby="h6-mega-dropdown<?php echo $v['id'];?>">
                                    <div id="son_topbar_<?php echo $v['id'];?>"  class="row">
                                   
                                    </div>
                                </div>
                                </li>
                                <?php } else { ?>
                                <li class="nav-item dropdown" onmouseenter="get_son_topbar(<?php echo $v['id'];?>)"> 
	                                <a class="nav-link dropdown-toggle" href="<?php echo $v['href'];?>" target="<?php echo $v['target'];?>"  id="h6-dropdown<?php echo $v['id'];?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                                <?php echo $v['name'];?>  <i class="fa fa-angle-down m-l-5"></i>
	                            	</a>
		                            <ul class="b-none dropdown-menu font-14 animated fadeInUp" id="son_topbar_<?php echo $v['id'];?>">
		                               
		                        	</ul>
                    			</li>
                                <?php } ?>
                                <?php $n++;}unset($n); ?>
                   
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
        
        
        
        
        <script src="<?php echo load_app_static();?>js/topbar.js"></script> 