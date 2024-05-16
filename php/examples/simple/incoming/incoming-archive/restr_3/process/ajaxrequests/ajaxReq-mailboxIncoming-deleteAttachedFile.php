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
$row2delete = isset($_POST['rowID']) ? $_POST['rowID'] : "";

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$output = $inbox_docFileIDadd = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		return 0;
	} else {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $row2delete != "") {

			$__file = mysqli_fetch_assoc(mysqlQuery("SELECT koddocmail, mainfile, file_truelocation, file_syspath FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE id=" . $row2delete));
			$__koddocmail = $__file['koddocmail'];
			$__mainfile = $__file['mainfile'];
			$_QRY_clearDocFileID = mysqlQuery("UPDATE " . __MAIL_INCOMING_TABLENAME . " SET inbox_docFileID = NULL WHERE koddocmail IN ('" . $__koddocmail . "')");
			// 
			// Удаление записи в таблице файлов
			$_QRY_deleteFile = mysqlQuery("DELETE FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE id=" . $row2delete);
			// 
			// Удаление оригинального файла ($__tmp2) и сим-ссылки ($__tmp1) с диска
			$__tmp1 = unlink($__file['file_syspath']);
			$__tmp2 = unlink($__file['file_truelocation']);
			$output = ($__tmp1 && $__tmp2 && $_QRY_deleteFile && $_QRY_clearDocFileID) ? "1/" . $__koddocmail : "0/" . $__koddocmail;
			$output .= "/" . $__mainfile;
			// 
			// Обновление поля `inbox_docFileIDadd`
			$_QRY_attachedFiles = mysqlQuery("SELECT * FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE mainfile='0' AND koddocmail='" . $__koddocmail . "'");
			while ($_ROW_attachedFiles = mysqli_fetch_assoc($_QRY_attachedFiles)) {
				$inbox_docFileIDadd .= $_ROW_attachedFiles['id'] . ",";
			}
			// 
			// $lastsymbol = mb_substr($inbox_docFileIDadd, -1, 1);
			// if ($lastsymbol == ',') {
			// 	$inbox_docFileIDadd = substr_replace($inbox_docFileIDadd, '', -1, 0);
			// }
			$inbox_docFileIDadd = $inbox_docFileIDadd != "" ? $inbox_docFileIDadd : null;

			$updateDocmailTable = mysqlQuery("UPDATE " . __MAIL_INCOMING_TABLENAME . " SET inbox_docFileIDadd = '{$inbox_docFileIDadd}' WHERE koddocmail = '{$__koddocmail}'");
			// 
		} else {
			$output = "-1";
		}
	}
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
