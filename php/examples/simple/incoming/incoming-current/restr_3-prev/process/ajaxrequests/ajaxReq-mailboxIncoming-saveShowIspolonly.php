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
$showispolonly = ($_POST['showispolonly'] == 'true') ? 1 : 0;
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
		if (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 0) == 1) {
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				$_QRY1 = mysqlQuery("UPDATE mailbox_sp_users SET showispolonly='" . $showispolonly . "' WHERE ID='" . $userID . "'");
				$_QRY2 = mysqlQuery("UPDATE mailbox_userSettingsUI SET incoming_showIspolOnly='" . $showispolonly . "' WHERE ID='" . $userID . "'");
				if ($_QRY1 && $_QRY2) {
					$output = '1';
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
	echo $output . " / " . $userID . " : " . $showispolonly . "(" . $_POST['showispolonly'] . ")";
}
