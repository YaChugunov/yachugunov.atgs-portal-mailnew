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
// $_rowid = isset($_POST['rowid']) ? $_POST['rowid'] : "";
$_koddocmail = isset($_POST['koddocmail']) ? $_POST['koddocmail'] : "";
$_action = isset($_POST['action']) ? $_POST['action'] : "";
$_userid = $_SESSION['id'];
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$output = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		return $output = "error -3";
	} else {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && "" != $_koddocmail) {
			if ($_action == "check") {
				$_reqDB = mysqli_fetch_array(mysqlQuery("SELECT outbox_docID, inuse_flag, inuse_userid FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE koddocmail = '{$_koddocmail}'"));
				if (!empty($_reqDB)) {
					$docID = $_reqDB['outbox_docID'];
					$inUseFlag = $_reqDB['inuse_flag'];
					$inUseUserID = !empty($_reqDB['inuse_userid']) ? $_reqDB['inuse_userid'] : null;
					$inUseUserLastname = "";
					$inUseUserFirstname = "";
					if (!empty($inUseUserID)) {
						$_reqDB1 = mysqli_fetch_array(mysqlQuery("SELECT lastname, firstname FROM users WHERE id = '{$inUseUserID}'"));
						if (!empty($_reqDB1)) {
							$inUseUserLastname = !empty($_reqDB1['lastname']) ? $_reqDB1['lastname'] : 'no data';
							$inUseUserFirstname = !empty($_reqDB1['firstname']) ? $_reqDB1['firstname'] : 'no data';
							$output_response = json_encode([$docID, $inUseFlag, $inUseUserID, $inUseUserLastname, $inUseUserFirstname]);
						} else {
							$output_response = "error on check";
						}
					} else {
						$output_response = "no data on check";
					}
				} else {
					$output = "no data";
				}
			} elseif ($_action == "set") {
				$_reqDB = mysqli_fetch_array(mysqlQuery("SELECT outbox_docID, inuse_flag, inuse_userid FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE koddocmail = '{$_koddocmail}'"));
				if (!empty($_reqDB)) {
					$docID = $_reqDB['outbox_docID'];
					$inUseFlag = $_reqDB['inuse_flag'];
					$inUseUserID = !empty($_reqDB['inuse_userid']) ? $_reqDB['inuse_userid'] : null;
					$inUseUserLastname = "";
					$inUseUserFirstname = "";
					$_reqDB1 = mysqlQuery("UPDATE " . __MAIL_OUTGOING_TABLENAME . " SET inuse_flag='1', inuse_userid='{$_userid}' WHERE koddocmail = '{$_koddocmail}'");
					if ($_reqDB1) {
						$output_response = json_encode([$docID, $inUseFlag, $inUseUserID, $inUseUserLastname, $inUseUserFirstname]);
					} else {
						$output_response = "error on set";
					}
				} else {
					$output = "no data";
				}
			} elseif ($_action == "unset") {
				$_reqDB = mysqli_fetch_array(mysqlQuery("SELECT outbox_docID, inuse_flag, inuse_userid FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE koddocmail = '{$_koddocmail}'"));
				if (!empty($_reqDB)) {
					$docID = $_reqDB['outbox_docID'];
					$inUseFlag = $_reqDB['inuse_flag'];
					$inUseUserID = !empty($_reqDB['inuse_userid']) ? $_reqDB['inuse_userid'] : null;
					$inUseUserLastname = "";
					$inUseUserFirstname = "";
					$_reqDB1 = mysqlQuery("UPDATE " . __MAIL_OUTGOING_TABLENAME . " SET inuse_flag='0', inuse_userid='NULL' WHERE koddocmail = '{$_koddocmail}'");
					if ($_reqDB1) {
						$output_response = json_encode([$docID, $inUseFlag, $inUseUserID, $inUseUserLastname, $inUseUserFirstname]);
					} else {
						$output_response = "error on unset";
					}
				} else {
					$output = "no data";
				}
			} elseif ($_action == "unsetAll") {
				$_reqDB1 = mysqlQuery("UPDATE " . __MAIL_OUTGOING_TABLENAME . " SET inuse_flag='0', inuse_userid='NULL' WHERE inuse_userid = '{$_userid}'");
				if ($_reqDB1) {
					$output_response = "unset all user docs";
				} else {
					$output_response = "error on unset all";
				}
			} else {
				$output = "error -1";
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
