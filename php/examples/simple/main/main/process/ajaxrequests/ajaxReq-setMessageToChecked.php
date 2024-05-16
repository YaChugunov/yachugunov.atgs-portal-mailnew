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
$msgid = isset($_POST['msgid']) ? $_POST['msgid'] : "";
$output = "";
if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
    if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
        return 0;
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

            if ((checkServiceAccess('allservices') == 1 && checkServiceAccess('mailnew') == 1 && checkUserRestrictions($_SESSION['id'], 'mailnew', 3, 0) == 1) or (checkServiceAccess('allservices') == 1 && checkServiceAccess('mailnew') == 0 && checkIsItSuperadmin($_SESSION['id']) == 1) or (checkServiceAccess('allservices') == 0 && checkIsItSuperadmin($_SESSION['id']) == 1) && !empty($msgid)) {
                $_QRY_GETMSG = mysqli_fetch_array(mysqlQuery("SELECT checked FROM portal_push_messages WHERE msg_id='{$msgid}'"));
                if ($_QRY_GETMSG) {
                    $updatechecked = !empty($_QRY_GETMSG['checked']) ? "{$userid}," : "{$userid}";
                    $_QRY_UPDMSG = mysqlQuery("UPDATE portal_push_messages SET checked = CONCAT('{$updatechecked}', checked) WHERE msg_id='{$msgid}'");
                    $_QRY_LOGCHK = mysqlQuery("INSERT INTO portal_push_checked (msg_id, check_timestamp, check_userid) VALUES ('{$msgid}', NOW(), '{$userid}')");
                    if ($_QRY_UPDMSG && $_QRY_LOGCHK) {
                        $output .= "success";
                    } else {
                        $output .= "error1";
                    }
                }
            } else {
                $output .= "empty";
            }
        } else {
            $output .= "error2";
        }
    }
} else {
    $output .= "error3";
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;