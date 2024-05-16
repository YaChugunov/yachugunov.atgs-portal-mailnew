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
$koddocmail = isset($_POST['koddocmail']) ? $_POST['koddocmail'] : "";
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function updateCntComments($db, $id, $koddocmail) {

	$_timestamp = date("Y-m-d H:i:s");
	$_koddocmail = $koddocmail;

	$_reqDB_Users = $db->sql("SELECT firstname, middlename, lastname FROM users WHERE id=" . $_SESSION['id'])->fetchAll();
	$_username = $_reqDB_Users[0]['lastname'] . " " . $_reqDB_Users[0]['firstname'];
	$_commentID = "MA.I." . $_koddocmail . ".K." . time() . "-" . str_pad($_SESSION['id'], 4, "0", STR_PAD_LEFT);

	$_reqDB_contrKOD = $db->sql("SELECT outbox_docContractor_kodispolout FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE koddocmail=" . $_koddocmail)->fetchAll();
	$_contrKOD = ($_reqDB_contrKOD[0]['outbox_docContractor_kodispolout'] != NULL && $_reqDB_contrKOD[0]['outbox_docContractor_kodispolout'] != "") ? $_reqDB_contrKOD[0]['outbox_docContractor_kodispolout'] : "";

	$msgComm = "Удален комментарий ( <span class='text-info'>" . $id . "</span> ) к входящему документу пользователем <span class='text-success'>" . $_username . "</span> )";
	// Определяем количество комментариев по документы в журнале комментариев
	$_reqDB_cntComments = mysqli_fetch_assoc(mysqlQuery("SELECT COUNT(*) as CommCounts FROM " . __MAIL_OUTGOING_PREFIX . "_logComments WHERE action = 'COMMENT' AND koddocmail = '{$_koddocmail}'"));
	$db->update(__MAIL_OUTGOING_TABLENAME, array(
		'cntComments'	=> $_reqDB_cntComments['CommCounts']
	), array(
		'koddocmail' 	=> $_koddocmail,
	));
	$db->insert(__MAIL_OUTGOING_PREFIX . '_logChanges', array(
		'koddel'          => "",
		'recordType'      => "-1",
		'recordNum'       => null,
		'recordID'        => $_commentID,
		'timestamp'       => $_timestamp,
		'action'          => "COMM",
		'koddocmail'      => $_koddocmail,
		'userid'          => $_SESSION['id'],
		'oldSettings'     => null,
		'newSettings'     => null,
		'kodispol'        => $_contrKOD,
		'oldSettings'     => null,
		'newSettings'     => null,
		'changes'         => $_contrKOD,
		'changesOldVal'   => null,
		'changesNewVal'   => null,
		'changesTitle'    => "Удален комментарий",
		'changesStr'      => $msgComm,
		'changesText'     => $msgComm,
		'changesCount'    => null,
	));
}
#
#
function updateFields($db, $action, $id, $values, $row, $koddocmail) {

	$_timestamp = date("Y-m-d H:i:s");
	$_action = "COMMENT";
	$_koddocmail = $koddocmail;
	$_recordID = "MA.I." . $koddocmail . ".L." . time() . "-" . str_pad($_SESSION['id'], 4, "0", STR_PAD_LEFT);
	$_commentID = $_koddocmail . ".K." . time();

	$_reqDB_Users = $db->sql("SELECT firstname, middlename, lastname FROM users WHERE id=" . $_SESSION['id'])->fetchAll();
	$_username = $_reqDB_Users[0]['lastname'] . " " . $_reqDB_Users[0]['firstname'];

	$_reqDB_contrKOD = $db->sql("SELECT outbox_docContractor_kodispolout FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE koddocmail=" . $_koddocmail)->fetchAll();
	$_contrKOD = ($_reqDB_contrKOD[0]['outbox_docContractor_kodispolout'] != NULL && $_reqDB_contrKOD[0]['outbox_docContractor_kodispolout'] != "") ? $_reqDB_contrKOD[0]['outbox_docContractor_kodispolout'] : "";

	$msgComm = "";
	if ($action == "CRT") {
		// $msgComm .= "<p class='record-id small'><b>#" . $_recordID . "</b></p>";
		$msgComm .= "<p class='record-title1 comment'><span>Новый комментарий</span></p>";
		// $msgComm .= "<p class='record-id comment small'>#" . $_commentID . "</p>";
		$msgComm .= "<p class='record-new comment'>" . $values[__MAIL_OUTGOING_PREFIX . '_logComments']['commentText'] . "</p>";

		$db->update(__MAIL_OUTGOING_PREFIX . '_logComments', array(
			'koddel'					=> '',
			'timestamp'				=> $_timestamp,
			'action'					=> $_action,
			'commentID'				=> $_commentID,
			'userid'					=> $_SESSION['id'],
			'username'				=> $_username
		), array(
			'id' => $id,
		));
		$db->insert(__MAIL_OUTGOING_PREFIX . '_logChanges', array(
			'koddel'          => "",
			'recordType'      => "0",
			'recordNum'       => "1",
			'recordID'        => $_recordID,
			'timestamp'       => $_timestamp,
			'action'          => "COMM-CRT",
			'koddocmail'      => $_koddocmail,
			'userid'          => $_SESSION['id'],
			'kodispol'        => $_contrKOD,
			'oldSettings'     => null,
			'newSettings'     => null,
			'changes'         => $_contrKOD,
			'changesOldVal'   => null,
			'changesNewVal'   => $values[__MAIL_OUTGOING_PREFIX . '_logComments']['commentText'],
			'changesTitle'    => "<span class=''>Новый комментарий</span>",
			'changesStr'      => "<span class=''>Новый комментарий</span>",
			'changesText'     => $msgComm,
			'changesCount'    => null,
		));
	}
	if ($action == "PREUPD") {
		$_REQ = mysqlQuery("UPDATE " . __MAIL_OUTGOING_PREFIX . "_logComments SET prevcommentText = commentText WHERE id = {$id}");
	}
	#
	#
	if ($action == "UPD") {
		$_reqDB_3 = $db->sql("SELECT commentID FROM " . __MAIL_OUTGOING_PREFIX . "_logComments WHERE id=" . $id)->fetchAll();

		// $msgComm .= "<p class='record-id small'><b>#" . $_recordID . "</b></p>";
		$msgComm .= "<p class='record-title1'><span>Редактирование комментария</span></p>";
		// $msgComm .= "<p class='record-id comment small'>#" . $_reqDB_3[0]['commentID'] . "</p>";
		$msgComm .= "<p class='record-changes'>";
		$msgComm .= "<span class='comment old'>" . $row[__MAIL_OUTGOING_PREFIX . '_logComments']['prevcommentText'] . "</span>";
		$msgComm .= "<span class='comment arrows'>>>></span>";
		$msgComm .= "<span class='comment new'>" . $row[__MAIL_OUTGOING_PREFIX . '_logComments']['commentText'] . "</span>";
		$msgComm .= "</p>";
		$db->update(__MAIL_OUTGOING_PREFIX . '_logComments', array(
			'update_timestamp' => $_timestamp,
			'update_userid'		 => $_SESSION['id'],
			'update_username'	 => $_username
		), array(
			'id' => $id,
		));
		$db->insert(__MAIL_OUTGOING_PREFIX . '_logChanges', array(
			'koddel'          => "",
			'recordType'      => "0",
			'recordNum'       => "1",
			'recordID'        => $_recordID,
			'timestamp'       => $_timestamp,
			'action'          => "COMM-UPD",
			'koddocmail'      => $_koddocmail,
			'userid'          => $_SESSION['id'],
			'kodispol'        => $_contrKOD,
			'oldSettings'     => null,
			'newSettings'     => null,
			'changes'         => $_contrKOD,
			'changesOldVal'   => $row[__MAIL_OUTGOING_PREFIX . '_logComments']['prevcommentText'],
			'changesNewVal'   => $row[__MAIL_OUTGOING_PREFIX . '_logComments']['commentText'],
			'changesTitle'    => "<span class=''>Редактирование комментария</span>",
			'changesStr'      => "<span class=''>Редактирование комментария</span>",
			'changesText'     => $msgComm,
			'changesCount'    => null,
		));
	}
	#
	#
	if ($action == "DEL") {
		$_reqDB_3 = $db->sql("SELECT commentID FROM " . __MAIL_OUTGOING_PREFIX . "_logComments WHERE id=" . $id)->fetchAll();

		// $msgComm .= "<p class='record-id small'><b>#" . $_recordID . "</b></p>";
		$msgComm .= "<p class='record-title1'><span>Удаление комментария</span></p>";
		// $msgComm .= "<p class='record-id comment small'>#" . $_reqDB_3[0]['commentID'] . "</p>";
		$msgComm .= "<p class='record-changes'><span class='comment delete'>Удален комментарий к текущему документу.</span></p>";
		$db->insert(__MAIL_OUTGOING_PREFIX . '_logChanges', array(
			'koddel'          => "",
			'recordType'      => "-1",
			'recordNum'       => null,
			'recordID'        => $_recordID,
			'timestamp'       => $_timestamp,
			'action'          => "COMM-DEL",
			'koddocmail'      => $_koddocmail,
			'userid'          => $_SESSION['id'],
			'kodispol'        => $_contrKOD,
			'oldSettings'     => null,
			'newSettings'     => null,
			'changes'         => $_contrKOD,
			'changesOldVal'   => $_reqDB_3[0]['commentText'],
			'changesNewVal'   => null,
			'changesTitle'    => "<span class=''>Удаление комментария</span>",
			'changesStr'      => "<span class=''>Удаление комментария</span>",
			'changesText'     => $msgComm,
			'changesCount'    => null,
		));
	}
}


/*
Example PHP implementation used for the index.html example
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
Editor::inst($db, __MAIL_OUTGOING_PREFIX . '_logComments')
	->fields(
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.id'),
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.koddel'),
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.timestamp')
			->getFormatter(Format::datetime(
				'Y-m-d H:i:s',
				'd.m.Y H:i:s'
			)),
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.action'),
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.koddocmail'),
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.userid'),
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.username'),
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.commentID'),
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.prevcommentText'),
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.commentText'),
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.commentAdd'),
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.update_timestamp')
			->getFormatter(Format::datetime(
				'Y-m-d H:i:s',
				'd.m.Y H:i:s'
			)),
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.update_userid'),
		Field::inst(__MAIL_OUTGOING_PREFIX . '_logComments.update_username'),

		Field::inst('users.firstname'),
		Field::inst('users.middlename'),
		Field::inst('users.lastname')
	)
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
	->on('postCreate', function ($editor, $id, $values, $row) use ($koddocmail) {
		// updateCntComments($editor->db(), $id, $koddocmail);
		updateFields($editor->db(), 'CRT', $id, $values, $row, $koddocmail);
	})
	->on('preEdit', function ($editor, $id, $values) use ($koddocmail) {
		// updateCntComments($editor->db(), $id, $koddocmail);
		updateFields($editor->db(), 'PREUPD', $id, $values, null, $koddocmail);
	})
	->on('postEdit', function ($editor, $id, $values, $row) use ($koddocmail) {
		// updateCntComments($editor->db(), $id, $koddocmail);
		updateFields($editor->db(), 'UPD', $id, $values, $row, $koddocmail);
	})
	->on('preRemove', function ($editor, $id, $values) use ($koddocmail) {
		updateFields($editor->db(), 'DEL', $id, null, null, $koddocmail);
	})

	->where(__MAIL_OUTGOING_PREFIX . '_logComments.koddocmail', $koddocmail, '=')
	->where(__MAIL_OUTGOING_PREFIX . '_logComments.koddel', 'deleted', '!=')
	->leftJoin('users', 'users.ID', '=', __MAIL_OUTGOING_PREFIX . '_logComments.userid')

	->process($_POST)
	->json();
