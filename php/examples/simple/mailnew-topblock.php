<?php
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
#
$classActive_main = "";
$classActive_in = "";
$classActive_out = "";
$classActive_archin = "";
$classActive_archout = "";
$classActive_profile = "";
$classActive_settings = "";
$classActive_help = "";
$classActive_devlog = "";
if (isset($_GET['type'])) {
  if ($_GET['type'] == 'in' && isset($_GET['mode']) && $_GET['mode'] == 'archive') {
    $classActive_archin = " active";
  } elseif ($_GET['type'] == 'out' && isset($_GET['mode']) && $_GET['mode'] == 'archive') {
    $classActive_archout = " active";
  } elseif ($_GET['type'] == 'in') {
    if (isset($_GET['mode']) && $_GET['mode'] == 'profile') {
      $classActive_profile = " active";
    } else {
      $classActive_in = " active";
    }
  } elseif ($_GET['type'] == 'out') {
    if (isset($_GET['mode']) && $_GET['mode'] == 'profile') {
      $classActive_profile = " active";
    } else {
      $classActive_out = " active";
    }
  } elseif ($_GET['type'] == 'main') {
    $classActive_main = " active";
  } elseif ($_GET['type'] == 'settings') {
    $classActive_settings = " active";
  } elseif ($_GET['type'] == 'help') {
    $classActive_help = " active";
  } elseif ($_GET['type'] == 'devlog') {
    $classActive_devlog = " active";
  } else {
    $classActive_main = " active";
    $classActive_in = "";
    $classActive_out = "";
    $classActive_archin = "";
    $classActive_archout = "";
    $classActive_profile = "";
    $classActive_settings = "";
    $classActive_help = "";
    $classActive_devlog = "";
  }
} else {
  if (isset($_GET['mode']) && $_GET['mode'] == 'profile') {
    $classActive_profile = " active";
  } else {
    $classActive_main = " active";
  }
}
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
?>


<?php
/**
 * * Выбираем тему оформления
 * @param use_lightTheme = 1 Активна светлая тема оформления
 * @param use_lightTheme = 0 Активна темная тема оформления (по умолчанию) 
 * 
 */
if ($use_lightTheme === '1') {
  $navbarService_style = "background-color:#F1F1F1 !important";
?>
  <style>
    div.navbar-service-container {
      background-color: #F1F1F1 !important;
      margin-top: 110px;
      border-bottom-left-radius: 2rem;
      border-bottom-right-radius: 2rem;
      padding: 0.5rem 1.0rem !important;
      /* box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important; */
    }

    nav#navbar-service.navbar {
      border-bottom-left-radius: 1rem;
      border-bottom-right-radius: 1rem;
    }



    nav#navbar-service .navbar-brand,
    nav#nav-submenu .navbar-brand {
      font-size: 1rem;
    }

    nav#navbar-service.navbar a.navbar-brand {
      line-height: 1em;
      margin-left: 0;
      margin-right: 0;
    }

    nav#navbar-service.navbar a.navbar-brand:hover {
      text-decoration: none;
    }

    nav#navbar-service.navbar a.navbar-brand span.navb-line1 {
      color: #111111 !important;
      font-family: 'Oswald', sans-serif;
      font-size: 1rem;
      font-weight: 200;
      text-transform: none;
    }

    nav#navbar-service.navbar .nav-item a.nav-link {
      color: #111111 !important;
      font-family: 'Stolzl Book', sans-serif;
      font-size: 1.0rem;
      /* border: 1px solid transparent; */
      /* border-radius: 8px; */
    }

    nav#navbar-service.navbar .nav-item.active a.nav-link {
      color: #FFFFFF !important;
      border-radius: 8px;
      background-color: #0178FA;
    }

    nav#nav-submenu.navbar .nav-item.active a.nav-link {
      color: #FFFFFF !important;
      outline: 1px solid #0178FA;
      outline-offset: -1px;
      border-radius: 8px;
    }

    nav#nav-submenu.navbar a.navbar-brand span.navb-line2 {
      color: #333;
      font-family: 'Stolzl Book', sans-serif;
      font-size: 0.85rem;
      font-weight: 400;
      text-transform: none;
    }

    nav#nav-submenu.navbar a.nav-link {
      color: #333;
      font-family: 'Stolzl Book', sans-serif;
      font-size: 0.85rem;
    }

    nav#navbar-service.navbar .nav-item:not(.active) a.nav-link:hover,
    nav#nav-submenu.navbar .nav-item:not(.active) a.nav-link:hover {
      text-decoration: underline;
    }

    #navbar-topmain .navbar-brand {
      margin-right: 0;
    }
  </style>
<?php
} else {
  $navbarService_style = "background-color:#161617 !important";
?>
  <style>
    div.navbar-service-container {
      background-color: #161617 !important;
      margin-top: 110px;
      border-bottom-left-radius: 2rem;
      border-bottom-right-radius: 2rem;
      padding: 0.5rem 1.0rem !important;
    }

    nav#navbar-service.navbar {
      border-bottom-left-radius: 1rem;
      border-bottom-right-radius: 1rem;
    }

    nav#navbar-service .navbar-brand,
    nav#nav-submenu .navbar-brand {
      font-size: 1rem;
    }

    nav#navbar-service.navbar a.navbar-brand {
      line-height: 1em;
      margin-left: 0;
      margin-right: 0;
    }

    nav#navbar-service.navbar a.navbar-brand:hover {
      text-decoration: none;
    }

    nav#navbar-service.navbar a.navbar-brand span.navb-line1 {
      color: #F8F9FA !important;
      font-family: 'Oswald', sans-serif;
      font-size: 1rem;
      font-weight: 200;
      text-transform: none;
    }

    nav#navbar-service.navbar .nav-item a.nav-link {
      color: #F8F9FA !important;
      font-family: 'Stolzl Book', sans-serif;
      font-size: 1.0rem;
      /* border: 1px solid transparent; */
      /* border-radius: 8px; */
    }

    nav#navbar-service.navbar .nav-item.active a.nav-link {
      color: #FFF;
      border-radius: 8px;
      background-color: #0178FA;
    }

    nav#nav-submenu.navbar .nav-item.active a.nav-link {
      color: #0178FA;
      outline: 1px solid #0178FA;
      outline-offset: -1px;
      border-radius: 8px;
    }

    nav#nav-submenu.navbar a.navbar-brand span.navb-line2 {
      color: #333;
      font-family: 'Stolzl Book', sans-serif;
      font-size: 0.85rem;
      font-weight: 400;
      text-transform: none;
    }

    nav#nav-submenu.navbar a.nav-link {
      color: #333;
      font-family: 'Stolzl Book', sans-serif;
      font-size: 0.85rem;
    }

    nav#navbar-service.navbar .nav-item:not(.active) a.nav-link:hover,
    nav#nav-submenu.navbar .nav-item:not(.active) a.nav-link:hover {
      text-decoration: underline;
    }

    #navbar-topmain .navbar-brand {
      margin-right: 0;
    }
  </style>
<?php
}
?>



<div class="navbar-service-container container-xl p-3">
  <nav id="navbar-service" class="navbar navbar-expand-lg text-light" style="<?php echo $navbarService_style; ?>">
    <div class="container-fluid">
      <!-- <a class="navbar-brand pr-3" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/index.php">
<img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/images/favicons/favicon.ico" alt="" width="32"
     height="32" class="d-inline-block align-text-middle">
</a> -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-center" id="collapsibleNavbar">
        <ul class="navbar-nav">
          <li class="nav-item<?php echo $classActive_main; ?>">
            <a class="nav-link" href="/mailnew/index.php?type=main">Главная</a>
          </li>
          <li class="nav-item<?php echo $classActive_in; ?>">
            <a class="nav-link" href="/mailnew/index.php?type=in&mode=thisyear">Входящие</a>
          </li>
          <li class="nav-item<?php echo $classActive_archin; ?>">
            <a class="nav-link" href="/mailnew/index.php?type=in&mode=archive&year=<?php echo date("Y") - 1; ?>">Архив
              входящих</a>
          </li>
          <li class="nav-item<?php echo $classActive_out; ?>">
            <a class="nav-link" href="/mailnew/index.php?type=out&mode=thisyear">Исходящие</a>
          </li>
          <li class="nav-item<?php echo $classActive_archout; ?>">
            <a class="nav-link" href="/mailnew/index.php?type=out&mode=archive&year=<?php echo date("Y") - 1; ?>">Архив
              исходящих</a>
          </li>
          <li class="nav-item<?php echo $classActive_profile; ?>">
            <a class="nav-link" href="/mailnew/index.php?mode=profile">Профиль</a>
          </li>
          <li class="nav-item<?php echo $classActive_settings; ?>">
            <a class="nav-link" href="/mailnew/index.php?type=settings">Настройки</a>
          </li>
          <li class="nav-item<?php echo $classActive_help; ?>">
            <a class="nav-link" href="/mailnew/index.php?type=help">Помощь</a>
          </li>
          <?php
          // if (checkIsItSuperadmin_defaultDB($_SESSION['id']) == 1 || $_SESSION['id'] === '1011') {
          if (1 === 1) {
            echo '<li class="nav-item' . $classActive_devlog . '">';
            echo '<a class="nav-link" href="/mailnew/index.php?type=devlog">Журнал обновлений<sup><i class="fa-solid fa-exclamation-circle fa-xs pl-1 text-danger"></i></sup></a>';
            echo '</li>';
          }
          ?>
        </ul>
      </div>
    </div>
  </nav>

</div>