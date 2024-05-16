<?php
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
#
#

#
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
?>
    <style>
        #navbar-topmain {
            font-family: 'Stolzl Book', sans-serif;
            font-size: 12px;
            height: 110px;
            background-color: #F1F1F1 !important;
        }

        #navbar-topmain img.logo {
            width: 64px;
            height: 64px;
        }

        #navbar-topmain .navbar-title {
            font-family: 'Stolzl Book', sans-serif;
        }

        #navbar-topmain .navbar-title h1 {
            font-size: 2.0rem;
            color: #000000;
        }

        #navbar-topmain .navbar-title h3 {
            font-size: 0.8rem;
            color: #333333;
            opacity: 0.95;
        }

        div#listMessages-icon:hover {
            cursor: pointer;
        }

        div#userSettings-icon:hover {
            cursor: pointer;
        }

        #navbar-side.sidenav {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 10;
            top: 110px;
            left: 0;
            background-color: #F1F1F1 !important;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 40px;
        }

        #navbar-side.sidenav .title {
            font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 1.15rem;
            font-weight: 600;
            color: #343a40;
            padding: 8px;
            margin-left: 16px;
            /* background-color: #FFFFFF; */
            white-space: nowrap;
            /* border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem; */
        }

        #navbar-side.sidenav a {
            padding: 4px 8px 4px 32px;
            text-decoration: none;
            color: #AAAAAA;
            display: block;
            transition: 0.3s;
        }

        #navbar-side.sidenav a:hover,
        #navbar-side.sidenav a.nav-link.subnav:hover {
            color: #000000;
        }

        #navbar-side.sidenav .closebtn {
            position: absolute;
            top: -15px;
            right: 5px;
            font-size: 2rem;
        }

        #navbar-side.sidenav a.nav-link {
            font-family: 'Stolzl Book', sans-serif;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        #navbar-side.sidenav a.nav-link.subnav {
            font-size: 0.8rem;
            color: #888888;
            padding: 2px 8px 2px 32px;
        }

        @media screen and (max-height: 450px) {
            #navbar-side.sidenav {
                padding-top: 15px;
            }

            #navbar-side.sidenav a {
                font-size: 16px;
            }
        }
    </style>
<?php
} else {
?>
    <style>
        #navbar-topmain {
            font-family: 'Stolzl Book', sans-serif;
            font-size: 12px;
            height: 110px;
            background-color: #161617;
        }

        #navbar-topmain img.logo {
            width: 64px;
            height: 64px;
        }

        #navbar-topmain .navbar-title {
            font-family: 'Stolzl Book', sans-serif;
        }

        #navbar-topmain .navbar-title h1 {
            font-size: 2.0rem;
            color: #FFFFFF;
        }

        #navbar-topmain .navbar-title h3 {
            font-size: 0.8rem;
            color: #FFFFFF;
            opacity: 0.65;
        }

        div#listMessages-icon:hover {
            cursor: pointer;
        }

        div#userSettings-icon:hover {
            cursor: pointer;
        }

        #navbar-side.sidenav {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 10;
            top: 110px;
            left: 0;
            background-color: #161617;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 40px;
        }

        #navbar-side.sidenav .title {
            font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 1.15rem;
            font-weight: 600;
            color: #CCCCCC;
            padding: 8px;
            margin-left: 16px;
            /* background-color: #FFFFFF; */
            white-space: nowrap;
            /* border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem; */
        }

        #navbar-side.sidenav a {
            padding: 4px 8px 4px 32px;
            text-decoration: none;
            color: #AAAAAA;
            display: block;
            transition: 0.3s;
        }

        #navbar-side.sidenav a:hover,
        #navbar-side.sidenav a.nav-link.subnav:hover {
            color: #FFFFFF;
        }

        #navbar-side.sidenav .closebtn {
            position: absolute;
            top: -15px;
            right: 5px;
            font-size: 2rem;
        }

        #navbar-side.sidenav a.nav-link {
            font-family: 'Stolzl Book', sans-serif;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        #navbar-side.sidenav a.nav-link.subnav {
            font-size: 0.8rem;
            color: #888888;
            padding: 2px 8px 2px 32px;
        }

        @media screen and (max-height: 450px) {
            #navbar-side.sidenav {
                padding-top: 15px;
            }

            #navbar-side.sidenav a {
                font-size: 16px;
            }
        }
    </style>
<?php
}
?>
<div id="navbar-side" class="sidenav"><a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;
    </a>
    <div class="title">Экосистема портала</div>
    <a class="nav-link" href="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_PORTALNEW; ?>">Портал</a>
    <a class="nav-link" href="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW; ?>/index.php?type=main">Почта</a>
    <a class="nav-link" href="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_SP; ?>/index.php?type=contragents&mode=main">Справочник</a>
    <div class="title">Старые сервисы</div>
    <a class="nav-link" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dognet">Договор (главная)</a><a class="nav-link" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mail/incoming.php?mode=thisyear" title="Входящая почта АТГС">Входящая почта АТГС</a><a class="nav-link" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mail/outgoing.php?mode=thisyear" title="Исходящая почта АТГС">Исходящая почта АТГС</a><a class="nav-link" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/hr" title="Сервис Кадры">Кадры</a><a class="nav-link" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/ism" title="Сервис ИСМ/СМК">ИСМ/СМК</a><a class="nav-link" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/eda" title="Заказать обед в разделе Еда 2.0 на Портале">Еда
        2.0</a><a class="nav-link" href="http://dinner.atgs.ru" title="Заказать обед на сайте dinner.atgs.ru" target="_blank" title="Заказать обед в разделе Еда 2.0 на Портале">Еда (dinner.atgs.ru)</a><a class="nav-link" href="https://www.atgs.ru" target="_blank" title="Сайт АТГС">Сайт АТГС</a>
</div>
<script type="text/javascript" language="javascript" class="init">
    function openNav() {
        document.getElementById("navbar-side").style.width = "285px";
        // document.getElementById("navbar-side").style.width = "15%";
        // document.getElementById("navbar-side").style.minWidth = "250px";
        // document.getElementById("navbar-side").style.maxWidth = "350px";
    }

    function closeNav() {
        document.getElementById("navbar-side").style.width = "0";
        document.getElementById("navbar-side").style.minWidth = "0";
        document.getElementById("navbar-side").style.maxWidth = "350px";
    }
</script>