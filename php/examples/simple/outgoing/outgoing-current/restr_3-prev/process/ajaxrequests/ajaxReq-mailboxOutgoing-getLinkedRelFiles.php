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
#
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

// Код документа в таблице mailbox_incoming
$_koddocmail = isset($_POST['koddocmail']) ? $_POST['koddocmail'] : "";

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$error = $output = $out = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		return -2;
	} else {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $_koddocmail != "") {
			$_reqDB_1 = mysqli_fetch_array(mysqlQuery("SELECT inbox_docFileID FROM " . __MAIL_INCOMING_TABLENAME . " WHERE koddocmail = '{$_koddocmail}'"));
			$inbox_docFileID = $_reqDB_1['inbox_docFileID'];
			if ($inbox_docFileID != "" && $inbox_docFileID != NULL) {
				$_reqDB_2 = mysqli_fetch_array(mysqlQuery("SELECT * FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE id = '{$inbox_docFileID}'"));
				$originalName = $_reqDB_2['file_originalname'];
				$file_url = $_reqDB_2['file_url'];
				$output .= '<div class="relDocFileID-link"><a href="' . $file_url . '" target="_blank">' . $originalName . '</a></div>';
				$output .= '///';
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
