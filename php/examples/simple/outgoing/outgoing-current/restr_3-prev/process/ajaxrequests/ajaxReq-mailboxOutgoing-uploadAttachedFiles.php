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

// ID пользователя ( $_SESSION[id] )
$_userID = isset($_POST['userID']) && $_POST['userID'] != "" ? $_POST['userID'] : "";
// ID записи в таблице mailbox_outgoing_files в виде "row_XXXXX"
$_rowID = isset($_POST['rowID']) && $_POST['rowID'] != "" ? str_replace('row_', '', $_POST['rowID']) : "";
// Номер документа в таблице mailbox_outgoing
$_docID = isset($_POST['docID']) && $_POST['docID'] != "" ? $_POST['docID'] : "";
// Код документа в таблице mailbox_outgoing
$_koddocmail = isset($_POST['koddocmail']) ? $_POST['koddocmail'] : "";

// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения нового номера файла (file_id) для таблицы файлов
//
function newFileID() {
	$query = mysqlQuery("SELECT MAX(file_id) as lastFileID FROM " . __MAIL_OUTGOING_FILES_TABLENAME . " ORDER BY id DESC");
	$row = mysqli_fetch_assoc($query);
	$newFileID = $row['lastFileID'];
	$newFileID++;
	return $newFileID;
}
function currDocID($currID) {
	$query = mysqlQuery("SELECT outbox_docID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $currID);
	$row = mysqli_fetch_assoc($query);
	$currDocID = $row['outbox_docID'];
	$_SESSION['editedOutboxDocID'] = $currDocID;
	return $currDocID;
}
function reArrayFiles(&$file_post) {
	$file_ary = array();
	$file_count = count($file_post['name']);
	$file_keys = array_keys($file_post);

	for ($i = 0; $i < $file_count; $i++) {
		foreach ($file_keys as $key) {
			$file_ary[$i][$key] = $file_post[$key][$i];
		}
	}
	return $file_ary;
}
// Snippet from PHP Share: http://www.phpshare.org
function formatSizeUnits($bytes) {
	if ($bytes >= 1073741824) {
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	} elseif ($bytes >= 1048576) {
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	} elseif ($bytes >= 1024) {
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	} elseif ($bytes > 1) {
		$bytes = $bytes . ' bytes';
	} elseif ($bytes == 1) {
		$bytes = $bytes . ' byte';
	} else {
		$bytes = '0 bytes';
	}
	return $bytes;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
#
/* 
	ПУТИ ДЛЯ СОХРАНЕНИЯ ЗАГРУЖАЕМЫХ ФАЙЛОВ
	> Массив директорий и вспомогательных параметров
*/
if ("POST" == $_SERVER["REQUEST_METHOD"]) {
	$d = dir(__MAIL_OUTGOING_STORAGE_SERVERPATH . __MAIL_OUTGOING_STORAGE_SUBFOLDER . "/");
	$docpath = $d->path;
	$webpath = __SERVICENAME_MAILNEW . __MAIL_OUTGOING_STORAGE_WORKPATH . __MAIL_OUTGOING_STORAGE_SUBFOLDER . "/";
	$syspath = __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_STORAGE_WORKPATH . __MAIL_OUTGOING_STORAGE_SUBFOLDER . "/";
	$newFileID = newFileID();
	$varFileArray = [
		"year"    => date("Y"),
		"fID"     => "",
		"docpath" => $docpath,
		"webpath" => $webpath,
		"syspath" => $syspath,
	];
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
// Название <input type="file">
$input_name = 'file';

// Разрешенные расширения файлов.
$allow = array();

// Запрещенные расширения файлов.
$deny = array(
	'phtml', 'php', 'php3', 'php4', 'php5', 'php6', 'php7', 'phps', 'cgi', 'pl', 'asp',
	'aspx', 'shtml', 'shtm', 'htaccess', 'htpasswd', 'ini', 'log', 'sh', 'js', 'html',
	'htm', 'css', 'sql', 'spl', 'scgi', 'fcgi', 'exe'
);

// Директория куда будут загружаться файлы.
// $path = __DIR__ . '/uploads/';
$path = $varFileArray['docpath'];

$data = array();

if (!isset($_FILES[$input_name])) {
	$error = 'Файлы не загружены.';
} else {
	// Преобразуем массив $_FILES в удобный вид для перебора в foreach.
	$files = reArrayFiles($_FILES[$input_name]);
	//
	$outbox_docFileIDadd = '';
	// 
	foreach ($files as $file) {
		$target_dir = $varFileArray['docpath'];
		$target_file = $target_dir . basename($file["name"]);
		$__pref = date('Y') . $_SESSION['id'] . date('mdHis');
		$__name = $__pref . "-" . $file['name'];
		$__nameTmp = $file["tmp_name"];

		$parts = pathinfo($target_file);
		$__ext = $parts['extension'];

		$md5 = md5(uniqid());
		$__nameMD5 = "{$md5}.{$__ext}";

		$error = $success = '';
		$tableB = '<table class="table table-striped table-listattachments-inform"><tbody>';
		$tableE = '</tbody></table>';
		// Проверим на ошибки загрузки.
		if (!empty($file['error']) || empty($file['tmp_name'])) {
			$error = 'Не удалось загрузить файл.';
		} elseif ($file['tmp_name'] == 'none' || !is_uploaded_file($file['tmp_name'])) {
			$error = 'Не удалось загрузить файл.';
		} else {
			// Оставляем в имени файла только буквы, цифры и некоторые символы.
			$pattern = "[^a-zа-яё0-9,~!@#%^-_\$\?\(\)\{\}\[\]\.]";
			$name = mb_eregi_replace($pattern, '-', $file['name']);
			$name = mb_ereg_replace('[-]+', '-', $name);
			$parts = pathinfo($name);

			if (empty($name) || empty($parts['extension'])) {
				$error = 'Недопустимый тип файла';
			} elseif (!empty($allow) && !in_array(strtolower($parts['extension']), $allow)) {
				$error = 'Недопустимый тип файла';
			} elseif (!empty($deny) && in_array(strtolower($parts['extension']), $deny)) {
				$error = 'Недопустимый тип файла';
			} else {

				// $counter = 0;
				// $fname = $name;
				// while (file_exists($path . $name)) {
				// 	$fname = $name . $counter . '.' . $__ext;
				// 	$counter++;
				// };
				// 

				// Перемещаем файл в директорию.
				if (move_uploaded_file($file['tmp_name'], $path . $__name)) {
					// Делаем запись в БД о добавленном файле
					$file_year = $varFileArray['year'];
					$file_id = $varFileArray['year'] . "00" . $_docID;
					$file_name = $__name;
					$file_originalname = $file['name'];

					$path_parts = pathinfo($varFileArray['docpath'] . $__name);
					$file_extension = $path_parts['extension'];

					$file_symname = "{$md5}.{$file_extension}";
					$file_size = filesize($varFileArray['docpath'] . $__name);
					$file_truelocation = $varFileArray['docpath'] . $__name;
					$file_syspath = $varFileArray['syspath'] . $file_symname;
					$file_webpath = $varFileArray['webpath'] . $file_symname;
					$file_url = str_replace($_SERVER['DOCUMENT_ROOT'], 'http://' . $_SERVER['HTTP_HOST'], $varFileArray['syspath'] . $file_symname);

					// Далее можно сохранить название файла в БД и т.п.
					symlink($file_truelocation, $file_syspath);
					// 
					$insertRowToFilesTable = $db_handle->runQuery("INSERT INTO " . __MAIL_OUTGOING_FILES_TABLENAME . " (koddocmail, mainfile, flag, file_year, file_id, file_name, file_originalname, file_extension, file_symname, file_size, file_truelocation, file_syspath, file_webpath, file_url) VALUES ('{$_koddocmail}', '0', 'PREUPL', '{$file_year}', '{$file_id}', '{$file_name}', '{$file_originalname}', '{$file_extension}', '{$file_symname}', '{$file_size}', '{$file_truelocation}', '{$file_syspath}', '{$file_webpath}', '{$file_url}')");
					// $newFileID = mysqli_insert_id($_QRY_SaveToDB);
					$addedRowID = $db_handle->lastInsertID();
					$outbox_docFileIDadd .= $addedRowID . ",";
					// 
					$success .= '<td><span data-toggle="popover" data-content="Файл успешно загружен, но еще не прикреплен" class="attached-file-temporary"><i class="fa-regular fa-clock"></i></span></td>';
					$success .= '<td><a href="' . $file_url . '" target="_blank">' . $file_originalname . '</a> ( ' . formatSizeUnits($file_size) . ' )</td>';
					$success .= '<td><span rowid="' . $addedRowID . '" koddocmail="' . $_koddocmail . '" class="remove-file"><i class="fa-solid fa-trash-can"></i></span></td>';
				} else {
					$error .= '<td>Не удалось загрузить файл.</td>';
				}
			}
		}

		if (!empty($success)) {
			$data[] = '<tr class="text-default">' . $success . '</tr>';
		}
		if (!empty($error)) {
			$data[] = '<tr class="text-danger">' . $error . '</tr>';
		}
	}
	//
	// 
}
unset($_POST);
// Вывод сообщений о результате загрузки.
header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
exit();