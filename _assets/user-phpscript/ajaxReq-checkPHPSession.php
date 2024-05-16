<?php
# Подключаем конфигурационный файл
require($_SERVER['DOCUMENT_ROOT'] . '/config.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
require(__DIR_ROOT . __SERVICENAME_MAILNEW . '/config.mail.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаемся к базе
require(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/dbconn/db_connection.php');
// require('../_assets/drivers/db_controller.php');
// $db_handle = new DBController();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем общие функции безопасности
require(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/functions/func.secure.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем собственные функции сервиса Почта
require(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/functions/func.mail.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
// You can access the values posted by jQuery.ajax
// through the global variable $_POST, like this:
// print_r ($_POST);
$sessID = $_POST['sessionID'];
$__ip = $_SERVER['REMOTE_ADDR'];
//
$output1 = "";
if ($sessID != "") {
	$output1 = (is_session_exists()) ? "1" : "0";
	if ($output1 == "1") {
		$__login = $_SESSION['login'];
		$_QRY = mysqlQuery_defaultDB("SELECT SESSID FROM log_auth WHERE ip='$__ip' AND user_login='$__login' ORDER BY lastactivity_timestamp DESC LIMIT 1");
		$_ROW = mysqli_fetch_array($_QRY, MYSQLI_ASSOC);
		$output1 = ($sessID <> $_ROW['SESSID']) ? "-1" : "1";
	}
} else {
	$output1 = "0";
}
echo $output1;
