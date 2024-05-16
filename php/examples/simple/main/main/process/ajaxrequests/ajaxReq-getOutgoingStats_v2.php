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
$ispolOnly = isset($_POST['ispolOnly']) ? $_POST['ispolOnly'] : "";

$userid = $_SESSION['id'];
$year = date("Y");
$output = "";
if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
    if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
        return 0;
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

            if ((checkServiceAccess('allservices') == 1 && checkServiceAccess('mailnew') == 1 && checkUserRestrictions($_SESSION['id'], 'mailnew', 3, 0) == 1) or (checkServiceAccess('allservices') == 1 && checkServiceAccess('mailnew') == 0 && checkIsItSuperadmin($_SESSION['id']) == 1) or (checkServiceAccess('allservices') == 0 && checkIsItSuperadmin($_SESSION['id']) == 1)) {

                $strSql_ispolOnly = ($ispolOnly == '1') ? " AND outbox_docContractorID LIKE '%{$userid}%'" : "";

                $_QRY_docsTotal = mysqli_fetch_assoc(mysqlQuery("SELECT COUNT(*) AS docsTotal FROM mailbox_outgoing WHERE YEAR(outbox_docDate)='{$year}'" . $strSql_ispolOnly . " AND koddel!='99'"));
                $docsTotal = $_QRY_docsTotal['docsTotal'];

                $_QRY_docsNoispol = mysqli_fetch_assoc(mysqlQuery("SELECT COUNT(*) AS docsNoispol FROM mailbox_outgoing WHERE (outbox_docContractorID='1' OR outbox_docContractorID IS NULL OR outbox_docContractorID='')  AND YEAR(outbox_docDate)='{$year}'" . $strSql_ispolOnly . " AND koddel!='99'"));
                $docsNoispol = $_QRY_docsNoispol['docsNoispol'];

                $_QRY_docsNoattach = mysqli_fetch_assoc(mysqlQuery("SELECT COUNT(*) AS docsNoattach FROM mailbox_outgoing WHERE (outbox_docFileID='' OR outbox_docFileID IS NULL) AND (outbox_docFileIDadd='' OR outbox_docFileIDadd IS NULL) AND YEAR(outbox_docDate)='{$year}'" . $strSql_ispolOnly . " AND koddel!='99'"));
                $docsNoattach = $_QRY_docsNoattach['docsNoattach'];

                // $_QRY_ctrlOn = mysqli_fetch_assoc(mysqlQuery("SELECT COUNT(*) AS ctrlOn FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND outbox_controlIspolStatus NOT IN (0,1) AND YEAR(outbox_docDate)='{$year}' AND koddel!='99'"));
                $_QRY_ctrlOn = mysqli_fetch_assoc(mysqlQuery("SELECT COUNT(*) AS ctrlOn FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND YEAR(outbox_docDate)='{$year}'" . $strSql_ispolOnly . " AND koddel!='99'"));
                $ctrlOn = $_QRY_ctrlOn['ctrlOn'];

                $_QRY_ctrlNotexec = mysqli_fetch_assoc(mysqlQuery("SELECT COUNT(*) AS ctrlNotexec FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND outbox_controlIspolStatus IN (2,3) AND outbox_controlIspolCheckout!='1' AND YEAR(outbox_docDate)='{$year}'" . $strSql_ispolOnly . " AND koddel!='99'"));
                $ctrlNotexec = $_QRY_ctrlNotexec['ctrlNotexec'];

                $_QRY_ctrlDLon = mysqli_fetch_assoc(mysqlQuery("SELECT COUNT(*) AS ctrlDLon FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND outbox_controlIspolUseDeadline='1' AND TIMESTAMPDIFF(HOUR, NOW(), outbox_docDateDeadline) >= 72 AND outbox_controlIspolStatus IN (2,3) AND outbox_controlIspolCheckout!='1' AND YEAR(outbox_docDate)='{$year}'" . $strSql_ispolOnly . " AND koddel!='99'"));
                $ctrlDLon = $_QRY_ctrlDLon['ctrlDLon'];

                $_QRY_ctrlDL3days = mysqli_fetch_assoc(mysqlQuery("SELECT COUNT(*) AS ctrlDL3days FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND outbox_controlIspolUseDeadline='1' AND TIMESTAMPDIFF (HOUR, NOW(), outbox_docDateDeadline) >= 24 AND TIMESTAMPDIFF (HOUR, NOW(), outbox_docDateDeadline) < 72 AND outbox_controlIspolStatus IN (2,3) AND outbox_controlIspolCheckout!='1' AND YEAR(outbox_docDate)='{$year}'" . $strSql_ispolOnly . " AND koddel!='99'"));
                $ctrlDL3days = $_QRY_ctrlDL3days['ctrlDL3days'];

                $_QRY_ctrlDL1day = mysqli_fetch_assoc(mysqlQuery("SELECT COUNT(*) AS ctrlDL1day FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND outbox_controlIspolUseDeadline='1' AND TIMESTAMPDIFF(HOUR, NOW(), outbox_docDateDeadline) < 24 AND TIMESTAMPDIFF(HOUR, NOW(), outbox_docDateDeadline) >= 0 AND outbox_controlIspolStatus IN (2,3) AND outbox_controlIspolCheckout!='1' AND YEAR(outbox_docDate)='{$year}'" . $strSql_ispolOnly . " AND koddel!='99'"));
                $ctrlDL1day = $_QRY_ctrlDL1day['ctrlDL1day'];

                $_QRY_ctrlDLexpired = mysqli_fetch_assoc(mysqlQuery("SELECT COUNT(*) AS ctrlDLexpired FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND outbox_controlIspolUseDeadline='1' AND TIMESTAMPDIFF (HOUR, NOW(), outbox_docDateDeadline) < 0 AND outbox_controlIspolStatus IN (2,3) AND outbox_controlIspolCheckout!='1' AND YEAR(outbox_docDate)='{$year}'" . $strSql_ispolOnly . " AND koddel!='99'"));
                $ctrlDLexpired = $_QRY_ctrlDLexpired['ctrlDLexpired'];


                $_QRY = $_QRY_docsTotal && $_QRY_docsNoispol && $_QRY_docsNoattach && $_QRY_ctrlOn && $_QRY_ctrlNotexec && $_QRY_ctrlDLon && $_QRY_ctrlDL3days && $_QRY_ctrlDL1day && $_QRY_ctrlDLexpired;

                if (!empty($_QRY)) {
                    $output .= $docsTotal . "///" . $docsNoispol . "///" . $docsNoattach . "///" . $ctrlOn . "///" . $ctrlNotexec . "///" . $ctrlDLon . "///" . $ctrlDL3days . "///" . $ctrlDL1day . "///" . $ctrlDLexpired;
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