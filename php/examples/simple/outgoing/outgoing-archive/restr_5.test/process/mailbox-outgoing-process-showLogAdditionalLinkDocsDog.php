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
$_reqDB = mysqli_fetch_assoc(mysqlQuery("SELECT dognet_rowIDs_links FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE koddocmail='{$koddocmail}'"));
$rowIDS = $_reqDB['dognet_rowIDs_links'] != "" && $_reqDB['dognet_rowIDs_links'] != null ? $_reqDB['dognet_rowIDs_links'] : null;
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
Editor::inst($db, 'dognet_docbase')
	->fields(
		Field::inst('dognet_docbase.ID'),
		Field::inst('dognet_docbase.koddoc'),
		Field::inst('dognet_docbase.docnumber'),
		Field::inst('dognet_docbase.docnameshot'),
		Field::inst('dognet_docbase.kodzakaz'),

		Field::inst('sp_contragents.kodcontragent'),
		Field::inst('sp_contragents.nameshort'),
		Field::inst('sp_contragents.namefull'),

		Field::inst('sp_objects.kodobject'),
		Field::inst('sp_objects.nameobjectshot'),
	)

	->on('preGet', function ($editor, $id) use ($rowIDS) {
		$editor->where(function ($q) use ($rowIDS) {
			if ($rowIDS != null && !empty($rowIDS)) {
				$q->where('dognet_docbase.ID', '( SELECT ID FROM dognet_docbase WHERE ID IN (' . $rowIDS . ') )', 'IN', false);
				$q->order('dognet_docbase.docnumber desc');
			} else {
				$q->where('dognet_docbase.ID', '-1');
			}
		});
	})

	->leftJoin('sp_contragents', 'sp_contragents.kodcontragent', '=', 'dognet_docbase.kodzakaz')
	->leftJoin('sp_objects', 'sp_objects.kodobject', '=', 'dognet_docbase.kodobject')

	->process($_POST)
	->json();
