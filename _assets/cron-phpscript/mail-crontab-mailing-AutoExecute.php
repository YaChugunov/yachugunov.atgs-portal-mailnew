<?php
date_default_timezone_set('Europe/Moscow');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем конфигурационный файл
// require_once("/var/www/html/atgs-portal.local/www/config.inc.php");
#
# Подключаемся к базе
require_once("/var/www/html/atgs-portal.local/www/_assets/drivers/db_connection.php");
require_once("/var/www/html/atgs-portal.local/www/_assets/drivers/db_controller.php");
$db_handle = new DBController();
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
# Подключаем общие функции безопасности
require("/var/www/html/atgs-portal.local/www/_assets/functions/funcSecure.inc.php");
# Подключаем собственные функции сервиса Почта
// require("/var/www/html/atgs-portal.local/www/eda/_assets/functions/funcEda.inc.php");

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$datetimenow = date("Y-m-d H:i:s");

$_QRY = mysqli_fetch_array(mysqlQuery("SELECT * FROM eda_automailing_settings WHERE id='1'"));
if ($_QRY['enbl'] == 1) {

	$datetime1 = date("Y-m-d H:i:s", strtotime('-1 minutes', strtotime($datetimenow)));
	$datetime2 = date("Y-m-d H:i:s", strtotime('+1 minutes', strtotime($datetimenow)));
	
	$_QRY1 = mysqli_fetch_array(mysqlQuery("SELECT * FROM eda_automailing_schedule WHERE mailing_datetime BETWEEN '".$datetime1."' AND '".$datetime2."' AND mailing_on = '1' LIMIT 1"));
	
	if ($_QRY1) {
		$num = $_QRY1['mailing_num'];
		$tip = $_QRY1['mailing_tip'];
		$day = $_QRY1['mailing_day'];
		$time = $_QRY1['mailing_time'];
		$date = $_QRY1['mailing_date'];
	
		if ($_QRY1['mailing_tip'] == '1') {
			$_QRY2 = mysqlQuery("INSERT INTO eda_automailing_log (send_tip, send_auto, send_datetime, send_comment) VALUES ('1', '1', '$datetimenow', 'Crontab have completed schedule #".$num." (".$tip." / ".$day." / ".$time." / ".$date.")')");
			if ($_QRY2) { echo "Record to table 'eda_automailing_log' is inserted at ".date("Y-m-d H:m:i")." : success"; }
			else { echo "Crontab is not performed at ".date("Y-m-d H:m:i")." ..."; }
			// 
			// 
			include_once("/var/www/html/atgs-portal.local/www/eda/php/examples/simple/main/main/restr_5/adminize/mailing/mailing-messages/eda-mailingMsg-tip1.php");
			// 
			// 
		}
		elseif ($_QRY1['mailing_tip'] == '2') {
			$_QRY2 = mysqlQuery("INSERT INTO eda_automailing_log (send_tip, send_auto, send_datetime, send_comment) VALUES ('2', '1', '$datetimenow', 'Crontab have completed schedule #".$num." (".$tip." / ".$day." / ".$time." / ".$date.")')");
			if ($_QRY2) { echo "Record to table 'eda_automailing_log' is inserted at ".date("Y-m-d H:m:i")." : success"; }
			else { echo "Crontab is not performed at ".date("Y-m-d H:m:i")." ..."; }
			// 
			// 
			include_once("/var/www/html/atgs-portal.local/www/eda/php/examples/simple/main/main/restr_5/adminize/mailing/mailing-messages/eda-mailingMsg-tip2.php");
			// 
			// 
		}
		elseif ($_QRY1['mailing_tip'] == '3') {
			$_QRY2 = mysqlQuery("INSERT INTO eda_automailing_log (send_tip, send_auto, send_datetime, send_comment) VALUES ('3', '1', '$datetimenow', 'Crontab have completed schedule #".$num." (".$tip." / ".$day." / ".$time." / ".$date.")')");
			if ($_QRY2) { echo "Record to table 'eda_automailing_log' is inserted at ".date("Y-m-d H:m:i")." : success"; }
			else { echo "Crontab is not performed at ".date("Y-m-d H:m:i")." ..."; }
			// 
			// 
			include_once("/var/www/html/atgs-portal.local/www/eda/php/examples/simple/main/main/restr_5/adminize/mailing/mailing-messages/eda-mailingMsg-tip3.php");
			// 
			// 
		}
		elseif ($_QRY1['mailing_tip'] == '4') {
			$_QRY2 = mysqlQuery("INSERT INTO eda_automailing_log (send_tip, send_auto, send_datetime, send_comment) VALUES ('4', '1', '$datetimenow', 'Crontab have completed schedule #".$num." (".$tip." / ".$day." / ".$time." / ".$date.")')");
			if ($_QRY2) { echo "Record to table 'eda_automailing_log' is inserted at ".date("Y-m-d H:m:i")." : success"; }
			else { echo "Crontab is not performed at ".date("Y-m-d H:m:i")." ..."; }
			// 
			// 
			include_once("/var/www/html/atgs-portal.local/www/eda/php/examples/simple/main/main/restr_5/adminize/mailing/mailing-messages/eda-mailingMsg-tip4.php");
			// 
			// 
		}
	}

}
