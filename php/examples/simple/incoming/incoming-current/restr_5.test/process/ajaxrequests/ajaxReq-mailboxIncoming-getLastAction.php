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
// session_start();# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

// Код документа в таблице ".__MAIL_INCOMING_PREFIX."
$_koddocmail = isset($_POST['koddocmail']) ? $_POST['koddocmail'] : "";
$_parameter = isset($_POST['parameter']) ? $_POST['parameter'] : "";

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$error = $output = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		return -2;
	} else {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $_koddocmail != "" && $_parameter != "") {
			$_reqDB = mysqli_fetch_array(mysqlQuery("SELECT * FROM " . __MAIL_INCOMING_PREFIX . "_logChanges WHERE `action` IN ('CRT','UPD') AND `koddocmail` = '{$_koddocmail}' AND `koddel` <> 'deleted' AND `changes` LIKE '%{$_parameter}%' ORDER BY `timestamp` DESC LIMIT 1"));
			$changes = $_reqDB['changes'];
			$timestamp = $_reqDB['timestamp'];
			if ($changes != "" && $timestamp != "") {
				$output = $changes .  "///" . date('d.m.Y H:i:s', strtotime($timestamp));
			} else {
				$output = 0;
			}
		} else {
			$output = -1;
		}
	}
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
