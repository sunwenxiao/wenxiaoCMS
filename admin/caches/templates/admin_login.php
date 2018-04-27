<?php defined('WENXIAOCMS') or exit('Access denied!'); ?>
<!DOCTYPE html>
<html lang="en" class="loading">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Apex admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Apex admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title><?php echo $adminlogin['title'];?></title>
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo load_app_static();?>app-assets/img/ico/apple-icon-60.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo load_app_static();?>app-assets/img/ico/apple-icon-76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo load_app_static();?>app-assets/img/ico/apple-icon-120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo load_app_static();?>app-assets/img/ico/apple-icon-152.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo load_app_static();?>app-assets/img/ico/favicon.ico">
    <link rel="shortcut icon" type="image/png" href="<?php echo load_app_static();?>app-assets/img/ico/favicon-32.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900" rel="stylesheet">
    
    
    <link rel="stylesheet" type="text/css" href="<?php echo load_app_static();?>app-assets/fonts/feather/style.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo load_app_static();?>app-assets/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo load_app_static();?>app-assets/fonts/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo load_app_static();?>app-assets/vendors/css/perfect-scrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo load_app_static();?>app-assets/vendors/css/prism.min.css">
    
    
    <link rel="stylesheet" type="text/css" href="<?php echo load_app_static();?>app-assets/css/app.css">
    
    
    
    
    
  </head>
  <body data-col="1-column" class=" 1-column  blank-page blank-page">
    
    <div class="wrapper">
      <div class="main-panel">
        <div class="main-content">
          <div class="content-wrapper " style="background:url(<?php echo load_app_static();?>/images/bizhi.jpg) center fixed;background-size:contain background-position: center 0;
background-repeat: no-repeat;
background-attachment: fixed;
background-size: cover;
 -webkit-background-size: cover;">
<section id="login">
    <div class="container-fluid">
        <div class="row full-height-vh">
            <div class="col-12 d-flex align-items-center justify-content-center">
                <div class="card gradient-indigo-purple text-center width-400">
                    <div class="card-img overlap">
                        <img alt="element 06" class="mb-1" src="<?php echo load_app_static();?>app-assets/img/portrait/avatars/avatar-08.png" width="190">
                    </div>
                    <div class="card-body">
                        <div class="card-block">
                            <h2 class="white"><?php echo $adminlogin['heading'];?></h2>
                            <form>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input type="email" class="form-control" name="inputEmail" id="inputEmail" placeholder="Email" required >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input type="password" class="form-control" name="inputPass" id="inputPass" placeholder="Password" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0 ml-3">
                                                <input type="checkbox" class="custom-control-input" checked id="rememberme">
                                                <label class="custom-control-label float-left white" for="rememberme">重置密码</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-pink btn-block btn-raised">提交</button>
                                        <button type="button" class="btn btn-secondary btn-block btn-raised">注销</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="float-left"><a href="" class="white">重置密码</a></div>
                        <div class="float-right"><a href="?m=login&a=register" class="white">新用户?</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

          </div>
        </div>
      </div>
    </div>
    
<?php include template('admin_footer_js'); ?>