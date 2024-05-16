<!DOCTYPE html>
<html lang="ru">

<head>

      <meta http-equiv="content-type" content="text/html; charset=UTF8">

      <title>Почта&nbsp;&bull;&nbsp;Корпоративный портал АО АТГС</title>

      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="">
      <meta name="keywords" content="">
      <meta name="author" content="">

      <!-- FAVICON -->
      <link rel="icon" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/favicon.ico"> <!-- 32×32 -->
      <link rel="manifest" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/manifest.webmanifest">
      <link rel="apple-touch-icon" sizes="180x180" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/images/favicons/appletouch-180x180.png">
      <link rel="icon" type="image/png" sizes="64x64" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/images/favicons/favicon.ico">
      <link rel="icon" type="image/png" sizes="32x32" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/images/favicons/favicon.ico">
      <link rel="icon" type="image/png" sizes="16x16" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/images/favicons/favicon.ico">
      <link rel="mask-icon" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/images/safari-pinned-tab.svg" color="#5bbad5">

      <!-- Jquery, Datatables & Bootstrap >>>>> -->
      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Bootstrap/Bootstrap-4.6.2/css/bootstrap.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/DataTables-1.13.3/css/dataTables.bootstrap4.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Buttons-2.3.5/css/buttons.bootstrap4.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/DateTime-1.3.1/css/dataTables.dateTime.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap5.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Select-1.6.1/css/select.bootstrap4.css" rel="stylesheet">

      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Editor-PHP-1.9.7/css/editor.bootstrap4.min.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Editor-PHP-1.9.7/css/editor.dataTables.min.css" rel="stylesheet">


      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Other/Select2/4.1.0/css/select2.min.css" rel="stylesheet">

      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Other/Confirm/3.3.4/jquery-confirm.min.css" rel="stylesheet">

      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Bootstrap/Bootstrap-4.6.2/js/bootstrap.bundle.js">
      </script>
      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/DataTables-1.13.3/js/jquery.dataTables.js">
      </script>
      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/DataTables-1.13.3/js/dataTables.bootstrap4.js">
      </script>
      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Buttons-2.3.5/js/dataTables.buttons.js">
      </script>
      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Buttons-2.3.5/js/buttons.bootstrap4.js">
      </script>
      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Buttons-2.3.5/js/buttons.html5.js">
      </script>
      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Buttons-2.3.5/js/buttons.print.js">
      </script>
      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/DateTime-1.3.1/js/dataTables.dateTime.js">
      </script>
      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.js">
      </script>
      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Responsive-2.4.0/js/responsive.bootstrap4.js">
      </script>
      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Select-1.6.1/js/dataTables.select.js">
      </script>
      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Editor-PHP-1.9.7/js/dataTables.editor.js">
      </script>
      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Datatables/Editor-PHP-1.9.7/js/editor.bootstrap4.min.js">
      </script>
      <!-- <<<<< Jquery, Datatables & Bootstrap -->


      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Other/Select2/4.1.0/js/select2.full.min.js">
      </script>

      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Moment/moment-with-locales.js">
      </script>

      <script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Other/Confirm/3.3.4/jquery-confirm.min.js">
      </script>


      <!-- ICONS >>>>> -->
      <!-- our project just needs Font Awesome Solid + Brands -->
      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/css/icons/fontawesome6/css/fontawesome.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/css/icons/fontawesome6/css/brands.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/css/icons/fontawesome6/css/solid.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/css/icons/fontawesome6/css/regular.css" rel="stylesheet">
      <!-- <<<<< ICONS -->

      <link rel="stylesheet" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/css/style.css">
      <link rel="stylesheet" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/css/fonts-import.css">

      <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/RobinHerbots/jquery.inputmask@5.0.0-beta.87/css/inputmask.css">

      <!-- Latest compiled JavaScript -->
      <script type="text/javascript" language="javascript" class="init" src="https://cdn.jsdelivr.net/gh/RobinHerbots/jquery.inputmask@5.0.0-beta.87/dist/jquery.inputmask.min.js">
      </script>

      <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
      <!--[if lt IE 9]>
		<script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/js/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/js/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

      <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/css/navbar-main.css" rel="stylesheet">



      <link href="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW; ?>/_assets/css/mailnew.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW; ?>/_assets/css/messages-list.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW; ?>/_assets/css/messages-push.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW; ?>/_assets/css/modals.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW; ?>/_assets/css/tooltips.css" rel="stylesheet">
      <link href="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW; ?>/_assets/css/popovers.css" rel="stylesheet">
</head>

<body>
      <div id="servicename-mailnew">
            <!-- Блок, который будет отображаться над страницей -->
            <div id="before-load">
                  <!-- Иконка Font Awesome -->
                  <!-- 	<i class="fa fa-spinner fa-spin"></i> -->
                  <i></i>
            </div>

            <?php
            // include($_SERVER['DOCUMENT_ROOT'] . "/mailnew/_assets/menu.inc/menu-top.php"); 
            require("mailnew-navbar.php");
            ?>

            <div class="body">
                  <div class="vh-content">