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

// Код документа в таблице mailbox_incoming
$_koddocmail = isset($_POST['koddocmail']) ? $_POST['koddocmail'] : "";

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$data = array();
$error = $success = $output = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
    if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
        return 0;
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $_koddocmail != "") {
            $_QRY_Main = mysqli_fetch_assoc(mysqlQuery("SELECT inbox_docIDSTR, inbox_docDate, inbox_docType, inbox_docAbout, inbox_docContractorSTR, inbox_docRecipientSTR, inbox_docSourceID, inbox_docSourceDate FROM " . __MAIL_INCOMING_TABLENAME . " WHERE koddocmail='{$_koddocmail}'"));
            $doctype = $_QRY_Main['inbox_docType'] != "" ? $_QRY_Main['inbox_docType'] : "999";
            $_QRY_docType = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM mailbox_sp_doctypes_incoming WHERE type_id='{$doctype}' AND status='1'"));

            $outDoc_docid = !empty($_QRY_Main['inbox_docIDSTR']) ? $_QRY_Main['inbox_docIDSTR'] : "---";
            $outDoc_docdate = !empty($_QRY_Main['inbox_docDate']) ? date("d.m.Y H:i", strtotime($_QRY_Main['inbox_docDate'])) : "---";
            $outDoc_docabout = !empty($_QRY_Main['inbox_docAbout']) ? $_QRY_Main['inbox_docAbout'] : "---";
            $outDoc_contractor = !empty($_QRY_Main['inbox_docContractorSTR']) ? $_QRY_Main['inbox_docContractorSTR'] : "---";
            $outDoc_recipient = !empty($_QRY_Main['inbox_docRecipientSTR']) ? $_QRY_Main['inbox_docRecipientSTR'] : "---";
            $outDoc_sourceid = !empty($_QRY_Main['inbox_docSourceID']) ? $_QRY_Main['inbox_docSourceID'] : "---";
            $outDoc_sourcedate = !empty($_QRY_Main['inbox_docSourceDate']) ? date("d.m.Y", strtotime($_QRY_Main['inbox_docSourceDate'])) : "---";
            $outDoc_type = !empty($_QRY_docType['type_name_full']) ? $_QRY_docType['type_name_full'] : "---";
            $output = "1-2/" . $outDoc_docid . "///" . $outDoc_docdate . "///" . $outDoc_type . "///" . $outDoc_docabout . "///" . $outDoc_contractor . "///" . $outDoc_recipient . "///" . $outDoc_sourceid . "///" . $outDoc_sourcedate;
            $output = !empty($output) ? $output : "Данные по документу не найдены";
        } else {
            $output = '<span style="color: red">Что-то пошло не так...</span>';
        }
    }
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
