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
$_rowid = isset($_POST['rowid']) ? $_POST['rowid'] : "";
$_userid = $_SESSION['id'];
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$output = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		return $output = "error -3";
	} else {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && "" != $_rowid) {
			$_reqDB = mysqli_fetch_array(mysqlQuery("SELECT id, koddocmail, inbox_docSender_kodzakaz, inbox_controlIspolActive, inbox_controlIspolStatus, inbox_docContractorID, outbox_docID_rel, outbox_rowID_rel, outbox_koddocmail_rel FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id = '{$_rowid}'"));
			$docDate = '---';
			$docAbout = '---';
			$relRowid = $_reqDB['id'];
			$relKoddocmail = $_reqDB['koddocmail'];
			$relKodcontragent = $_reqDB['inbox_docSender_kodzakaz'];
			$relControlIspolActive = $_reqDB['inbox_controlIspolActive'];
			$relControlIspolStatus = $_reqDB['inbox_controlIspolStatus'];
			$relOutboxDocIDRel = !empty($_reqDB['outbox_docID_rel']) ? $_reqDB['outbox_docID_rel'] : "no";
			$relOutboxRowIDRel = !empty($_reqDB['outbox_rowID_rel']) ? $_reqDB['outbox_rowID_rel'] : "no";
			$relOutboxKodRel = !empty($_reqDB['outbox_koddocmail_rel']) ? $_reqDB['outbox_koddocmail_rel'] : "no";
			$relKoddocmail = $_reqDB['inbox_controlIspolActive'];
			if (!empty($_reqDB)) {
				$_reqDB1 = mysqli_fetch_array(mysqlQuery("SELECT outbox_docDate, outbox_docAbout FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id = '{$relOutboxRowIDRel}'"));
				$docDate = !empty($_reqDB1['outbox_docDate']) ? date("d.m.Y", strtotime($_reqDB1['outbox_docDate'])) : "---";
				$docAbout = $_reqDB1['outbox_docAbout'];
				// Отдаем массив (или не массив) с параметрами входящего документа
				// $output_response = $relKodcontragent;
				if (strpos($_reqDB['inbox_docContractorID'], $_userid) !== false) {
					$ispolMe = '1';
				} else {
					$ispolMe = '0';
				}
				$output_response = json_encode([$relRowid, $relKoddocmail, $relKodcontragent, $relControlIspolActive, $relControlIspolStatus, $ispolMe, $relOutboxDocIDRel, $relOutboxRowIDRel, $relOutboxKodRel, $docDate, $docAbout]);
			} else {
				$output_response = "no data";
			}
			$output = $output_response;
		} else {
			$output = "error -2";
		}
	}
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
