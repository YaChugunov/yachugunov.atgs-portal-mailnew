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
// session_start();# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
$koddocmail = isset($_POST['koddocmail']) ? $_POST['koddocmail'] : "";
$_reqDB = mysqli_fetch_assoc(mysqlQuery("SELECT outbox_rowIDs_links FROM " . __MAIL_INCOMING_TABLENAME . " WHERE koddocmail='{$koddocmail}'"));
$rowIDS = $_reqDB['outbox_rowIDs_links'] != "" && $_reqDB['outbox_rowIDs_links'] != null ? $_reqDB['outbox_rowIDs_links'] : null;
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
Editor::inst($db, __MAIL_OUTGOING_TABLENAME)
	->fields(
		Field::inst(__MAIL_OUTGOING_TABLENAME . '.ID'),
		Field::inst(__MAIL_OUTGOING_TABLENAME . '.koddocmail'),
		Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docID'),
		Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docAbout'),

		Field::inst('sp_contragents.kodcontragent'),
		Field::inst('sp_contragents.nameshort'),
	)

	->on('preGet', function ($editor, $id) use ($rowIDS) {
		$editor->where(function ($q) use ($rowIDS) {
			if ($rowIDS != null && !empty($rowIDS)) {
				$q->where(__MAIL_OUTGOING_TABLENAME . '.ID', '( SELECT ID FROM ' . __MAIL_OUTGOING_TABLENAME . ' WHERE ID IN (' . $rowIDS . ') )', 'IN', false);
				$q->order(__MAIL_OUTGOING_TABLENAME . '.outbox_docID desc');
			} else {
				$q->where(__MAIL_OUTGOING_TABLENAME . '.ID', '-1');
			}
		});
	})

	->leftJoin('sp_contragents', 'sp_contragents.kodcontragent', '=', __MAIL_OUTGOING_TABLENAME . '.outbox_docRecipient_kodzakaz  ')

	->process($_POST)
	->json();
