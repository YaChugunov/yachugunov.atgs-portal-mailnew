<?php
date_default_timezone_set('Europe/Moscow');
# Подключаем конфигурационный файл
require($_SERVER['DOCUMENT_ROOT'] . "/config.inc.php");
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
require_once(__DIR_ROOT . __SERVICENAME_MAILNEW . '/config.mail.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаемся к базе
require_once(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/dbconn/db_connection.php');
require_once(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/dbconn/db_controller.php');
$db_handle = new DBController();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем общие функции безопасности
require_once(__DIR_ROOT . '/_assets/functions/funcSecure.inc.php');
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
$_userid = isset($_POST['userid']) ? $_POST['userid'] : "";

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$output = "";
$output_getUserCheckout = "";
$output_getUserList = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		return $output = "-3";
	} else {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && "" != $_koddocmail) {
			$sqlReqString1 = "SELECT ispolStatus FROM " . __MAIL_OUTGOING_PREFIX . "_logCheckouts WHERE koddocmail = '{$_koddocmail}' AND userid = '{$_userid}' AND koddel <> 'deleted'";
			$_reqDB_1 = mysqli_fetch_array(mysqlQuery("SELECT ispolStatus FROM " . __MAIL_OUTGOING_PREFIX . "_logCheckouts WHERE koddocmail = '{$_koddocmail}' AND userid = '{$_userid}' AND koddel <> 'deleted'"));
			$_reqDB_2 = mysqli_fetch_array(mysqlQuery("SELECT outbox_docContractor_kodispolout, outbox_docContractorID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE koddocmail = '{$_koddocmail}'"));
			$ispolStatus = $_reqDB_1['ispolStatus'];
			$ispolList1 = $_reqDB_2['outbox_docContractor_kodispolout'];
			$ispolList2 = $_reqDB_2['outbox_docContractorID'];
			if (!empty($_reqDB_1)) {
				$output_getUserCheckout = $ispolStatus;
			} else {
				$output_getUserCheckout = "-1";
			}
			if (!empty($_reqDB_2)) {
				$output_getUserList = $ispolList2;
			} else {
				$output_getUserList = "-1";
			}
			$output = $output_getUserCheckout . "///" . $output_getUserList . "///" . $_koddocmail . "///" . $_userid . "///" . $sqlReqString1 . "///" . $ispolStatus;
		} else {
			$output = "-2";
		}
	}
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
