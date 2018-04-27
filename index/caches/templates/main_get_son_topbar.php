<?php defined('WENXIAOCMS') or exit('Access denied!'); ?>
										<div class="col-lg-<?php echo $father_info['col-lg'] ?> inside-bg hidden-md-down">
                                            <div class="bg-img" style="background-image:url(<?php echo load_app_static();?><?php echo $father_info['bg-img']; ?>)">
                                                <h3 class="text-white font-light"><?php echo $father_info['title'];?></h3>
                                            </div>
                                        </div>
                                        
                                        
                                        <?php if($father_info['father_type']==1) { ?>
                                        <?php $n=1;if(is_array($son_topbar_list_info)) foreach($son_topbar_list_info AS $son_info) { ?>
                                        <div class="col-lg-<?php echo $son_info['col-lg'] ?> col-md-<?php echo $son_info['col-md'] ?>">
                                                <ul class="list-style-none">
                                                    <li><h6><?php echo $son_info['name'];?></h6></li>
                                                    <?php if (count($son_info['son_son'])>0){ 
                                                    		foreach ($son_info['son_son'] as $son_son_info){?>
                                                     <li><a href="<?php echo $son_son_info['href'];?>" target="<?php echo $son_son_info['target'];?>" ><?php echo $son_son_info['name'];?></a></li>
                                                    <?php }}?>
                                                </ul>
                                        </div>
                                        <?php $n++;}unset($n); ?>
                                        <?php } elseif ($father_info['father_type']==2) { ?>
                                        <?php $n=1;if(is_array($son_topbar_list_info)) foreach($son_topbar_list_info AS $son_info) { ?>
                                        <?php if($son_info['name'] ) { ?>
                                        <?php if($son_info['is_hanve_son']>0) { ?>	
                                        <li class="dropdown-submenu" onmouseenter="get_son_topbar(<?php echo $son_info['id'];?>)"> 
                                        <a class="dropdown-toggle dropdown-item" href="<?php echo $son_info['href'];?>" target="<?php echo $son_info['target'];?>"  aria-haspopup="true" aria-expanded="false">
                                        <?php echo $son_info['name'];?> 
                                        <i class="fa fa-angle-right ml-auto"></i>
                                        </a>
			                                <ul class="dropdown-menu font-14 b-none animated flipInY" id="son_topbar_<?php echo $son_info['id'];?>">
			                                </ul>
		                            	</li>
                                        <?php } else { ?>
		                                <li><a class="dropdown-item" href="<?php echo $son_info['href'];?>" target="_blank"><?php echo $son_info['name'];?></a></li>
		                                <?php } ?>
		                                <?php } else { ?>
		                                <li class="divider" role="separator"></li>
		                                <?php } ?>
		                            	<?php $n++;}unset($n); ?>
                                        <?php } ?>