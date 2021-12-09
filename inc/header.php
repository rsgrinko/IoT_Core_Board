<?php
/**
*	Данный файл входит в состав системы IoT Core System
*	Разработчик: Роман Сергеевич Гринько
*	E-mail: rsgrinko@gmail.com
*	Сайт: https://it-stories.ru
*/

/**
 * Защита от запуска файла без подключения ядра
 */
if(!defined('CORE_LOADED') or CORE_LOADED !== true) {
    die();
}

if(!CUser::is_user() and $_SERVER['REQUEST_URI'] !== '/login.php') {
	header("Location: login.php");
	die();
}
?>
<!DOCTYPE html>

<html class="app-ui">

    <head>
        <!-- Meta -->
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />

        <!-- Document title -->
        <title>IoT Core System - Панель управления</title>

        <meta name="description" content="AppUI - Admin Dashboard Template & UI Framework" />
        <meta name="author" content="rustheme" />
        <meta name="robots" content="noindex, nofollow" />

        <script>
            var userId = "<?php echo $USER['id']; ?>";
            var userLogin = "<?php echo $USER['login']; ?>";
            var phpVars = {
                            'userId': '<?php echo $USER['id']; ?>',
                            'userLogin': '<?php echo $USER['login']; ?>',
                            'date': '<?php echo date("d.m.Y H:i:s"); ?>',
                        };
            var arrUser = <?php echo CJson::create($USER); ?>            
        </script>
        
        <!-- Favicons -->
        <link rel="apple-touch-icon" href="assets/img/favicons/apple-touch-icon.png" />
        <link rel="icon" href="assets/img/favicons/favicon.ico" />

        <!-- Google fonts -->
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,400italic,500,900%7CRoboto+Slab:300,400%7CRoboto+Mono:400" />

        <!-- Page JS Plugins CSS -->
        <link rel="stylesheet" href="assets/js/plugins/slick/slick.min.css" />
        <link rel="stylesheet" href="assets/js/plugins/slick/slick-theme.min.css" />

        <!-- AppUI CSS stylesheets -->
        <link rel="stylesheet" id="css-font-awesome" href="assets/css/font-awesome.css" />
        <link rel="stylesheet" id="css-ionicons" href="assets/css/ionicons.css" />
        <link rel="stylesheet" id="css-bootstrap" href="assets/css/bootstrap.css" />
        <link rel="stylesheet" id="css-app" href="assets/css/app.css" />
        <link rel="stylesheet" id="css-app-custom" href="assets/css/app-custom.css" />

        <!-- End Stylesheets -->

        <!-- AppUI Core JS: jQuery, Bootstrap, slimScroll, scrollLock and App.js -->
        <script src="assets/js/core/jquery.min.js"></script>
        <script src="assets/js/core/bootstrap.min.js"></script>
        <script src="assets/js/core/jquery.slimscroll.min.js"></script>
        <script src="assets/js/core/jquery.scrollLock.min.js"></script>
        <script src="assets/js/core/jquery.placeholder.min.js"></script>
        <script src="assets/js/app.js"></script>
        <script src="assets/js/app-custom.js"></script>

        <!-- Page Plugins -->
        <script src="assets/js/plugins/slick/slick.min.js"></script>
        <script src="assets/js/plugins/chartjs/Chart.min.js"></script>
        <script src="assets/js/plugins/flot/jquery.flot.min.js"></script>
        <script src="assets/js/plugins/flot/jquery.flot.pie.min.js"></script>
        <script src="assets/js/plugins/flot/jquery.flot.stack.min.js"></script>
        <script src="assets/js/plugins/flot/jquery.flot.resize.min.js"></script>

        <script src="assets/js/plugins/jquery-ui/jquery-ui.min.js"></script>

		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/modules/exporting.js"></script>
		<script src="https://code.highcharts.com/modules/export-data.js"></script>
		<script src="https://code.highcharts.com/modules/accessibility.js"></script>

		<link rel="stylesheet" id="css-app-custom" href="assets/css/custom.css" />

        <script>
                $(document).ready(function () {
                    setInterval(function () {
                        $.ajax({
                            url: "ajax/keepAlive.php",
                            data: {
                                userId: userId,
                            },
                            success: function (data) {
                                if(data.status !== 'ok') {
                                    document.location.href = "login.php";
                                }
                            },
                            dataType: "json"
                        });
                    }, 5000);
                });
        </script>

    </head>

    <body class="app-ui layout-has-drawer layout-has-fixed-header">
        <div class="app-layout-canvas">
            <div class="app-layout-container">

                <!-- Drawer -->
                <aside class="app-layout-drawer">

                    <!-- Drawer scroll area -->
                    <div class="app-layout-drawer-scroll">
                        <!-- Drawer logo -->
                        <div id="logo" class="drawer-header">
	                        <span class="discoBall"></span>
                            <a href="index.php"><img class="img-responsive" src="assets/img/logo/logo-backend.png" title="AppUI" alt="AppUI" /></a>
                        </div>

                        <!-- Drawer navigation -->
                        <nav class="drawer-main">
                            <ul class="nav nav-drawer">

                                <li class="nav-item nav-drawer-header">Основное</li>

                                <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '';?>">
                                    <a href="<?php echo HOME; ?>/"><i class="ion-ios-speedometer"></i> Мониторинг</a>
                                </li>

                                <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'control.php' ? 'active' : '';?>">
                                    <a href="<?php echo HOME; ?>/control.php"><i class="ion-ios-toggle"></i> Управление</a>
                                </li>

                                <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'board.php' ? 'active' : '';?>">
                                    <a href="<?php echo HOME; ?>/board.php"><i class="ion-android-settings"></i> Конфигурация</a>
                                </li>

                                <li class="nav-item nav-drawer-header">Компоненты</li>

								<?php if(CUSer::is_admin()): ?>
								<li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : '';?>">
                                    <a href="<?php echo HOME; ?>/events.php"><i class="fa fa-list"></i> Журнал событий</a>
								</li>

                                <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'boardlist.php' ? 'active' : '';?>">
                                    <a href="<?php echo HOME; ?>/boardlist.php"><i class="fa fa-wifi"></i> Устройства</a>
								</li>

								<li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '';?>">
                                    <a href="<?php echo HOME; ?>/users.php"><i class="fa fa-user"></i> Пользователи</a>
								</li>

                                <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'mailings.php') ? 'active' : '';?>">
                                    <a href="<?php echo HOME; ?>/mailings.php"><i class="fa fa-tasks"></i> Почтовые рассылки</a>
								</li>

								<li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'cron.php' or basename($_SERVER['PHP_SELF']) == 'add_cron.php' or basename($_SERVER['PHP_SELF']) == 'edit_cron.php') ? 'active' : '';?>">
                                    <a href="<?php echo HOME; ?>/cron.php"><i class="fa fa-tasks"></i> Планировщик</a>
								</li>

								<li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'sql.php') ? 'active' : '';?>">
                                    <a href="<?php echo HOME; ?>/sql.php"><i class="fa fa-database"></i> SQL запросы</a>
								</li>
								<?php endif; ?>


								<li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'firmware_list.php' ? 'active' : '';?>">
                                    <a href="<?php echo HOME; ?>/firmware_list.php"><i class="fa fa-upload"></i> Прошивки</a>
								</li>




                                <li class="nav-item nav-item-has-subnav">
                                    <a href="javascript:void(0)"><i class="ion-ios-calculator-outline"></i> UI Elements</a>
                                    <ul class="nav nav-subnav">

                                        <li>
                                            <a href="base_ui_buttons.html">Buttons</a>
                                        </li>

                                        <li>
                                            <a href="base_ui_cards.html">Cards</a>
                                        </li>

                                        <li>
                                            <a href="base_ui_cards_api.html">Cards API</a>
                                        </li>

                                        <li>
                                            <a href="base_ui_layout.html">Layout</a>
                                        </li>

                                        <li>
                                            <a href="base_ui_grid.html">Grid</a>
                                        </li>

                                        <li>
                                            <a href="base_ui_icons.html">Icons</a>
                                        </li>

                                        <li>
                                            <a href="base_ui_modals_tooltips.html">Modals / Tooltips</a>
                                        </li>

                                        <li>
                                            <a href="base_ui_alerts_notify.html">Alerts / Notify</a>
                                        </li>

                                        <li>
                                            <a href="base_ui_pagination.html">Pagination</a>
                                        </li>

                                        <li>
                                            <a href="base_ui_progress.html">Progress</a>
                                        </li>

                                        <li>
                                            <a href="base_ui_tabs.html">Tabs</a>
                                        </li>

                                        <li>
                                            <a href="base_ui_typography.html">Typography</a>
                                        </li>

                                        <li>
                                            <a href="base_ui_widgets.html">Widgets</a>
                                        </li>

                                    </ul>
                                </li>

                                <li class="nav-item nav-item-has-subnav">
                                    <a href="javascript:void(0)"><i class="ion-ios-compose-outline"></i> Forms</a>
                                    <ul class="nav nav-subnav">

                                        <li>
                                            <a href="base_forms_elements.html">Elements</a>
                                        </li>

                                        <li>
                                            <a href="base_forms_samples.html">Samples</a>
                                        </li>

                                        <li>
                                            <a href="base_forms_pickers_select.html">Pickers &amp; Select</a>
                                        </li>

                                        <li>
                                            <a href="base_forms_validation.html">Validation</a>
                                        </li>

                                        <li>
                                            <a href="base_forms_wizard.html">Wizard</a>
                                        </li>

                                    </ul>
                                </li>

                                <li class="nav-item nav-item-has-subnav">
                                    <a href="javascript:void(0)"><i class="ion-ios-list-outline"></i> Tables</a>
                                    <ul class="nav nav-subnav">

                                        <li>
                                            <a href="base_tables_styles.html">Styles</a>
                                        </li>

                                        <li>
                                            <a href="base_tables_responsive.html">Responsive</a>
                                        </li>

                                        <li>
                                            <a href="base_tables_tools.html">Tools</a>
                                        </li>

                                        <li>
                                            <a href="base_tables_pricing.html">Pricing</a>
                                        </li>

                                        <li>
                                            <a href="base_tables_datatables.html">Wizard</a>
                                        </li>

                                    </ul>
                                </li>

                                <li class="nav-item nav-item-has-subnav">
                                    <a href="javascript:void(0)"><i class="ion-ios-browsers-outline"></i> Pages</a>
                                    <ul class="nav nav-subnav">

                                        <li>
                                            <a href="base_pages_blank.html">Blank</a>
                                        </li>

                                        <li>
                                            <a href="base_pages_inbox.html">Inbox</a>
                                        </li>

                                        <li>
                                            <a href="base_pages_invoice.html">Invoice</a>
                                        </li>

                                        <li>
                                            <a href="base_pages_profile.html">Profile</a>
                                        </li>

                                        <li>
                                            <a href="base_pages_search.html">Search</a>
                                        </li>

                                    </ul>
                                </li>

                                <li class="nav-item nav-item-has-subnav">
                                    <a href="javascript:void(0)"><i class="ion-social-javascript-outline"></i> JS plugins</a>
                                    <ul class="nav nav-subnav">

                                        <li>
                                            <a href="base_js_maps.html">Maps</a>
                                        </li>

                                        <li>
                                            <a href="base_js_sliders.html">Sliders</a>
                                        </li>

                                        <li>
                                            <a href="base_js_charts_flot.html">Charts - Flot</a>
                                        </li>

                                        <li>
                                            <a href="base_js_charts_chartjs.html">Charts - Chart.js</a>
                                        </li>

                                        <li>
                                            <a href="base_js_charts_sparkline.html">Charts - Sparkline</a>
                                        </li>

                                        <li>
                                            <a href="base_js_draggable.html">Draggable</a>
                                        </li>

                                        <li>
                                            <a href="base_js_syntax_highlight.html">Syntax highlight</a>
                                        </li>

                                    </ul>
                                </li>

                            </ul>
                        </nav>
                        <!-- End drawer navigation -->

                        <div class="drawer-footer">
                            <p class="copyright">Developed by</p>
                            <a href="https://it-stories.ru/about/" target="_blank">Roman S Grinko</a>
                        </div>
                    </div>
                    <!-- End drawer scroll area -->
                </aside>
                <!-- End drawer -->

                <!-- Header -->
                <header class="app-layout-header">
                    <nav class="navbar navbar-default">
                        <div class="container-fluid">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-navbar-collapse" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
                                <button class="pull-left hidden-lg hidden-md navbar-toggle" type="button" data-toggle="layout" data-action="sidebar_toggle">
					<span class="sr-only">Toggle drawer</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
                                <span class="navbar-page-title">IoT Core Control</span>
                            </div>

                            <div class="collapse navbar-collapse" id="header-navbar-collapse">


                                <ul id="main-menu" class="nav navbar-nav navbar-left">
                                    <li class="dropdown">
                                        <a href="#" data-toggle="dropdown">Устройства <span class="caret"></span></a>

                                        <ul class="dropdown-menu">
	                                        <?php
                                                $userDevices = getUserDevices($USER['id']);
		                                        foreach($userDevices as $device):
	                                        ?>
                                            	<li><a href="boardinfo.php?id=<?php echo $device['id']; ?>">MAC: <?php echo $device['mac'];?>, HW: <?php echo $device['hw'];?>, FW: <?php echo $device['fw'];?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" data-toggle="dropdown">Профиль <span class="caret"></span></a>

                                        <ul class="dropdown-menu">
                                            <li><a href="editprofile.php">Редактировать профиль</a></li>
                                            <li><a href="logout.php">Выход</a></li>
                                            <li><a href="javascript:void(0)">Visits</a></li>
                                            <li><a href="javascript:void(0)">Changelog</a></li>
                                        </ul>
                                    </li>
                                </ul>
                                <!-- .navbar-left -->

                                <ul class="nav navbar-nav navbar-right navbar-toolbar hidden-sm hidden-xs">
                                    <li>
                                        <!-- Opens the modal found at the bottom of the page -->
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#apps-modal"><i class="ion-grid"></i></a>
                                    </li>

                                    <li class="dropdown">
                                        <a href="javascript:void(0)" data-toggle="dropdown"><i class="ion-ios-bell"></i> <span class="badge">3</span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li class="dropdown-header">Profile</li>
                                            <li>
                                                <a tabindex="-1" href="javascript:void(0)"><span class="badge pull-right">3</span> News </a>
                                            </li>
                                            <li>
                                                <a tabindex="-1" href="javascript:void(0)"><span class="badge pull-right">1</span> Messages </a>
                                            </li>
                                            <li class="divider"></li>
                                            <li class="dropdown-header">More</li>
                                            <li>
                                                <a tabindex="-1" href="javascript:void(0)">Edit Profile..</a>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="dropdown dropdown-profile">
                                        <a href="javascript:void(0)" data-toggle="dropdown">
                                            <span class="m-r-sm"><?php echo $USER['name']; ?> <span class="caret"></span></span>
                                            <img class="img-avatar img-avatar-48" src="<?php echo $USER['image']!=='' ? $USER['image'] : 'assets/img/avatars/avatar3.jpg'; ?>" alt="User profile pic" />
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li class="dropdown-header">
                                                Pages
                                            </li>
                                            <li>
                                                <a href="base_pages_profile.html">Профиль</a>
                                            </li>
                                            <li>
                                                <a href="base_pages_profile.html"><span class="badge badge-success pull-right">3</span> Blog</a>
                                            </li>
                                            <li>
                                                <a href="logout.php?">Выход</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                                <!-- .navbar-right -->
                            </div>
                        </div>
                        <!-- .container-fluid -->
                    </nav>
                    <!-- .navbar-default -->
                </header>
                <!-- End header -->

                <main class="app-layout-content">
<div class="container-fluid p-y-md">