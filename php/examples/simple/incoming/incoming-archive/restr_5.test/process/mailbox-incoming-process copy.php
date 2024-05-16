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
// session_start();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
$__startDate = $_SESSION['in_startTableDate'];
$__endDate   = $_SESSION['in_endTableDate'];
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----


# Import PHPMailer classes into the global namespace
# These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


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
  $query  = mysqlQuery("SELECT MAX(koddocmail) as lastKod FROM " . __MAIL_INCOMING_TABLENAME . " ORDER BY id DESC");
  $row    = mysqli_fetch_assoc($query);
  $lastKod = $row['lastKod'];
  $newKod = $lastKod + rand(3, 13);
  return $newKod;
}
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения нового номера документа/записи (docID) для таблицы записей
//
function newDocID() {
  $query    = mysqlQuery("SELECT MAX(inbox_docID) as lastDocID FROM " . __MAIL_INCOMING_TABLENAME . " WHERE YEAR(inbox_docDate)=YEAR(NOW()) ORDER BY id DESC");
  $row      = mysqli_fetch_assoc($query);
  $newDocID = $row['lastDocID'];
  $newDocID++;
  return $newDocID;
}
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения нового номера файла (file_id) для таблицы файлов
//
function newFileID() {
  $query     = mysqlQuery("SELECT MAX(file_id) as lastFileID FROM " . __MAIL_INCOMING_FILES_TABLENAME . " ORDER BY id DESC");
  $row       = mysqli_fetch_assoc($query);
  $newFileID = $row['lastFileID'];
  $newFileID++;
  return $newFileID;
}
function currDocID($currID) {
  $query                        = mysqlQuery("SELECT inbox_docID FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $currID);
  $row                          = mysqli_fetch_assoc($query);
  $currDocID                    = $row['inbox_docID'];
  $_SESSION['editedInboxDocID'] = $currDocID;
  return $currDocID;
}
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
//
//
//
function mailboxIncomingTestStats($db) {
  for ($god = date('Y'); $god <= 2007; $god--) {
    $cnt_all     = mysqlQuery("SELECT COUNT(id) as all_cnt FROM " . __MAIL_INCOMING_TABLENAME . " WHERE YEAR(inbox_docDate = " . $god);
    $row_all     = mysqli_fetch_assoc($cnt_all);
    $all_records = $row_all['all_cnt'];
    // ----- ----- -----
    $cnt_type0     = mysqlQuery("SELECT COUNT(id) as type0_cnt FROM " . __MAIL_INCOMING_TABLENAME . " WHERE inbox_docType = '0' AND YEAR(inbox_docDate = " . $god);
    $row_type0     = mysqli_fetch_assoc($cnt_type0);
    $type0_records = $row_type0['type0_cnt'];
    // ----- ----- -----
    $cnt_type1     = mysqlQuery("SELECT COUNT(id) as type1_cnt FROM " . __MAIL_INCOMING_TABLENAME . " WHERE inbox_docType = '1' AND YEAR(inbox_docDate = " . $god);
    $row_type1     = mysqli_fetch_assoc($cnt_type1);
    $type1_records = $row_type1['type1_cnt'];
    // ----- ----- -----
    $cnt_type2           = mysqlQuery("SELECT COUNT(id) as type2_cnt FROM " . __MAIL_INCOMING_TABLENAME . " WHERE inbox_docType = '2' AND YEAR(inbox_docDate = " . $god);
    $row_type2           = mysqli_fetch_assoc($cnt_type2);
    $type2_records       = $row_type2['type2_cnt'];
    $cnt_type2_norel     = mysqlQuery("SELECT COUNT(id) as type2_norel_cnt FROM " . __MAIL_INCOMING_TABLENAME . " WHERE inbox_docType = '2' AND (outbox_rowID_rel = '0' OR outbox_rowID_rel = '') AND YEAR(inbox_docDate = " . $god);
    $row_type2_norel     = mysqli_fetch_assoc($cnt_type2_norel);
    $type2_norel_records = $row_type2_norel['type2_norel_cnt'];
    // ----- ----- -----
    $cnt_type3           = mysqlQuery("SELECT COUNT(id) as type3_cnt FROM " . __MAIL_INCOMING_TABLENAME . " WHERE inbox_docType = '3' AND YEAR(inbox_docDate = " . $god);
    $row_type3           = mysqli_fetch_assoc($cnt_type3);
    $type3_records       = $row_type3['type3_cnt'];
    $cnt_type3_norel     = mysqlQuery("SELECT COUNT(id) as type3_norel_cnt FROM " . __MAIL_INCOMING_TABLENAME . " WHERE inbox_docType = '3' AND (outbox_rowID_rel = '0' OR outbox_rowID_rel = '') AND YEAR(inbox_docDate = " . $god);
    $row_type3_norel     = mysqli_fetch_assoc($cnt_type3_norel);
    $type3_norel_records = $row_type3_norel['type3_norel_cnt'];
    // ----- ----- -----
    $cnt_noattach     = mysqlQuery("SELECT COUNT(id) as noattach_cnt FROM " . __MAIL_INCOMING_TABLENAME . " WHERE (inbox_docFileID = '' OR inbox_docFileID = NULL) AND YEAR(inbox_docDate = " . $god);
    $row_noattach     = mysqli_fetch_assoc($cnt_noattach);
    $noattach_records = $row_noattach['noattach_cnt'];
    // ----- ----- -----

    //   $qry_allRecords_upd = mysqlQuery("UPDATE ".__MAIL_INCOMING_TABLENAME."_stats SET all_records = ".$all_records.", type0_records = ".$type0_records.", type1_records = ".$type1_records.", type2_records = ".$type2_records.", type3_records = ".$type3_records.", type2_norel_records = ".$type2_norel_records.", type3_norel_records = ".$type3_norel_records.", noattach_records = ".$noattach_records." WHERE year = ".$god);
    //   $row_allRecords_upd = mysqli_fetch_assoc($qry_allRecords_upd);

    $db->update(__MAIL_INCOMING_TABLENAME . '_stats', array(
      'all_records'         => $all_records,
      'type0_records'       => $type0_records,
      'type1_records'       => $type1_records,
      'type2_records'       => $type2_records,
      'type3_records'       => $type3_records,
      'type2_norel_records' => $type2_norel_records,
      'type3_norel_records' => $type3_norel_records,
      'noattach_records'    => $noattach_records,
    ), array(
      'year' => $god,
    ));
    // ----- ----- -----
  }
  return;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
#
/* 
	ПУТИ ДЛЯ СОХРАНЕНИЯ ЗАГРУЖАЕМЫХ ФАЙЛОВ
	> Массив директорий и вспомогательных параметров
*/
if ("POST" == $_SERVER["REQUEST_METHOD"]) {
  $d = dir(__MAIL_INCOMING_STORAGE_SERVERPATH . __MAIL_INCOMING_STORAGE_SUBFOLDER . "/");
  $docpath = $d->path;
  $webpath = __SERVICENAME_MAILNEW . __MAIL_INCOMING_STORAGE_WORKPATH . __MAIL_INCOMING_STORAGE_SUBFOLDER . "/";
  $syspath = __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_STORAGE_WORKPATH . __MAIL_INCOMING_STORAGE_SUBFOLDER . "/";
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
// Записываем в БД в таблицу mailbox_log отчет о действии пользователя
function insRecord2log($db, $action, $id, $values) {
  $query    = $db->sql("SELECT MAX(inbox_docID) as lastDocID FROM " . __MAIL_INCOMING_TABLENAME . " WHERE YEAR(inbox_docDate)=YEAR(NOW()) ORDER BY id DESC")->fetchAll();
  $newDocID = $query[0]['lastDocID'];
  $newDocID++;
  $db->insert('mailbox_log', array(
    'log_whoID'   => $_SESSION['id'],
    'log_whoNAME' => $_SESSION['lastname'],
    'log_action'  => $action,
    'log_values'  => json_encode($values),
    'log_row'     => $id,
    //   'log_docID'  => json_encode( $values['inbox_docID'], JSON_NUMERIC_CHECK ),
    'log_docID'   => $newDocID,
    'log_when'    => date('Y-m-d H:i:s'),
  ));
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function fixCreate($db, $action, $id, $values) {
  $db->update(__MAIL_INCOMING_TABLENAME, array(
    'inbox_docCreatedByID'  => $_SESSION['id'],
    'inbox_docCreatedWhen'  => date('Y-m-d H:i:s'),
    'inbox_docCreatedBySTR' => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
    'inbox_docUpdatedByID'  => $_SESSION['id'],
    'inbox_docUpdatedBySTR' => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
    'inbox_docUpdatedWhen'  => date('Y-m-d H:i:s'),
  ), array(
    'id' => $id,
  ));
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function mbLcfirst($str) {
  return mb_strtolower(mb_substr($str, 0, 1)) . mb_substr($str, 1);
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function fixLog($db, $action, $id, $values) {

  $__USERID   = $_SESSION['id'];
  // Проверем был ли отмечен документ как выполненный
  // @ $checkout
  $reqDB = $db->sql("SELECT * FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
  $arrValues = [
    'inbox_docID' => $values[__MAIL_INCOMING_TABLENAME]['inbox_docID'],
    'inbox_docDate' => validateDate($values[__MAIL_INCOMING_TABLENAME]['inbox_docDate'], "d.m.Y H:i:s") ? date("Y-m-d H:i:s", strtotime($values[__MAIL_INCOMING_TABLENAME]['inbox_docDate'])) : null,
    'inbox_docType' => $values[__MAIL_INCOMING_TABLENAME]['inbox_docType'],
    'inbox_docAbout' => $values[__MAIL_INCOMING_TABLENAME]['inbox_docAbout'],
    'inbox_docSender_kodzakaz' => $values[__MAIL_INCOMING_TABLENAME]['inbox_docSender_kodzakaz'],
    'inbox_docSourceID' => $values[__MAIL_INCOMING_TABLENAME]['inbox_docSourceID'],
    'inbox_docSourceDate' => validateDate($values[__MAIL_INCOMING_TABLENAME]['inbox_docSourceDate'], "d.m.Y") ? date("Y-m-d", strtotime($values[__MAIL_INCOMING_TABLENAME]['inbox_docSourceDate'])) : null,
    // 'outbox_koddocmail_rel' => $values[__MAIL_INCOMING_TABLENAME]['outbox_koddocmail_rel'],
    // 'outbox_docID_rel' => $values[__MAIL_INCOMING_TABLENAME]['outbox_docID_rel'],
    'outbox_rowIDadd_rel' => ($values[__MAIL_INCOMING_TABLENAME]['outbox_rowIDadd_rel'] == "") ? null : $values[__MAIL_INCOMING_TABLENAME]['outbox_rowIDadd_rel'],
    // 'inbox_docFileID' => $values[__MAIL_INCOMING_TABLENAME]['inbox_docFileIDtmp'],
    // 'inbox_docFileIDadd' => $values[__MAIL_INCOMING_TABLENAME]['inbox_docFileIDadd'],
    'inbox_docRecipient_kodzayvtel' => $values[__MAIL_INCOMING_TABLENAME]['inbox_docRecipient_kodzayvtel'],
    'inbox_docContractor_kodzayvispol' => $values[__MAIL_INCOMING_TABLENAME]['inbox_docContractor_kodzayvispol'],
    'inbox_controlIspolActive' => $values[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolActive'],
    'inbox_controlIspolUseDeadline' => $values[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolUseDeadline'],
    'inbox_docDateDeadline' => validateDate($values[__MAIL_INCOMING_TABLENAME]['inbox_docDateDeadline'], "d.m.Y H:i:s") ? date("Y-m-d H:i:s", strtotime($values[__MAIL_INCOMING_TABLENAME]['inbox_docDateDeadline'])) : null,
    'inbox_controlIspolMailReminder1' => $values[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolMailReminder1'],
    'inbox_controlIspolMailReminder2' => $values[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolMailReminder2'],
    'inbox_controlIspolCheckout' => $values[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolCheckout'],
  ];
  $arrFromDB = [
    'inbox_docID' => $reqDB[0]['inbox_docID'],
    'inbox_docDate' => validateDate($reqDB[0]['inbox_docDate'], "Y-m-d H:i:s") ? $reqDB[0]['inbox_docDate'] : null,
    'inbox_docType' => $reqDB[0]['inbox_docType'],
    'inbox_docAbout' => $reqDB[0]['inbox_docAbout'],
    'inbox_docSender_kodzakaz' => $reqDB[0]['inbox_docSender_kodzakaz'],
    'inbox_docSourceID' => $reqDB[0]['inbox_docSourceID'],
    'inbox_docSourceDate' => validateDate($reqDB[0]['inbox_docSourceDate'], "Y-m-d") ? $reqDB[0]['inbox_docSourceDate'] : null,
    // 'outbox_koddocmail_rel' => $reqDB[0]['outbox_koddocmail_rel'],
    // 'outbox_docID_rel' => $reqDB[0]['outbox_docID_rel'],
    'outbox_rowIDadd_rel' => $reqDB[0]['outbox_rowIDadd_rel'],
    // 'inbox_docFileID' => $reqDB[0]['inbox_docFileID'],
    // 'inbox_docFileIDadd' => $reqDB[0]['inbox_docFileIDadd'],
    'inbox_docRecipient_kodzayvtel' => $reqDB[0]['inbox_docRecipient_kodzayvtel'],
    'inbox_docContractor_kodzayvispol' => $reqDB[0]['inbox_docContractor_kodzayvispol'],
    'inbox_controlIspolActive' => $reqDB[0]['inbox_controlIspolActive'],
    'inbox_controlIspolUseDeadline' => $reqDB[0]['inbox_controlIspolUseDeadline'],
    'inbox_docDateDeadline' => validateDate($reqDB[0]['inbox_docDateDeadline'], "Y-m-d H:i:s") ? $reqDB[0]['inbox_docDateDeadline'] : null,
    'inbox_controlIspolMailReminder1' => $reqDB[0]['inbox_controlIspolMailReminder1'],
    'inbox_controlIspolMailReminder2' => $reqDB[0]['inbox_controlIspolMailReminder2'],
    'inbox_controlIspolCheckout' => $reqDB[0]['inbox_controlIspolCheckout'],
  ];
  //
  //
  $timestamp = date('Y-m-d H:i:s');
  $koddocmail = $reqDB[0]['koddocmail'];
  $recordID = "MA.I." . $koddocmail . ".L." . time() . "-" . str_pad($_SESSION['id'], 4, "0", STR_PAD_LEFT);
  //
  //
  if ($action == "CRT") {
    $reqDB = $db->sql("SELECT * FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $arrFromDB = [
      'inbox_docID' => $reqDB[0]['inbox_docID'],
      'inbox_docDate' => validateDate($reqDB[0]['inbox_docDate'], "Y-m-d H:i:s") ? $reqDB[0]['inbox_docDate'] : null,
      'inbox_docType' => $reqDB[0]['inbox_docType'],
      'inbox_docAbout' => $reqDB[0]['inbox_docAbout'],
      'inbox_docSender_kodzakaz' => $reqDB[0]['inbox_docSender_kodzakaz'],
      'inbox_docSourceID' => $reqDB[0]['inbox_docSourceID'],
      'inbox_docSourceDate' => validateDate($reqDB[0]['inbox_docSourceDate'], "Y-m-d") ? $reqDB[0]['inbox_docSourceDate'] : null,
      // 'outbox_koddocmail_rel' => $reqDB[0]['outbox_koddocmail_rel'],
      // 'outbox_docID_rel' => $reqDB[0]['outbox_docID_rel'],
      'outbox_rowIDadd_rel' => $reqDB[0]['outbox_rowIDadd_rel'],
      // 'inbox_docFileID' => $reqDB[0]['inbox_docFileID'],
      // 'inbox_docFileIDadd' => $reqDB[0]['inbox_docFileIDadd'],
      'inbox_docRecipient_kodzayvtel' => $reqDB[0]['inbox_docRecipient_kodzayvtel'],
      'inbox_docContractor_kodzayvispol' => $reqDB[0]['inbox_docContractor_kodzayvispol'],
      'inbox_controlIspolActive' => $reqDB[0]['inbox_controlIspolActive'],
      'inbox_controlIspolUseDeadline' => $reqDB[0]['inbox_controlIspolUseDeadline'],
      'inbox_docDateDeadline' => validateDate($reqDB[0]['inbox_docDateDeadline'], "Y-m-d H:i:s") ? $reqDB[0]['inbox_docDateDeadline'] : null,
      'inbox_controlIspolMailReminder1' => $reqDB[0]['inbox_controlIspolMailReminder1'],
      'inbox_controlIspolMailReminder2' => $reqDB[0]['inbox_controlIspolMailReminder2'],
      'inbox_controlIspolCheckout' => $reqDB[0]['inbox_controlIspolCheckout'],
    ];
    $oldSettings = json_decode(json_encode($arrFromDB), JSON_FORCE_OBJECT);
    // 
    $reqDB1 = $db->sql("SELECT kodispol FROM mailbox_sp_users WHERE id='" . $_SESSION['id'] . "'")->fetchAll();
    $kodispol = (!empty($reqDB1) && $reqDB1[0]['kodispol'] != "") ? $reqDB1[0]['kodispol'] : null;
    //
    $db->insert(__MAIL_INCOMING_PREFIX . '_logChanges', array(
      'koddel'          => "",
      'recordID'        => $recordID,
      'recordType'      => "0",
      'recordNum'       => "1",
      'timestamp'       => $timestamp,
      'action'          => $action,
      'koddocmail'      => $reqDB[0]['koddocmail'],
      'userid'          => $_SESSION['id'],
      'kodispol'        => $kodispol,
      'oldSettings'     => null,
      'newSettings'     => json_encode($oldSettings),
      'changes'         => null,
      'changesOldVal'   => null,
      'changesNewVal'   => null,
      'changesTitle'    => "Создана запись о документе",
      'changesStr'      => "Создана запись о документе",
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
      $changesStr = "";
      $changesText = "";
      $countChanges = 0;
      // Пробегаемся по массиву изменений
      foreach ($arrChanges as $key => $value) {
        $reqDB_0 = $db->sql("SELECT * FROM " . __MAIL_INCOMING_PREFIX . "_logMapping WHERE parameter='" . $key . "'")->fetchAll();
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
        if ($key == 'inbox_docDate') {
          $oldVal = empty($oldSettings[$key]) ? "Нет даты" : (validateDate($oldSettings[$key], "Y-m-d H:i:s") ? date('d.m.Y H:i:s', strtotime($oldSettings[$key])) : "Нет даты");
          $newVal = empty($value) ? "Нет даты" : (validateDate($value, "d.m.Y H:i:s") ? date('d.m.Y H:i:s', strtotime($value)) : "Нет даты");
        } else if ($key == 'inbox_docDateDeadline') {
          $oldVal = empty($oldSettings[$key]) ? "Нет даты" : (validateDate($oldSettings[$key], "Y-m-d H:i:s") ? date('d.m.Y H:i:s', strtotime($oldSettings[$key])) : "Нет даты");
          $newVal = empty($value) ? "Нет даты" : (validateDate($value, "d.m.Y H:i:s") ? date('d.m.Y H:i:s', strtotime($value)) : "Нет даты");
        } else if ($key == 'inbox_docSourceDate') {
          $oldVal = empty($oldSettings[$key]) ? "Нет даты" : (validateDate($oldSettings[$key], "Y-m-d") ? date('d.m.Y', strtotime($oldSettings[$key])) : "Нет даты");
          $newVal = empty($value) ? "Нет даты" : (validateDate($value, "d.m.Y") ? date('d.m.Y', strtotime($value)) : "Нет даты");
        } elseif ($key == 'inbox_docSender_kodzakaz') {
          $reqDB_old = $db->sql("SELECT nameshort FROM sp_contragents WHERE kodcontragent='" . $oldSettings[$key] . "'")->fetchAll();
          $reqDB_new = $db->sql("SELECT nameshort FROM sp_contragents WHERE kodcontragent='" . $value . "'")->fetchAll();
          $oldVal = $reqDB_old[0]['nameshort'];
          $newVal = $reqDB_new[0]['nameshort'];
        } elseif ($key == 'inbox_docRecipient_kodzayvtel') {
          $reqDB_old = $db->sql("SELECT namezayvfio FROM mailbox_sp_users WHERE kodzayvtel='" . $oldSettings[$key] . "'")->fetchAll();
          $reqDB_new = $db->sql("SELECT namezayvfio FROM mailbox_sp_users WHERE kodzayvtel='" . $value . "'")->fetchAll();
          $oldVal = $reqDB_old[0]['namezayvfio'];
          $newVal = $reqDB_new[0]['namezayvfio'];
        } elseif ($key == 'inbox_docContractor_kodzayvispol') {
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
        } elseif ($key == 'inbox_docType') {
          $reqDB_old = $db->sql("SELECT type_name_short FROM mailbox_sp_doctypes_incoming WHERE type_id='" . $oldSettings[$key] . "'")->fetchAll();
          $reqDB_new = $db->sql("SELECT type_name_short FROM mailbox_sp_doctypes_incoming WHERE type_id='" . $value . "'")->fetchAll();
          $oldVal = $reqDB_old[0]['type_name_short'];
          $newVal = $reqDB_new[0]['type_name_short'];
        } elseif ($key == 'inbox_controlIspolCheckout') {
          //
          // Фиксируем время выставления метки об исполнении документа в самой записи документа
          if ($oldSettings[$key] == '0' && $value == '1') {
            $db->update(__MAIL_INCOMING_TABLENAME, array(
              'inbox_controlIspolCheckoutWhen'  => $timestamp,
            ), array(
              'koddocmail' => $koddocmail,
            ));
          } elseif ($oldSettings[$key] == '1' && $value == '0') {
            $db->update(__MAIL_INCOMING_TABLENAME, array(
              'inbox_controlIspolCheckoutWhen'  => null,
            ), array(
              'koddocmail' => $koddocmail,
            ));
          }
          $oldVal = $oldSettings[$key];
          $newVal = $value;
        } elseif ($key == 'outbox_rowIDadd_rel') {
          $arrRowIDaddRel_old = explode(",", $oldSettings[$key]);
          $arrRowIDaddRel_new = explode(",", $value);

          $resVal_old = '';
          foreach ($arrRowIDaddRel_old as $tmpVal1) {
            $reqDB_old = $db->sql("SELECT outbox_docID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id='" . $tmpVal1 . "'")->fetchAll();
            $resVal_old .= $reqDB_old[0]['outbox_docID'] . ",";
          }
          $oldVal = rtrim($resVal_old, ",");
          //
          $resVal_new = '';
          foreach ($arrRowIDaddRel_new as $tmpVal2) {
            $reqDB_new = $db->sql("SELECT outbox_docID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id='" . $tmpVal2 . "'")->fetchAll();
            $resVal_new .= $reqDB_new[0]['outbox_docID'] . ",";
          }
          $newVal = rtrim($resVal_new, ",");
        } elseif ($key == 'inbox_controlIspolUseDeadline') {
          if ($oldSettings[$key] == 1 && $value == 0) {
            $oldVal = $oldSettings[$key] . " / " . date('d.m.Y H:i:s', strtotime($arrFromDB['inbox_docDateDeadline']));
            $newVal = $value;
          } else if ($oldSettings[$key] == 0 && $value == 1) {
            $oldVal = $oldSettings[$key];
            $newVal = $value . " / " . date('d.m.Y H:i:s', strtotime($arrValues['inbox_docDateDeadline']));
          }
        } else {
          $oldVal = $oldSettings[$key];
          $newVal = $value;
        }
        // --- -- --- -- ---
        $changesStr = "<span class='text-warning' style='margin-right:10px'>" . ($oldVal == "" ? "---" : $oldVal) . "</span>>>><span class='text-success' style='margin-left:10px'>" . ($newVal == "" ? "---" : $newVal) . "</span>";
        $changesText .= "<span class='bg-info'><b>" . $msgOnchange . " " . mbLcfirst($reqDB_0[0]['description']) . "</b></span>";
        $changesText .= "<br>";
        $changesText .= $changesStr;
        $changesText .= "///";
        $countChanges++;
        // 
        $reqDB1 = $db->sql("SELECT kodispol FROM mailbox_sp_users WHERE id='" . $_SESSION['id'] . "'")->fetchAll();
        $kodispol = (!empty($reqDB1) && $reqDB1[0]['kodispol'] != "") ? $reqDB1[0]['kodispol'] : null;
        //
        $db->insert(__MAIL_INCOMING_PREFIX . '_logChanges', array(
          'koddel'          => "",
          'recordType'      => "1",
          'recordNum'       => $countChanges,
          'recordID'        => $recordID,
          'timestamp'       => $timestamp,
          'action'          => $action,
          'koddocmail'      => $reqDB[0]['koddocmail'],
          'userid'          => $_SESSION['id'],
          'kodispol'        => $kodispol,
          'oldSettings'     => json_encode($oldSettings),
          'newSettings'     => json_encode($newSettings),
          'changes'         => $changes,
          'changesOldVal'   => $oldVal == "" ? "---" : $oldVal,
          'changesNewVal'   => $newVal == "" ? "---" : $newVal,
          'changesTitle'    => $msgOnchange . " " . mbLcfirst($reqDB_0[0]['description']),
          'changesStr'      => $msgOnchange . " " . mbLcfirst($reqDB_0[0]['description']),
          'changesText'     => $msgOnchange . " " . mbLcfirst($reqDB_0[0]['description']),
          'changesCount'    => null,
        ));
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
      $db->insert(__MAIL_INCOMING_PREFIX . '_logChanges', array(
        'koddel'          => "",
        'recordType'      => "2",
        'recordNum'       => null,
        'recordID'        => $recordID,
        'timestamp'       => $timestamp,
        'action'          => $action,
        'koddocmail'      => $reqDB[0]['koddocmail'],
        'userid'          => $_SESSION['id'],
        'kodispol'        => $kodispol,
        'oldSettings'     => json_encode($oldSettings),
        'newSettings'     => json_encode($newSettings),
        'changes'         => $changes,
        'changesOldVal'   => null,
        'changesNewVal'   => null,
        'changesTitle'    => $msgOnchange,
        'changesStr'      => $changesStr,
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
    $db->insert(__MAIL_INCOMING_PREFIX . '_logChanges', array(
      'koddel'          => "",
      'recordType'      => "-1",
      'recordNum'       => null,
      'recordID'        => null,
      'timestamp'       => $timestamp,
      'action'          => $action,
      'koddocmail'      => $reqDB[0]['koddocmail'],
      'userid'          => $_SESSION['id'],
      'kodispol'        => $kodispol,
      'oldSettings'     => json_encode($oldSettings),
      'newSettings'     => null,
      'changes'         => null,
      'changesOldVal'   => null,
      'changesNewVal'   => null,
      'changesTitle'    => "Запись была удалена",
      'changesStr'      => null,
      'changesText'     => null,
      'changesCount'    => null,
    ));
  }
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function fixUpdate($db, $action, $id, $values) {
  $db->update(__MAIL_INCOMING_TABLENAME, array(
    'inbox_docUpdatedByID'  => $_SESSION['id'],
    'inbox_docUpdatedBySTR' => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
    'inbox_docUpdatedWhen'  => date('Y-m-d H:i:s'),
  ), array(
    'id' => $id,
  ));
  PORTAL_SYSLOG('99921000', '0000002', $id, null, null, null);
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# ФУНКЦИЯ
# Назначение: 
#
function updateLogCheckouts($db, $id, $values) {
  //
  $_reqDB = $db->sql("SELECT koddocmail, inbox_docContractor_kodzayvispol FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
  // 
  // Готовим переменные
  $koddocmail = $_reqDB[0]['koddocmail'];
  $ispolNew = $values[__MAIL_INCOMING_TABLENAME]['inbox_docContractor_kodzayvispol'];
  //
  // Удаляем из таблицы исполнения (".__MAIL_INCOMING_PREFIX."_logCheckouts) отстутствующих исполнителей в списке по документу
  $_reqDel = mysqlQuery("DELETE FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND kodispol NOT IN ({$ispolNew})");
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# ФУНКЦИЯ
# Назначение: обновление общего статуса исполнения документа (".__MAIL_INCOMING_PREFIX.".inbox_controlIspolCheckout)
#
function updateIspolCheckout($db, $id, $values, $row) {

  $arrispol = explode(",", $row[__MAIL_INCOMING_TABLENAME]['inbox_docContractor_kodzayvispol']);
  // 
  // Готовим переменные
  $userid = $_SESSION['id'];
  $koddocmail = $row[__MAIL_INCOMING_TABLENAME]['koddocmail'];
  $datenow = date("Y-m-d H:i:s");
  //
  $__reqIspolStatus = mysqli_fetch_assoc(mysqlQuery("SELECT ispolStatus FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'"));
  $ispolStatusOld = $__reqIspolStatus['ispolStatus'];
  $ispolStatusNew = $values['ispolStatus'];
  //
  if ($ispolStatusOld != $ispolStatusNew) {
    $__reqDB_Update = mysqlQuery("UPDATE " . __MAIL_INCOMING_PREFIX . "_logCheckouts SET timestamp='{$datenow}', ispolStatus='{$ispolStatusNew}' WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'");
  }
  //
  $_reqDB2 = mysqli_fetch_assoc(mysqlQuery("SELECT COUNT(ispolStatus) as ispolCounts FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND ispolStatus='1'"));
  //
  $cntIspol = count($arrispol);
  $cntCheckouts = $_reqDB2['ispolCounts'];
  $tmp = $__reqIspolStatus['ispolStatus'];
  if ($cntIspol == $cntCheckouts) {
    $_reqDB3 = $db->sql("UPDATE " . __MAIL_INCOMING_TABLENAME . " SET inbox_controlIspolCheckout='1', inbox_controlIspolCheckoutWhen='{$datenow}' WHERE id='{$id}'");
  } else {
    $_reqDB3 = $db->sql("UPDATE " . __MAIL_INCOMING_TABLENAME . " SET inbox_controlIspolCheckout='0', inbox_controlIspolCheckoutWhen=NULL WHERE id='{$id}'");
  }
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function updateIspolStatus($db, $id, $values) {
  $__reqKoddocmail = $db->sql("SELECT koddocmail, inbox_docContractor_kodzayvispol FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
  $userid = $_SESSION['id'];
  $koddocmail = $__reqKoddocmail[0]['koddocmail'];
  $kodispol = $__reqKoddocmail[0]['inbox_docContractor_kodzayvispol'];

  $__reqIspolStatus = $db->sql("SELECT ispolStatus FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'")->fetchAll();
  $ispolStatus = $__reqIspolStatus[0]['ispolStatus'];
  if ($ispolStatus != $values['ispolStatus']) {
    $db->update(__MAIL_INCOMING_PREFIX . '_logCheckouts', array(
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
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### 
##### 
##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function clearRelFields($db, $id, $values) {
  #
  $_REQ_1 = $db->sql("SELECT outbox_rowID_rel FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
  $outbox_rowID_rel = $_REQ_1[0]['outbox_rowID_rel'];
  $outbox_rowID_relV =  $values[__MAIL_INCOMING_TABLENAME]['outbox_rowID_rel'];
  $outbox_docTypeV =  $values[__MAIL_INCOMING_TABLENAME]['inbox_docType'];
  if (empty($outbox_rowID_relV) && !empty($outbox_rowID_rel)) {
    # ----- ----- ----- ----- ----- 
    # Тип документа
    $_REQ_2 = $db->sql("SELECT outbox_docType_prev FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $outbox_rowID_rel)->fetchAll();
    $docTYPEprev = $_REQ_2[0]['outbox_docType_prev'];
    $_REQ_3 = $db->sql("SELECT * FROM mailbox_sp_doctypes_outgoing WHERE type_id = " . $docTYPEprev)->fetchAll();
    $docTypeSTRprev = !empty($_REQ_3[0]['type_name_short']) ? $_REQ_3[0]['type_name_short'] : "---";

    $db->update(__MAIL_INCOMING_TABLENAME, array(
      'outbox_docID_rel'       => null,
      'outbox_UID_rel'         => null,
      'outbox_koddocmail_rel'  => null,
      'outbox_fileID_rel'      => null
    ), array(
      'id' => $id
    ));
    $db->update(__MAIL_OUTGOING_TABLENAME, array(
      'inbox_docID_rel'        => null,
      'inbox_rowID_rel'        => null,
      'inbox_UID_rel'          => null,
      'inbox_koddocmail_rel'   => null,
      'inbox_fileID_rel'       => null
    ), array(
      'id' => $outbox_rowID_rel
    ));
    if ($outbox_docTypeV < 2 || empty($outbox_rowID_relV)) {
      $db->update(__MAIL_OUTGOING_TABLENAME, array(
        'outbox_docType'          => $docTYPEprev,
        'outbox_docTypeSTR'       => $docTypeSTRprev
      ), array(
        'id' => $outbox_rowID_rel
      ));
    }
  }
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### 
##### ОСНОВНАЯ ФУНКЦИЯ ОБРАБОТКИ ФОРМЫ
##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function updateFields($db, $action, $id, $values, $row) {
  # ----- ----- ----- ----- ----- 
  # Получатель документа
  $recipientKoddocmail = $row[__MAIL_INCOMING_TABLENAME]['koddocmail'];
  $recipientKodzayvtel = $row[__MAIL_INCOMING_TABLENAME]['inbox_docRecipient_kodzayvtel'];
  $__recipientDATA = $db->sql("SELECT id, namezayvfio FROM mailbox_sp_users WHERE kodzayvtel=" . $recipientKodzayvtel)->fetchAll();
  $recipientID     = $__recipientDATA[0]['id'];
  $recipientSTR    = $__recipientDATA[0]['namezayvfio'];
  $_sessionUserID  = $_SESSION['id'];
  # ----- ----- ----- ----- ----- 
  # Исполнитель документа
  $contractorKOD = $row[__MAIL_INCOMING_TABLENAME]['inbox_docContractor_kodzayvispol'];
  $__contractorKOD = $db->sql("SELECT inbox_docContractor_kodzayvispol FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
  $multiispol = (!empty($contractorKOD) && (strpos($contractorKOD, ',') !== false)) ? 1 : 0;
  $arrispol = explode(",", $contractorKOD);

  if (!empty($contractorKOD)) {
    if (0 == $multiispol) {
      $__contractorDATA = $db->sql("SELECT id, kodispol, namezayvfio, emailaddress, dept_num, koddept FROM mailbox_sp_users WHERE kodispol=" . $contractorKOD)->fetchAll();
      if ($__contractorDATA) {
        !empty($__contractorDATA[0]['id']) ? $contractorID = $__contractorDATA[0]['id'] : $contractorID = '0';
        !empty($__contractorDATA[0]['dept_num']) ? $contractorDEPT = $__contractorDATA[0]['dept_num'] : $contractorDEPT = '0';
        !empty($__contractorDATA[0]['koddept']) ? $contractorDEPTKOD = $__contractorDATA[0]['koddept'] : $contractorDEPTKOD = '0';
        !empty($__contractorDATA[0]['namezayvfio']) ? $contractorSTR = $__contractorDATA[0]['namezayvfio'] : $contractorSTR    = '';
        '000000000000000' == $__contractorDATA[0]['kodispol'] ? $contractorSTR = '---' : $contractorSTR    = $__contractorDATA[0]['namezayvfio'];
        !empty($__contractorDATA[0]['emailaddress']) ? $contractorEMAIL = $__contractorDATA[0]['emailaddress'] : $contractorEMAIL = '';
        '000000000000000' == $__contractorDATA[0]['kodispol'] ? $contractorEMAIL = '---' : $contractorEMAIL = $__contractorDATA[0]['emailaddress'];
        # ----- ----- ----- ----- ----- 
        # Формируем набор исполнителей в таблице logCheckouts
        $_tmpKodispol = $__contractorDATA[0]['kodispol'];
        $_tmpKoddocmail = $recipientKoddocmail;
        $_reqIspolCheck1 = mysqlQuery("SELECT * FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE kodispol='{$_tmpKodispol}' AND koddocmail='{$_tmpKoddocmail}'");
        if (mysqli_num_rows($_reqIspolCheck1) < 1) {
          $db->insert(__MAIL_INCOMING_PREFIX . '_logCheckouts', array(
            'timestamp'       => date("Y-m-d H:i:s"),
            'koddocmail'      => $recipientKoddocmail,
            'userid'          => $__contractorDATA[0]['id'],
            'kodispol'        => $__contractorDATA[0]['kodispol'],
          ));
        }
      }
    } else {
      $contractorID = "";
      $contractorDEPT = "";
      $contractorDEPTKOD = "";
      $contractorSTR = "";
      $contractorEMAIL = "";
      foreach ($arrispol as $value) {
        $__contractorDATA = $db->sql("SELECT id, kodispol, namezayvfio, emailaddress, dept_num, koddept FROM mailbox_sp_users WHERE kodispol=" . $value)->fetchAll();
        if ($__contractorDATA) {
          $contractorID .= !empty($__contractorDATA[0]['id']) ? $__contractorDATA[0]['id'] . "," : '0,';
          $contractorDEPT .= !empty($__contractorDATA[0]['dept_num']) ? $__contractorDATA[0]['dept_num'] . "," : '0,';
          $contractorDEPTKOD .= !empty($__contractorDATA[0]['koddept']) ? $__contractorDATA[0]['koddept'] . "," : '0,';
          $contractorSTR .= !empty($__contractorDATA[0]['namezayvfio']) ? ('000000000000000' == $__contractorDATA[0]['kodispol']) ? '---,' : $__contractorDATA[0]['namezayvfio'] . "," : ',';
          $contractorEMAIL .= !empty($__contractorDATA[0]['emailaddress']) ? ('000000000000000' == $__contractorDATA[0]['kodispol']) ? '---,' : $__contractorDATA[0]['emailaddress'] . "," : ',';
        }
        # ----- ----- ----- ----- ----- 
        # Формируем набор исполнителей в таблице logCheckouts
        $_tmpKodispol = $__contractorDATA[0]['kodispol'];
        $_tmpKoddocmail = $recipientKoddocmail;
        $_reqIspolCheck1 = mysqlQuery("SELECT * FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE kodispol='{$_tmpKodispol}' AND koddocmail='{$_tmpKoddocmail}'");
        if (mysqli_num_rows($_reqIspolCheck1) < 1) {
          $db->insert(__MAIL_INCOMING_PREFIX . '_logCheckouts', array(
            'timestamp'       => date("Y-m-d H:i:s"),
            'koddocmail'      => $recipientKoddocmail,
            'userid'          => $__contractorDATA[0]['id'],
            'kodispol'        => $__contractorDATA[0]['kodispol'],
          ));
        }
      }
      $contractorID = rtrim($contractorID, ",");
      $contractorDEPT = rtrim($contractorDEPT, ",");
      $contractorDEPTKOD = rtrim($contractorDEPTKOD, ",");
      $contractorSTR = rtrim($contractorSTR, ",");
      $contractorEMAIL = rtrim($contractorEMAIL, ",");
    }
  }
  # ----- ----- ----- ----- ----- 
  # Получатель документа
  $zakazKOD = $row[__MAIL_INCOMING_TABLENAME]['inbox_docSender_kodzakaz'];
  if (json_encode($values['enblSenderManual'], JSON_NUMERIC_CHECK) != '[1]') {
    $__zakazDATA = $db->sql("SELECT kodzakaz, nameshort, zakfio FROM sp_contragents WHERE kodcontragent=" . $zakazKOD)->fetchAll();
    if ('000000000000000' != $zakazKOD) {
      $zakaz = $__zakazDATA[0]['nameshort'];
      $zakazName = $__zakazDATA[0]['zakfio'];
      $db->update(__MAIL_INCOMING_TABLENAME, array(
        'inbox_docSender'     => $zakaz,
        'inbox_docSenderName' => $zakazName,
      ), array(
        'id' => $id,
      ));
    }
  }
  # ----- ----- ----- ----- ----- 
  # Тип документа
  $docTYPE = $row[__MAIL_INCOMING_TABLENAME]['inbox_docType'];
  $__docTypeDATA = $db->sql("SELECT * FROM mailbox_sp_doctypes_incoming WHERE type_id = " . $docTYPE)->fetchAll();
  $docTypeNAME = !empty($__docTypeDATA[0]['type_name_full']) ? $__docTypeDATA[0]['type_name_full'] : "---";
  $docTypeSTR = !empty($__docTypeDATA[0]['type_name_short']) ? $__docTypeDATA[0]['type_name_short'] : "---";
  # ----- ----- ----- ----- ----- 
  $__newKod   = newKoddocmail();
  $__newDocID = newDocID();
  # ----- ----- ----- ----- ----- 
  # Формируем переменные текущей записи
  $inbox_koddocmail = $row[__MAIL_INCOMING_TABLENAME]['koddocmail'];
  $inbox_docType = $row[__MAIL_INCOMING_TABLENAME]['inbox_docType'];
  $inbox_docID = $row[__MAIL_INCOMING_TABLENAME]['inbox_docID'];
  $inbox_UID = $row[__MAIL_INCOMING_TABLENAME]['inbox_UID'];
  $inbox_docFileID = $row[__MAIL_INCOMING_TABLENAME]['inbox_docFileID'];
  $outbox_rowID_rel = $row[__MAIL_INCOMING_TABLENAME]['outbox_rowID_rel'];
  #
  # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
  # СОЗДАНИЕ ЗАПИСИ
  #
  if ('CRT' == $action) {

    PORTAL_SYSLOG('99921000', '0000001', $id, $__newKod, $__newDocID, null);

    $__newUID = date('Y') . "-" . $__newDocID;
    $db->update(__MAIL_INCOMING_TABLENAME, array(
      'koddocmail'               => $__newKod,
      'koddocmailmain'           => $__newKod,
      'inbox_UID'                => $__newUID,
      'inbox_docID'              => $__newDocID,
      'inbox_docIDSTR'           => $__newDocID,
      'inbox_docTypeSTR'         => $docTypeSTR,
      'inbox_docRecipientID'     => $recipientID,
      'inbox_docRecipientSTR'    => $recipientSTR,
      'inbox_docContractorMULTI' => $multiispol,
      'inbox_docContractorID'    => $contractorID,
      'inbox_docContractorDEPT'  => $contractorDEPTKOD,
      'inbox_docContractorSTR'   => $contractorSTR,
      'inbox_docContractorEMAIL' => $contractorEMAIL,
      'inbox_docCreatedByID'     => $_SESSION['id'],
      'inbox_docCreatedWhen'     => date('Y-m-d H:i:s'),
      'inbox_docCreatedBySTR'    => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
      'inbox_docUpdatedByID'     => $_SESSION['id'],
      'inbox_docUpdatedBySTR'    => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
      'inbox_docUpdatedWhen'     => date('Y-m-d H:i:s'),
    ), array(
      'id' => $id,
    ));
    $db->update('mailbox_counters', array(
      'newDocID_incoming'          => $__newDocID
    ), array(
      'id' => 1
    ));
    # ----- ----- ----- ----- ----- 
    # 
    if (!empty($outbox_rowID_rel) and $inbox_docType >= 2) {

      $_REQ_Outgoing = $db->sql("SELECT outbox_docType, outbox_docID, outbox_UID, koddocmail, outbox_docFileID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $outbox_rowID_rel)->fetchAll();
      if (isset($_REQ_Outgoing) && !empty($_REQ_Outgoing)) {
        $db->update(__MAIL_INCOMING_TABLENAME, array(
          'outbox_docID_rel' => $_REQ_Outgoing[0]['outbox_docID'],
          'outbox_UID_rel' => $_REQ_Outgoing[0]['outbox_UID'],
          'outbox_koddocmail_rel' => $_REQ_Outgoing[0]['koddocmail'],
          'outbox_fileID_rel' => $_REQ_Outgoing[0]['outbox_docFileID']
        ), array(
          'id' => $id
        ));
        if ($_REQ_Outgoing[0]['outbox_docType'] == 3) {
          $db->update(__MAIL_OUTGOING_TABLENAME, array(
            'inbox_docID_rel' => $inbox_docID,
            'inbox_rowID_rel' => $id,
            'inbox_UID_rel' => $inbox_UID,
            'inbox_koddocmail_rel' => $inbox_koddocmail,
            'inbox_fileID_rel' => $inbox_docFileID
          ), array(
            'id' => $outbox_rowID_rel
          ));
        }
      }
    } else {
      $db->update(__MAIL_OUTGOING_TABLENAME, array(
        'inbox_docID_rel' => null,
        'inbox_rowID_rel' => null,
        'inbox_UID_rel' => null,
        'inbox_koddocmail_rel' => null,
        'inbox_fileID_rel' => null
      ), array(
        'id' => $id
      ));
    }
    #
    # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
    # ИЗМЕНЕНИЕ ЗАПИСИ
    #
  } elseif ('UPD' == $action) {
    $UID = date('Y') . "-" . $inbox_docID;
    $db->update(__MAIL_INCOMING_TABLENAME, array(
      'inbox_docID'              => $inbox_docID,
      'inbox_docTypeSTR'         => $docTypeSTR,
      'inbox_UID'                => $UID,
      'inbox_docRecipientID'     => $recipientID,
      'inbox_docRecipientSTR'    => $recipientSTR,
      'inbox_docContractorMULTI' => $multiispol,
      'inbox_docContractorID'    => $contractorID,
      'inbox_docContractorDEPT'  => $contractorDEPTKOD,
      'inbox_docContractorSTR'   => $contractorSTR,
      'inbox_docContractorEMAIL' => $contractorEMAIL,
      'inbox_docUpdatedByID'     => $_SESSION['id'],
      'inbox_docUpdatedBySTR'    => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
      'inbox_docUpdatedWhen'     => date('Y-m-d H:i:s'),
    ), array(
      'id' => $id,
    ));
  }
  # ----- ----- ----- ----- ----- 
  # 
  if (!empty($outbox_rowID_rel)) {
    #
    # UPD 20.12.22
    #
    # ТИП ДОКУМЕНТА ОТВЕТ 
    #
    $_REQ_Outgoing = $db->sql("SELECT outbox_docType, outbox_docType_prev, outbox_docID, outbox_UID, koddocmail, outbox_docFileID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $outbox_rowID_rel)->fetchAll();
    #
    if ($inbox_docType == 2) {
      #
      if (isset($_REQ_Outgoing) && !empty($_REQ_Outgoing)) {
        $db->update(__MAIL_INCOMING_TABLENAME, array(
          'outbox_docID_rel'      => $_REQ_Outgoing[0]['outbox_docID'],
          'outbox_UID_rel'        => $_REQ_Outgoing[0]['outbox_UID'],
          'outbox_koddocmail_rel' => $_REQ_Outgoing[0]['koddocmail'],
          'outbox_fileID_rel'     => $_REQ_Outgoing[0]['outbox_docFileID'],
        ), array(
          'id' => $id,
        ));
        #
        if ($_REQ_Outgoing[0]['outbox_docType'] >= 0) {
          $db->update(__MAIL_OUTGOING_TABLENAME, array(
            'inbox_rowID_rel'        => $id,
            'inbox_docID_rel'        => $inbox_docID,
            'inbox_UID_rel'          => $inbox_UID,
            'inbox_koddocmail_rel'   => $inbox_koddocmail,
            'inbox_fileID_rel'       => $inbox_docFileID
          ), array(
            'id' => $outbox_rowID_rel
          ));
          if (json_encode($values['enbl_outbox_docType_change'], JSON_NUMERIC_CHECK) == '[1]') {
            $db->update(__MAIL_OUTGOING_TABLENAME, array(
              'outbox_docType'         => 3,
              'outbox_docTypeSTR'      => 'Зап',
              'outbox_docType_prev'    => $_REQ_Outgoing[0]['outbox_docType']
            ), array(
              'id' => $outbox_rowID_rel
            ));
          }
        }
      }
    } else {
      $db->update(__MAIL_INCOMING_TABLENAME, array(
        'outbox_docID_rel'      => null,
        'outbox_UID_rel'        => null,
        'outbox_koddocmail_rel' => null,
        'outbox_fileID_rel'     => null
      ), array(
        'id' => $id,
      ));
      # ----- ----- ----- ----- ----- 
      # Тип документа
      $docTYPEprev = $_REQ_Outgoing[0]['outbox_docType_prev'];
      $__docTypeDATAprev = $db->sql("SELECT * FROM mailbox_sp_doctypes_outgoing WHERE type_id = " . $docTYPEprev)->fetchAll();
      $docTypeSTRprev = !empty($__docTypeDATAprev[0]['type_name_short']) ? $__docTypeDATAprev[0]['type_name_short'] : "---";
      #
      $db->update(__MAIL_OUTGOING_TABLENAME, array(
        'outbox_docType'          => $docTYPEprev,
        'outbox_docTypeSTR'       => $docTypeSTRprev,
        'inbox_docID_rel'         => null,
        'inbox_rowID_rel'         => null,
        'inbox_UID_rel'           => null,
        'inbox_koddocmail_rel'    => null,
        'inbox_fileID_rel'        => null
      ), array(
        'id' => $outbox_rowID_rel
      ));
    }
  }
  # ----- ----- ----- ----- ----- 
  # 
  $__sID      = $db->sql("SELECT koddocmail, inbox_docID, inbox_docFileID FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
  $__year     = date('Y');
  $__fileID   = $__sID[0]['inbox_docFileID'];
  $file_id    = $__year . "00" . $__sID[0]['inbox_docID'];
  $koddocmail = $__sID[0]['koddocmail'];
  $db->update(__MAIL_INCOMING_FILES_TABLENAME, array(
    'flag'       => 'CHU',
    'koddocmail' => $koddocmail,
    'file_id'    => $file_id,
  ), array(
    'id' => $__fileID,
  ));
  $db->update(__MAIL_INCOMING_FILES_TABLENAME, array(
    'flag' => 'CHU',
  ), array(
    'koddocmail' => $koddocmail,
    'mainfile'   => '0',
  ));
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function updateFileIDadd($db, $action, $id, $values, $row) {
  if ('UPD' == $action) {
    $_koddocmail = $row[__MAIL_INCOMING_TABLENAME]['koddocmail'];
    $_inbox_docFileIDadd = "";
    $_REQ_UploadedFiles = mysqlQuery("SELECT id FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE koddocmail='{$_koddocmail}' AND mainfile='0'");
    while ($_ROW_UploadedFiles = mysqli_fetch_assoc($_REQ_UploadedFiles)) {
      $_inbox_docFileIDadd .= $_ROW_UploadedFiles['id'] . ",";
    }
    //
    // 
    $db->update(__MAIL_INCOMING_TABLENAME, array(
      'inbox_docFileIDadd' => $_inbox_docFileIDadd,
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
  $__rowFileID = $db->sql("SELECT * FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
  $row2delete  = $__rowFileID[0]['inbox_docFileID'];
  $addFiles2delete  = $__rowFileID[0]['inbox_docFileIDadd'];
  if (!is_null($row2delete) && !empty($row2delete)) {
    // Удаление оригинального файла ($__tmp2) и сим-ссылки ($__tmp1) с диска
    $__file = $db->sql("SELECT file_truelocation, file_syspath FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE id=" . $row2delete)->fetchAll();
    $__tmp1 = unlink($__file[0]['file_syspath']);
    $__tmp2 = unlink($__file[0]['file_truelocation']);
    // Удаление записи в таблице файлов
    if ($__tmp1 && $__tmp2) {
      $query = $db->sql("DELETE FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE id=" . $row2delete);
      PORTAL_SYSLOG('99921000', '000000D', $row2delete, null, null, null);
    }
  }
  $addFiles2delete = $addFiles2delete != "" ? substr($addFiles2delete, 0, -1) : "";
  if (!is_null($addFiles2delete) && !empty($addFiles2delete)) {
    $arrAddFiles2delete = explode(",", $addFiles2delete);
    foreach ($arrAddFiles2delete as $key => $value) {
      // Удаление оригинального файла ($__tmp2) и сим-ссылки ($__tmp1) с диска
      $__file1 = $db->sql("SELECT file_truelocation, file_syspath FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE id=" . $value)->fetchAll();
      $__tmp11 = unlink($__file1[0]['file_syspath']);
      $__tmp21 = unlink($__file1[0]['file_truelocation']);
      // Удаление записи в таблице файлов
      if ($__tmp11 && $__tmp21) {
        $query = $db->sql("DELETE FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE id=" . $value);
      }
    }
  }
  //
  $arrFromDB = [
    'inbox_docID' => $__rowFileID[0]['inbox_docID'],
    'inbox_docDate' => $__rowFileID[0]['inbox_docDate'],
    'inbox_docType' => $__rowFileID[0]['inbox_docType'],
    'inbox_docAbout' => $__rowFileID[0]['inbox_docAbout'],
    'inbox_docSender_kodzakaz' => $__rowFileID[0]['inbox_docSender_kodzakaz'],
    'inbox_docSourceID' => $__rowFileID[0]['inbox_docSourceID'],
    'inbox_docSourceDate' => $__rowFileID[0]['inbox_docSourceDate'],
    // 'outbox_koddocmail_rel' => $__rowFileID[0]['outbox_koddocmail_rel'],
    // 'outbox_docID_rel' => $__rowFileID[0]['outbox_docID_rel'],
    'inbox_docFileID' => $__rowFileID[0]['inbox_docFileID'],
    'inbox_docFileIDadd' => $__rowFileID[0]['inbox_docFileIDadd'],
    'inbox_docRecipient_kodzayvtel' => $__rowFileID[0]['inbox_docRecipient_kodzayvtel'],
    'inbox_docContractor_kodzayvispol' => $__rowFileID[0]['inbox_docContractor_kodzayvispol'],
    'inbox_controlIspolActive' => $__rowFileID[0]['inbox_controlIspolActive'],
    'inbox_docDateDeadline' => $__rowFileID[0]['inbox_docDateDeadline'],
    'inbox_controlIspolMailReminder1' => $__rowFileID[0]['inbox_controlIspolMailReminder1'],
    'inbox_controlIspolMailReminder2' => $__rowFileID[0]['inbox_controlIspolMailReminder2'],
    'inbox_controlIspolCheckout' => $__rowFileID[0]['inbox_controlIspolCheckout'],
  ];
  $settings = json_decode(json_encode($arrFromDB), JSON_FORCE_OBJECT);
  // 
  $reqDB1 = $db->sql("SELECT kodispol FROM mailbox_sp_users WHERE id='" . $_SESSION['id'] . "'")->fetchAll();
  $kodispol = (!empty($reqDB1) && $reqDB1[0]['kodispol'] != "") ? $reqDB1[0]['kodispol'] : null;
  //
  $db->insert(__MAIL_INCOMING_PREFIX . '_logChanges', array(
    'koddel'          => "",
    'recordType'      => "-1",
    'recordNum'       => null,
    'recordID'        => null,
    'timestamp'       => date('Y-m-d H:i:s'),
    'action'          => 'DEL',
    'koddocmail'      => $reqDB1[0]['koddocmail'],
    'userid'          => $_SESSION['id'],
    'kodispol'        => $kodispol,
    'oldSettings'     => null,
    'newSettings'     => json_encode($settings),
    'changes'         => null,
    'changesOldVal'   => null,
    'changesNewVal'   => null,
    'changesTitle'    => "Запись была удалена",
    'changesStr'      => null,
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
  PORTAL_SYSLOG('99921000', '000000F', $id, null, null, null);
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function updateSyslogAfterremove($db, $id) {
  PORTAL_SYSLOG('99921000', '0000003', $id, null, null, null);
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function backupRemovedRecords($db, $id) {
  // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 
  // ОПЕРАЦИИ ПРИ УДАЛЕНИИ ОСНОВНОЙ ЗАПИСИ ВО ВХОДЯЩЕЙ ПОЧТЕ ".__MAIL_INCOMING_PREFIX."
  // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 
  // > Основное поле-маркер удаленной записи - KODDEL (практически во всх таблицах)
  /* 
  Маркируем как удаленные:
    - ".__MAIL_INCOMING_PREFIX."_logChanges
    - ".__MAIL_INCOMING_PREFIX."_logComments
    - ".__MAIL_INCOMING_PREFIX."_logCheckouts
    - ".__MAIL_INCOMING_PREFIX."_logControl
    - ".__MAIL_INCOMING_PREFIX."_logMailing
    - ".__MAIL_INCOMING_PREFIX."_files
  */
  $_REQ_Koddocmail = $db->sql("SELECT koddocmail FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
  $_koddocmail = $_REQ_Koddocmail[0]['koddocmail'];

  $_REQ_UpdateKoddel_1 = $db->sql("UPDATE " . __MAIL_INCOMING_PREFIX . "_logChanges SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
  $_REQ_UpdateKoddel_2 = $db->sql("UPDATE " . __MAIL_INCOMING_PREFIX . "_logComments SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
  $_REQ_UpdateKoddel_3 = $db->sql("UPDATE " . __MAIL_INCOMING_PREFIX . "_logCheckouts SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
  $_REQ_UpdateKoddel_4 = $db->sql("UPDATE " . __MAIL_INCOMING_PREFIX . "_logControl SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
  $_REQ_UpdateKoddel_5 = $db->sql("UPDATE " . __MAIL_INCOMING_PREFIX . "_logMailing SET koddel='deleted' WHERE send_koddocmail='" . $_koddocmail . "'");
  // Маркируем таблицу прикрепленных файлов
  $_REQ_UpdateKoddel_Files = $db->sql("UPDATE " . __MAIL_INCOMING_FILES_TABLENAME . " SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
  // Копируем запись в таблицу для удаленных записей
  $_REQ_InsertDeleted = $db->sql("INSERT INTO " . __MAIL_INCOMING_PREFIX . " SELECT * FROM " . __MAIL_INCOMING_TABLENAME . " WHERE koddocmail='" . $_koddocmail . "'");
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
# ФУНКЦИЯ: ОТПРАВКА EMAIL ИСПОЛНИТЕЛЯ ДОКУМЕНТА С ФИКСАЦИЕЙ ВРЕМЕННОЙ МЕТКИ И ОТПРАВИВШЕГО
# ---
# inbox_docContractorID - ID ответственного из ".__MAIL_INCOMING_PREFIX."
# email - email из users, где id = inbox_docContractorID
# inbox_emailSentByID - ID отправившего
# inbox_emailSentWhen - временная метка отправки
#
#
#
function sendEmail2Contractor($db, $action, $id, $values) {

  $query1        = $db->sql("SELECT koddocmail, inbox_docID, inbox_docContractorID, inbox_docContractorMULTI, inbox_docContractor_kodzayvispol, inbox_docDateDeadline, inbox_docSender, inbox_docSenderName, inbox_docSourceID, inbox_docAbout, inbox_docTypeSTR, inbox_docFileID, toSendEmail FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
  $__koddocmail  = $query1[0]['koddocmail'];
  $__docID       = $query1[0]['inbox_docID'];
  $__docFileID   = $query1[0]['inbox_docFileID'];
  $__contrID     = $query1[0]['inbox_docContractorID'];
  $__contrKOD    = $query1[0]['inbox_docContractor_kodzayvispol'];
  $__deadline    = date_create($query1[0]['inbox_docDateDeadline']);
  $__ispolMULTI  = $query1[0]['inbox_docContractorMULTI'];
  $__senderORG   = $query1[0]['inbox_docSender'];
  $__senderNAME  = $query1[0]['inbox_docSenderName'];
  $__sourceID    = $query1[0]['inbox_docSourceID'];
  $__docABOUT    = $query1[0]['inbox_docAbout'];
  $__docTYPE     = $query1[0]['inbox_docTypeSTR'];
  $__toSendEmail = $query1[0]['toSendEmail'];

  if ($query1 && "" != $__contrKOD && null != $__contrKOD) {

    $arrispol = explode(",", $__contrKOD);
    foreach ($arrispol as $value) {
      $__contractorDATA1 = $db->sql("SELECT id, emailaddress, namezayvfio FROM mailbox_sp_users WHERE kodispol=" . $value)->fetchAll();
      $__contractorDATA2 = $db->sql("SELECT firstname, middlename, lastname FROM users WHERE id=" . $__contractorDATA1[0]['id'])->fetchAll();
      $__fileName        = "";
      $__fileURL         = "";
      $__fileLink        = "---";

      if ("" != $__docFileID) {
        $__contractorDATA3 = $db->sql("SELECT file_originalname, file_url FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE id=" . $__docFileID)->fetchAll();
        $__fileName        = (!empty($__contractorDATA3) && "" != $__contractorDATA3[0]['file_originalname']) ? "Прикрепленный файл" : "---";
        $__fileURL         = !empty($__contractorDATA3) ? $__contractorDATA3[0]['file_url'] : "";
        $__fileLink        = ("" != $__fileURL) ? '<a href="' . $__fileURL . '" title="">' . $__fileName . '</a>' : "---";
      }
      if ($__contractorDATA1 && $__contractorDATA2) {
        $__email = $__contractorDATA1[0]['emailaddress'];
        $__IO    = !empty($__contractorDATA2) ? $__contractorDATA2[0]['firstname'] . " " . $__contractorDATA2[0]['middlename'] : '';
        $__FIO   = $__contractorDATA1[0]['namezayvfio'];
        //
        //
        if (json_encode($values['toSendEmail'], JSON_NUMERIC_CHECK) == '[1]') {
          //
          // Записываем в БД в таблицу ".__MAIL_INCOMING_PREFIX.", кто поставил галочку "Отправка на email" в окне редактирования и когда
          $db->update(__MAIL_INCOMING_TABLENAME, array(
            'inbox_emailSentByID'  => $_SESSION['id'],
            'inbox_emailSentBySTR' => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
            'inbox_emailSentWhen'  => date('Y-m-d H:i:s'),
          ), array(
            'id' => $id,
          ));
          # ----- ----- ----- ----- -----
          #
          # БЛОК ОТПРАВКИ СООБЩЕНИЯ
          #
          #
          $mail    = new PHPMailer;
          $message = "";
          #
          #
          # SERVER SETTINGS
          #
          #
          # Enable verbose debug output
          $mail->SMTPDebug = SMTP::DEBUG_SERVER;
          # Disable verbose debug output
          # $mail->SMTPDebug = 0;
          # Send using SMTP
          $mail->isSMTP();
          # Set the SMTP server to send through
          $mail->Host = 'mail.atgs.ru';
          # Enable SMTP authentication
          $mail->SMTPAuth = true;
          # SMTP connection will not close after each email sent, reduces SMTP overhead
          $mail->SMTPKeepAlive = true;
          # SMTP username
          $mail->Username = 'portal@atgs.ru';
          # SMTP password
          $mail->Password = 'iu3Li,quohch';
          # Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
          # TCP port
          $mail->Port = 587;
          #
          $mail->setLanguage('ru', $_SERVER['DOCUMENT_ROOT'] . "/mail/_assets/_PHPMailer/language/");
          $mail->CharSet = "utf-8";
          #
          # From
          $from_name  = "АТГС.Портал / Корпоративные сервисы";
          $from_email = "portal@atgs.ru";
          $from_name  = "=?utf-8?B?" . base64_encode($from_name) . "?=";
          $mail->setFrom($from_email, $from_name);
          # Mail address
          $email_to = $__email;
          // $email_to = 'chugunov@atgs.ru';
          $email_admin = 'chugunov@atgs.ru';
          #
          # Тема сообения
          $subjectTxt = "Почта АТГС [Входящие] : Вы назначены ответственным по документу №" . $__docID;
          $subject    = "=?utf-8?B?" . base64_encode($subjectTxt) . "?=";
          #
          $mail->addAddress($email_to);
          $mail->addCC($email_admin);
          $mail->addReplyTo('notreply@atgs.ru', 'Do not reply');
          #
          # Message body
          $_msgTitle    = "Новое письмо";
          $_msgText     = '<span style="font-size:28px">' . $__IO . ', </span><br>вы назначены ответственным по входящему документу №' . $__docID;
          $_docDeadline = date_format($__deadline, 'd.m.Y');

          $message .= '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
<!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<meta content="width=device-width" name="viewport"/>
<!--[if !mso]><!-->
<meta content="IE=edge" http-equiv="X-UA-Compatible"/>
<!--<![endif]-->
<title></title>
<!--[if !mso]><!-->
<!--<![endif]-->
<style type="text/css">
		body {
			margin: 0;
			padding: 0;
		}

		table,
		td,
		tr {
			vertical-align: top;
			border-collapse: collapse;
		}

		* {
			line-height: inherit;
		}

		a[x-apple-data-detectors=true] {
			color: inherit !important;
			text-decoration: none !important;
		}
	</style>
<style id="media-query" type="text/css">
		@media (max-width: 620px) {

			.block-grid,
			.col {
				min-width: 320px !important;
				max-width: 100% !important;
				display: block !important;
			}

			.block-grid {
				width: 100% !important;
			}

			.col {
				width: 100% !important;
			}

			.col_cont {
				margin: 0 auto;
			}

			img.fullwidth,
			img.fullwidthOnMobile {
				max-width: 100% !important;
			}

			.no-stack .col {
				min-width: 0 !important;
				display: table-cell !important;
			}

			.no-stack.two-up .col {
				width: 50% !important;
			}

			.no-stack .col.num2 {
				width: 16.6% !important;
			}

			.no-stack .col.num3 {
				width: 25% !important;
			}

			.no-stack .col.num4 {
				width: 33% !important;
			}

			.no-stack .col.num5 {
				width: 41.6% !important;
			}

			.no-stack .col.num6 {
				width: 50% !important;
			}

			.no-stack .col.num7 {
				width: 58.3% !important;
			}

			.no-stack .col.num8 {
				width: 66.6% !important;
			}

			.no-stack .col.num9 {
				width: 75% !important;
			}

			.no-stack .col.num10 {
				width: 83.3% !important;
			}

			.video-block {
				max-width: none !important;
			}

			.mobile_hide {
				min-height: 0px;
				max-height: 0px;
				max-width: 0px;
				display: none;
				overflow: hidden;
				font-size: 0px;
			}

			.desktop_hide {
				display: block !important;
				max-height: none !important;
			}
		}
	</style>
</head>
<body class="clean-body" style="margin: 0; padding: 0; -webkit-text-size-adjust: 100%; background-color: #fff;">
<!--[if IE]><div class="ie-browser"><![endif]-->
<table bgcolor="#fff" cellpadding="0" cellspacing="0" class="nl-container" role="presentation" style="table-layout: fixed; vertical-align: top; min-width: 320px; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff; width: 100%;" valign="top" width="100%">
<tbody>
<tr style="vertical-align: top;" valign="top">
<td style="word-break: break-word; vertical-align: top;" valign="top">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color:#fff"><![endif]-->
<div style="background-color:transparent;">
<div class="block-grid mixed-two-up" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="150" style="background-color:transparent;width:150px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
<div class="col num3" style="display: table-cell; vertical-align: top; max-width: 320px; min-width: 150px; width: 150px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<div class="mobile_hide">
<div align="left" class="img-container left fixedwidth fullwidthOnMobile" style="padding-right: 0px;padding-left: 0px;">
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr style="line-height:0px"><td style="padding-right: 0px;padding-left: 0px;" align="left"><![endif]--><img alt="Alternate text" border="0" class="left fixedwidth fullwidthOnMobile" src="http://atgs.ru/ext/img/59986fe-e033-481a-9f16-935182b7541e.png" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; width: 100%; max-width: 112px; display: block;" title="Alternate text" width="112"/>
<!--[if mso]></td></tr></table><![endif]-->
</div>
</div>
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td><td align="center" width="450" style="background-color:transparent;width:450px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
<div class="col num9" style="display: table-cell; vertical-align: top; min-width: 320px; max-width: 450px; width: 450px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 25px; padding-bottom: 15px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:25px;padding-right:10px;padding-bottom:15px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 36px; line-height: 1.2; word-break: break-word; text-align: left; mso-line-height-alt: 34px; margin: 0;"><span style="color: #ffb400; font-size: 36px; text-transform: uppercase"><span style=""><strong>' . $_msgTitle . '</strong></span></span></p>
<p style="font-size: 20px; line-height: 1.2; word-break: break-word; text-align: left; mso-line-height-alt: 24px; margin: 0;"><span style="color: #ffffff; font-size: 20px; background-color: #000000;"><span style=""><strong>Документ во входящих № ' . $__docID . '</strong></span></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:transparent;width:600px; border-top: 1px solid transparent; border-left: 1px solid transparent; border-bottom: 1px solid transparent; border-right: 1px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:15px;"><![endif]-->
<div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 598px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:1px solid transparent; border-left:1px solid transparent; border-bottom:1px solid transparent; border-right:1px solid transparent; padding-top:5px; padding-bottom:15px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 14px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 17px; margin: 0;"><strong><span style="color: #000000; font-size: 18px;"><span style="">' . $_msgText . '</span></span></strong></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid mixed-two-up" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="200" style="background-color:transparent;width:200px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num4" style="display: table-cell; vertical-align: top; max-width: 320px; min-width: 200px; width: 200px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="font-size: 12px; color: #333333;">ССЫЛКА</span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td><td align="center" width="400" style="background-color:transparent;width:400px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num8" style="display: table-cell; vertical-align: top; min-width: 320px; max-width: 400px; width: 400px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="color: #00a6dc;"><strong><span style="font-size: 12px;">' . $__fileLink . '</span></strong></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid mixed-two-up" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="200" style="background-color:transparent;width:200px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num4" style="display: table-cell; vertical-align: top; max-width: 320px; min-width: 200px; width: 200px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="font-size: 12px; color: #333333;">ТИП ДОКУМЕНТА</span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td><td align="center" width="400" style="background-color:transparent;width:400px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num8" style="display: table-cell; vertical-align: top; min-width: 320px; max-width: 400px; width: 400px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="color: #000000;"><strong><span style="font-size: 12px;">' . $__docTYPE . '</span></strong></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid mixed-two-up" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="200" style="background-color:transparent;width:200px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num4" style="display: table-cell; vertical-align: top; max-width: 320px; min-width: 200px; width: 200px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="font-size: 12px; color: #333333;">ОТПРАВИТЕЛЬ</span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td><td align="center" width="400" style="background-color:transparent;width:400px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num8" style="display: table-cell; vertical-align: top; min-width: 320px; max-width: 400px; width: 400px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="color: #000000;"><strong><span style="font-size: 12px;">' . $__senderORG . '</span></strong></span> / <span style="color: #000000;"><strong><span style="font-size: 12px;">' . $__senderNAME . '</span></strong></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid mixed-two-up" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="200" style="background-color:transparent;width:200px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num4" style="display: table-cell; vertical-align: top; max-width: 320px; min-width: 200px; width: 200px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="font-size: 12px; color: #333333;">ОПИСАНИЕ</span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td><td align="center" width="400" style="background-color:transparent;width:400px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num8" style="display: table-cell; vertical-align: top; min-width: 320px; max-width: 400px; width: 400px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; text-align: left; mso-line-height-alt: 14px; margin: 0;"><strong><span style="color: #000000; font-size: 12px;">' . $__docABOUT . '</span></strong></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid mixed-two-up" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="200" style="background-color:transparent;width:200px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num4" style="display: table-cell; vertical-align: top; max-width: 320px; min-width: 200px; width: 200px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="font-size: 12px; color: #333333;">ИСХОДЯЩИЙ НОМЕР</span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td><td align="center" width="400" style="background-color:transparent;width:400px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num8" style="display: table-cell; vertical-align: top; min-width: 320px; max-width: 400px; width: 400px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; text-align: left; mso-line-height-alt: 14px; margin: 0;"><span style="font-size: 12px;"><strong><span style="color: #000000;">' . $__sourceID . '</span></strong></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:transparent;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
<div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<table border="0" cellpadding="0" cellspacing="0" class="divider" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top" width="100%">
<tbody>
<tr style="vertical-align: top;" valign="top">
<td class="divider_inner" style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 15px; padding-right: 10px; padding-bottom: 5px; padding-left: 10px;" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" class="divider_content" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 1px solid #BBBBBB; width: 100%;" valign="top" width="100%">
<tbody>
<tr style="vertical-align: top;" valign="top">
<td style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top"><span></span></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 0px; padding-bottom: 0px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:0px;padding-right:10px;padding-bottom:0px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 9px; line-height: 1.2; word-break: break-word; text-align: left; mso-line-height-alt: 11px; margin: 0;"><span style="font-size: 9px; color: #999999;"><em><span style="">Служба уведомлений <span style="color: #333333;">АТГС.Портал</span></span></em></span></p>
<p style="font-size: 9px; line-height: 1.2; word-break: break-word; text-align: left; mso-line-height-alt: 11px; margin: 0;"><span style="font-size: 9px; color: #999999;"><em><span style="">Данное сообщение отправлено роботом. Не используйте адрес его отправителя для обратной связи.</span></em></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
</td>
</tr>
</tbody>
</table>
<!--[if (IE)]></div><![endif]-->
</body>
</html>
';
          #
          # Content
          $mail->isHTML(true); // Set email format to HTML
          $mail->Subject = $subject;
          $mail->Body    = $message;
          $mail->AltBody = 'Ваш почтовый клиент не принимает сообщений в формате HTML. Вариант рассылки в формате PLAIN TEXT будет реализован позже.';
          #
          # Send the message, check for errors
          #
          # Открыли файл для записи данных в конец файла
          #
          $filename = $_SERVER['DOCUMENT_ROOT'] . "/mail/PHPMailer_errors.log";
          if (is_writable($filename)) {

            if (!$handle = fopen($filename, 'a')) {
              echo "<span style='color:red; text-align:center'><i>Не могу открыть лог-файл для записи отчета об отправке.</i></span>";
              exit;
            }

            if (!$mail->send()) {
              $err  = $mail->ErrorInfo . PHP_EOL;
              $text = date('Y-m-d h:i:s') . " : ошибка рассылки на ( $email_to ) : " . $err;
              // Записываем $somecontent в наш открытый файл.
              if (fwrite($handle, $text) === false) {
                echo "<span style='color:red; text-align:center'><i>Не могу произвести запись в лог файл.</i></span>";
                exit;
              }
              echo "<span style='color:red; text-align:center'><i>Ошибка при отправке сообщения : $err.</i></span>";
              fclose($handle);
            } else {
              $text = date('Y-m-d h:i:s') . " : сообщение на ( $email_to ) успешно отправлено" . PHP_EOL;
              // Записываем $somecontent в наш открытый файл.
              if (fwrite($handle, $text) === false) {
                echo "<span style='color:red; text-align:center'><i>Не могу произвести запись в лог-файл.</i></span>";
                exit;
              }
              echo "<span style='color:green; text-align:center'><i>Сообщение успешно отправлено. Запись в лог-файл произведена.</i></span>";
              fclose($handle);
              //
              // Записываем в БД в таблицу ".__MAIL_INCOMING_PREFIX.", кто поставил галочку "Отправка на email" в окне редактирования и когда
              $db->update(
                __MAIL_INCOMING_TABLENAME,
                array(
                  'toSendEmail' => "1",
                ),
                array(
                  'id' => $id,
                )
              );
              //
              $timestamp = date('Y-m-d H:i:s');
              //
              // Проверочная запись сформированного сообщения
              $db->insert('mailbox_test_EmailSending', array(
                'email_when'      => $timestamp,
                'email_header'    => "",
                'email_name'      => $__FIO,
                'email_address'   => $email_to,
                'email_subject'   => $subject,
                'email_text'      => $message,
                'email_whoSendID' => $_SESSION['id'],
                'email_docID'     => $__docID,
                'email_docRow'    => $id,
              ));
              //
              $msgMail = "Отправлено email-уведомление на <span class='text-success'>" . $email_to . "</span> о назначении ответственного ( <span class='text-success'>" . $__FIO . "</span> )";
              $db->insert(__MAIL_INCOMING_PREFIX . '_logChanges', array(
                'koddel'          => "",
                'recordType'      => "0",
                'recordNum'       => "1",
                'recordID'        => null,
                'timestamp'       => $timestamp,
                'action'          => "MAIL_NOTIFY",
                'koddocmail'      => $__koddocmail,
                'userid'          => $_SESSION['id'],
                'kodispol'        => $__contrKOD,
                'oldSettings'     => null,
                'newSettings'     => null,
                'changes'         => $__contrKOD,
                'changesOldVal'   => null,
                'changesNewVal'   => null,
                'changesTitle'    => "Отправлено email-уведомление",
                'changesStr'      => $msgMail,
                'changesText'     => $msgMail,
                'changesCount'    => null,
              ));
            }
          } else {
            echo "<span style='color:red; text-align:center'><i>Лог-файл недоступен для записи.</i></span>";
          }
        }
      }
    }
  }
}
#
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
Editor::inst($db, __MAIL_INCOMING_TABLENAME)
  ->fields(
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.ID'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.koddocmail'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docType')
      ->options(
        Options::inst()
          ->table('mailbox_sp_doctypes_incoming')
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
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docType_lock'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docType_prev'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docTypeSTR'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docID'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_UID'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docIDSTR'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_rowIDs_links')
      ->setFormatter(Format::ifEmpty(NULL)),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docIDs_links')
      ->options(
        Options::inst()
          ->table(__MAIL_INCOMING_TABLENAME)
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
            $q->where('inbox_docDate', '( SELECT inbox_docDate FROM ' . __MAIL_INCOMING_TABLENAME . ' WHERE inbox_docDate >= ' . $__startDate . ' AND inbox_docDate <= ' . $__endDate . ' )', 'IN', false);
          })
          ->order('inbox_docDate DESC')
      )
      ->validator(Validate::dbValues())
      ->setFormatter(Format::ifEmpty(NULL)),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.dognet_rowIDs_links')
      ->setFormatter(Format::ifEmpty(NULL)),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.dognet_docIDs_links')
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
    Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_rowIDs_links')
      ->setFormatter(Format::ifEmpty(NULL)),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_docIDs_links')
      ->options(
        Options::inst()
          ->table(__MAIL_OUTGOING_TABLENAME)
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
            $q->where('outbox_docDate', '( SELECT outbox_docDate FROM ' . __MAIL_OUTGOING_TABLENAME . ' WHERE outbox_docDate >= ' . $__startDate . ' AND outbox_docDate <= ' . $__endDate . ' )', 'IN', false);
          })
          ->order('outbox_docDate DESC')
      )
      ->validator(Validate::dbValues())
      ->setFormatter(Format::ifEmpty(NULL)),
    // 
    //     
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    // 
    Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_rowIDadd_rel')
      ->setFormatter(Format::ifEmpty(NULL)),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_rowIDList_rel')
      ->options(
        Options::inst()
          ->table(__MAIL_OUTGOING_TABLENAME)
          ->value('id')
          ->label(array('id', 'outbox_docID', 'outbox_docRecipient', 'outbox_docDate', 'outbox_docAbout'))
          ->render(function ($row) {
            $date    = date_create($row['outbox_docDate']);
            $docDate = date_format($date, "d.m.Y");
            return $docDate . ' / №' . $row['outbox_docID'] . ' / ' . $row['outbox_docRecipient'] . ' / ' . $row['outbox_docAbout'];
          })
          ->where(function ($q) use ($__startDate, $__endDate) {
            $q->where('outbox_docDate', '( SELECT outbox_docDate FROM ' . __MAIL_OUTGOING_TABLENAME . ' WHERE outbox_docDate >= ' . $__startDate . ' AND outbox_docDate <= ' . $__endDate . ' )', 'IN', false);
          })
          ->order('outbox_docDate DESC')
      )
      ->validator(Validate::dbValues())
      ->setFormatter(Format::ifEmpty(NULL)),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_rowID_rel')
      ->options(
        Options::inst()
          ->table(__MAIL_OUTGOING_TABLENAME)
          ->value('id')
          ->label(array('id', 'outbox_docID', 'outbox_docRecipient', 'outbox_docDate', 'outbox_docAbout'))
          ->render(function ($row) {
            $date    = date_create($row['outbox_docDate']);
            $docDate = date_format($date, "d.m.Y");
            return $docDate . ' / №' . $row['outbox_docID'] . ' / ' . $row['outbox_docRecipient'] . ' / ' . $row['outbox_docAbout'];
          })
          ->where(function ($q) use ($__startDate, $__endDate) {
            $q->where('outbox_docDate', '( SELECT outbox_docDate FROM ' . __MAIL_OUTGOING_TABLENAME . ' WHERE outbox_docDate >= ' . $__startDate . ' AND outbox_docDate <= ' . $__endDate . ' )', 'IN', false);
          })
          ->order('outbox_docDate DESC')
      )
      ->validator(Validate::dbValues())
      ->setFormatter(Format::ifEmpty(NULL)),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_docID_rel'),
    //
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    // 
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docDate')
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
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docAbout')
      ->validator(Validate::notEmpty(
        ValidateOptions::inst()
          ->message('Краткое описание обязательно')
      )),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docSender_kodzakaz')
      ->options(
        Options::inst()
          ->table('sp_contragents')
          ->value('kodcontragent')
          ->label(array('kodcontragent', 'nameshort', 'zakfio'))
          ->render(function ($row) {
            return $row['nameshort'] . " / " . $row['zakfio'];
          })
          ->where(function ($q) {
            $q->where('koddel', '99', '!=');
          })
      )
      ->validator(Validate::notEmpty(
        ValidateOptions::inst()
          ->message('Отправитель письма обязателен')
      )),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docSender'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docSenderName'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docRecipient_kodzayvtel')
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
          ->message('Получатель письма обязателен')
      )),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docRecipientID'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docRecipientSTR'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docSourceID')
      ->validator(Validate::notEmpty(
        ValidateOptions::inst()
          ->message('Исх номер обязателен')
      )),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docSourceDate')
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
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docFileID')
      ->setFormatter(Format::ifEmpty(null))
      ->upload(
        Upload::inst(
          function ($file, $id) use ($varFileArray, $db) {
            $__pref    = date('Y') . $_SESSION['id'] . date('mdHis');
            $__name    = $__pref . "-" . $file['name'];
            $__nameTmp = $file['tmp_name'];
            $__ext     = explode('.', $__name);
            $__ext     = strtolower(end($__ext));

            $md5       = md5(uniqid());
            $__nameMD5 = "{$md5}.{$__ext}";

            $__url = str_replace($_SERVER['DOCUMENT_ROOT'], 'http://' . $_SERVER['HTTP_HOST'], $varFileArray['syspath'] . $__nameMD5);

            move_uploaded_file($__nameTmp, $varFileArray['docpath'] . "{$__name}");
            symlink($varFileArray['docpath'] . "{$__name}", $varFileArray['syspath'] . $__nameMD5);

            $db->update(
              __MAIL_INCOMING_FILES_TABLENAME, // Database table to update
              [
                'mainfile'          => '1',
                'flag'              => 'PREUPL',
                'file_year'         => $varFileArray['year'],
                'file_id'           => '',
                'file_name'         => $__name,
                'file_originalname' => $file['name'],
                'file_symname'      => $__nameMD5,
                // Правка от 05/06/2019
                //        'file_truelocation' => $varFileArray['docpath']."{$__name}.{$__ext}",
                'file_truelocation' => $varFileArray['docpath'] . "{$__name}",
                // ---
                'file_syspath'      => $varFileArray['syspath'] . $__nameMD5,
                'file_webpath'      => $varFileArray['webpath'] . $__nameMD5,
                'file_url'          => $__url,

              ],
              ['id' => $id]
            );
            return $id;
          }
        )
          ->db(
            __MAIL_INCOMING_FILES_TABLENAME,
            'id',
            array(
              'file_extension'    => Upload::DB_EXTN,
              'file_size'         => Upload::DB_FILE_SIZE,
              'file_webpath'      => '',
              'file_truelocation' => '',
              'file_originalname' => '',
              'koddocmail'        => '',
            )
          )
          ->validator(Validate::fileSize(35000000, 'Размер документа не должен превышать 20МБ'))
          ->validator(Validate::fileExtensions(array('png', 'jpg', 'pdf'), "Загрузите документ"))
      ),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docFileIDadd'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_fileID_rel')
      ->options(
        Options::inst()
          ->table(__MAIL_OUTGOING_FILES_TABLENAME)
          ->value('id')
          ->label(array('file_webpath', 'file_name'))
      ),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_OUTGOING_FILES_TABLENAME . '.file_webpath'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_OUTGOING_FILES_TABLENAME . '.file_name'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractor_kodzayvispol')
      ->options(
        Options::inst()
          ->table('mailbox_sp_users')
          ->value('kodispol')
          ->label(array('status_ispol', 'kodispol', 'namezayvfio'))
          ->render(function ($row) {
            return ($row['namezayvfio']);
          })
          ->where(function ($q) {
            $q->where('status_ispol', '1', '=');
            $q->and_where('enable', '1', '=');
          })
      )
      ->validator(Validate::notEmpty(
        ValidateOptions::inst()
          ->message('Ответственный обязателен')
      )),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractorMULTI'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractorID'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractorDEPT'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractorSTR'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractorEMAIL'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docDateDeadline')
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
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractorComment'),
    //   Field::inst( 'inbox_noticeEmail' ),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docComment'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docCreatedByID')->set(Field::SET_CREATE),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docCreatedBySTR')->set(Field::SET_CREATE),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docCreatedWhen')
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
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docUpdatedByID')->set(Field::SET_EDIT),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docUpdatedBySTR')->set(Field::SET_EDIT),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docUpdatedWhen')
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
    Field::inst(__MAIL_INCOMING_TABLENAME . '.toSendEmail'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_emailSentByID')->set(Field::SET_CREATE),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_emailSentBySTR')->set(Field::SET_CREATE),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_emailSentWhen')
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
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolActive'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolCheckout'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolCheckoutWhen')
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
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolStatus'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolWarning'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolAlarm'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolDays'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailReminder1'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailReminder2'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailToRukOk'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailToRukAlarm'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolUseDeadline'),
    Field::inst(__MAIL_INCOMING_TABLENAME . '.cntComments'),
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    Field::inst(__MAIL_INCOMING_FILES_TABLENAME . '.id'),
    Field::inst(__MAIL_INCOMING_FILES_TABLENAME . '.file_webpath'),
    Field::inst(__MAIL_INCOMING_FILES_TABLENAME . '.file_originalname'),
  )
  // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
  ->on('preUpload', function ($editor, $data) {
  })

  ->on('postUpload', function ($editor, $id, $files, $data) {
    updateSyslogAfterupload($editor->db(), $id);
  })

  ->on('preEdit', function ($editor, $id, $values) {
    fixLog($editor->db(), 'UPD', $id, $values);
    updateLogCheckouts($editor->db(), $id, $values);
    clearRelFields($editor->db(), $id, $values);
  })

  ->on('preCreate', function ($editor, $values) {
  })

  ->on('preGet', function ($editor, $id) use ($__startDate, $__endDate) {
    $editor->where(function ($q) use ($__startDate, $__endDate) {
      $q->where(__MAIL_INCOMING_TABLENAME . '.inbox_docDate', '( SELECT inbox_docDate FROM ' . __MAIL_INCOMING_TABLENAME . ' WHERE inbox_docDate >= ' . $__startDate . ' AND inbox_docDate <= ' . $__endDate . ' )', 'IN', false);
    });
  })

  ->on('postCreate', function ($editor, $id, $values, $row) {
    // updateFields($editor->db(), 'CRT', $id, $values, $row);
    // sendEmail2Contractor($editor->db(), 'Email', $id, $values);
    // fixCreate($editor->db(), 'CRT', $id, $values);
    // fixLog($editor->db(), 'CRT', $id, $values);
  })

  ->on('postEdit', function ($editor, $id, $values, $row) {
    fixUpdate($editor->db(), 'UPD', $id, $values);
    updateFields($editor->db(), 'UPD', $id, $values, $row);
    // updateIspolStatus($editor->db(), $id, $values);
    // sendEmail2Contractor($editor->db(), 'Email', $id, $values);
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
  ->leftJoin('sp_contragents', 'sp_contragents.kodcontragent', '=', __MAIL_INCOMING_TABLENAME . '.inbox_docSender_kodzakaz')
  ->leftJoin(__MAIL_INCOMING_FILES_TABLENAME, __MAIL_INCOMING_FILES_TABLENAME . '.id', '=', __MAIL_INCOMING_TABLENAME . '.inbox_docFileID')
  ->leftJoin(__MAIL_OUTGOING_FILES_TABLENAME, __MAIL_OUTGOING_FILES_TABLENAME . '.id', '=', __MAIL_INCOMING_TABLENAME . '.outbox_fileID_rel')
  ->process($_POST)
  ->json();
