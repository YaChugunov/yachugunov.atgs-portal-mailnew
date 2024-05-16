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
Editor::inst($db, __MAIL_INCOMING_PREFIX . '_logChanges')
	->fields(
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.id'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.koddel'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.recordType'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.recordNum'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.timestamp')
			->getFormatter(Format::datetime(
				'Y-m-d H:i:s',
				'd.m.Y H:i:s'
			)),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.action'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.koddocmail'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.userid'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.kodispol'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.oldSettings'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.newSettings'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.changes'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.changesOldVal'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.changesNewVal'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.changesTitle'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.changesStr'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.changesText'),
		Field::inst(__MAIL_INCOMING_PREFIX . '_logChanges.changesCount'),

		Field::inst('mailbox_sp_users.namezayvtel'),
		Field::inst('mailbox_sp_users.namezayvfio')
	)

	->where(__MAIL_INCOMING_PREFIX . '_logChanges.koddocmail', $_POST['koddocmail'], '=')
	->where(__MAIL_INCOMING_PREFIX . '_logChanges.recordType', "2", '<')
	->leftJoin('mailbox_sp_users', 'mailbox_sp_users.ID', '=', __MAIL_INCOMING_PREFIX . '_logChanges.userid')

	->process($_POST)
	->json();