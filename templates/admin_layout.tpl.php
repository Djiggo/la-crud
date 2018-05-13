<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Автожители | Админ панель</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="/assets/admin/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/assets/admin/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="/assets/admin/dist/css/skins/_all-skins.min.css">

    <link rel="stylesheet" href="/assets/admin/plugins/iCheck/flat/blue.css">
    <link rel="stylesheet" href="/assets/admin/plugins/morris/morris.css">
    <link rel="stylesheet" href="/assets/admin/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <link rel="stylesheet" href="/assets/admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

    <link href="/assets/admin/dist/css/jquery-ui.css" rel="stylesheet">
    <link href="/assets/admin/dist/css/jquery-ui.theme.css" rel="stylesheet">
    <link href="/assets/admin/dist/css/colorbox.css" rel="stylesheet">
    <link href="/assets/admin/dist/css/pqselect.min.css" rel="stylesheet">

    <script src="/assets/admin/plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="/assets/admin/dist/js/jquery.ui.js"></script>
    <script>
        $.widget.bridge('uibutton', $.ui.button);
    </script>
    <script src="/assets/admin/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="/assets/admin/plugins/morris/morris.min.js"></script>
    <script src="/assets/admin/plugins/sparkline/jquery.sparkline.min.js"></script>
    <script src="/assets/admin/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="/assets/admin/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="/assets/admin/plugins/knob/jquery.knob.js"></script>
    <script src="/assets/admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <script src="/assets/admin/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script src="/assets/admin/plugins/fastclick/fastclick.js"></script>

    <script src="/assets/admin/dist/js/app.min.js"></script>
    <script src="/assets/admin/dist/js/main.js"></script>
    <script src="/assets/admin/dist/js/pqselect.min.js"></script>
    <script src="/assets/admin/dist/js/jquery.colorbox-min.js"></script>
    <script src="/assets/admin/plugins/tinymce/tinymce.min.js"></script>

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <div class="logo">
            <span class="logo-mini"><b>A</b>LT</span>
            <span class="logo-lg"><b>Автожители</b></span>
        </div>
        <nav class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li><a href="/admin">Консоль</a></li>
                    <li><a href="/">На сайт</a></li>

                </ul>
            </div>
        </nav>
    </header>
    <?php
    $current_user = \Auto\User\UserFactory::getCurrentUser();
    ?>
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <?php
                    if ($current_user->image) {
                        $img_url = \Core\ImageCache::getThumbUrl($current_user->image, 160, 160);
                    } else {
                        $img_url = '/assets/images/user_nophoto.png';

                    } ?>
                    <img src="<?= $img_url ?>" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p><?= $current_user->last_name ?> <?= $current_user->name ?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>

            <!-- sidebar menu: : style can be found in sidebar.less -->

            <ul class="sidebar-menu">

                <?php if ($current_user->isCanForRight("edit_users")) : ?>
                    <li class="treeview"><a href="#"><i class="fa fa-users"></i>
                            <span>Пользователи</span><i class="fa fa-angle-left pull-right"></i></a>
                        <ul class="treeview-menu">
                            <li><a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\User\User::class) ?>">Все
                                    пользователи</a></li>
                            <?php if ($current_user->isCanForRight("edit_users")) : ?>
                                <li><a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\User\BanUser::class) ?>">Баны
                                        юзеров</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ($current_user->isCanForRight("edit_cars")) : ?>
                    <li class="treeview"><a href="#"><i class="fa fa-car"></i>
                            <span>Автомобили</span><i class="fa fa-angle-left pull-right"></i></a>
                        <ul class="treeview-menu">
                            <li><a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\Car\Brand::class) ?>">Бренды</a></li>
                            <li><a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\Car\Model::class) ?>">Модели </a>
                            </li>
                            <li><a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\Car\Mod::class) ?>">Модификации </a>
                            </li>
                            <li>
                                <a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\Car\Build::class) ?>">Комплектации </a>
                            </li>
                            <li><a href="/admin/parseimg"> Поиск фотографий </a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (Auto\User\UserHelper::isUserCanRightFor($current_user->getId(), \Auto\User\Constants::PERMISSION_EDIT_GROUPS)): ?>
                    <li class="treeview">
                        <a href="#"> <i class="fa fa-group "></i>
                            <span>Группы</span><i class="fa fa-angle-left pull-right"></i> </a>
                        <ul class="treeview-menu">
                            <li><a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\Group\Group::class) ?>">Все
                                    группы</a></li>
                            <li>
                                <a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\Group\Event::class) ?>">События</a>
                            </li>
                            <li>
                                <a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\Group\EventGallery::class) ?>">Галерея
                                    событий</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ($current_user->isCanForRight("edit_blocks")) : ?>
                    <li><a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\Block\Block::class) ?>">
                            <i class="fa fa-th-large"></i>
                            <span>Блоки</span> </a></li>
                <?php endif; ?>
                <?php if ($current_user->isCanForRight("edit_pages")) : ?>
                    <li><a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\Page\Page::class) ?>">
                            <i class="fa fa-file-text"></i>
                            <span>Страницы</span> </a>
                    </li>
                <?php endif; ?>
                <?php if ($current_user->isCanForRight("edit_news")) : ?>
                    <li class="treeview">
                        <a href="#"> <i class="fa fa-newspaper-o"></i>
                            <span>Новости</span><i class="fa fa-angle-left pull-right"></i> </a>
                        <ul class="treeview-menu">
                            <li><a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\News\News::class) ?>">Все новости</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="/admin/crud/list/Auto.News.NewsTaxonomy">
                            <i class="fa fa-book"></i>
                            <span>Таксономии</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($current_user->isCanForRight("edit_comments")) : ?>
                    <li class="treeview">
                        <a href="#"> <i class="fa fa-comments"></i>
                            <span>Комментарии</span><i class="fa fa-angle-left pull-right"></i> </a>
                        <ul class="treeview-menu">
                            <li><a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\Comments\Comment::class) ?>">Все
                                    комментарии</a></li>
                            <li>
                                <a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\Comments\Thread::class) ?>">Потоки</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="<?= \Admin\CRUD\Helpers::getListUrl(\Auto\Redirect\Redirect::class) ?>">
                        <i class="fa fa-link"></i>
                        <span>Редиректы</span> </a>
                </li>


            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <div class="content-wrapper">
        <section class="content">
            <?= $layout_content ?>
        </section>
    </div>
</div>
<script>
    (function ($) {
        var active_menu = $(".sidebar-menu a[href='" + location.pathname + "']");
        active_menu.closest("ul.treeview-menu").addClass("menu-open").show();
        active_menu.closest(".sidebar-menu > li").addClass("active");
        active_menu.closest("li").addClass("active");
    })(jQuery);
</script>
</body>
</html>
