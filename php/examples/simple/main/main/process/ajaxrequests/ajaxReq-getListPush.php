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
$output = "";
if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
    if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
        $output .= "error";
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

            if ((checkServiceAccess('allservices') == 1 && checkServiceAccess('mailnew') == 1 && checkUserRestrictions($_SESSION['id'], 'mailnew', 3, 0) == 1) or (checkServiceAccess('allservices') == 1 && checkServiceAccess('mailnew') == 0 && checkIsItSuperadmin($_SESSION['id']) == 1) or (checkServiceAccess('allservices') == 0 && checkIsItSuperadmin($_SESSION['id']) == 1)) {
                $_QRY_USEPUSH = mysqli_fetch_array(mysqlQuery("SELECT use_pushMessages FROM mailbox_userSettingsUI WHERE id='{$userid}'"));

                $_QRY_CHECKPUSH = mysqlQuery("SELECT *, checked LIKE '%{$userid}%' as readed FROM portal_push_messages WHERE msg_active='1' AND msg_status<>'1' AND msg_timestamp>=curdate() AND checked NOT LIKE '%{$userid}%' AND ( for_singleuser LIKE '%{$userid}%' OR for_groupuser LIKE '%{$userid}%' OR for_groupuser='all' )");
                if ($_QRY_CHECKPUSH && mysqli_num_rows($_QRY_CHECKPUSH) > 0) {
                    $i = 1;
                    while ($_ROW_CHECKPUSH = mysqli_fetch_array($_QRY_CHECKPUSH)) {
                        $output .= $i . "///" . trim($_ROW_CHECKPUSH['msg_timestamp']) . "///" . trim($_ROW_CHECKPUSH['servicename']) . "///" . trim($_ROW_CHECKPUSH['msg_type']) . "///" . trim($_ROW_CHECKPUSH['msg_title']) . "///" . trim($_ROW_CHECKPUSH['msg_maintext']) . "///" . trim($_ROW_CHECKPUSH['msg_subtext1']) . "///" . trim($_ROW_CHECKPUSH['msg_subtext2']) . "///" . trim($_ROW_CHECKPUSH['msg_subtext3']) . "///" . trim($_ROW_CHECKPUSH['msg_specialtext']) . "///" . trim($_ROW_CHECKPUSH['msg_link1']) . "///" . trim($_ROW_CHECKPUSH['msg_link2']) . "///" . trim($_ROW_CHECKPUSH['msg_id']) . "///" .  trim($_ROW_CHECKPUSH['readed']);
                        $output .= "<!>";
                        $i++;
                    }
                    // $output = rtrim($output, "<!>");
                    $output .= $_QRY_USEPUSH['use_pushMessages'];
                } else {
                    $output .= "no messages";
                }
            } else {
                $output .= "error";
            }
        } else {
            $output .= "error";
        }
    }
} else {
    $output .= "error";
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;