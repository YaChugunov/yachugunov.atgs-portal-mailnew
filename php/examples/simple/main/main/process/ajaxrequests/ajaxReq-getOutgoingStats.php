<?php
date_default_timezone_set('Europe/Moscow');
# Подключаем конфигурационный файл
require($_SERVER['DOCUMENT_ROOT'] . '/config.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
require_once(__DIR_ROOT . __SERVICENAME_MAILNEW . '/config.mail.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаемся к базе
require_once(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/dbconn/db_connection.php');
require_once(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/dbconn/db_controller.php');
$db_handle = new DBController();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем общие функции безопасности
require_once(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/functions/func.secure.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем собственные функции сервиса Почта
require_once(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/functions/func.mail.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Включаем режим сессии
// session_start();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$userid = $_SESSION['id'];
$year = date("Y");
$output = "";
if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
    if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
        return 0;
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

            if ((checkServiceAccess('allservices') == 1 && checkServiceAccess('mailnew') == 1 && checkUserRestrictions($_SESSION['id'], 'mailnew', 3, 0) == 1) or (checkServiceAccess('allservices') == 1 && checkServiceAccess('mailnew') == 0 && checkIsItSuperadmin($_SESSION['id']) == 1) or (checkServiceAccess('allservices') == 0 && checkIsItSuperadmin($_SESSION['id']) == 1)) {
                $_QRY = mysqli_fetch_array(mysqlQuery("SELECT * FROM mailbox_outgoing_stats WHERE statYear = '{$year}'"));
                if (!empty($_QRY)) {
                    $output .= $_QRY['docs_total'] . "///" . $_QRY['docs_noispol'] . "///" . $_QRY['docs_noattach'] . "///" . $_QRY['control_on'] . "///" . $_QRY['control_notexec'] . "///" . $_QRY['control_DLon'] . "///" . $_QRY['control_DL3days'] . "///" . $_QRY['control_DL1day'] . "///" . $_QRY['control_DLexpired'];
                } else {
                    $output .= 'error';
                }
            }
        } else {
            $output .= 'error';
        }
    }
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;