<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title></title>
    <meta name="keywords" content="Bootstrap模版,Bootstrap模版下载,Bootstrap教程,Bootstrap中文" />
    <meta name="description" content="站长素材提供Bootstrap模版,Bootstrap教程,Bootstrap中文翻译等相关Bootstrap插件下载" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- basic styles -->

    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/font-awesome.min.css" />

    <!--[if IE 7]>
    <link rel="stylesheet" href="assets/css/font-awesome-ie7.min.css" />
    <![endif]-->

    <!-- page specific plugin styles -->

    <!-- fonts -->

    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300" />

    <!-- ace styles -->

    <link rel="stylesheet" href="assets/css/ace.min.css" />
    <link rel="stylesheet" href="assets/css/ace-rtl.min.css" />
    <link rel="stylesheet" href="assets/css/ace-skins.min.css" />

    <!--[if lte IE 8]>
    <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
    <![endif]-->

    <!-- inline styles related to this page -->

    <!-- ace settings handler -->

    <script src="assets/js/ace-extra.min.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

    <!--[if lt IE 9]>
    <script src="assets/js/html5shiv.js"></script>
    <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<div class="navbar navbar-default" id="navbar">
    <script type="text/javascript">
        try{ace.settings.check('navbar' , 'fixed')}catch(e){}
    </script>

    <div class="navbar-container" id="navbar-container">
        <div class="navbar-header pull-left">
            <a href="http://www.sdu.edu.cn" >
                <img style="display: inline-block;margin-top: 7px;float: left;height: 30px;" src="assets/img/Sbadge.png" />
            </a>
            <a href="#" class="navbar-brand">
                <small>
                    山东大学场馆管理系统
                </small>
            </a><!-- /.brand -->
        </div><!-- /.navbar-header -->
        <div class="navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">

                <li class="purple">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon-bell-alt icon-animated-bell"></i>
                        <span class="badge badge-important">8</span>
                    </a>

                    <ul class="pull-right dropdown-navbar navbar-pink dropdown-menu dropdown-caret dropdown-close">
                        <li class="dropdown-header">
                            <i class="icon-warning-sign"></i>
                            8 Notifications
                        </li>

                        <li>
                            <a href="#">
                                <div class="clearfix">
											<span class="pull-left">
												<i class="btn btn-xs no-hover btn-pink icon-comment"></i>
												New Comments
											</span>
                                    <span class="pull-right badge badge-info">+12</span>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <i class="btn btn-xs btn-primary icon-user"></i>
                                Bob just signed up as an editor ...
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                See all notifications
                                <i class="icon-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="green">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon-envelope icon-animated-vertical"></i>
                        <span class="badge badge-success">5</span>
                    </a>

                    <ul class="pull-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
                        <li class="dropdown-header">
                            <i class="icon-envelope-alt"></i>
                            5 Messages
                        </li>

                        <li>
                            <a href="#">
                                <img src="assets/avatars/avatar.png" class="msg-photo" alt="Alex's Avatar" />
                                <span class="msg-body">
											<span class="msg-title">
												<span class="blue">Alex:</span>
												Ciao sociis natoque penatibus et auctor ...
											</span>

											<span class="msg-time">
												<i class="icon-time"></i>
												<span>a moment ago</span>
											</span>
										</span>
                            </a>
                        </li>

                        <li>
                            <a href="inbox.html">
                                See all messages
                                <i class="icon-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- #section:basics/navbar.dropdown 导航栏管理员部分-->
                <div class="navbar-buttons navbar-header pull-right" role="navigation" id="before-login">
                    <button type="button" class="btn btn-primary btn-lg admin-login-btn" data-toggle="modal" data-target="#myModal">
                        管理员登录
                    </button>
                </div>
                <!-- 登录模态框 -->

                <!-- Modal -->
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title" id="myModalLabel"><b>管理员登录</b></h5>
                            </div>
                            <div class="modal-body">

                                <!-- 登录的form表单 -->
                                <form  id="login-form">
                                    <div class="input-style"><span>账号：</span><input class="login-input" type="text" placeholder="学号或教职工号" name="school-id"/></div>
                                    <div class="input-style"><span>密码：</span><input class="login-input" type="password" name="password"/></div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary login-btn-style" data-dismiss="modal" id="user-login-btn">登录</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="navbar-buttons navbar-header pull-right" role="navigation" style="display:none" id="after-login">
                    <ul class="nav ace-nav">
                        <!-- #section:basics/navbar.user_menu -->
                        <li class="light-blue">
                            <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                                <img class="nav-user-photo" src="../assets/avatars/avatar2.png"/>
                                <span class="user-info">
									<small>Welcome,</small>
									<span id="username-info"></span>       <!-- 显示管理员的用户名 -->
								</span>

                                <i class="ace-icon fa fa-caret-down"></i>
                            </a>

                            <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-closer">
                                <li class="divider"></li>

                                <li id="logout">
                                    <a href="#">
                                        <i class="ace-icon fa fa-power-off"></i>
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- /section:basics/navbar.user_menu -->
                    </ul>
                </div>
            </ul><!-- /.ace-nav -->
        </div><!-- /.navbar-header -->
    </div><!-- /.container -->
</div>

<div class="main-container" id="main-container">
    <script type="text/javascript">
        try{ace.settings.check('main-container' , 'fixed')}catch(e){}
    </script>

    <div class="main-container-inner">
        <a class="menu-toggler" id="menu-toggler" href="#">
            <span class="menu-text"></span>
        </a>

        <div class="sidebar" id="sidebar">
            <script type="text/javascript">
                try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
            </script>

            <div class="sidebar-shortcuts" id="sidebar-shortcuts">
                <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
                    <button class="btn btn-success">
                        <i class="icon-signal"></i>
                    </button>

                    <button class="btn btn-info">
                        <i class="icon-pencil"></i>
                    </button>

                    <button class="btn btn-warning">
                        <i class="icon-group"></i>
                    </button>

                    <button class="btn btn-danger">
                        <i class="icon-cogs"></i>
                    </button>
                </div>

                <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
                    <span class="btn btn-success"></span>

                    <span class="btn btn-info"></span>

                    <span class="btn btn-warning"></span>

                    <span class="btn btn-danger"></span>
                </div>
            </div><!-- #sidebar-shortcuts -->

            <ul class="nav nav-list">
                <li class="active">
                    <a href="index.html">
                        <i class="icon-home"></i>
                        <span class="menu-text"> 主页 </span>
                    </a>
                </li>

                <!--体育场馆介绍-->
                <li class="">
                    <a href="#" class="dropdown-toggle">
                        <i class="icon-dashboard"></i>
                        <span class="menu-text"> 体育场馆介绍 </span>
                        <b class="arrow icon-angle-down"></b>
                    </a>
                    <ul class="submenu">
                        <li class="">
                            <a href="#">
                                <i class="menu-icon icon-caret-right"></i>
                                综合体育馆
                            </a>
                           
                        </li>
                        <li class="">
                            <a href="#">
                                <i class="menu-icon icon-caret-right"></i>
                                中心校区
                            </a>
                           
                        </li>
                        <li class="">
                            <a href="#">
                                <i class="menu-icon icon-caret-right"></i>
                                洪家楼校区
                            </a>
                           
                        </li>
                        <li class="">
                            <a href="#">
                                <i class="menu-icon icon-caret-right"></i>
                                千佛山校区
                            </a>
                           
                        </li>
                        <li class="">
                            <a href="#">
                                <i class="menu-icon icon-caret-right"></i>
                                趵突泉校区
                            </a>
                           
                        </li>
                        <li class="">
                            <a href="#">
                                <i class="menu-icon icon-caret-right"></i>
                                兴隆山校区
                            </a>
                           
                        </li>
                        <li class="">
                            <a href="#">
                                <i class="menu-icon icon-caret-right"></i>
                                软件园校区
                            </a>
                           
                        </li>
                    </ul>
                </li><!--体育场馆介绍end-->


                <!--体育活动专栏-->
                <li class="">
                    <a href="#">
                        <i class="menu-icon icon-trophy"></i>
                        <span class="menu-text"> 体育活动专栏 </span>
                    </a>
                   
                </li><!--体育活动专栏end-->

                <!--大型活动专栏-->
                <li class="">
                    <a href="#">
                        <i class="menu-icon icon-list-alt"></i>
                        <span class="menu-text"> 大型活动专栏 </span>
                    </a>
                   
                </li><!--大型活动专栏end-->

                <!--场馆申请-->
                <li class="">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon icon-pencil"></i>
                        <span class="menu-text"> 场馆申请 </span>
                        <b class="arrow icon-angle-down"></b>
                    </a>
                    <ul class="submenu">
                        <li class="">
                            <a href="#">
                                <i class="menu-icon icon-caret-right"></i>
                                校内活动场地申请
                            </a>
                           
                        </li>
                        <li class="">
                            <a href="#">
                                <i class="menu-icon icon-caret-right"></i>
                                校外申请
                            </a>
                           
                        </li>
                    </ul>
                </li><!--场馆申请end-->

                <!--文件下载-->
                <li class="">
                    <a href="#">
                        <i class="menu-icon icon-cloud-download"></i>
                        <span class="menu-text"> 文件下载 </span>
                    </a>
                </li><!--文件下载end-->

                <!--留言板-->
                <li class="">
                    <a href="#">
                        <i class="menu-icon icon-comment-alt"></i>
                        <span class="menu-text"> 留言板 </span>
                    </a>
                </li><!--留言板end-->

                <!--管理员-->
                <li class="" style="">
                    <a href="#" class="dropdown-toggle" id="admin-show" {{--style="display: none;"--}}>
                        <i class="menu-icon icon-user"></i>
                        <span class="menu-text"> 管理员 </span>
                        <b class="arrow icon-angle-down"></b>
                    </a>
                    <ul class="submenu">
                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon icon-caret-right"></i>
                                新闻&公告管理
                                <b class="arrow icon-angle-down"></b>
                            </a>
                            <ul class="submenu">
                                <li class="">
                                    <a href="#">
                                        <i class="menu-icon icon-caret-right"></i>
                                        新闻&公告编辑
                                    </a>
                                </li>
                                <li class="">
                                    <a href="#">
                                        <i class="menu-icon icon-caret-right"></i>
                                        新闻&公告审核
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon icon-caret-right"></i>
                                场馆管理
                                <b class="arrow icon-angle-down"></b>
                            </a>
                           
                            <ul class="submenu">
                                <li class="">
                                    <a href="#">
                                        <i class="menu-icon icon-caret-right"></i>
                                        场馆开放表格编辑
                                    </a>
                                   
                                </li>
                                <li class="">
                                    <a href="#">
                                        <i class="menu-icon icon-caret-right"></i>
                                        设施保养表格编辑
                                    </a>
                                   
                                </li>
                            </ul>
                        </li>

                        <li class="">
                            <a href="#">
                                <i class="menu-icon icon-caret-right"></i>
                                账号权限
                            </a>
                           
                        </li>

                        <li class="">
                            <a href="#">
                                <i class="menu-icon icon-caret-right"></i>
                                留言板
                            </a>
                           
                        </li>

                        <li class="">
                            <a href="#">
                                <i class="menu-icon icon-caret-right"></i>
                                申请表格审核
                            </a>
                           
                        </li>

                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon icon-caret-right"></i>
                                器材管理
                                <b class="arrow icon-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="#">
                                        <i class="menu-icon icon-caret-right"></i>
                                        器材登记
                                    </a>

                                </li>
                                <li class="">
                                    <a href="#">
                                        <i class="menu-icon icon-caret-right"></i>
                                        器材查看
                                    </a>

                                </li>
                                <li class="">
                                    <a href="#">
                                        <i class="menu-icon icon-caret-right"></i>
                                        器材调入调出
                                    </a>

                                </li>
                            </ul>
                        </li>
                        <li class="">
                            <a href="#">
                                <i class="menu-icon icon-caret-right"></i>
                                上传文件
                            </a>

                        </li>
                    </ul>
                </li><!--管理员end-->
                <!--内务管理员-->
                <li class="" >
                    <a href="#" id="account-admin-show" {{--style="display: none;"--}}>
                        <i class="menu-icon icon-calendar"></i>
                        <span class="menu-text"> 内务管理 </span>
                    </a>
                </li><!--内务管理员end-->

            </ul><!-- /.nav-list -->

            <div class="sidebar-collapse" id="sidebar-collapse">
                <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
            </div>

            <script type="text/javascript">
                try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
            </script>
        </div>

        <div class="main-content">
            <div class="breadcrumbs" id="breadcrumbs">
                <script type="text/javascript">
                    try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
                </script>

                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home home-icon"></i>
                        <a href="#">Home</a>
                    </li>

                    <li>
                        <a href="#">Other Pages</a>
                    </li>
                    <li class="active">Blank Page</li>
                </ul><!-- .breadcrumb -->

                <div class="nav-search" id="nav-search">
                    <form class="form-search">
								<span class="input-icon">
									<input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
									<i class="icon-search nav-search-icon"></i>
								</span>
                    </form>
                </div><!-- #nav-search -->
            </div>

            {{--主页内容--}}
            <div class="page-content">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div><!-- /.main-content -->
    </div><!-- /.main-container-inner -->

    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
        <i class="icon-double-angle-up icon-only bigger-110"></i>
    </a>
</div><!-- /.main-container -->

<!-- basic scripts -->

<!--[if !IE]> -->

<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

<!-- <![endif]-->

<!--[if IE]>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<![endif]-->

<!--[if !IE]> -->

<script type="text/javascript">
    window.jQuery || document.write("<script src='assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
</script>

<!-- <![endif]-->

<!--[if IE]>
<script type="text/javascript">
    window.jQuery || document.write("<script src='assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

<script type="text/javascript">
    if("ontouchend" in document) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
</script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/typeahead-bs2.min.js"></script>

<!-- page specific plugin scripts -->

<!-- ace scripts -->

<script src="assets/js/ace-elements.min.js"></script>
<script src="assets/js/ace.min.js"></script>

<!-- inline scripts related to this page -->
<div style="display:none"><script src='http://v7.cnzz.com/stat.php?id=155540&web_id=155540' language='JavaScript' charset='gb2312'></script></div>
</body>
</html>