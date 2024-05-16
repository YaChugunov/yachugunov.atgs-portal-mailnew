<?php
date_default_timezone_set('Europe/Moscow');
# Подключаем конфигурационный файл
require($_SERVER['DOCUMENT_ROOT'] . "/config.inc.php");
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаемся к базе
require_once(__DIR_ROOT . __SERVICENAME_MAIL . '/_assets/drivers/db_connection.php');
require_once(__DIR_ROOT . __SERVICENAME_MAIL . '/_assets/drivers/db_controller.php');
$db_handle = new DBController();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем общие функции безопасности
require_once(__DIR_ROOT . '/_assets/functions/funcSecure.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем собственные функции сервиса Почта
require_once(__DIR_ROOT . __SERVICENAME_MAIL . '/_assets/functions/funcMail.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Включаем режим сессии
// session_start();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
$__startDate = $_SESSION['out_startTableDate'];
$__endDate = $_SESSION['out_endTableDate'];
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция проверки строки на правильность даты
//
function validateDate($date, $format = 'Y-m-d') {
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) === $date;
}
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения нового кода документа/записи (koddocmail) для таблицы записей
//
function newKoddocmail() {
	$query = mysqlQuery("SELECT MAX(koddocmail) as lastKod FROM mailbox_outgoing_test ORDER BY id DESC");
	$row = mysqli_fetch_assoc($query);
	$newKod = $row['lastKod'];
	$newKod++;
	return $newKod;
}
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения текущего номера документа/записи (docID) открытого для редактирования
//
function editingDocID($id) {
	$query = mysqlQuery("SELECT outbox_docID FROM mailbox_outgoing_test WHERE id=" . $id);
	$row = mysqli_fetch_assoc($query);
	$DocID = $row['outbox_docID'];
	$_SESSION['editingDocID'] = $DocID;
	return $DocID;
}
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения нового номера документа/записи (docID) для таблицы записей
//
function newDocID() {
	$query = mysqlQuery("SELECT MAX(outbox_docID) as lastDocID FROM mailbox_outgoing_test WHERE YEAR(outbox_docDate)=YEAR(NOW()) ORDER BY id DESC");
	$row = mysqli_fetch_assoc($query);
	$newDocID = $row['lastDocID'];
	$newDocID++;
	return $newDocID;
}
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения нового номера файла (file_id) для таблицы файлов
//
function newFileID() {
	$query = mysqlQuery("SELECT MAX(file_id) as lastFileID FROM mailbox_outgoing_files_test ORDER BY id DESC");
	$row = mysqli_fetch_assoc($query);
	$newFileID = $row['lastFileID'];
	$newFileID++;
	return $newFileID;
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
	$webpath = __SERVICENAME_MAIL . __MAIL_OUTGOING_STORAGE_WORKPATH . __MAIL_OUTGOING_STORAGE_SUBFOLDER . "/";
	$syspath = __DIR_ROOT . __SERVICENAME_MAIL . __MAIL_OUTGOING_STORAGE_WORKPATH . __MAIL_OUTGOING_STORAGE_SUBFOLDER . "/";
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
# Записываем в БД в таблицу mailbox_log отчет о действии пользователя
function insRecord2log($db, $action, $id, $values) {
	$query = $db->sql("SELECT MAX(outbox_docID) as lastDocID FROM mailbox_outgoing_test WHERE YEAR(outbox_docDate)=YEAR(NOW()) ORDER BY id DESC")->fetchAll();
	$newDocID = $query[0]['lastDocID'];
	$newDocID++;
	$db->insert('mailbox_log', array(
		'log_whoID'		=> $_SESSION['id'],
		'log_whoNAME'	=> $_SESSION['lastname'],
		'log_action'	=> $action,
		'log_values'	=> json_encode($values),
		'log_row'		=> $id,
		// 		'log_docID'		=> json_encode( $values['inbox_docID'], JSON_NUMERIC_CHECK ),
		'log_docID'		=> $newDocID,
		'log_when'		=> date('Y-m-d H:i:s')
	));
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
function fixCreate($db, $action, $id, $values) {
	$db->update('mailbox_outgoing_test', array(
		'outbox_docCreatedByID'	=> $_SESSION['id'],
		'outbox_docCreatedWhen'	=> date('Y-m-d H:i:s'),
		'outbox_docCreatedBySTR'	=> $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
		'outbox_docUpdatedByID'	=> $_SESSION['id'],
		'outbox_docUpdatedBySTR'	=> $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
		'outbox_docUpdatedWhen'	=> date('Y-m-d H:i:s')
	), array(
		'id' => $id
	));
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
function mbLcfirst($str) {
	return mb_strtolower(mb_substr($str, 0, 1)) . mb_substr($str, 1);
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
function fixLog($db, $action, $id, $values) {

	$__USERID   = $_SESSION['id'];
	// Проверем был ли отмечен документ как выполненный
	// @ $checkout
	$reqDB = $db->sql("SELECT * FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
	$arrValues = [
		'outbox_docID' => $values['mailbox_outgoing_test']['outbox_docID'],
		'outbox_docDate' => validateDate($values['mailbox_outgoing_test']['outbox_docDate'], "d.m.Y H:i:s") ? date("Y-m-d H:i:s", strtotime($values['mailbox_outgoing_test']['outbox_docDate'])) : null,
		'outbox_docType' => $values['mailbox_outgoing_test']['outbox_docType'],
		'outbox_docAbout' => $values['mailbox_outgoing_test']['outbox_docAbout'],
		'outbox_docRecipient_kodzakaz' => $values['mailbox_outgoing_test']['outbox_docRecipient_kodzakaz'],
		'outbox_docSourceID' => $values['mailbox_outgoing_test']['outbox_docSourceID'],
		'outbox_docSourceDate' => validateDate($values['mailbox_outgoing_test']['outbox_docSourceDate'], "d.m.Y") ? date("Y-m-d", strtotime($values['mailbox_outgoing_test']['outbox_docSourceDate'])) : null,
		// 'inbox_koddocmail_rel' => $values['mailbox_outgoing_test']['inbox_koddocmail_rel'],
		// 'inbox_docID_rel' => $values['mailbox_outgoing_test']['inbox_docID_rel'],
		'inbox_rowIDadd_rel' => ($values['mailbox_outgoing_test']['inbox_rowIDadd_rel'] == "") ? null : $values['mailbox_outgoing_test']['inbox_rowIDadd_rel'],
		// 'outbox_docFileID' => $values['mailbox_outgoing_test']['outbox_docFileIDtmp'],
		// 'outbox_docFileIDadd' => $values['mailbox_outgoing_test']['outbox_docFileIDadd'],
		'outbox_docSender_kodzayvtel' => $values['mailbox_outgoing_test']['outbox_docSender_kodzayvtel'],
		'outbox_docContractor_kodispolout' => $values['mailbox_outgoing_test']['outbox_docContractor_kodispolout'],
		'outbox_controlIspolActive' => $values['mailbox_outgoing_test']['outbox_controlIspolActive'],
		'outbox_controlIspolUseDeadline' => $values['mailbox_outgoing_test']['outbox_controlIspolUseDeadline'],
		'outbox_docDateDeadline' => validateDate($values['mailbox_outgoing_test']['outbox_docDateDeadline'], "d.m.Y H:i:s") ? date("Y-m-d H:i:s", strtotime($values['mailbox_outgoing_test']['outbox_docDateDeadline'])) : null,
		'outbox_controlIspolMailReminder1' => $values['mailbox_outgoing_test']['outbox_controlIspolMailReminder1'],
		'outbox_controlIspolMailReminder2' => $values['mailbox_outgoing_test']['outbox_controlIspolMailReminder2'],
		'outbox_controlIspolCheckout' => $values['mailbox_outgoing_test']['outbox_controlIspolCheckout'],
	];
	$arrFromDB = [
		'outbox_docID' => $reqDB[0]['outbox_docID'],
		'outbox_docDate' => validateDate($reqDB[0]['outbox_docDate'], "Y-m-d H:i:s") ? $reqDB[0]['outbox_docDate'] : null,
		'outbox_docType' => $reqDB[0]['outbox_docType'],
		'outbox_docAbout' => $reqDB[0]['outbox_docAbout'],
		'outbox_docRecipient_kodzakaz' => $reqDB[0]['outbox_docRecipient_kodzakaz'],
		'outbox_docSourceID' => $reqDB[0]['outbox_docSourceID'],
		'outbox_docSourceDate' => validateDate($reqDB[0]['outbox_docSourceDate'], "Y-m-d") ? $reqDB[0]['outbox_docSourceDate'] : null,
		// 'inbox_koddocmail_rel' => $reqDB[0]['inbox_koddocmail_rel'],
		// 'inbox_docID_rel' => $reqDB[0]['inbox_docID_rel'],
		'inbox_rowIDadd_rel' => $reqDB[0]['inbox_rowIDadd_rel'],
		// 'outbox_docFileID' => $reqDB[0]['outbox_docFileID'],
		// 'outbox_docFileIDadd' => $reqDB[0]['outbox_docFileIDadd'],
		'outbox_docSender_kodzayvtel' => $reqDB[0]['outbox_docSender_kodzayvtel'],
		'outbox_docContractor_kodispolout' => $reqDB[0]['outbox_docContractor_kodispolout'],
		'outbox_controlIspolActive' => $reqDB[0]['outbox_controlIspolActive'],
		'outbox_controlIspolUseDeadline' => $reqDB[0]['outbox_controlIspolUseDeadline'],
		'outbox_docDateDeadline' => validateDate($reqDB[0]['outbox_docDateDeadline'], "Y-m-d H:i:s") ? $reqDB[0]['outbox_docDateDeadline'] : null,
		'outbox_controlIspolMailReminder1' => $reqDB[0]['outbox_controlIspolMailReminder1'],
		'outbox_controlIspolMailReminder2' => $reqDB[0]['outbox_controlIspolMailReminder2'],
		'outbox_controlIspolCheckout' => $reqDB[0]['outbox_controlIspolCheckout'],
	];
	//
	//
	$timestamp = date('Y-m-d H:i:s');
	$koddocmail = $reqDB[0]['koddocmail'];
	$recordID = "MA.O." . $koddocmail . ".L." . time() . "-" . str_pad($_SESSION['id'], 4, "0", STR_PAD_LEFT);
	//
	//
	/*
	$results = array();
	foreach ($one as $key => $value) {
	  $result = array();
	  if ($value != $two->$key) {
		$result['key_name'] = $key;
		$result['old_value'] = $value;
		$result['new_value'] = $two->$key;
		array_push($results, $result);
	  }
	}
  */
	if ($action == "CRT") {
		$reqDB = $db->sql("SELECT * FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
		$arrFromDB = [
			'outbox_docID' => $reqDB[0]['outbox_docID'],
			'outbox_docDate' => validateDate($reqDB[0]['outbox_docDate'], "Y-m-d H:i:s") ? $reqDB[0]['outbox_docDate'] : null,
			'outbox_docType' => $reqDB[0]['outbox_docType'],
			'outbox_docAbout' => $reqDB[0]['outbox_docAbout'],
			'outbox_docRecipient_kodzakaz' => $reqDB[0]['outbox_docRecipient_kodzakaz'],
			'outbox_docSourceID' => $reqDB[0]['outbox_docSourceID'],
			'outbox_docSourceDate' => validateDate($reqDB[0]['outbox_docSourceDate'], "Y-m-d") ? $reqDB[0]['outbox_docSourceDate'] : null,
			// 'inbox_koddocmail_rel' => $reqDB[0]['inbox_koddocmail_rel'],
			// 'inbox_docID_rel' => $reqDB[0]['inbox_docID_rel'],
			'inbox_rowIDadd_rel' => $reqDB[0]['inbox_rowIDadd_rel'],
			// 'outbox_docFileID' => $reqDB[0]['outbox_docFileID'],
			// 'outbox_docFileIDadd' => $reqDB[0]['outbox_docFileIDadd'],
			'outbox_docSender_kodzayvtel' => $reqDB[0]['outbox_docSender_kodzayvtel'],
			'outbox_docContractor_kodispolout' => $reqDB[0]['outbox_docContractor_kodispolout'],
			'outbox_controlIspolActive' => $reqDB[0]['outbox_controlIspolActive'],
			'outbox_controlIspolUseDeadline' => $reqDB[0]['outbox_controlIspolUseDeadline'],
			'outbox_docDateDeadline' => validateDate($reqDB[0]['outbox_docDateDeadline'], "Y-m-d H:i:s") ? $reqDB[0]['outbox_docDateDeadline'] : null,
			'outbox_controlIspolMailReminder1' => $reqDB[0]['outbox_controlIspolMailReminder1'],
			'outbox_controlIspolMailReminder2' => $reqDB[0]['outbox_controlIspolMailReminder2'],
			'outbox_controlIspolCheckout' => $reqDB[0]['outbox_controlIspolCheckout'],
		];
		$oldSettings = json_decode(json_encode($arrFromDB), JSON_FORCE_OBJECT);
		// 
		$reqDB1 = $db->sql("SELECT kodispol FROM mailbox_sp_users WHERE id='" . $_SESSION['id'] . "'")->fetchAll();
		$kodispol = (!empty($reqDB1) && $reqDB1[0]['kodispol'] != "") ? $reqDB1[0]['kodispol'] : null;
		//
		$db->insert('mailbox_outgoing_logChanges', array(
			'koddel'          => "",
			'recordID'        => $recordID,
			'timestamp'       => $timestamp,
			'action'          => $action,
			'koddocmail'      => $reqDB[0]['koddocmail'],
			'userid'          => $_SESSION['id'],
			'kodispol'        => $kodispol,
			'oldSettings'     => null,
			'newSettings'     => json_encode($oldSettings),
			'changes'         => null,
			'changesText'     => "Создана запись о документе",
			'changesCount'    => null,
		));
	}
	//
	//
	if ($action == "UPD") {

		$noChanges = ($arrValues === $arrFromDB);
		// Далее отрабатываем все только при наличии отличий, чтобы не засорять лог записями без изменений
		if (!$noChanges) {
			$newSettings = json_decode(json_encode($arrValues), JSON_FORCE_OBJECT);
			$oldSettings = json_decode(json_encode($arrFromDB), JSON_FORCE_OBJECT);
			// Определяем отличия двух структур (массивов)incomi
			$changes = json_encode(array_diff_assoc($newSettings, $oldSettings));
			// Переводим структуру в массив
			$arrChanges = json_decode(json_encode(array_diff_assoc($newSettings, $oldSettings)), true);
			$changesText = "";
			$countChanges = 0;
			// Пробегаемся по массиву изменений
			foreach ($arrChanges as $key => $value) {
				$reqDB_0 = $db->sql("SELECT * FROM mailbox_outgoing_logMapping WHERE parameter='" . $key . "'")->fetchAll();
				if ($reqDB_0[0]['bin'] == 1) {
					$binText = explode("/", $reqDB_0[0]['msgOnchange']);
					$msgOnchange = ($value == 0) ? $binText[0] : $binText[1];
				} else {
					$msgOnchange = $reqDB_0[0]['msgOnchange'];
				}
				/* --- -- --- -- --- --- -- --- -- --- 
		  Если изменяются параметры с датой, то приводим к рос. формату
		  Если изменяются параметры с кодом вместо значения, то получаем значение
		  */
				if ($key == 'outbox_docDate') {
					$oldVal = empty($oldSettings[$key]) ? "Нет даты" : (validateDate($oldSettings[$key], "Y-m-d H:i:s") ? date('d.m.Y H:i:s', strtotime($oldSettings[$key])) : "Нет даты");
					$newVal = empty($value) ? "Нет даты" : (validateDate($value, "d.m.Y H:i:s") ? date('d.m.Y H:i:s', strtotime($value)) : "Нет даты");
				} else if ($key == 'outbox_docDateDeadline') {
					$oldVal = empty($oldSettings[$key]) ? "Нет даты" : (validateDate($oldSettings[$key], "Y-m-d H:i:s") ? date('d.m.Y H:i:s', strtotime($oldSettings[$key])) : "Нет даты");
					$newVal = empty($value) ? "Нет даты" : (validateDate($value, "Y-m-d H:i:s") ? date('d.m.Y H:i:s', strtotime($value)) : "Нет даты");
				} else if ($key == 'outbox_docSourceDate') {
					$oldVal = empty($oldSettings[$key]) ? "Нет даты" : (validateDate($oldSettings[$key], "Y-m-d") ? date('d.m.Y', strtotime($oldSettings[$key])) : "Нет даты");
					$newVal = empty($value) ? "Нет даты" : (validateDate($value, "Y-m-d") ? date('d.m.Y', strtotime($value)) : "Нет даты");
				} elseif ($key == 'outbox_docRecipient_kodzakaz') {
					$reqDB_old = $db->sql("SELECT nameshort FROM sp_contragents WHERE kodcontragent='" . $oldSettings[$key] . "'")->fetchAll();
					$reqDB_new = $db->sql("SELECT nameshort FROM sp_contragents WHERE kodcontragent='" . $value . "'")->fetchAll();
					$oldVal = $reqDB_old[0]['nameshort'];
					$newVal = $reqDB_new[0]['nameshort'];
				} elseif ($key == 'outbox_docSender_kodzayvtel') {
					$reqDB_old = $db->sql("SELECT namezayvfio FROM mailbox_sp_users WHERE kodzayvtel='" . $oldSettings[$key] . "'")->fetchAll();
					$reqDB_new = $db->sql("SELECT namezayvfio FROM mailbox_sp_users WHERE kodzayvtel='" . $value . "'")->fetchAll();
					$oldVal = $reqDB_old[0]['namezayvfio'];
					$newVal = $reqDB_new[0]['namezayvfio'];
				} elseif ($key == 'outbox_docContractor_kodispolout') {
					//
					$arrIspol_old = explode(",", $oldSettings[$key]);
					$arrIspol_new = explode(",", $value);
					$oldVal = "";
					$newVal = "";
					foreach ($arrIspol_old as $valOld) {
						$reqDB_old = $db->sql("SELECT kodispol, namezayvfio FROM mailbox_sp_users WHERE kodispol=" . $valOld)->fetchAll();
						if ($reqDB_old) {
							$oldVal .= !empty($reqDB_old[0]['namezayvfio']) ? ('000000000000000' == $reqDB_old[0]['kodispol']) ? '---,' : $reqDB_old[0]['namezayvfio'] . "," : ',';
						}
					}
					foreach ($arrIspol_new as $valNew) {
						$reqDB_new = $db->sql("SELECT kodispol, namezayvfio FROM mailbox_sp_users WHERE kodispol=" . $valNew)->fetchAll();
						if ($reqDB_new) {
							$newVal .= !empty($reqDB_new[0]['namezayvfio']) ? ('000000000000000' == $reqDB_new[0]['kodispol']) ? '---,' : $reqDB_new[0]['namezayvfio'] . "," : ',';
						}
					}
					$oldVal = rtrim($oldVal, ",");
					$newVal = rtrim($newVal, ",");
				} elseif ($key == 'outbox_docType') {
					$reqDB_old = $db->sql("SELECT type_name_short FROM mailbox_sp_doctypes_outgoing WHERE type_id='" . $oldSettings[$key] . "'")->fetchAll();
					$reqDB_new = $db->sql("SELECT type_name_short FROM mailbox_sp_doctypes_outgoing WHERE type_id='" . $value . "'")->fetchAll();
					$oldVal = $reqDB_old[0]['type_name_short'];
					$newVal = $reqDB_new[0]['type_name_short'];
				} elseif ($key == 'outbox_controlIspolCheckout') {
					//
					// Фиксируем время выставления метки об исполнении документа в самой записи документа
					if ($oldSettings[$key] == '0' && $value == '1') {
						$db->update('mailbox_outgoing_test', array(
							'outbox_controlIspolCheckoutWhen'  => $timestamp,
						), array(
							'koddocmail' => $koddocmail,
						));
					} elseif ($oldSettings[$key] == '1' && $value == '0') {
						$db->update('mailbox_outgoing_test', array(
							'outbox_controlIspolCheckoutWhen'  => null,
						), array(
							'koddocmail' => $koddocmail,
						));
					}
					$oldVal = $oldSettings[$key];
					$newVal = $value;
				} elseif ($key == 'inbox_rowIDadd_rel') {
					$arrRowIDaddRel_old = explode(",", $oldSettings[$key]);
					$arrRowIDaddRel_new = explode(",", $value);

					$resVal_old = '';
					foreach ($arrRowIDaddRel_old as $tmpVal1) {
						$reqDB_old = $db->sql("SELECT inbox_docID FROM mailbox_incoming_test WHERE id='" . $tmpVal1 . "'")->fetchAll();
						$resVal_old .= $reqDB_old[0]['inbox_docID'] . ",";
					}
					$oldVal = rtrim($resVal_old, ",");
					//
					$resVal_new = '';
					foreach ($arrRowIDaddRel_new as $tmpVal2) {
						$reqDB_new = $db->sql("SELECT inbox_docID FROM mailbox_incoming_test WHERE id='" . $tmpVal2 . "'")->fetchAll();
						$resVal_new .= $reqDB_new[0]['inbox_docID'] . ",";
					}
					$newVal = rtrim($resVal_new, ",");
				} elseif ($key == 'outbox_controlIspolUseDeadline') {
					if ($oldSettings[$key] == 1 && $value == 0) {
						$oldVal = $oldSettings[$key] . " / " . date('d.m.Y H:i:s', strtotime($arrFromDB['outbox_docDateDeadline']));
						$newVal = $value;
					} else if ($oldSettings[$key] == 0 && $value == 1) {
						$oldVal = $oldSettings[$key];
						$newVal = $value . " / " . date('d.m.Y H:i:s', strtotime($arrValues['outbox_docDateDeadline']));
					}
				} else {
					$oldVal = $oldSettings[$key];
					$newVal = $value;
				}
				// --- -- --- -- ---
				$changesText .= "<p class='record-title1'><span>" . $msgOnchange . " " . mbLcfirst($reqDB_0[0]['description']) . "</span></p>";
				$changesText .= "<span class='text-warning' style='margin-right:10px'>" . ($oldVal == "" ? "---" : $oldVal) . "</span>>>><span class='text-success' style='margin-left:10px'>" . ($newVal == "" ? "---" : $newVal) . "</span>";
				$changesText .= "///";
				$countChanges++;
			}
			$changesText = $changesText != "" ? substr($changesText, 0, -3) : "";
			// 
			$reqDB1 = $db->sql("SELECT kodispol FROM mailbox_sp_users WHERE id='" . $_SESSION['id'] . "'")->fetchAll();
			$kodispol = (!empty($reqDB1) && $reqDB1[0]['kodispol'] != "") ? $reqDB1[0]['kodispol'] : null;
			//
			//
			//
			//
			//
			$db->insert('mailbox_outgoing_logChanges', array(
				'koddel'          => "",
				'recordID'        => $recordID,
				'timestamp'       => $timestamp,
				'action'          => $action,
				'koddocmail'      => $reqDB[0]['koddocmail'],
				'userid'          => $_SESSION['id'],
				'kodispol'        => $kodispol,
				'oldSettings'     => json_encode($oldSettings),
				'newSettings'     => json_encode($newSettings),
				'changes'         => $changes,
				'changesText'     => $changesText,
				'changesCount'    => $countChanges,
			));
		}
	}
	//
	//
	if ($action == "DEL") {
		// 
		$reqDB1 = $db->sql("SELECT kodispol FROM mailbox_sp_users WHERE id='" . $_SESSION['id'] . "'")->fetchAll();
		$kodispol = (!empty($reqDB1) && $reqDB1[0]['kodispol'] != "") ? $reqDB1[0]['kodispol'] : null;
		//
		$oldSettings = json_decode(json_encode($arrFromDB), JSON_FORCE_OBJECT);
		//
		$db->insert('mailbox_outgoing_logChanges', array(
			'timestamp'       => $timestamp,
			'action'          => $action,
			'koddocmail'      => $reqDB[0]['koddocmail'],
			'userid'          => $_SESSION['id'],
			'kodispol'        => $kodispol,
			'oldSettings'     => json_encode($oldSettings),
			'newSettings'     => null,
			'changes'         => null,
			'changesText'     => null,
			'changesCount'    => null,
		));
	}
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
function fixUpdate($db, $action, $id, $values) {
	$db->update('mailbox_outgoing_test', array(
		'outbox_docUpdatedByID'	=> $_SESSION['id'],
		'outbox_docUpdatedBySTR'	=> $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
		'outbox_docUpdatedWhen'	=> date('Y-m-d H:i:s')
	), array(
		'id' => $id
	));
	PORTAL_SYSLOG('99922000', '0000002', $id, null, null, null);
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# ФУНКЦИЯ
# Назначение: 
#
function updateLogCheckouts($db, $id, $values) {
	//
	$_reqDB = $db->sql("SELECT koddocmail, outbox_docContractor_kodispolout FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
	// 
	// Готовим переменные
	$koddocmail = $_reqDB[0]['koddocmail'];
	$ispolNew = $values['mailbox_outgoing_test']['outbox_docContractor_kodispolout'];
	//
	// Удаляем из таблицы исполнения (mailbox_outgoing_logCheckouts) отстутствующих исполнителей в списке по документу
	$_reqDel = mysqlQuery("DELETE FROM mailbox_outgoing_logCheckouts WHERE koddocmail='{$koddocmail}' AND kodispol NOT IN ({$ispolNew})");
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# ФУНКЦИЯ
# Назначение: обновление общего статуса исполнения документа (mailbox_outgoing_test.inbox_controlIspolCheckout)
#
function updateIspolCheckout($db, $id, $values, $row) {

	$arrispol = explode(",", $row['mailbox_outgoing_test']['outbox_docContractor_kodispolout']);
	// 
	// Готовим переменные
	$userid = $_SESSION['id'];
	$koddocmail = $row['mailbox_outgoing_test']['koddocmail'];
	$datenow = date("Y-m-d H:i:s");
	//
	$__reqIspolStatus = mysqli_fetch_assoc(mysqlQuery("SELECT ispolStatus FROM mailbox_outgoing_logCheckouts WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'"));
	$ispolStatusOld = $__reqIspolStatus['ispolStatus'];
	$ispolStatusNew = $values['ispolStatus'];
	//
	if ($ispolStatusOld != $ispolStatusNew) {
		$__reqDB_Update = mysqlQuery("UPDATE mailbox_outgoing_logCheckouts SET timestamp='{$datenow}', ispolStatus='{$ispolStatusNew}' WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'");
	}
	//
	$_reqDB2 = mysqli_fetch_assoc(mysqlQuery("SELECT COUNT(ispolStatus) as ispolCounts FROM mailbox_outgoing_logCheckouts WHERE koddocmail='{$koddocmail}' AND ispolStatus='1'"));
	//
	$cntIspol = count($arrispol);
	$cntCheckouts = $_reqDB2['ispolCounts'];
	$tmp = $__reqIspolStatus['ispolStatus'];
	if ($cntIspol == $cntCheckouts) {
		$_reqDB3 = $db->sql("UPDATE mailbox_outgoing_test SET outbox_controlIspolCheckout='1', outbox_controlIspolCheckoutWhen='{$datenow}' WHERE id='{$id}'");
	} else {
		$_reqDB3 = $db->sql("UPDATE mailbox_outgoing_test SET outbox_controlIspolCheckout='0', outbox_controlIspolCheckoutWhen=NULL WHERE id='{$id}'");
	}
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function updateIspolStatus($db, $id, $values) {
	$__reqKoddocmail = $db->sql("SELECT koddocmail, outbox_docContractor_kodispolout FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
	$userid = $_SESSION['id'];
	$koddocmail = $__reqKoddocmail[0]['koddocmail'];
	$kodispol = $__reqKoddocmail[0]['inbox_docContractor_kodzayvispol'];

	$__reqIspolStatus = $db->sql("SELECT ispolStatus FROM mailbox_outgoing_logCheckouts WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'")->fetchAll();
	$ispolStatus = $__reqIspolStatus[0]['ispolStatus'];
	if ($ispolStatus != $values['ispolStatus']) {
		$db->update('mailbox_outgoing_logCheckouts', array(
			'timestamp'       => date("Y-m-d H:i:s"),
			'ispolStatus'     => $values['ispolStatus']
		), array(
			'koddocmail'      => $koddocmail,
			'userid'          => $userid,
		));
	}
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function updateFields($db, $action, $id, $values, $row) {
	$__senderKOD = $db->sql("SELECT outbox_docSender_kodzayvtel FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
	$__senderDATA = $db->sql("SELECT id, namezayvfio FROM mailbox_sp_users WHERE kodzayvtel=" . $__senderKOD[0]['outbox_docSender_kodzayvtel'])->fetchAll();
	$senderID = $__senderDATA[0]['id'];
	$senderSTR = $__senderDATA[0]['namezayvfio'];
	// ----- ----- -----
	$__contractorKOD = $db->sql("SELECT outbox_docContractor_kodispolout FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
	if ($__contractorKOD) {
		$__contractorDATA = $db->sql("SELECT id, kodispolout, namezayvfio, emailaddress, dept_num, koddept FROM mailbox_sp_users WHERE kodispolout=" . $__contractorKOD[0]['outbox_docContractor_kodispolout'])->fetchAll();
		if ($__contractorDATA) {
			!empty($__contractorDATA[0]['id']) ? $contractorID = $__contractorDATA[0]['id'] : $contractorID = '0';
			!empty($__contractorDATA[0]['dept_num']) ? $contractorDEPT = $__contractorDATA[0]['dept_num'] : $contractorDEPT = '0';
			!empty($__contractorDATA[0]['koddept']) ? $contractorDEPTKOD = $__contractorDATA[0]['koddept'] : $contractorDEPTKOD = '0';
			!empty($__contractorDATA[0]['namezayvfio']) ? $contractorSTR = $__contractorDATA[0]['namezayvfio'] : $contractorSTR = '---';
			$__contractorDATA[0]['kodispolout'] == '000000000000000' ? $contractorSTR = '---' : $contractorSTR = $__contractorDATA[0]['namezayvfio'];
			!empty($__contractorDATA[0]['emailaddress']) ? $contractorEMAIL = $__contractorDATA[0]['emailaddress'] : $contractorEMAIL = '';
			$__contractorDATA[0]['kodispolout'] == '000000000000000' ? $contractorEMAIL = '---' : $contractorEMAIL = $__contractorDATA[0]['emailaddress'];
		}
	}
	// ----- ----- -----
	$__zakazKOD = $db->sql("SELECT outbox_docRecipient_kodzakaz FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
	if (json_encode($values['enblRecipientManual'], JSON_NUMERIC_CHECK) != '[1]') {
		$__zakazDATA = $db->sql("SELECT kodcontragent, nameshort, zakfio FROM sp_contragents WHERE kodcontragent=" . $__zakazKOD[0]['outbox_docRecipient_kodzakaz'])->fetchAll();
		if ($__zakazKOD[0]['outbox_docRecipient_kodzakaz'] != '000000000000000') {
			$zakaz = $__zakazDATA[0]['nameshort'];
			$zakazName = $__zakazDATA[0]['zakfio'];
			$db->update('mailbox_outgoing_test', array(
				'outbox_docRecipient'			=> $zakaz,
				'outbox_docRecipientName'		=> $zakazName
			), array(
				'id' => $id
			));
		}
	}
	// ----- ----- -----
	$__docTYPE = $db->sql("SELECT outbox_docType FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
	if ($__docTYPE[0]['outbox_docType'] == '0') {
		$docTypeNAME = "Общее";
	} else if ($__docTYPE[0]['outbox_docType'] == '1') {
		$docTypeNAME = "Информационное";
	} else if ($__docTYPE[0]['outbox_docType'] == '2') {
		$docTypeNAME = "Ответное";
	} else if ($__docTYPE[0]['outbox_docType'] == '3') {
		$docTypeNAME = "Запрос овета";
	} else $docTypeNAME = "---";
	$docTypeSTR = mb_substr($docTypeNAME, 0, 3);
	// ----- ----- -----
	// 	$__docID = $db->sql( "SELECT MAX(outbox_docID) as lastDocID, MAX(koddocmail) as lastKod  FROM mailbox_outgoing_test WHERE YEAR(outbox_docDate)=YEAR(NOW()) ORDER BY id DESC" )->fetchAll();
	// 	$newDocID = $__docID[0]['lastDocID'];
	// 	$newKod = $__docID[0]['lastKod'];
	// 	$newDocID++;
	// 	$newKod++;
	// ----- ----- -----
	$__newKod = newKoddocmail();
	$__newDocID = newDocID();

	$arrValues = json_decode(json_encode($values), JSON_FORCE_OBJECT);

	if ($action == 'CRT') {

		PORTAL_SYSLOG('99922000', '0000001', $id, $__newKod, $__newDocID, null);

		$__newUID = date('Y') . "-" . $__newDocID;
		$db->update('mailbox_outgoing_test', array(
			'koddocmail'								=> $__newKod,
			'koddocmailmain'						=> $__newKod,
			'outbox_UID'								=> $__newUID,
			'outbox_docID'							=> $__newDocID,
			'outbox_docIDSTR'						=> $__newDocID,
			'outbox_docTypeSTR'					=> $docTypeSTR,
			'outbox_docSenderID'				=> $senderID,
			'outbox_docSenderSTR'				=> $senderSTR,
			'outbox_docContractorID'		=> $contractorID,
			'outbox_docContractorDEPT'	=> $contractorDEPTKOD,
			'outbox_docContractorSTR'		=> $contractorSTR,
			'outbox_docContractorEMAIL'	=> $contractorEMAIL,
			'outbox_docCreatedByID'			=> $_SESSION['id'],
			'outbox_docCreatedWhen'			=> date('Y-m-d H:i:s'),
			'outbox_docCreatedBySTR'		=> $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
			'outbox_docUpdatedByID'			=> $_SESSION['id'],
			'outbox_docUpdatedBySTR'		=> $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
			'outbox_docUpdatedWhen'			=> date('Y-m-d H:i:s')
		), array(
			'id' => $id
		));
		$db->update('mailbox_counters', array(
			'newDocID_outgoing'					=> $__newDocID
		), array(
			'id' => 1
		));
		// ----- ----- ----- ----- -----
		$__SQL1 = $db->sql("SELECT outbox_docType, outbox_docID, outbox_UID, koddocmail, outbox_docFileID, inbox_rowID_rel FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
		// ----- ----- ----- ----- -----
		if ($__SQL1) {
			$__relID = $__SQL1[0]['inbox_rowID_rel'];
			if (!empty($__relID) and $__SQL1[0]['outbox_docType'] >= 2) {
				$__SQL2 = $db->sql("SELECT inbox_docType, inbox_docID, inbox_UID, koddocmail, inbox_docFileID FROM mailbox_incoming_test WHERE id=" . $__relID)->fetchAll();

				if ($__SQL2) {
					$db->update('mailbox_outgoing_test', array(
						'inbox_docID_rel'		=> $__SQL2[0]['inbox_docID'],
						'docmailpaper'			=> $__relID,
						'inbox_UID_rel'			=> $__SQL2[0]['inbox_UID'],
						'inbox_koddocmail_rel'	=> $__SQL2[0]['koddocmail'],
						'inbox_fileID_rel'		=> $__SQL2[0]['inbox_docFileID']
					), array(
						'id' => $id
					));
					if ($__SQL2[0]['inbox_docType'] == 3) {
						$db->update('mailbox_incoming_test', array(
							'outbox_docID_rel'		=> $__SQL1[0]['outbox_docID'],
							'outbox_docID_rel2'		=> $id,
							'outbox_UID_rel'		=> $__SQL1[0]['outbox_UID'],
							'outbox_koddocmail_rel'	=> $__SQL1[0]['koddocmail'],
							'outbox_fileID_rel'		=> $__SQL1[0]['outbox_docFileID']
						), array(
							'id' => $__relID
						));
					}
				}
			} else {
				$db->update('mailbox_outgoing_test', array(
					'inbox_docID_rel'		=> null,
					'inbox_UID_rel'			=> null,
					'inbox_koddocmail_rel'	=> null,
					'inbox_fileID_rel'		=> null
				), array(
					'id' => $id
				));
			}
		}
	} elseif ($action == 'UPD') {

		$__docID = $db->sql("SELECT id, outbox_docID FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
		$UID = date('Y') . "-" . $__docID[0]['outbox_docID'];
		$db->update('mailbox_outgoing_test', array(
			'outbox_docIDSTR'						=> $__docID[0]['outbox_docID'],
			'outbox_docTypeSTR'					=> $docTypeSTR,
			'outbox_UID'								=> $UID,
			'outbox_docSenderID'				=> $senderID,
			'outbox_docSenderSTR'				=> $senderSTR,
			'outbox_docContractorID'		=> $contractorID,
			'outbox_docContractorDEPT'  => $contractorDEPTKOD,
			'outbox_docContractorSTR'		=> $contractorSTR,
			'outbox_docContractorEMAIL'	=> $contractorEMAIL,
			'outbox_docUpdatedByID'			=> $_SESSION['id'],
			'outbox_docUpdatedBySTR'		=> $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
			'outbox_docUpdatedWhen'			=> date('Y-m-d H:i:s')
		), array(
			'id' => $id
		));
		$db->update('mailbox_counters', array(
			'newDocID_outgoing'					=> $__newDocID
		), array(
			'id' => 1
		));
		// ----- ----- ----- ----- -----
		$__SQL1 = $db->sql("SELECT outbox_docType, outbox_docID, outbox_UID, koddocmail, outbox_docFileID, inbox_rowID_rel, docmailpaper FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
		// ----- ----- ----- ----- -----
		if ($__SQL1) {
			$__relID = $__SQL1[0]['inbox_rowID_rel'];
			$__relID2 = $__SQL1[0]['docmailpaper']; // резервное хранение id кросс-линка на строку
			if (!empty($__relID) && $__SQL1[0]['outbox_docType'] == 2) {
				$__SQL2 = $db->sql("SELECT inbox_docType, inbox_docID, inbox_UID, koddocmail, inbox_docFileID FROM mailbox_incoming_test WHERE id=" . $__relID)->fetchAll();
				// ----- ----- ----- ----- -----
				if ($__SQL2) {
					$db->update('mailbox_outgoing_test', array(
						'inbox_docID_rel'				=> $__SQL2[0]['inbox_docID'],
						'docmailpaper'					=> $__relID,
						'inbox_UID_rel'					=> $__SQL2[0]['inbox_UID'],
						'inbox_koddocmail_rel'	=> $__SQL2[0]['koddocmail'],
						'inbox_fileID_rel'			=> $__SQL2[0]['inbox_docFileID']
					), array(
						'id' => $id
					));
					/*
						UPD 14.01.2021
						По просьбе Романа Тимофеева
						строка 
						if ($__SQL2[0]['inbox_docType'] == 3) {
						заменена на
						if ($__SQL2[0]['inbox_docType'] >= 0) {
						Ранее кросс-линки формировались во входящих письмах только для писем с типом "Запрос ответа" (inbox_docType = 3).
						Теперь кросс-линки формируются для любых входящих писем (inbox_docType >= 0).
					*/
					if ($__SQL2[0]['inbox_docType'] >= 0) {
						/*
							UPD 19.12.2022
							При установленном флаге меняем тип входящего документа на "Запрос ответа"
						*/
						if (json_encode($values['enbl_inbox_docType_change'], JSON_NUMERIC_CHECK) == '[1]') {
							$db->update('mailbox_incoming_test', array(
								'inbox_docType'						=> 3,
								'inbox_docTypeSTR'				=> 'Зап',
								'outbox_rowID_rel'				=> $id,
								'outbox_docID_rel'				=> $__SQL1[0]['outbox_docID'],
								'outbox_UID_rel'					=> $__SQL1[0]['outbox_UID'],
								'outbox_koddocmail_rel'		=> $__SQL1[0]['koddocmail'],
								'outbox_fileID_rel'				=> $__SQL1[0]['outbox_docFileID']
							), array(
								'id' => $__relID
							));
						}
						/*
							>>>>>
							ЧТО ЭТО ТАКОЕ ? / 19.12.2022
							>>>>>
								$db->update('mailbox_outgoing_test', array(
									'inbox_docID_rel'				=> $__SQL1[0]['outbox_docID'],
									'inbox_docID_rel2'			=> $id,
									'inbox_UID_rel'					=> $__SQL1[0]['outbox_UID'],
									'inbox_koddocmail_rel'	=> $__SQL1[0]['koddocmail'],
									'inbox_fileID_rel'			=> $__SQL1[0]['outbox_docFileID']
								), array(
									'id' => $__relID
								));
							<<<<<
						*/
					}
				}
			} else {
				$db->update('mailbox_outgoing_test', array(
					'inbox_rowID_rel'					=> null,
					'inbox_docID_rel'					=> null,
					'docmailpaper'						=> null,
					'inbox_UID_rel'						=> null,
					'inbox_koddocmail_rel'		=> null,
					'inbox_fileID_rel'				=> null
				), array(
					'id' => $id
				));
				$db->update('mailbox_incoming_test', array(
					'outbox_docID_rel'				=> null,
					'outbox_rowID_rel'				=> null,
					'outbox_UID_rel'					=> null,
					'outbox_koddocmail_rel'		=> null,
					'outbox_fileID_rel'				=> null
				), array(
					'id' => $__relID
				));
			}
		} else {
			$db->update('mailbox_outgoing_test', array(
				'inbox_rowID_rel'					=> null,
				'inbox_docID_rel'					=> null,
				'docmailpaper'						=> null,
				'inbox_UID_rel'						=> null,
				'inbox_koddocmail_rel'		=> null,
				'inbox_fileID_rel'				=> null
			), array(
				'id' => $id
			));
			/*
			$db->update('mailbox_incoming_test', array(
				'outbox_docID_rel'				=> null,
				'outbox_rowID_rel'				=> null,
				'outbox_UID_rel'					=> null,
				'outbox_koddocmail_rel'		=> null,
				'outbox_fileID_rel'				=> null
			), array(
				'id' => $__relID
			));
			*/
		}
	}
	$__sID = $db->sql("SELECT koddocmail, outbox_docID, outbox_docFileID FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
	$__year = date('Y');
	$__fileID = $__sID[0]['outbox_docFileID'];
	$file_id = $__year . "00" . $__sID[0]['outbox_docID'];
	$koddocmail = $__sID[0]['koddocmail'];
	$db->update('mailbox_outgoing_files_test', array(
		'flag'       	=> 'CHU',
		'koddocmail'	=> $koddocmail,
		'file_id'		=> $file_id
	), array(
		'id' => $__fileID
	));
	$db->update('mailbox_outgoing_files_test', array(
		'flag' => 'CHU',
	), array(
		'koddocmail' => $koddocmail,
		'mainfile'   => '0',
	));
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function updateFileIDadd($db, $action, $id, $values, $row) {
	if ('UPD' == $action) {
		$_koddocmail = $row['mailbox_outgoing_test']['koddocmail'];
		$_outbox_docFileIDadd = "";
		$_REQ_UploadedFiles = mysqlQuery("SELECT id FROM mailbox_outgoing_files_test WHERE koddocmail='{$_koddocmail}' AND mainfile='0'");
		while ($_ROW_UploadedFiles = mysqli_fetch_assoc($_REQ_UploadedFiles)) {
			$_outbox_docFileIDadd .= $_ROW_UploadedFiles['id'] . ",";
		}
		//
		// 
		$db->update('mailbox_outgoing_test', array(
			'outbox_docFileIDadd' => $_outbox_docFileIDadd,
		), array(
			'id' => $id,
		));
	}
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function delAttachment($db, $id) {
	$__rowFileID = $db->sql("SELECT outbox_docFileID FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
	$row2delete = $__rowFileID[0]['outbox_docFileID'];
	$addFiles2delete  = $__rowFileID[0]['outbox_docFileIDadd'];
	if (!is_null($row2delete) && !empty($row2delete)) {
		// Удаление оригинального файла ($__tmp2) и сим-ссылки ($__tmp1) с диска
		$__file = $db->sql("SELECT file_truelocation, file_syspath FROM mailbox_outgoing_files_test WHERE id=" . $row2delete)->fetchAll();
		$__tmp1 = unlink($__file[0]['file_syspath']);
		$__tmp2 = unlink($__file[0]['file_truelocation']);
		// Удаление записи в таблице файлов
		if ($__tmp1 && $__tmp2) {
			$query = $db->sql("DELETE FROM mailbox_outgoing_files_test WHERE id=" . $row2delete);
			PORTAL_SYSLOG('99922000', '000000D', $row2delete, null, null, null);
		}
	}
	$addFiles2delete = $addFiles2delete != "" ? substr($addFiles2delete, 0, -1) : "";
	if (!is_null($addFiles2delete) && !empty($addFiles2delete)) {
		$arrAddFiles2delete = explode(",", $addFiles2delete);
		foreach ($arrAddFiles2delete as $key => $value) {
			// Удаление оригинального файла ($__tmp2) и сим-ссылки ($__tmp1) с диска
			$__file1 = $db->sql("SELECT file_truelocation, file_syspath FROM mailbox_outgoing_files_test WHERE id=" . $value)->fetchAll();
			$__tmp11 = unlink($__file1[0]['file_syspath']);
			$__tmp21 = unlink($__file1[0]['file_truelocation']);
			// Удаление записи в таблице файлов
			if ($__tmp11 && $__tmp21) {
				$query = $db->sql("DELETE FROM mailbox_outgoing_files_test WHERE id=" . $value);
			}
		}
	}
	//
	$arrFromDB = [
		'outbox_docID' => $__rowFileID[0]['outbox_docID'],
		'outbox_docDate' => $__rowFileID[0]['outbox_docDate'],
		'outbox_docType' => $__rowFileID[0]['outbox_docType'],
		'outbox_docAbout' => $__rowFileID[0]['outbox_docAbout'],
		'outbox_docRecipient_kodzakaz' => $__rowFileID[0]['outbox_docRecipient_kodzakaz'],
		'outbox_docSourceID' => $__rowFileID[0]['outbox_docSourceID'],
		'outbox_docSourceDate' => $__rowFileID[0]['outbox_docSourceDate'],
		// 'outbox_koddocmail_rel' => $__rowFileID[0]['outbox_koddocmail_rel'],
		// 'outbox_docID_rel' => $__rowFileID[0]['outbox_docID_rel'],
		'outbox_docFileID' => $__rowFileID[0]['outbox_docFileID'],
		'outbox_docFileIDadd' => $__rowFileID[0]['outbox_docFileIDadd'],
		'outbox_docSender_kodzayvtel' => $__rowFileID[0]['outbox_docSender_kodzayvtel'],
		'outbox_docContractor_kodispolout' => $__rowFileID[0]['outbox_docContractor_kodispolout'],
		'outbox_controlIspolActive' => $__rowFileID[0]['outbox_controlIspolActive'],
		'outbox_docDateDeadline' => $__rowFileID[0]['outbox_docDateDeadline'],
		'outbox_controlIspolMailReminder1' => $__rowFileID[0]['outbox_controlIspolMailReminder1'],
		'outbox_controlIspolMailReminder2' => $__rowFileID[0]['outbox_controlIspolMailReminder2'],
		'outbox_controlIspolCheckout' => $__rowFileID[0]['outbox_controlIspolCheckout'],
	];
	$settings = json_decode(json_encode($arrFromDB), JSON_FORCE_OBJECT);
	// 
	$reqDB1 = $db->sql("SELECT kodispolout FROM mailbox_sp_users WHERE id='" . $_SESSION['id'] . "'")->fetchAll();
	$kodispolout = (!empty($reqDB1) && $reqDB1[0]['kodispolout'] != "") ? $reqDB1[0]['kodispolout'] : null;
	//
	$db->insert('mailbox_incoming_logChanges', array(
		'timestamp'       => date('Y-m-d H:i:s'),
		'action'          => 'DEL',
		'koddocmail'      => $reqDB1[0]['koddocmail'],
		'userid'          => $_SESSION['id'],
		'kodispol'        => $kodispolout,
		'oldSettings'     => null,
		'newSettings'     => json_encode($settings),
		'changes'         => null,
		'changesText'     => null,
		'changesCount'    => null,
	));
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function updateSyslogAfterupload($db, $id) {
	PORTAL_SYSLOG('99922000', '000000F', $id, null, null, null);
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function updateSyslogAfterremove($db, $id) {
	PORTAL_SYSLOG('99922000', '0000003', $id, null, null, null);
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function backupRemovedRecords($db, $id) {
	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 
	// ОПЕРАЦИИ ПРИ УДАЛЕНИИ ОСНОВНОЙ ЗАПИСИ ВО ВХОДЯЩЕЙ ПОЧТЕ mailbox_incoming
	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 
	// > Основное поле-маркер удаленной записи - KODDEL (практически во всх таблицах)
	/* 
	Маркируем как удаленные:
	  - mailbox_incoming_logChanges
	  - mailbox_incoming_logComments
	  - mailbox_incoming_logCheckouts
	  - mailbox_incoming_logControl
	  - mailbox_incoming_logMailing
	  - mailbox_incoming_files
	*/
	$_REQ_Koddocmail = $db->sql("SELECT koddocmail FROM mailbox_outgoing_test WHERE id=" . $id)->fetchAll();
	$_koddocmail = $_REQ_Koddocmail[0]['koddocmail'];

	$_REQ_UpdateKoddel_1 = $db->sql("UPDATE mailbox_outgoing_logChanges SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
	$_REQ_UpdateKoddel_2 = $db->sql("UPDATE mailbox_outgoing_logComments SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
	$_REQ_UpdateKoddel_3 = $db->sql("UPDATE mailbox_outgoing_logCheckouts SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
	$_REQ_UpdateKoddel_4 = $db->sql("UPDATE mailbox_outgoing_logControl SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
	$_REQ_UpdateKoddel_5 = $db->sql("UPDATE mailbox_outgoing_logMailing SET koddel='deleted' WHERE send_koddocmail='" . $_koddocmail . "'");
	// Маркируем таблицу прикрепленных файлов
	$_REQ_UpdateKoddel_Files = $db->sql("UPDATE mailbox_outgoing_files_test SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
	// Копируем запись в таблицу для удаленных записей
	$_REQ_InsertDeleted = $db->sql("INSERT INTO mailbox_outgoing_deletedRecords SELECT * FROM mailbox_outgoing_test WHERE koddocmail='" . $_koddocmail . "'");
}
#
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
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
Editor::inst($db, 'mailbox_outgoing_test')
	->fields(
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.ID'),
		Field::inst('mailbox_outgoing_test.koddocmail'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docType')
			->options(
				Options::inst()
					->table('mailbox_sp_doctypes_outgoing')
					->value('type_id')
					->label(array('status', 'type_id', 'type_name_full', 'type_name_short'))
					->order('type_id asc')
					->render(function ($row) {
						return $row['type_name_full'];
					})
					->where(function ($q) {
						$q->where('status', '1', '=');
					})
			)
			->validator(Validate::notEmpty(ValidateOptions::inst()
				->message('Тип письма обязателен'))),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docTypeSTR'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docID'),
		Field::inst('mailbox_outgoing_test.outbox_docIDSTR'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_rowIDs_links')
			->setFormatter(Format::ifEmpty(NULL)),
		Field::inst('mailbox_outgoing_test.outbox_docIDs_links')
			->options(
				Options::inst()
					->table('mailbox_outgoing_test')
					->value('id')
					->label(array('id', 'outbox_docID', 'outbox_docRecipient', 'outbox_docDate', 'outbox_docAbout'))
					->render(function ($row) {
						$date = date_create($row['outbox_docDate']);
						$docDate = date_format($date, "d.m.Y");
						$tmp = $docDate . ' / №' . $row['outbox_docID'] . ' / ' . $row['outbox_docRecipient'] . ' / ' . $row['outbox_docAbout'];
						$output = (mb_strlen($tmp, 'UTF-8') > 160) ? mb_substr($tmp, 0, 120, 'UTF-8') . " ..." : $tmp;
						return $output;
					})
					->where(function ($q) use ($__startDate, $__endDate) {
						$q->where('outbox_docDate', '( SELECT outbox_docDate FROM mailbox_outgoing_test WHERE outbox_docDate >= ' . $__startDate . ' AND outbox_docDate <= ' . $__endDate . ' )', 'IN', false);
					})
					->order('outbox_docDate DESC')
			)
			->validator(Validate::dbValues())
			->setFormatter(Format::ifEmpty(NULL)),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.dognet_rowIDs_links')
			->setFormatter(Format::ifEmpty(NULL)),
		Field::inst('mailbox_outgoing_test.dognet_docIDs_links')
			->options(
				Options::inst()
					->table('dognet_docbase')
					->value('id')
					->label(array('id', 'koddel', 'docNumber', 'docnameshot'))
					->render(function ($row) {
						$tmp = '3-4/' . $row['docNumber'] . ' / ' . $row['docnameshot'];
						$output = (mb_strlen($tmp, 'UTF-8') > 160) ? mb_substr($tmp, 0, 120, 'UTF-8') . " ..." : $tmp;
						return $output;
					})
					->where(function ($q) {
						$q->where('koddel', '99', '!=');
					})
					->order('docNumber DESC')
			)
			->validator(Validate::dbValues())
			->setFormatter(Format::ifEmpty(NULL)),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.inbox_rowIDs_links')
			->setFormatter(Format::ifEmpty(NULL)),
		Field::inst('mailbox_outgoing_test.inbox_docIDs_links')
			->options(
				Options::inst()
					->table('mailbox_incoming_test')
					->value('id')
					->label(array('id', 'inbox_docID', 'inbox_docSender', 'inbox_docDate', 'inbox_docAbout'))
					->render(function ($row) {
						$date = date_create($row['inbox_docDate']);
						$docDate = date_format($date, "d.m.Y");
						$tmp = $docDate . ' / №' . $row['inbox_docID'] . ' / ' . $row['inbox_docSender'] . ' / ' . $row['inbox_docAbout'];
						$output = (mb_strlen($tmp, 'UTF-8') > 160) ? mb_substr($tmp, 0, 120, 'UTF-8') . " ..." : $tmp;
						return $output;
					})
					->where(function ($q) use ($__startDate, $__endDate) {
						$q->where('inbox_docDate', '( SELECT inbox_docDate FROM mailbox_incoming_test WHERE inbox_docDate >= ' . $__startDate . ' AND inbox_docDate <= ' . $__endDate . ' )', 'IN', false);
					})
					->order('inbox_docDate DESC')
			)
			->validator(Validate::dbValues())
			->setFormatter(Format::ifEmpty(NULL)),
		// 
		//     
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		// 
		Field::inst('mailbox_outgoing_test.inbox_rowIDadd_rel')
			->setFormatter(Format::ifEmpty(NULL)),
		Field::inst('mailbox_outgoing_test.inbox_rowIDList_rel')
			->options(
				Options::inst()
					->table('mailbox_incoming_test')
					->value('id')
					->label(array('id', 'inbox_docID', 'inbox_docSender', 'inbox_docDate', 'inbox_docAbout'))
					->render(function ($row) {
						$date    = date_create($row['inbox_docDate']);
						$docDate = date_format($date, "d.m.Y");
						return $docDate . ' / №' . $row['inbox_docID'] . ' / ' . $row['inbox_docSender'] . ' / ' . $row['inbox_docAbout'];
					})
					->where(function ($q) use ($__startDate, $__endDate) {
						$q->where('inbox_docDate', '( SELECT inbox_docDate FROM mailbox_incoming_test WHERE inbox_docDate >= ' . $__startDate . ' AND inbox_docDate <= ' . $__endDate . ' )', 'IN', false);
					})
					->order('inbox_docDate DESC')
			)
			->validator(Validate::dbValues())
			->setFormatter(Format::ifEmpty(NULL)),
		Field::inst('mailbox_outgoing_test.inbox_rowID_rel')
			->options(
				Options::inst()
					->table('mailbox_incoming_test')
					->value('id')
					->label(array('id', 'inbox_docID', 'inbox_docSender', 'inbox_docDate', 'inbox_docAbout'))
					->render(function ($row) {
						$date    = date_create($row['inbox_docDate']);
						$docDate = date_format($date, "d.m.Y");
						return $docDate . ' / №' . $row['inbox_docID'] . ' / ' . $row['inbox_docSender'] . ' / ' . $row['inbox_docAbout'];
					})
					->where(function ($q) use ($__startDate, $__endDate) {
						$q->where('inbox_docDate', '( SELECT inbox_docDate FROM mailbox_incoming_test WHERE inbox_docDate >= ' . $__startDate . ' AND inbox_docDate <= ' . $__endDate . ' )', 'IN', false);
					})
					->order('inbox_docDate DESC')
			)
			->validator(Validate::dbValues())
			->setFormatter(Format::ifEmpty(NULL)),
		Field::inst('mailbox_outgoing_test.inbox_docID_rel'),
		//
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		// 
		Field::inst('mailbox_outgoing_test.outbox_docDate')
			->validator(Validate::dateFormat(
				'd.m.Y H:i:s',
				ValidateOptions::inst()
					->allowEmpty(false)
			))
			->getFormatter(Format::datetime(
				'Y-m-d H:i:s',
				'd.m.Y H:i:s'
			))
			->setFormatter(Format::datetime(
				'd.m.Y H:i:s',
				'Y-m-d H:i:s'
			)),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docAbout')
			->validator(Validate::notEmpty(
				ValidateOptions::inst()
					->message('Краткое описание обязательно')
			)),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docRecipient_kodzakaz')
			->options(
				Options::inst()
					->table('sp_contragents')
					->value('kodzakaz')
					->label(array('kodzakaz', 'namezakshot', 'zakfio'))
					->render(function ($row) {
						return $row['namezakshot'] . " / " . $row['zakfio'];
					})
					->where(function ($q) {
						$q->where('koddel', '99', '!=');
					})
			)
			->validator(Validate::notEmpty(
				ValidateOptions::inst()
					->message('Получатель письма обязателен')
			)),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docRecipient'),
		Field::inst('mailbox_outgoing_test.outbox_docRecipientName'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docSender_kodzayvtel')
			->options(
				Options::inst()
					->table('mailbox_sp_users')
					->value('kodzayvtel')
					->label(array('status_zayvtel', 'kodzayvtel', 'namezayvfio'))
					->render(function ($row) {
						return ($row['namezayvfio']);
					})
					->where(function ($q) {
						$q->where('status_zayvtel', '1', '=');
					})
			)
			->validator(Validate::notEmpty(
				ValidateOptions::inst()
					->message('Отправитель письма обязателен')
			)),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docSenderID'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docSenderSTR'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docSourceID'),
		Field::inst('mailbox_outgoing_test.outbox_docSourceDate')
			->validator(Validate::dateFormat(
				'd.m.Y',
				ValidateOptions::inst()
					->allowEmpty(true)
			))
			->getFormatter(Format::datetime(
				'Y-m-d',
				'd.m.Y'
			))
			->setFormatter(Format::datetime(
				'd.m.Y',
				'Y-m-d'
			)),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docFileID')
			->setFormatter(Format::ifEmpty(null))
			->upload(
				Upload::inst(
					function ($file, $id) use ($varFileArray, $db) {
						$__pref = date('Y') . $_SESSION['id'] . date('mdHis');
						$__name = $__pref . "-" . $file['name'];
						$__nameTmp = $file['tmp_name'];
						$__ext = explode('.', $__name);
						$__ext = strtolower(end($__ext));

						$md5 = md5(uniqid());
						$__nameMD5 = "{$md5}.{$__ext}";

						$__url = str_replace($_SERVER['DOCUMENT_ROOT'], 'http://' . $_SERVER['HTTP_HOST'], $varFileArray['syspath'] . $__nameMD5);

						move_uploaded_file($__nameTmp, $varFileArray['docpath'] . "{$__name}");
						symlink($varFileArray['docpath'] . "{$__name}", $varFileArray['syspath'] . $__nameMD5);

						$db->update(
							'mailbox_outgoing_files_test', // Database table to update
							[
								'mainfile'          => '1',
								'flag'				=>	'PREUPL',
								'file_year'			=>	$varFileArray['year'],
								'file_id'			=>	'',
								'file_name'			=>	$__name,
								'file_originalname' => 	$file['name'],
								'file_symname'		=>	$__nameMD5,
								// Правка от 05/06/2019
								// 							'file_truelocation'	=>	$varFileArray['docpath']."{$__name}.{$__ext}",
								'file_truelocation'	=>	$varFileArray['docpath'] . "{$__name}",
								// ---
								'file_syspath'		=>	$varFileArray['syspath'] . $__nameMD5,
								'file_webpath'		=>	$varFileArray['webpath'] . $__nameMD5,
								'file_url'			=>	$__url
							],
							['id' => $id]
						);
						return $id;
					}
				)
					->db(
						'mailbox_outgoing_files_test',
						'id',
						array(
							'file_extension'	=> Upload::DB_EXTN,
							'file_size'			=> Upload::DB_FILE_SIZE,
							'file_webpath'      => '',
							'file_truelocation' => '',
							'file_originalname' => '',
							'koddocmail'        => '',
						)
					)
					->validator(Validate::fileSize(35000000, 'Размер документа не должен превышать 35МБ'))
					->validator(Validate::fileExtensions(array('png', 'jpg', 'pdf'), "Загрузите документ"))
			),
		Field::inst('mailbox_outgoing_test.outbox_docFileIDadd'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.inbox_fileID_rel')
			->options(
				Options::inst()
					->table('mailbox_incoming_files_test')
					->value('id')
					->label(array('file_webpath', 'file_name'))
			),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_incoming_files_test.file_webpath'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_incoming_files_test.file_url'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_incoming_files_test.file_name'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docContractor_kodispolout')
			->options(
				Options::inst()
					->table('mailbox_sp_users')
					->value('kodispolout')
					->label(array('status_ispolout', 'kodispolout', 'namezayvfio'))
					->render(function ($row) {
						return ($row['namezayvfio']);
					})
					->where(function ($q) {
						$q->where('status_ispolout', '1', '=');
					})
			)
			->validator(Validate::notEmpty(
				ValidateOptions::inst()
					->message('Определите исполнителя или выберите "---"')
			)),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docContractorID'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docContractorDEPT'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docContractorSTR'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docContractorEMAIL'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docDateDeadline')
			->validator(Validate::dateFormat(
				'd.m.Y H:i:s',
				ValidateOptions::inst()
					->allowEmpty(true)
			))
			->getFormatter(Format::datetime(
				'Y-m-d H:i:s',
				'd.m.Y H:i:s'
			))
			->setFormatter(Format::datetime(
				'd.m.Y H:i:s',
				'Y-m-d H:i:s'
			)),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docContractorComment'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docComment'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docCreatedByID')->set(Field::SET_CREATE),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docCreatedBySTR')->set(Field::SET_CREATE),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docCreatedWhen')
			->set(Field::SET_CREATE)
			->validator(Validate::dateFormat(
				'd.m.Y H:i:s',
				ValidateOptions::inst()
					->allowEmpty(false)
			))
			->getFormatter(Format::datetime(
				'Y-m-d H:i:s',
				'd.m.Y H:i:s'
			))
			->setFormatter(Format::datetime(
				'd.m.Y H:i:s',
				'Y-m-d H:i:s'
			)),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docUpdatedByID')->set(Field::SET_EDIT),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docUpdatedBySTR')->set(Field::SET_EDIT),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_docUpdatedWhen')
			->set(Field::SET_EDIT)
			->validator(Validate::dateFormat(
				'd.m.Y H:i:s',
				ValidateOptions::inst()
					->allowEmpty(false)
			))
			->getFormatter(Format::datetime(
				'Y-m-d H:i:s',
				'd.m.Y H:i:s'
			))
			->setFormatter(Format::datetime(
				'd.m.Y H:i:s',
				'Y-m-d H:i:s'
			)),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_emailSentByID')->set(Field::SET_CREATE),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_emailSentBySTR')->set(Field::SET_CREATE),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_emailSentWhen')
			->set(Field::SET_CREATE)
			->validator(Validate::dateFormat(
				'd.m.Y H:i:s',
				ValidateOptions::inst()
					->allowEmpty(false)
			))
			->getFormatter(Format::datetime(
				'Y-m-d H:i:s',
				'd.m.Y H:i:s'
			))
			->setFormatter(Format::datetime(
				'd.m.Y H:i:s',
				'Y-m-d H:i:s'
			)),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_test.outbox_controlIspolActive'),
		Field::inst('mailbox_outgoing_test.outbox_controlIspolCheckout'),
		Field::inst('mailbox_outgoing_test.outbox_controlIspolCheckoutWhen')
			->set(Field::SET_CREATE)
			->validator(Validate::dateFormat(
				'd.m.Y H:i:s',
				ValidateOptions::inst()
					->allowEmpty(true)
			))
			->getFormatter(Format::datetime(
				'Y-m-d H:i:s',
				'd.m.Y H:i:s'
			)),
		Field::inst('mailbox_outgoing_test.outbox_controlIspolStatus'),
		Field::inst('mailbox_outgoing_test.outbox_controlIspolWarning'),
		Field::inst('mailbox_outgoing_test.outbox_controlIspolAlarm'),
		Field::inst('mailbox_outgoing_test.outbox_controlIspolDays'),
		Field::inst('mailbox_outgoing_test.outbox_controlIspolMailReminder1'),
		Field::inst('mailbox_outgoing_test.outbox_controlIspolMailReminder2'),
		Field::inst('mailbox_outgoing_test.outbox_controlIspolMailToRukOk'),
		Field::inst('mailbox_outgoing_test.outbox_controlIspolMailToRukAlarm'),
		Field::inst('mailbox_outgoing_test.outbox_controlIspolUseDeadline'),
		Field::inst('mailbox_outgoing_test.cntComments'),
		// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
		Field::inst('mailbox_outgoing_files_test.id'),
		Field::inst('mailbox_outgoing_files_test.file_webpath'),
		Field::inst('mailbox_outgoing_files_test.file_originalname'),
	)
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
	//
	//
	->on('preUpload', function ($editor, $data) {
	})

	->on('postUpload', function ($editor, $id, $files, $data) {
		updateSyslogAfterupload($editor->db(), $id);
	})

	->on('preEdit', function ($editor, $id, $values) {
		$_SESSION['rowID_before_changes'] = $id;
		fixLog($editor->db(), 'UPD', $id, $values);
		updateLogCheckouts($editor->db(), $id, $values);
	})

	->on('preCreate', function ($editor, $values) {
	})

	->on('preGet', function ($editor, $id) use ($__startDate, $__endDate) {
		$editor->where(function ($q) use ($__startDate, $__endDate) {
			$q->where('mailbox_outgoing_test.outbox_docDate', '( SELECT outbox_docDate FROM mailbox_outgoing_test WHERE outbox_docDate >= ' . $__startDate . ' AND outbox_docDate <= ' . $__endDate . ' )', 'IN', false);
		});
	})

	->on('postCreate', function ($editor, $id, $values, $row) {
		updateFields($editor->db(), 'CRT', $id, $values, $row);
		fixCreate($editor->db(), 'CRT', $id, $values);
		fixLog($editor->db(), 'CRT', $id, $values);
	})
	->on('postEdit', function ($editor, $id, $values, $row) {
		fixUpdate($editor->db(), 'UPD', $id, $values);
		updateFields($editor->db(), 'UPD', $id, $values, $row);
		// updateIspolStatus($editor->db(), $id, $values);
		updateIspolCheckout($editor->db(), $id, $values, $row);
		updateFileIDadd($editor->db(), 'UPD', $id, $values, $row);
	})
	->on('preRemove', function ($editor, $id, $values) {
		fixLog($editor->db(), 'DEL', $id, $values);
		backupRemovedRecords($editor->db(), $id);
		delAttachment($editor->db(), $id);
		updateSyslogAfterremove($editor->db(), $id);
	})
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
	->leftJoin('sp_contragents', 'sp_contragents.kodcontragent', '=', 'mailbox_outgoing_test.outbox_docRecipient_kodzakaz')
	->leftJoin('mailbox_outgoing_files_test', 'mailbox_outgoing_files_test.id', '=', 'mailbox_outgoing_test.outbox_docFileID')
	->leftJoin('mailbox_incoming_files_test', 'mailbox_incoming_files_test.id', '=', 'mailbox_outgoing_test.inbox_fileID_rel')
	->process($_POST)
	->json();
