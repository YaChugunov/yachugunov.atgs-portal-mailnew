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
$userID = isset($_POST['userID']) ? $_POST['userID'] : $_SESSION['id'];
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# 

#
#
$output = "";
# --- - --- - --- - --- - --- - --- - --- - --- - --- - --- - --- - ---
if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		$output = '0';
	} else {
		if (checkUserRestrictions_defaultDB($_SESSION['id'], 'mail', 3, 0) == 1) {
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				$_QRY1 = mysqli_fetch_array(mysqlQuery("SELECT showispoloutonly FROM mailbox_sp_users WHERE id='" . $userID . "'"));
				$_QRY2 = mysqli_fetch_array(mysqlQuery("SELECT outgoing_showIspolOnly FROM mailbox_userSettingsUI WHERE id='" . $userID . "'"));
				if ($_QRY1 && $_QRY2) {
					// $output = $_QRY1['showispoloutonly'];
					$output = $_QRY2['outgoing_showIspolOnly'];
				} else {
					$output = '0';
				}
			} else {
				$output = '0';
			}
		} else {
			$output = '0';
		}
	}
	echo $output;
}
