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
    <title>Register Page - Apex responsive bootstrap 4 admin template</title>
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
          <div class="content-wrapper" style="background:url(<?php echo load_app_static();?>/images/bizhi_reg.jpg) center fixed;background-size:contain background-position: center 0;
background-repeat: no-repeat;
background-attachment: fixed;
background-size: cover;
 -webkit-background-size: cover;">
<section id="regestration">
    <div class="container">
        <div class="row full-height-vh">
            <div class="col-12 d-flex align-items-center justify-content-center">
                <div class="card">
                    <div class="card-body">
                        <div class="row d-flex">
                            <div class="col-12 col-sm-12 col-md-6 gradient-deep-orange-orange">
                                <div class="card-block">
                                    <div class="card-img overlap">  
                                        <img alt="Card image cap" src="<?php echo load_app_static();?>app-assets/img/elements/13.png" width="350" class="mx-auto d-block">
                                    </div>
                                    <h2 class="card-title font-large-1 text-center white mt-3">注册</h2>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 d-flex align-items-center">
                                <div class="card-block mx-auto">
                                    <form >
                                        <div class="input-group mb-3">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="icon-user"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" name="fname" id="fname" placeholder="姓名" required >
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="ft-mail"></i>
                                                </span>
                                            </div>
                                            <input type="email" class="form-control" name="inputEmail" id="inputEmail" placeholder="邮箱" required >
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="ft-lock"></i>
                                                </span>
                                            </div>
                                            <input type="password" class="form-control" name="inputPass" id="inputPass" placeholder="密码" required >
                                        </div>
                                        <div class="form-group col-sm-offset-1">
                                            <div class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                                                <input type="checkbox" class="custom-control-input" checked id="terms">
                                                <label class="custom-control-label pl-2" for="terms">我同意<a>文晓CMS框架条款</a></label>
                                            </div>
                                        </div>
                                        <div class="form-group text-center">
                                            <button type="button" class="btn gradient-mint">Get Started</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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