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

$_req1 = mysqlQuery("SELECT id, inbox_rowIDs_links FROM mailbox_outgoing WHERE outbox_docDate BETWEEN '2021-01-01 00:00:00' AND '2023-12-31 23:59:59'");

while ($_req1Rows = mysqli_fetch_assoc($_req1)) {
	$sourceLinks = explode(",", $_req1Rows['inbox_rowIDs_links']);
	$sourceID = $_req1Rows['id'];
	foreach ($sourceLinks as $value) {
		$_req2 = mysqli_fetch_assoc(mysqlQuery("SELECT outbox_rowIDs_links FROM mailbox_incoming WHERE WHERE id='{$value}'"));
		$strExist = strpos($_req2['outbox_rowIDs_links'], $sourceID);
		if (!empty($linkIncoming)) {
			if (!$strExist) {
				$linkIncoming .= "," . $id;
				// Записываем в БД в таблицу ".__MAIL_OUTGOING_PREFIX.", кто поставил галочку "Отправка на email" в окне редактирования и когда
				$db->update(__MAIL_INCOMING_TABLENAME, array('outbox_rowIDs_links' => $linkIncoming), array('id' => $value));
			} else {
				// $query = mysqlQuery("SELECT * FROM " . __MAIL_INCOMING_TABLENAME . " WHERE outbox_rowIDs_links != '' AND outbox_rowIDs_links IS NOT NULL AND");
			}
		} else {
			$linkIncoming .= $id;
		}
	}
	$ispoldepts = rtrim($ispoldepts, ",");
	$_req3 = mysqlQuery("UPDATE mailbox_outgoing SET outbox_docContractorDEPT='{$ispoldepts}' WHERE koddocmail='{$koddocmail}'");
	$output = "Ok";
}
// unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
