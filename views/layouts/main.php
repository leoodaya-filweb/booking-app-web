<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/aos.css">
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= Url::to('/dashboard')?>">
                
                <div class="sidebar-brand-text mx-3">Booking App</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">


            <?php
            
                if(Yii::$app->user->isGuest){

                    echo '
                        
                          
                            <li class="nav-item active">
                                <a class="nav-link" href="' . Url::to(["bookings/bookings"]) . '">
                                    <i class="fas fa-book"></i>
                                    <span>Booked Now</span></a>
                            </li>
                          
                              
                           
                        
                        ';
                    
                } else {

                    if(Yii::$app->user->identity->role_id <= 2){
                        echo '
                        
                          
                            <li class="nav-item active">
                                <a class="nav-link" href="' . Url::to(["site/dashboard"]) . '">
                                    <i class="fas fa-fw fa-tachometer-alt"></i>
                                    <span>Dashboard</span></a>
                            </li>

                            <!-- Divider -->
                            <hr class="sidebar-divider">

                           
                            
                             <!-- Nav Item - Utilities Collapse Menu -->
                            <li class="nav-item">
                                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                                    aria-expanded="true" aria-controls="collapseUtilities">
                                    <i class="fas fa-fw fa-user"></i>
                                    <span>Users</span>
                                </a>
                                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                                    data-parent="#accordionSidebar">
                                    <div class="bg-white py-2 collapse-inner rounded">
                                        <h6 class="collapse-header">Manage Users</h6>
                                        <a class="collapse-item" href="' . Url::to(["user/index"]) . '">Users</a>
                                        <a class="collapse-item" href="' . Url::to(["roles/index"]) . '">Roles</a>
                                        <a class="collapse-item" href="' . Url::to(["permissions/index"]) . '">Permissions</a>
                                       
                                    </div>
                                </div>
                            </li>

                           
                            <li class="nav-item">
                                <a class="nav-link" href="' . Url::to(["bookings/index"]) . '">
                                    <i class="fas fa-fw fa-calendar"></i>
                                    <span>Bookings</span></a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="' . Url::to(["rooms/index"]) . '">
                                    <i class="fas fa-fw fa-calendar"></i>
                                    <span>Rooms</span></a>
                            </li>
                            

                           

                            <!-- Nav Item - Utilities Collapse Menu -->
                            <li class="nav-item">
                                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#potekadropDown"
                                    aria-expanded="true" aria-controls="collapseUtilities">
                                    <i class="fas fa-fw fa-user"></i>
                                    <span>Poteka API</span>
                                </a>
                                <div id="potekadropDown" class="collapse" aria-labelledby="headingUtilities"
                                    data-parent="#accordionSidebar">
                                    <div class="bg-white py-2 collapse-inner rounded">
                                        <h6 class="collapse-header">Manage Poteka</h6>
                                        <a class="collapse-item" href="' . Url::to(["site/rectangle"]) . '">Rectangle Area</a>
                                       

                                       
                                       
                                    </div>
                                </div>
                            </li>
                        
                        ';
                    }
                    else{

                        echo '
                        
                          
                            <li class="nav-item active">
                                <a class="nav-link" href="' . Url::to(["bookings/bookings"]) . '">
                                    <i class="fas fa-book"></i>
                                    <span>Booked Now</span></a>
                            </li>

                             <hr class="sidebar-divider">
                           
                            <li class="nav-item">
                                <a class="nav-link" href="' . Url::to(["bookings/my-bookings"]) . '">
                                    <i class="fas fa-fw fa-calendar"></i>
                                    <span>My Bookings</span></a>
                            </li>
                          
                            
                        
                        ';

                        


                    }
                   
                }
            
            
            ?>
            


            
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                       

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow d-flex gap-2">

                            <?php 
                                if(Yii::$app->user->isGuest){
                                    echo '
                                  

                                        <a class="nav-link text-dark fw-bold" href="' . Url::to(["site/login"]) . '" 
                                            >
                                             <span>Login</span></a>
                                        </a>
                                        <a class="nav-link text-dark fw-bold" href="' . Url::to(["site/signup"]) . '" 
                                            >
                                             <span>Signup</span></a>
                                        </a>
                                    ';
                                }
                                else{
                                    echo '
                                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">' . (Yii::$app->user->identity->name ?? "") . '</span>
                                        <img class="img-profile rounded-circle"
                                            src="img/undraw_profile.svg">
                                    </a>
                                    ';
                                }
                            ?>
                            
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= Url::to(['user/profile'])?>">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="<?= Url::to(['user/change-password'])?>">
                                    <i class="fas fa-lock fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Change Password
                                </a>
                              
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                                <!-- End of Topbar -->
                <!-- Begin Page Content -->
                <div class="container-fluid">
                <?php if (!empty($this->params['breadcrumbs'])): ?>
                    <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
                <?php endif ?>
                <?= Alert::widget() ?>
                    <?= $content ?>
                </div>

                </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Booking App 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form method="post" action="<?= Url::to(['site/logout']) ?>">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

 
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->

    <script src="js/sb-admin-2.min.js"></script>
   
    <script src="js/aos.js"></script>

    <script>
        AOS.init();
    </script>



<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
