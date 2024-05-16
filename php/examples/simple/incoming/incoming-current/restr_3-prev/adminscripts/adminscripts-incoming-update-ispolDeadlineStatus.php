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

$output = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		$output = "Error -1";
	} else {
		// if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		if ($_SESSION['id'] == '999') {
			// Снимаем настройки дедлайна входящей почты
			$reqMailSettings = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM mailbox_systemSettings WHERE typeMailbox = 'incoming'"));
			// Текущая метка времени Unix
			$timeNow = time();
			$timeDLWarning = time() + $reqMailSettings['DLwarning_inSecs'];
			$timeDLAlarm = time() + $reqMailSettings['DLalarm_inSecs'];
			// Текущая дата
			$dateNow = date("Y-m-d 00:00:00");
			$reqMainData = mysqlQuery("SELECT koddocmail, inbox_docDateDeadline, inbox_controlIspolActive, inbox_controlIspolUseDeadline FROM mailbox_incoming WHERE inbox_docDate BETWEEN '2021-01-01 00:00:00' AND '2023-12-31 23:59:59'");
			while ($rowMainData = mysqli_fetch_assoc($reqMainData)) {
				$koddocmail = $rowMainData['koddocmail'];
				// Дедлайн по документу
				$dateDL = date("Y-m-d 00:00:00", strtotime($rowMainData['inbox_docDateDeadline']));
				if ($rowMainData['inbox_controlIspolActive'] == '1' && $rowMainData['inbox_controlIspolUseDeadline'] == '1') {
					if ((strtotime($dateDL) - $timeNow) > $reqMailSettings['DLwarning_inSecs']) {
						// До дедлайна по документу более 3-х суток
						$statusDL = '4';
					} elseif ((strtotime($dateDL) - $timeNow) > $reqMailSettings['DLalarm_inSecs'] && (strtotime($dateDL) - $timeNow) <= $reqMailSettings['DLwarning_inSecs']) {
						// До дедлайна по документу менее 3-х суток, но более 1-х
						$statusDL = '3';
					} elseif ((strtotime($dateDL) - $timeNow) <= $reqMailSettings['DLalarm_inSecs']) {
						// До дедлайна по документу менее 1-х суток
						$statusDL = '1';
					} elseif ((strtotime($dateDL) - $timeNow) < 0) {
						// Дедлайн по документу просрочен (разница отрицательная)
						$statusDL = '-1';
					}
				} else {
					// Не активен режим КИ или не установлен дедлайн по документу
					$statusDL = '0';
				}
				$reqUpdateStatusDL = mysqlQuery("UPDATE mailbox_incoming SET inbox_controlIspolStatusDeadline='{$statusDL}' WHERE koddocmail='{$koddocmail}'");
				$output = $reqUpdateStatusDL ? "Ok" : "Error 0";
			}
		} else {
			$output = 'Error -2';
		}
	}
}
// unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
