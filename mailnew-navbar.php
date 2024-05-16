<?php
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
#
#
$_QRY_USER = mysqli_fetch_array(mysqlQuery(" SELECT * FROM users WHERE login='{$_SESSION['login']}'"));
#
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
$_QRY_getUser = mysqlQuery("SELECT * FROM mailbox_userSettingsUI WHERE ID = '{$_SESSION['id']}'");
if ($_QRY_getUser->num_rows == 0) {
    //
    $_QRY_addUser = mysqlQuery("INSERT INTO mailbox_userSettingsUI (ID, userID, use_personalSettings, comment) VALUES ('{$_SESSION['id']}', '{$_SESSION['id']}', '1', '{$_SESSION['lastname']}')");
?>
    <script type="text/javascript" language="javascript" class="">
        location.reload();
    </script>
<?php
} else {
    $_ROW_getUser = mysqli_fetch_array($_QRY_getUser);
    $incoming_showDashboard = $_ROW_getUser['incoming_showDashboard'];
    $outgoing_showDashboard = $_ROW_getUser['outgoing_showDashboard'];
    $use_pushMessages = $_ROW_getUser['use_pushMessages'];
    $use_lightTheme = $_ROW_getUser['use_lightTheme'];
}
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/php/examples/simple/main/main/common-includes/navbar-side.php');
#
/**
 * * Выбираем тему оформления
 * @param use_lightTheme = 1 Активна светлая тема оформления
 * @param use_lightTheme = 0 Активна темная тема оформления (по умолчанию) 
 * 
 */
if ($use_lightTheme === '1') {
    include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/mailnew-loadCSS-lightTheme.php');
} else {
    include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/mailnew-loadCSS-darkTheme.php');
}
?>
<div id="navbar-topmain" class="<?php echo $navbarTopmain_class; ?>" style="<?php echo $navbarTopmain_style; ?>">
    <div class="row h-100">
        <div class="col-1 d-flex flex-row justify-content-start my-auto">
            <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portalnew" title="На главную страницу Портала" data-toggle="popover" data-content='<div class="text-center">На главную страницу Портала</div>'><img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portalnew/_assets/images/portal-main-logo.svg" class="img logo" alt="Портал"></a>
            <div style="cursor:pointer" onclick="openNav()" class="align-self-center text-secondary ml-3 py-2" data-toggle="popover" data-content='<div class="text-center">Боковое меню Портала</div>'><i class="fa-solid fa-bars fa-2xl mr-2"></i></div>
        </div>
        <div class="col-10 d-flex justify-content-center my-auto">
            <div class="navbar-title text-center">
                <a class="navbar-brand" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/index.php">
                    <img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/images/favicons/favicon.ico" alt="" width="32" height="32" class="d-inline-block align-text-middle">
                </a>
                <h1 class="service-title mb-1"></h1>
                <h3 class="service-subtitle"></h3>
            </div>
        </div>
        <div class="col-1 d-flex flex-row justify-content-end my-auto">
            <?php // if ($_SESSION['id'] === '999' && $_SESSION['login'] === 'yachugunov') { 
            ?>
            <div id="switchTheme-icon" class="mx-3 py-2 align-self-center" data-toggle="popover" data-content='<div class="text-center">Переключиться между светлой и темной темами отображения ("темная" - тема по умолчанию)</div>'>
                <i class="<?php echo $themeIcon_class; ?> fa-2xl text-secondary"></i>
            </div>
            <?php // } 
            ?>
            <div id="listMessages-icon" class="mx-3 py-2 align-self-center" data-toggle="popover" data-content='<div class="text-center">Уведомления для Вас от сервисов Портала и администратора</div>'>
                <i class="fa-solid fa-bell fa-2xl text-secondary"></i>
            </div>
            <div id="userSettings-icon" class="mx-3 py-2 align-self-center" data-toggle="popover" data-content='<div class="text-center">Ваши персональные настройки интерфейса Почты</div>'>
                <i class="fa-solid fa-gear fa-2xl text-secondary"></i>
            </div>
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <?php echo $_SESSION['lastname']; ?>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_PORTALNEW; ?>/?mode=profile&userid=<?php echo $_SESSION['id']; ?>">Профиль
                            пользователя</a>
                        <a class="dropdown-item" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/_auth/exit.php">Выйти
                            / Закончить сессию</a>
                    </div>
                </div>
            </div>
            <img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $_QRY_USER['avatar']; ?>" class="img logo rounded-circle ml-3">
        </div>
    </div>
</div>

<?php
if (checkIsItSuperadmin_defaultDB($_SESSION['id']) == 1) {
    // include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/php/examples/simple/main/main/common-includes/usermessages-sideblock.php');
}
?>