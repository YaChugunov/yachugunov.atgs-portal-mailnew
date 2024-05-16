#!/usr/bin/php
<?php
#
$_IS_CRONTAB = TRUE;
#
$path_parts = pathinfo($_SERVER['SCRIPT_FILENAME']); // определяем директорию скрипта
chdir($path_parts['dirname']); // задаем директорию выполнение скрипта
#
date_default_timezone_set('Europe/Moscow');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем конфигурационный файл
require_once("/var/www/html/atgs-portal.local/www/config.inc.php");
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
require_once("/var/www/html/atgs-portal.local/www/mailnew/config.mail.inc.php");
#
# Подключаемся к базе
require_once("/var/www/html/atgs-portal.local/www/mailnew/_assets/dbconn/db_connection.php");
require_once("/var/www/html/atgs-portal.local/www/mailnew/_assets/dbconn/db_controller.php");
$db_handle = new DBController();
#
# Подключаем общие функции безопасности
require("/var/www/html/atgs-portal.local/www/mailnew/_assets/functions/func.secure.inc.php");
# Подключаем собственные функции сервиса Почта
require("/var/www/html/atgs-portal.local/www/mailnew/_assets/functions/func.mail.inc.php");
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$output = "";

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
			$statusDL = 'more3';
		} elseif ((strtotime($dateDL) - $timeNow) > $reqMailSettings['DLalarm_inSecs'] && (strtotime($dateDL) - $timeNow) <= $reqMailSettings['DLwarning_inSecs']) {
			// До дедлайна по документу менее 3-х суток, но более 1-х
			$statusDL = 'less3';
		} elseif ((strtotime($dateDL) - $timeNow) <= $reqMailSettings['DLalarm_inSecs'] && (strtotime($dateDL) - $timeNow) > 0) {
			// До дедлайна по документу менее 1-х суток
			$statusDL = 'less1';
		} elseif ((strtotime($dateDL) - $timeNow) <= 0) {
			// Дедлайн по документу просрочен (разница отрицательная)
			$statusDL = 'expired';
		} else {
			$statusDL = 'dl';
		}
	} else {
		// Не активен режим КИ или не установлен дедлайн по документу
		$statusDL = 'nodl';
	}
	$reqUpdateStatusDL = mysqlQuery("UPDATE mailbox_incoming SET inbox_controlIspolStatusDeadline='{$statusDL}' WHERE koddocmail='{$koddocmail}'");
	$output = $reqUpdateStatusDL ? "Ok" : "Error 0";
}
// unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
