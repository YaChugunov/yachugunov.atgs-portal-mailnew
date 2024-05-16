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

$data = array();
$error = $success = $output = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		return 0;
	} else {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $_koddocmail != "") {
			$_QRY_FilesList = mysqlQuery("SELECT * FROM " . __MAIL_OUTGOING_FILES_TABLENAME . " WHERE mainfile = '0' AND koddocmail ='" . $_koddocmail . "'");
			while ($_ROW_FilesList = mysqli_fetch_assoc($_QRY_FilesList)) {
				if ($_ROW_FilesList['file_url'] == "") {
					$error = 'Битая ссылка на файл';
				} elseif ($_ROW_FilesList['file_originalname'] == "") {
					$error = 'Ошибка в имени файла';
				} else {
					if ($_ROW_FilesList['flag'] == 'PREUPL') {
						$success = '<span class="attached-file-temporary"><i class="fa-regular fa-clock"></i></span>';
					} elseif ($_ROW_FilesList['flag'] == 'CHU') {
						$success = '<span class="attached-file-clipped"><i class="fa-solid fa-paperclip"></i></span>';
					} else {
						$success = '';
					}
					$success .= '///';
					// 
					$success .= '<a href="' . $_ROW_FilesList['file_url'] . '" target="_blank" title="' . $_ROW_FilesList['file_originalname'] . '">' . $_ROW_FilesList['file_originalname'] . '</a>';
					$success .= '///';
					$success .= '<span rowid="' . $_ROW_FilesList['id'] . '" koddocmail="' . $_koddocmail . '" class="remove-file"><i class="fa-solid fa-trash-can"></i></span>';
				}
				if (!empty($success)) {
					$output .= $success;
					$output .= '|||';
				}
				//
				if (!empty($error)) {
					$output .= $error;
					$output .= '|||';
				}
			}
			$output = ($output != "") ? substr($output, 0, -3) : $output;
		} else {
			$output = '<span style="color: red">Что-то пошло не так...' . $_koddocmail . '</span>';
		}
	}
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;