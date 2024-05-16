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
$koddocmail = isset($_POST['koddocmail']) ? $_POST['koddocmail'] : "";
$_reqDB = mysqli_fetch_assoc(mysqlQuery("SELECT outbox_docFileID, outbox_docFileIDadd FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE koddocmail='{$koddocmail}'"));
$rowIDS = $_reqDB['outbox_docFileIDadd'] != "" && $_reqDB['outbox_docFileIDadd'] != NULL ? substr($_reqDB['outbox_docFileIDadd'], 0, -1) : "-";
$IDS = $_reqDB['outbox_docFileID'] != "" && $_reqDB['outbox_docFileID'] != NULL ? $_reqDB['outbox_docFileID'] : "-";
#
#
/*
 * Example PHP implementation used for the index.html example
*/
// DataTables PHP library
require(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/libs/Datatables/Editor-PHP-1.9.7/lib/DataTables.php');

// Alias Editor classes so they are easy to use
use
	DataTables\Editor,
	DataTables\Editor\Field,
	DataTables\Editor\Format,
	DataTables\Editor\Mjoin,
	DataTables\Editor\Options,
	DataTables\Editor\Upload,
	DataTables\Editor\Validate,
	DataTables\Editor\ValidateOptions;

// Build our Editor instance and process the data coming from _POST
Editor::inst($db, __MAIL_OUTGOING_FILES_TABLENAME)
	->fields(
		Field::inst(__MAIL_OUTGOING_FILES_TABLENAME . '.id'),
		Field::inst(__MAIL_OUTGOING_FILES_TABLENAME . '.file_originalname'),
		Field::inst(__MAIL_OUTGOING_FILES_TABLENAME . '.file_webpath'),
		Field::inst(__MAIL_OUTGOING_FILES_TABLENAME . '.koddocmail'),
		Field::inst(__MAIL_OUTGOING_FILES_TABLENAME . '.mainfile'),
		Field::inst(__MAIL_OUTGOING_FILES_TABLENAME . '.comment')
	)

	->on('preGet', function ($editor, $id) use ($IDS, $rowIDS) {
		$editor->where(function ($q) use ($IDS, $rowIDS) {
			// $q->where(__MAIL_OUTGOING_FILES_TABLENAME.'.id', '( SELECT id FROM '.__MAIL_OUTGOING_FILES_TABLENAME.' WHERE id IN (' . $rowIDS . ') OR id="' . $IDS . '" )', 'IN', false);
			$q->where(__MAIL_OUTGOING_FILES_TABLENAME . '.id', '( SELECT id FROM ' . __MAIL_OUTGOING_FILES_TABLENAME . ' WHERE id IN (' . $rowIDS . ') )', 'IN', false);
			// $q->order(__MAIL_OUTGOING_FILES_TABLENAME.'.mainfile desc');
			$q->order(__MAIL_OUTGOING_FILES_TABLENAME . '.file_originalname asc');
		});
	})

	->where(__MAIL_OUTGOING_FILES_TABLENAME . '.koddocmail', $koddocmail, '=')

	->process($_POST)
	->json();
