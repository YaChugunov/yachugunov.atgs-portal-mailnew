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
$__startDate_rel = $_SESSION['in_startTableDate'];
$__endDate = $_SESSION['in_endTableDate'];
$__endDate_rel = $_SESSION['in_endTableDate'];
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && __MAIL_TESTMODE_TYPE < 3) {
    define('__MAIL_RESTR3', '/restr_3.test');
} else {
    define('__MAIL_RESTR3', '/restr_3');
}
# Import PHPMailer classes into the global namespace
# These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function outputTempTest($msgFrom, $msgMain, $msgText1, $msgText2, $msgText3, $msgText4, $msgText5) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date("Y-m-d H:i:s");
    $col = "0";
    $_req = mysqlQuery("UPDATE outputTemp_test SET ip='{$ip}', msgDate='{$date}', msgCol='{$col}', msgFrom='{$msgFrom}', msgMain='{$msgMain}', msgText1='{$msgText1}', msgText2='{$msgText2}', msgText3='{$msgText3}' WHERE msgFrom LIKE '%{$msgFrom}%'");
}

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция проверки строки на правильность даты
//
function validateDate($date, $format) {
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
        'docmailext'            => 'Mailnew'
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
function UpdateControls($db, $action, $id, $values) {
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function checkChanges($db, $id, $values) {
    unset($_SESSION['inbox_checkChanges_is']);
    unset($_SESSION['inbox_checkChanges_dataForm']);
    unset($_SESSION['inbox_checkChanges_dataDB']);
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
        'inbox_docDateDeadline' => validateDate($values[__MAIL_INCOMING_TABLENAME]['inbox_docDateDeadline'], "d.m.Y") ? date("Y-m-d", strtotime($values[__MAIL_INCOMING_TABLENAME]['inbox_docDateDeadline'])) : null,
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
        'inbox_docDateDeadline' => validateDate($reqDB[0]['inbox_docDateDeadline'], "Y-m-d") ? $reqDB[0]['inbox_docDateDeadline'] : null,
        'inbox_controlIspolMailReminder1' => $reqDB[0]['inbox_controlIspolMailReminder1'],
        'inbox_controlIspolMailReminder2' => $reqDB[0]['inbox_controlIspolMailReminder2'],
        'inbox_controlIspolCheckout' => $reqDB[0]['inbox_controlIspolCheckout'],
    ];
    $checkChanges_dataForm = "";
    $checkChanges_dataDB = "";
    foreach ($arrValues as $key => $row) {
        $checkChanges_dataForm .= ($key) . ' = ' . $row . "\r\n";
    }
    foreach ($arrFromDB as $key => $row) {
        $checkChanges_dataDB .= ($key) . ' = ' . $row . "\r\n";
    }

    $_SESSION['inbox_checkChanges_is'] = ($arrValues === $arrFromDB) ? 'nochanges' : "changed";
    $_SESSION['inbox_checkChanges_dataDB'] = $checkChanges_dataDB;
    $_SESSION['inbox_checkChanges_dataForm'] = $checkChanges_dataForm;
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
        'inbox_docDateDeadline' => validateDate($values[__MAIL_INCOMING_TABLENAME]['inbox_docDateDeadline'], "d.m.Y") ? date("Y-m-d", strtotime($values[__MAIL_INCOMING_TABLENAME]['inbox_docDateDeadline'])) : null,
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
        'inbox_docDateDeadline' => validateDate($reqDB[0]['inbox_docDateDeadline'], "Y-m-d") ? $reqDB[0]['inbox_docDateDeadline'] : null,
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
            'inbox_docDateDeadline' => validateDate($reqDB[0]['inbox_docDateDeadline'], "Y-m-d") ? $reqDB[0]['inbox_docDateDeadline'] : null,
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
                    $oldVal = empty($oldSettings[$key]) ? "Нет даты" : (validateDate($oldSettings[$key], "Y-m-d") ? date('d.m.Y', strtotime($oldSettings[$key])) : "Нет даты");
                    $newVal = empty($value) ? "Нет даты" : (validateDate($value, "Y-m-d") ? date('d.m.Y', strtotime($value)) : "Нет даты");
                } else if ($key == 'inbox_docSourceDate') {
                    $oldVal = empty($oldSettings[$key]) ? "Нет даты" : (validateDate($oldSettings[$key], "Y-m-d") ? date('d.m.Y', strtotime($oldSettings[$key])) : "Нет даты");
                    $newVal = empty($value) ? "Нет даты" : (validateDate($value, "Y-m-d") ? date('d.m.Y', strtotime($value)) : "Нет даты");
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
                        $oldVal = $oldSettings[$key] . " / " . date('d.m.Y', strtotime($arrFromDB['inbox_docDateDeadline']));
                        $newVal = $value;
                    } else if ($oldSettings[$key] == 0 && $value == 1) {
                        $oldVal = $oldSettings[$key];
                        $newVal = $value . " / " . date('d.m.Y', strtotime($arrValues['inbox_docDateDeadline']));
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
    // PORTAL_SYSLOG('99921000', '0000002', $id, null, null, null);
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### 
# ФУНКЦИЯ
# Назначение: 
##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function updateLogCheckouts($db, $id, $values) {
    // Берем из сессии ID пользователя
    $userid = $_SESSION['id'];
    // Текущая временная метка
    $datenow = date("Y-m-d H:i:s");
    // SQL: (reqSQL_1) Снимаем из текущей записи ID документа (koddocmail)
    $_reqSQL_1 = $db->sql("SELECT koddocmail, inbox_docContractor_kodzayvispol FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $koddocmail = $_reqSQL_1[0]['koddocmail'];
    $kodispol = $_reqSQL_1[0]['inbox_docContractor_kodzayvispol'];
    // --- --- --- --- ---
    // Берем набор исполнителей из формы редактирования
    $ispolNew = $values[__MAIL_INCOMING_TABLENAME]['inbox_docContractor_kodzayvispol'];
    // SQL: (reqSQL_3) Удаляем из таблицы исполнения (".__MAIL_INCOMING_PREFIX."_logCheckouts) отстутствующих исполнителей в списке по документу
    $_reqSQL_3 = $db->sql("DELETE FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND kodispol NOT IN ({$ispolNew})");
    // --- --- --- --- ---
    // SQL: (reqSQL_2) Снимаем из таблицы отметок об исполнении (TBLPREFIX_logCheckouts) статус текущего документа (koddocmail) по текущему пользователю (userid)
    $_sqlString_2 = "SELECT ispolStatus FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'";
    $_reqSQL_2 = $db->sql($_sqlString_2)->fetchAll();
    //
    $ispolStatusOld = isset($_reqSQL_2[0]['ispolStatus']) ? $_reqSQL_2[0]['ispolStatus'] : "0";
    $ispolStatusNew = $values['ispolStatus'];
    // SQL: (reqSQL_4) Если новый статус не соответствует старому - обновляем его
    if ($ispolStatusOld != $ispolStatusNew) {
        $__reqSQL_5 = $db->sql("SELECT * FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'")->fetchAll();
        if ($__reqSQL_5) {
            $__reqSQL_41 = $db->sql("UPDATE " . __MAIL_INCOMING_PREFIX . "_logCheckouts SET timestamp='{$datenow}', ispolStatus='{$ispolStatusNew}' WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'");
        } else {
            $__reqSQL_42 = $db->sql("INSERT INTO " . __MAIL_INCOMING_PREFIX . "_logCheckouts (koddel, timestamp, koddocmail, userid, kodispol, ispolStatus, comment) VALUES ('', '{$datenow}', '{$koddocmail}', '{$userid}', '{$kodispol}', '{$ispolStatusNew}', '')");
        }
    }
    // 
    // ОТЛАДКА >>>>>
    $msgMain = $koddocmail;
    $msgText1 = "ID исполнителей: " . $ispolNew;
    $msgText2 = "Старый статус: " . $ispolStatusOld;
    $msgText3 = "Новый сатус: " . $ispolStatusNew;
    $msgText4 = "";
    $msgText5 = "";
    outputTempTest("updateLogCheckouts", $msgMain, $msgText1, $msgText2, $msgText3, $msgText4, $msgText5);
    // <<<<< ОТЛАДКА
    //
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
function updateIspolStatus($db, $id, $values) {

    $userid = $_SESSION['id'];
    //
    $__reqKoddocmail = $db->sql("SELECT koddocmail, inbox_docContractor_kodzayvispol FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    if ($__reqKoddocmail) {
        $koddocmail = $__reqKoddocmail[0]['koddocmail'];
        $kodispol = $__reqKoddocmail[0]['inbox_docContractor_kodzayvispol'];
    }
    //
    if ($koddocmail != "" &&  $kodispol != "") {
        $__reqIspolStatus = $db->sql("SELECT ispolStatus FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'")->fetchAll();
        if (!empty($__reqIspolStatus)) {
            $ispolStatus = $__reqIspolStatus[0]['ispolStatus'];
            // 
            // ОТЛАДКА >>>>>
            $msgMain = $koddocmail;
            $msgText1 = $koddocmail . " // " . $kodispol . " >>> " . $ispolStatus . " // " . $values['ispolStatus'];
            $msgText2 = "";
            $msgText3 = "";
            $msgText4 = "";
            $msgText5 = "";
            outputTempTest("updateIspolStatus", $msgMain, $msgText1, $msgText2, $msgText3, $msgText4, $msgText5);
            // <<<<< ОТЛАДКА
            //
            if ($__reqIspolStatus && $ispolStatus != $values['ispolStatus']) {
                $db->update(__MAIL_INCOMING_PREFIX . '_logCheckouts', array(
                    'timestamp'       => date("Y-m-d H:i:s"),
                    'ispolStatus'     => $values['ispolStatus']
                ), array(
                    'koddocmail'      => $koddocmail,
                    'userid'          => $userid,
                ));
            }
        }
    }
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### 
##### Отметка документа исполненным другими отвветственными (кроме текущего пользователя)
##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function updateispolStatusOther($db, $id, $values) {

    $userid = $_SESSION['id'];
    //
    $__reqKoddocmail = $db->sql("SELECT koddocmail, inbox_docContractor_kodzayvispol, inbox_docContractorID FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    if ($__reqKoddocmail) {
        $koddocmail = $__reqKoddocmail[0]['koddocmail'];
        $kodispol = $__reqKoddocmail[0]['inbox_docContractor_kodzayvispol'];
    }
    //
    if ($values['ispolStatusOtherOff'] == '1') {
        if ($koddocmail != "" &&  $kodispol != "") {
            $arrContractorID = explode(",", $__reqKoddocmail[0]['inbox_docContractorID']);
            foreach ($arrContractorID as $value) {
                //
                if ($value != $userid) {
                    $db->update(__MAIL_INCOMING_PREFIX . '_logCheckouts', array(
                        'timestamp'     => date("Y-m-d H:i:s"),
                        'ispolStatus'   => '0',
                        'comment'       => "ispolStatusOtherOff = 1",
                    ), array(
                        'koddocmail'    => $koddocmail,
                        'userid'        => $value,
                    ));
                }
            }
        }
    }
    //
    if ($values['ispolStatusOtherOn'] == '1') {
        if ($koddocmail != "" &&  $kodispol != "") {
            $arrContractorID = explode(",", $__reqKoddocmail[0]['inbox_docContractorID']);
            foreach ($arrContractorID as $value) {
                //
                if ($value != $userid) {
                    $db->update(__MAIL_INCOMING_PREFIX . '_logCheckouts', array(
                        'timestamp'     => date("Y-m-d H:i:s"),
                        'ispolStatus'   => '1',
                        'comment'       => "ispolStatusOtherOn = 1",
                    ), array(
                        'koddocmail'    => $koddocmail,
                        'userid'        => $value,
                    ));
                }
            }
        }
    }
    # ----- ----- ----- ----- ----- 
    # 
    // Формируем список ID пользователей исполнивших документ
    if ($__reqKoddocmail && $koddocmail != "") {
        $_checkoutUserIDs = "";
        $_checkoutUserDates = "";
        $_QRY_CheckoutIDs = $db->sql("SELECT userid, timestamp FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND ispolStatus='1'")->fetchAll();
        foreach ($_QRY_CheckoutIDs as $key => $value) {
            $_checkoutUserIDs .= $_QRY_CheckoutIDs[$key]['userid'] . ",";
            $_checkoutUserDates .= $_QRY_CheckoutIDs[$key]['timestamp'] . ",";
        }
        $_checkoutUserIDs = rtrim($_checkoutUserIDs, ",");
        $_checkoutUserDates = rtrim($_checkoutUserDates, ",");
        $db->update(__MAIL_INCOMING_TABLENAME, array(
            'inbox_controlIspolCheckoutID'      => $_checkoutUserIDs,
            'inbox_controlIspolCheckoutDates'   => $_checkoutUserDates,
        ), array(
            'id' => $id
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
    if ($_REQ_1 && $_REQ_1[0]['outbox_rowID_rel'] !== "" && $_REQ_1[0]['outbox_rowID_rel'] !== NULL) {
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
                'outbox_docID_rel'          => null,
                'outbox_UID_rel'            => null,
                'outbox_koddocmail_rel'     => null,
                'outbox_fileID_rel'         => null,
            ), array(
                'id' => $id
            ));
            $db->update(__MAIL_OUTGOING_TABLENAME, array(
                'inbox_docID_rel'           => null,
                'inbox_rowID_rel'           => null,
                'inbox_UID_rel'             => null,
                'inbox_koddocmail_rel'      => null,
                'inbox_fileID_rel'          => null,
            ), array(
                'id' => $outbox_rowID_rel
            ));
            if ($outbox_docTypeV < 2 || empty($outbox_rowID_relV)) {
                $db->update(__MAIL_OUTGOING_TABLENAME, array(
                    'outbox_docType'        => $docTYPEprev,
                    'outbox_docTypeSTR'     => $docTypeSTRprev,
                    'outbox_docType_lock'   => '0',
                ), array(
                    'id' => $outbox_rowID_rel
                ));
            }
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
#857
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
        $__zakazDATA = $db->sql("SELECT kodcontragent, nameshort, namefull, zakfio FROM sp_contragents WHERE kodcontragent=" . $zakazKOD)->fetchAll();
        if ('000000000000000' != $zakazKOD) {
            $zakaz = !empty($__zakazDATA[0]['nameshort']) ? $__zakazDATA[0]['nameshort'] : (!empty($__zakazDATA[0]['namefull']) ? $__zakazDATA[0]['namefull'] : "---");
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
    $inbox_docType_prev = $row[__MAIL_INCOMING_TABLENAME]['inbox_docType_prev'];
    $inbox_docID = $row[__MAIL_INCOMING_TABLENAME]['inbox_docID'];
    $inbox_UID = $row[__MAIL_INCOMING_TABLENAME]['inbox_UID'];
    $inbox_docFileID = $row[__MAIL_INCOMING_TABLENAME]['inbox_docFileID'];
    $outbox_rowID_rel = $row[__MAIL_INCOMING_TABLENAME]['outbox_rowID_rel'];
    #
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    # 12.04.2023
    # Статус исполнения документа
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    $ctl = $row[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolActive'];
    $_SQLReq_countChk = $db->sql("SELECT COUNT(*) as countChk FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$inbox_koddocmail}' AND ispolStatus = '1'")->fetchAll();

    $arrispol = explode(",", $row[__MAIL_INCOMING_TABLENAME]['inbox_docContractor_kodzayvispol']);
    $ispol_cnt = count($arrispol);
    $chkout_cnt = $_SQLReq_countChk[0]['countChk'];
    $CHECK_CNT = ($ispol_cnt == $chkout_cnt) ? 1 : 0;

    // $_SQLReq_userChk = $db->sql("SELECT ispolStatus FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$inbox_koddocmail}' AND userid='{$_sessionUserID}'")->fetchAll();
    // $chkoutUser_db = $_SQLReq_userChk[0]['ispolStatus'];
    // $chkoutUser_formVal = $values['ispolStatus'];
    // $CHECK_STATUS = ($chkoutUser_db == $chkoutUser_formVal) ? 1 : 0;


    $chkout_row = $row[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolCheckout'];
    $chkout = (($chkout_row == '1') & ($ispol_cnt == $chkout_cnt)) ? 1 : 0;
    $DL = date('Y-m-d', strtotime($row[__MAIL_INCOMING_TABLENAME]['inbox_docDateDeadline']));
    $datenow = date('Y-m-d', time());
    $useDL = $row[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolUseDeadline'];

    if ($ctl == '1') {
        if ($chkout == '1') {
            $checkStatus = 1;
        } else {
            if ($useDL && ($datenow >= $DL)) {
                $checkStatus = 3;
            } else {
                $checkStatus = 2;
            }
        }
    } else {
        $checkStatus = 0;
    }



    # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    #
    # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
    # СОЗДАНИЕ ЗАПИСИ
    #
    if ('CRT' == $action) {

        // PORTAL_SYSLOG('99921000', '0000001', $id, $__newKod, $__newDocID, null);

        $__newUID = date('Y') . "-" . $__newDocID;
        $db->update(__MAIL_INCOMING_TABLENAME, array(
            'koddocmail'                    => $__newKod,
            'koddocmailmain'                => $__newKod,
            'inbox_UID'                     => $__newUID,
            'inbox_docID'                   => $__newDocID,
            'inbox_docIDSTR'                => $__newDocID,
            'inbox_docTypeSTR'              => $docTypeSTR,
            'inbox_docRecipientID'          => $recipientID,
            'inbox_docRecipientSTR'         => $recipientSTR,
            'inbox_docContractorMULTI'      => $multiispol,
            'inbox_docContractorID'         => $contractorID,
            'inbox_docContractorDEPT'       => $contractorDEPTKOD,
            'inbox_docContractorSTR'        => $contractorSTR,
            'inbox_docContractorEMAIL'      => $contractorEMAIL,
            'inbox_docCreatedByID'          => $_SESSION['id'],
            'inbox_docCreatedWhen'          => date('Y-m-d H:i:s'),
            'inbox_docCreatedBySTR'         => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
            'inbox_docUpdatedByID'          => $_SESSION['id'],
            'inbox_docUpdatedBySTR'         => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
            'inbox_docUpdatedWhen'          => date('Y-m-d H:i:s'),
            'inbox_controlIspolStatus'      => $checkStatus,
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
            'inbox_controlIspolStatus' => $checkStatus,
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
                            'outbox_docType_prev'    => $_REQ_Outgoing[0]['outbox_docType'],
                            'outbox_docType_lock'    => '1',
                        ), array(
                            'id' => $outbox_rowID_rel
                        ));
                    }
                }
            } else {
                $db->update(__MAIL_INCOMING_TABLENAME, array(
                    'outbox_docID_rel'                => null,
                    'outbox_rowID_rel'                => null,
                    'outbox_UID_rel'                    => null,
                    'outbox_koddocmail_rel'        => null,
                    'outbox_fileID_rel'                => null
                ), array(
                    'id' => $id
                ));
                #
                #
                $db->update(__MAIL_OUTGOING_TABLENAME, array(
                    'inbox_docID_rel'                    => null,
                    'docmailpaper'                        => null,
                    'inbox_UID_rel'                        => null,
                    'inbox_koddocmail_rel'        => null,
                    'inbox_fileID_rel'                => null
                ), array(
                    'id' => $outbox_rowID_rel
                ));
            }
        } elseif ($inbox_docType != 3) {
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
            if ($docTYPEprev != NULL && $docTYPEprev != "") {
                $__docTypeDATAprev = $db->sql("SELECT * FROM mailbox_sp_doctypes_outgoing WHERE type_id = " . $docTYPEprev)->fetchAll();
                $docTypeSTRprev = !empty($__docTypeDATAprev[0]['type_name_short']) ? $__docTypeDATAprev[0]['type_name_short'] : "---";
                $db->update(__MAIL_OUTGOING_TABLENAME, array(
                    'outbox_docType'          => $docTYPEprev,
                    'outbox_docTypeSTR'       => $docTypeSTRprev,
                ), array(
                    'id' => $outbox_rowID_rel
                ));
            }
            #
            $db->update(__MAIL_OUTGOING_TABLENAME, array(
                'inbox_docID_rel'         => null,
                'inbox_rowID_rel'         => null,
                'inbox_UID_rel'           => null,
                'inbox_koddocmail_rel'    => null,
                'inbox_fileID_rel'        => null
            ), array(
                'id' => $outbox_rowID_rel
            ));
        }
    } else {
        $db->update(__MAIL_INCOMING_TABLENAME, array(
            'outbox_rowID_rel'      => null,
            'outbox_docID_rel'      => null,
            'outbox_UID_rel'        => null,
            'outbox_koddocmail_rel' => null,
            'outbox_fileID_rel'     => null,
        ), array(
            'id' => $id
        ));
    }
    # ----- ----- ----- ----- ----- 
    # 
    // Формируем список ID пользователей исполнивших документ
    $_checkoutUserIDs = "";
    $_checkoutUserDates = "";
    $_QRY_CheckoutIDs = $db->sql("SELECT userid, timestamp FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$inbox_koddocmail}' AND ispolStatus='1'")->fetchAll();
    foreach ($_QRY_CheckoutIDs as $key => $value) {
        $_checkoutUserIDs .= $_QRY_CheckoutIDs[$key]['userid'] . ",";
        $_checkoutUserDates .= $_QRY_CheckoutIDs[$key]['timestamp'] . ",";
    }
    $_checkoutUserIDs = rtrim($_checkoutUserIDs, ",");
    $_checkoutUserDates = rtrim($_checkoutUserDates, ",");
    $db->update(__MAIL_INCOMING_TABLENAME, array(
        'inbox_controlIspolCheckoutID'      => $_checkoutUserIDs,
        'inbox_controlIspolCheckoutDates'   => $_checkoutUserDates,
        'inbox_controlIspolCheckoutComment' => null,
    ), array(
        'id' => $id
    ));
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
#
# UPD 15.07.23
# Пишем комментарий исполнителя в общий чат с комментариями
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function updateLogComments($db, $action, $id, $values) {
    $reqDB = $db->sql("SELECT koddocmail, inbox_docComment FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $_timestamp = date("Y-m-d H:i:s");
    $_koddocmail = $reqDB[0]['koddocmail'];
    $_commentID = $_koddocmail . ".K." . time();
    $_userid = $_SESSION['id'];
    $_username = $_SESSION['lastname'] . " " . $_SESSION['firstname'];
    if ('CRT' == $action) {
        $contractorCheckoutComment = $values[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolCheckoutComment'];
        $docCommentForm = $values[__MAIL_INCOMING_TABLENAME]['inbox_docComment'];
        if (!empty($contractorCheckoutComment)) {
            $db->insert(__MAIL_INCOMING_PREFIX . '_logComments', array(
                'koddel'          => "",
                'timestamp'       => $_timestamp,
                'action'          => 'FORM',
                'koddocmail'      => $_koddocmail,
                'commentID'       => $_commentID,
                'userid'          => $_userid,
                'username'        => $_username,
                'prevcommentText' => null,
                'commentText'     => $contractorCheckoutComment,
                'commentAdd'      => 'Через форму документа добавлен комментарий исполнителя (к отметке)',
            ));
            $values[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolCheckoutComment'] = "";
        }
        if (!empty($docCommentForm)) {
            $db->insert(__MAIL_INCOMING_PREFIX . '_logComments', array(
                'koddel'          => "",
                'timestamp'       => $_timestamp,
                'action'          => 'FORM',
                'koddocmail'      => $_koddocmail,
                'commentID'       => $_commentID,
                'userid'          => $_userid,
                'username'        => $_username,
                'prevcommentText' => null,
                'commentText'     => $docCommentForm,
                'commentAdd'      => 'В форме документа обновлена дополнительная информация',
            ));
        }
    }
    if ('UPD' == $action) {
        $contractorCheckoutComment = $values[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolCheckoutComment'];
        $docCommentForm = $values[__MAIL_INCOMING_TABLENAME]['inbox_docComment'];
        if (!empty($contractorCheckoutComment)) {
            $db->insert(__MAIL_INCOMING_PREFIX . '_logComments', array(
                'koddel'          => "",
                'timestamp'       => $_timestamp,
                'action'          => 'FORM',
                'koddocmail'      => $_koddocmail,
                'commentID'       => $_commentID,
                'userid'          => $_userid,
                'username'        => $_username,
                'prevcommentText' => null,
                'commentText'     => $contractorCheckoutComment,
                'commentAdd'      => 'Через форму документа добавлен комментарий ответственного',
            ));
            $values[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolCheckoutComment'] = "";
        }
        if ($docCommentForm != $reqDB[0]['inbox_docComment']) {
            $db->insert(__MAIL_INCOMING_PREFIX . '_logComments', array(
                'koddel'          => "",
                'timestamp'       => $_timestamp,
                'action'          => 'FORM',
                'koddocmail'      => $_koddocmail,
                'commentID'       => $_commentID,
                'userid'          => $_userid,
                'username'        => $_username,
                'prevcommentText' => null,
                'commentText'     => $docCommentForm,
                'commentAdd'      => 'В форме документа обновлена дополнительная информация',
            ));
        }
    }
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
# UPD 19.07.23
# Обновляем количество комментариев к документу
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function updateCountComments($db, $id) {
    $reqDB = $db->sql("SELECT koddocmail FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $_koddocmail = $reqDB[0]['koddocmail'];
    $_reqDB1 = $db->sql("SELECT COUNT(*) as CommCounts FROM " . __MAIL_INCOMING_TABLENAME . "_logComments WHERE action IN ('COMM','FORM') AND koddocmail = '{$_koddocmail}' AND koddel NOT IN ('deleted', 'NULL')")->fetchAll();
    $counts  = $_reqDB1[0]['CommCounts'];
    $_reqDB2 = $db->sql("UPDATE " . __MAIL_INCOMING_TABLENAME . " SET cntComments = '{$counts}' WHERE koddocmail = '{$_koddocmail}'");
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
            // PORTAL_SYSLOG('99921000', '000000D', $row2delete, null, null, null);
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
    // PORTAL_SYSLOG('99921000', '000000F', $id, null, null, null);
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function updateSyslogAfterremove($db, $id) {
    // PORTAL_SYSLOG('99921000', '0000003', $id, null, null, null);
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
    // $_REQ_UpdateKoddel_5 = $db->sql("UPDATE " . __MAIL_INCOMING_PREFIX . "_logMailing SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
    // Маркируем таблицу прикрепленных файлов
    $_REQ_UpdateKoddel_Files = $db->sql("UPDATE " . __MAIL_INCOMING_FILES_TABLENAME . " SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
    // Копируем запись в таблицу для удаленных записей
    $_REQ_InsertDeleted = $db->sql("INSERT INTO " . __MAIL_INCOMING_PREFIX . "_deletedRecords SELECT * FROM " . __MAIL_INCOMING_TABLENAME . " WHERE koddocmail='" . $_koddocmail . "'");
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
function sendEmailToIspol_YouAreIspolnitel($db, $action, $id, $values, $row) {

    $query1 = $db->sql("SELECT koddocmail, inbox_docID, inbox_docContractorID, inbox_docContractorMULTI, inbox_docContractor_kodzayvispol, inbox_docContractorSTR, inbox_docDateDeadline, inbox_docSender, inbox_docSenderName, inbox_docSourceID, inbox_docSourceDate, inbox_docAbout, inbox_docTypeSTR, inbox_docFileID, inbox_docFileIDadd, toSendEmail, inbox_controlIspolActive, inbox_controlIspolStatus FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $__koddocmail  = $query1[0]['koddocmail'];
    $__docID       = $query1[0]['inbox_docID'];
    $__docFileID   = $query1[0]['inbox_docFileID'];
    $__docFileIDadd = $query1[0]['inbox_docFileIDadd'];
    $__contrID     = $query1[0]['inbox_docContractorID'];
    $__contrKOD    = $query1[0]['inbox_docContractor_kodzayvispol'];
    $__contrName   = $query1[0]['inbox_docContractorSTR'];
    $__deadline    = date_create($query1[0]['inbox_docDateDeadline']);
    $__ispolMULTI  = $query1[0]['inbox_docContractorMULTI'];
    $__senderORG   = $query1[0]['inbox_docSender'];
    $__senderNAME  = $query1[0]['inbox_docSenderName'];
    $__sourceID    = $query1[0]['inbox_docSourceID'];
    $__sourceDate  = date_create($query1[0]['inbox_docSourceDate']);
    $__docABOUT    = $query1[0]['inbox_docAbout'];
    $__docTYPE     = $query1[0]['inbox_docTypeSTR'];
    $__toSendEmail = $query1[0]['toSendEmail'];
    $__controlActive = $query1[0]['inbox_controlIspolActive'];
    $__controlStatus = $query1[0]['inbox_controlIspolStatus'];


    $__fileAddLinks = "---";

    if ($query1 && "" != $__contrKOD && null != $__contrKOD) {

        $_ispolList = str_replace(",", ", ", $__contrName);
        $arrispol = explode(",", $__contrKOD);
        $arrFileIDAdd = explode(",", $__docFileIDadd);
        foreach ($arrispol as $value) {
            $__contractorDATA1 = $db->sql("SELECT id, emailaddress, namezayvfio FROM mailbox_sp_users WHERE kodispol=" . $value)->fetchAll();
            $__contractorDATA2 = $db->sql("SELECT firstname, middlename, lastname FROM users WHERE id=" . $__contractorDATA1[0]['id'])->fetchAll();
            $__fileName        = "";
            $__fileURL         = "";
            $__fileLink        = "---";

            if ("" != $__docFileID) {
                $__contractorDATA3 = $db->sql("SELECT file_originalname, file_url FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE id=" . $__docFileID)->fetchAll();
                $__fileName        = (!empty($__contractorDATA3) && "" != $__contractorDATA3[0]['file_originalname']) ? $__contractorDATA3[0]['file_originalname'] : "---";
                $__fileURL         = !empty($__contractorDATA3) ? $__contractorDATA3[0]['file_url'] : "";
                $__fileLink        = ("" != $__fileURL) ? '<a href="' . $__fileURL . '" title="" style="text-decoration:none !important">' . $__fileName . '</a>' : "---";
            }
            if ("" != $__docFileIDadd) {
                $__fileAddLinks = "";
                foreach ($arrFileIDAdd as &$value) {
                    if ($value != "") {
                        $__QRY_FileIDadd = $db->sql("SELECT file_originalname, file_url FROM mailbox_incoming_files WHERE id=" . $value)->fetchAll();
                        $__fileAddName = (!empty($__QRY_FileIDadd) && "" != $__QRY_FileIDadd[0]['file_originalname']) ? $__QRY_FileIDadd[0]['file_originalname'] : "---";
                        $__fileAddURL = !empty($__QRY_FileIDadd) ? $__QRY_FileIDadd[0]['file_url'] : "";
                        $__fileAddLink = ("" != $__fileAddURL) ? '<a href="' . $__fileAddURL . '" title="" style="text-decoration:none !important">' . $__fileAddName . '</a>' : "---";
                        $__fileAddLinks .= $__fileAddLink . ", ";
                    }
                }
                $__fileAddLinks = rtrim($__fileAddLinks, ', ');
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
                    $mail->setLanguage('ru', __DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/libs/PHPMailer/language/');
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
                    $_msgTitle    = "Входящий документ";
                    $_msgText     = '<span style="font-size:28px">' . $__IO . ', </span><br>вы назначены ответственным по входящему документу №' . $__docID;
                    $_docDeadline = validateDate($query1[0]['inbox_docDateDeadline'], "Y-m-d") ? date('d.m.Y', strtotime($query1[0]['inbox_docDateDeadline'])) : "---";
                    $_sourceDate = validateDate($query1[0]['inbox_docSourceDate'], "Y-m-d") ? date('d.m.Y', strtotime($query1[0]['inbox_docSourceDate'])) : "---";

                    $message .= makeMailMsg_NotifyIspol_Type2($__koddocmail, $_msgTitle, $__docID, $__IO, $__fileLink, $__fileAddLinks, $__docTYPE, $__senderORG, $__docABOUT, $__sourceID, $_sourceDate, $__controlActive, $__controlStatus, $_docDeadline, $_ispolList);
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
                    $filename = __DIR_ROOT . __SERVICENAME_MAILNEW . "/logs/PHPMailer_errors.log";
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
                            // $db->insert('mailbox_test_EmailSending', array(
                            //     'email_when'      => $timestamp,
                            //     'email_header'    => "",
                            //     'email_name'      => $__FIO,
                            //     'email_address'   => $email_to,
                            //     'email_subject'   => $subject,
                            //     'email_text'      => $message,
                            //     'email_whoSendID' => $_SESSION['id'],
                            //     'email_docID'     => $__docID,
                            //     'email_docRow'    => $id,
                            // ));
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
function sendEmailToIspol_FullCheckout($db, $action, $id, $values, $row) {

    $_sqlReq_1 = $db->sql("SELECT * FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();

    $__koddocmail  = $_sqlReq_1[0]['koddocmail'];
    $__docID       = $_sqlReq_1[0]['inbox_docID'];
    $__docFileID   = $_sqlReq_1[0]['inbox_docFileID'];
    $__docFileIDadd = $_sqlReq_1[0]['inbox_docFileIDadd'];
    $__contrID     = $_sqlReq_1[0]['inbox_docContractorID'];
    $__contrKOD    = $_sqlReq_1[0]['inbox_docContractor_kodzayvispol'];
    $__contrName   = $_sqlReq_1[0]['inbox_docContractorSTR'];
    $__deadline    = date_create($row[__MAIL_INCOMING_TABLENAME]['inbox_docDateDeadline']);
    $__ispolMULTI  = $_sqlReq_1[0]['inbox_docContractorMULTI'];
    $__senderORG   = $_sqlReq_1[0]['inbox_docSender'];
    $__senderNAME  = $_sqlReq_1[0]['inbox_docSenderName'];
    $__sourceID    = $_sqlReq_1[0]['inbox_docSourceID'];
    $__sourceDate  = date_create($row[__MAIL_INCOMING_TABLENAME]['inbox_docSourceDate']);
    $__docABOUT    = $_sqlReq_1[0]['inbox_docAbout'];
    $__docTYPE     = $_sqlReq_1[0]['inbox_docTypeSTR'];
    $__toSendEmail = $_sqlReq_1[0]['toSendEmail'];
    $__controlActive = $_sqlReq_1[0]['inbox_controlIspolActive'];
    $__controlCheckout = $_sqlReq_1[0]['inbox_controlIspolCheckout'];
    $__controlStatus = $_sqlReq_1[0]['inbox_controlIspolStatus'];


    $__fileAddLinks = "---";

    if ($__controlCheckout == "1" && ($_sqlReq_1[0]['inbox_controlIspolMailNotifyCheckout'] == "1" or $values[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolMailNotifyCheckout'] == "1")) {

        $_ispolList = str_replace(",", ", ", $__contrName);
        $arrispol = explode(",", $__contrKOD);
        $arrFileIDAdd = explode(",", $__docFileIDadd);
        foreach ($arrispol as $value) {
            $__contractorDATA1 = $db->sql("SELECT id, emailaddress, namezayvfio FROM mailbox_sp_users WHERE kodispol=" . $value)->fetchAll();
            $__contractorDATA2 = $db->sql("SELECT firstname, middlename, lastname FROM users WHERE id=" . $__contractorDATA1[0]['id'])->fetchAll();
            $__fileName        = "";
            $__fileURL         = "";
            $__fileLink        = "---";

            if ("" != $__docFileID) {
                $__contractorDATA3 = $db->sql("SELECT file_originalname, file_url FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE id=" . $__docFileID)->fetchAll();
                $__fileName        = (!empty($__contractorDATA3) && "" != $__contractorDATA3[0]['file_originalname']) ? $__contractorDATA3[0]['file_originalname'] : "---";
                $__fileURL         = !empty($__contractorDATA3) ? $__contractorDATA3[0]['file_url'] : "";
                $__fileLink        = ("" != $__fileURL) ? '<a href="' . $__fileURL . '" title="" style="text-decoration:none !important">' . $__fileName . '</a>' : "---";
            }
            if ("" != $__docFileIDadd) {
                $__fileAddLinks = "";
                foreach ($arrFileIDAdd as &$value) {
                    if ($value != "") {
                        $__QRY_FileIDadd = $db->sql("SELECT file_originalname, file_url FROM mailbox_incoming_files WHERE id=" . $value)->fetchAll();
                        $__fileAddName = (!empty($__QRY_FileIDadd) && "" != $__QRY_FileIDadd[0]['file_originalname']) ? $__QRY_FileIDadd[0]['file_originalname'] : "---";
                        $__fileAddURL = !empty($__QRY_FileIDadd) ? $__QRY_FileIDadd[0]['file_url'] : "";
                        $__fileAddLink = ("" != $__fileAddURL) ? '<a href="' . $__fileAddURL . '" title="" style="text-decoration:none !important">' . $__fileAddName . '</a>' : "---";
                        $__fileAddLinks .= $__fileAddLink . ", ";
                    }
                }
                $__fileAddLinks = rtrim($__fileAddLinks, ', ');
            }
            if ($__contractorDATA1 && $__contractorDATA2) {
                $__email = $__contractorDATA1[0]['emailaddress'];
                $__IO    = !empty($__contractorDATA2) ? $__contractorDATA2[0]['firstname'] . " " . $__contractorDATA2[0]['middlename'] : '';
                $__FIO   = $__contractorDATA1[0]['namezayvfio'];
                //
                //
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
                $mail = new PHPMailer;
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
                $mail->setLanguage('ru', __DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/libs/PHPMailer/language/');
                $mail->CharSet = "utf-8";
                #
                # From
                $from_name  = "АТГС.Портал / Корпоративные сервисы";
                $from_email = "portal@atgs.ru";
                $from_name  = "=?utf-8?B?" . base64_encode($from_name) . "?=";
                $mail->setFrom($from_email, $from_name);
                # Mail address
                // $email_to = $__email;
                $email_to = 'chugunov@atgs.ru';
                $email_admin = 'chugunov@atgs.ru';
                #
                # Тема сообения
                $subjectTxt = "Почта АТГС [Входящие] : Документ №" . $__docID . " исполнен";
                $subject    = "=?utf-8?B?" . base64_encode($subjectTxt) . "?=";
                #
                $mail->addAddress($email_to);
                $mail->addCC($email_admin);
                $mail->addReplyTo('notreply@atgs.ru', 'Do not reply');
                #
                # Message body
                $_msgTitle    = "Входящий документ";
                $_msgText     = '<span style="font-size:28px">' . $__IO . ', </span><br>документ №' . $__docID . " исполнен";
                $_docDeadline = validateDate($_sqlReq_1[0]['inbox_docDateDeadline'], "Y-m-d") ? date('d.m.Y', strtotime($_sqlReq_1[0]['inbox_docDateDeadline'])) : "---";
                $_sourceDate = validateDate($_sqlReq_1[0]['inbox_docSourceDate'], "Y-m-d") ? date('d.m.Y', strtotime($_sqlReq_1[0]['inbox_docSourceDate'])) : "---";

                $message .= makeMailMsg_Notify_FullCheckout('incoming', $__koddocmail, $_msgTitle, $__docID, $__IO, $__fileLink, $__fileAddLinks, $__docTYPE, $__senderORG, $__docABOUT, $__sourceID, $_sourceDate, $__controlActive, $__controlStatus, $_docDeadline, $_ispolList);
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
                $filename = __DIR_ROOT . __SERVICENAME_MAILNEW . "/logs/PHPMailer_errors.log";
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
                        $_SQLReq_NotifyCheckout_Update = $db->sql("UPDATE " . __MAIL_INCOMING_TABLENAME . " SET inbox_controlIspolMailNotifyCheckout = 0 WHERE koddocmail = '{$__koddocmail}'");
                    }
                } else {
                    echo "<span style='color:red; text-align:center'><i>Лог-файл недоступен для записи.</i></span>";
                }
            }
        }
    }
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### 
# ФУНКЦИЯ
# Назначение: обновление общего статуса исполнения документа (".__MAIL_INCOMING_PREFIX.".inbox_controlIspolCheckout)
##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function updateIspolCheckout($db, $id, $values, $row) {
    // Берем из сессии ID пользователя
    $userid = $_SESSION['id'];
    // Текущая временная метка
    $datenow = date("Y-m-d H:i:s");
    // Разбираем в массив строку с исполнителями напрямую из БД ($row) и подсчитываем количество
    $arrispol = explode(",", $row[__MAIL_INCOMING_TABLENAME]['inbox_docContractor_kodzayvispol']);
    $cntIspol = count($arrispol);
    //
    $koddocmail = $row[__MAIL_INCOMING_TABLENAME]['koddocmail'];
    // SQL: (reqSQL_1) СЧитаем количество отметок об исполнении по текущему документу (koddocmail)
    $_reqSQL_1 = $db->sql("SELECT COUNT(ispolStatus) as ispolCounts FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND ispolStatus='1'")->fetchAll();
    $cntCheckouts = $_reqSQL_1[0]['ispolCounts'];
    // 
    // ОТЛАДКА >>>>>
    $msgMain = $koddocmail;
    $msgText1 = "cntIspol = " . $cntIspol . " // cntCheckouts = " . $cntCheckouts;
    $msgText2 = "";
    $msgText3 = "";
    $msgText4 = "";
    $msgText5 = "";
    outputTempTest("updateIspolCheckout", $msgMain, $msgText1, $msgText2, $msgText3, $msgText4, $msgText5);
    // <<<<< ОТЛАДКА
    //

    if (($cntIspol == $cntCheckouts) && ($row[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolCheckout'] != "1")) {
        $_reqDB3 = $db->sql("UPDATE " . __MAIL_INCOMING_TABLENAME . " SET inbox_controlIspolCheckout='1', inbox_controlIspolCheckoutWhen='{$datenow}' WHERE id='{$id}'");

        # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
        # 12.04.2023
        # Статус исполнения документа
        # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
        $ctl = $row[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolActive'];
        $chkout = 1;
        $DL = date('Y-m-d', strtotime($row[__MAIL_INCOMING_TABLENAME]['inbox_docDateDeadline']));
        $datenow = date('Y-m-d', time());
        $useDL = $row[__MAIL_INCOMING_TABLENAME]['inbox_controlIspolUseDeadline'];

        if ($ctl == '1') {
            if ($chkout == '1') {
                $checkStatus = 1;
            } else {
                if ($useDL && ($datenow > $DL)) {
                    $checkStatus = 3;
                } else {
                    $checkStatus = 2;
                }
            }
        } else {
            $checkStatus = 0;
        }
        $_sqlReq_1 = $db->sql("UPDATE " . __MAIL_INCOMING_TABLENAME . " SET inbox_controlIspolStatus='{$checkStatus}' WHERE id='{$id}'");
        # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
        # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
        sendEmailToIspol_FullCheckout($db, 'Email', $id, $values, $row);
    }
    if ($cntIspol != $cntCheckouts) {
        $_reqDB3 = $db->sql("UPDATE " . __MAIL_INCOMING_TABLENAME . " SET inbox_controlIspolCheckout='0', inbox_controlIspolCheckoutWhen=NULL WHERE id='{$id}'");
    }
}




function stripWhitespaces($string) {
    $old_string = $string;
    $string = strip_tags($string);
    $string = preg_replace('/([^\pL\pN\pP\pS\pZ])|([\xC2\xA0])/u', ' ', $string);
    $string = str_replace('  ', ' ', $string);
    $string = trim($string);

    if ($string === $old_string) {
        return $string;
    } else {
        return stripWhitespaces($string);
    }
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function newUserMessage($db, $action, $id, $values) {
    $_QRY = $db->sql("SELECT * FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    if ($_SESSION['inbox_checkChanges_is'] == "changed") {
        if ((!empty($_QRY[0]['inbox_docContractorID']) && $_QRY[0]['inbox_docContractorID'] != "1") && (!empty($_QRY[0]['inbox_docContractor_kodzayvispol']) && $_QRY[0]['inbox_docContractor_kodzayvispol'] != "000000000000000")) {
            $msg_maintext = '';
            $msg_subtext1 = '';
            $msg_subtext2 = '';
            $msg_subtext3 = '';
            $msg_specialtext = '';
            $comment = '';
            $servicename = "почта";
            $parent_id = $_QRY[0]['koddocmail'];
            if ($_QRY[0]['inbox_docContractorMULTI'] == "1") {
                $for_singleuser = "";
                $for_groupuser = trim($_QRY[0]['inbox_docContractorID']);
                // $for_groupuser .= ",1011"; // Пока для проверки
            } else {
                $for_singleuser = trim($_QRY[0]['inbox_docContractorID']);
                // $for_singleuser .= ",1011"; // Пока для проверки
                $for_groupuser = "";
            }

            $msg_title = ($action == "CRT") ? "Новый входящий документ" : "Изменения по входящему документу";
            $msg_maintext .= trim($_QRY[0]["inbox_docAbout"]);
            $msg_subtext1 .= 'Документ : ' . trim('№ 1-2/' . $_QRY[0]["inbox_docID"] . ' от ' . date("d.m.Y H:i", strtotime($_QRY[0]["inbox_docDate"])));
            $msg_subtext2 .= 'Контрагент : ' . trim($_QRY[0]["inbox_docSender"]);
            $msg_subtext3 .= 'Ответственные : ' . trim(str_replace(',', ' , ', $_QRY[0]["inbox_docContractorSTR"]));
            // 
            $msg_specialtext .= '';
            //
            $msg_link1 = trim('http://' . $_SERVER["HTTP_HOST"] . '/mailnew/index.php?type=in&mode=profile&uid=' . $parent_id);
            $msg_link2 = '';
            //
            $query = mysqlQuery("SELECT MAX(msg_id) as lastKod FROM portal_push_messages ORDER BY id DESC");
            $row = mysqli_fetch_assoc($query);
            $newKod = $row['lastKod'];
            $newKod++;

            $msg_id_new = $newKod;
            $msg_title = ($action == "CRT") ? "Новый входящий документ" : "Изменения по входящему документу";
            $msg_type = ($action == "CRT") ? "info" : "warning";
            $msg_active = "1";
            $msg_status = "0";
            $comment .= "CheckChanges is " . $_SESSION['inbox_checkChanges_is'] . "\r\n";
            $comment .= "Data in Form" . "\r\n";
            $comment .= $_SESSION['inbox_checkChanges_dataForm'];
            $comment .= "---\r\n";
            $comment .= "Data in DB" . "\r\n";
            $comment .= $_SESSION['inbox_checkChanges_dataDB'];
            $_QRY_INS = $db->sql("INSERT INTO portal_push_messages (msg_timestamp, msg_id, parent_id, servicename, msg_type, msg_active, msg_status, msg_title, msg_maintext, msg_subtext1, msg_subtext2, msg_subtext3, msg_specialtext, msg_link1, msg_link2, for_singleuser, for_groupuser, comment) VALUES (NOW(), '$msg_id_new', '$parent_id', '$servicename', '$msg_type', '$msg_active', '$msg_status', '$msg_title', '$msg_maintext', '$msg_subtext1', '$msg_subtext2', '$msg_subtext3', '$msg_specialtext', '$msg_link1', '$msg_link2', '$for_singleuser', '$for_groupuser', '$comment')");
        }
    }
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function dateDiff($date_earlier, $date_later, $output_format) {
    // $date_later - поздняя дата (ГГГГ-ММ-ДД)
    // $date_earlier - ранняя дата (ГГГГ-ММ-ДД)
    // $output_format - в чем выводить разницу (years, month, days, hours, mins, secs)

    // Declare and define two dates
    $date1 = strtotime($date_earlier . " 10:00:00");
    $date2 = strtotime($date_later . " 10:00:00");

    // Formulate the Difference between two dates
    $diff = abs($date2 - $date1);

    // To get the year divide the resultant date into
    // total seconds in a year (365*60*60*24)
    $years = floor($diff / (365 * 60 * 60 * 24));

    // To get the month, subtract it with years and
    // divide the resultant date into
    // total seconds in a month (30*60*60*24)
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));

    // To get the day, subtract it with years and
    // months and divide the resultant date into
    // total seconds in a days (60*60*24)
    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

    // To get the hour, subtract it with years,
    // months & seconds and divide the resultant
    // date into total seconds in a hours (60*60)
    $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));

    // To get the minutes, subtract it with years,
    // months, seconds and hours and divide the
    // resultant date into total seconds i.e. 60
    $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);

    // To get the minutes, subtract it with years,
    // months, seconds, hours and minutes
    $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));

    switch ($output_format) {
        case 'years':
            $output = $years;
            break;
        case 'months':
            $output = $months;
            break;
        case 'days':
            $output = $days;
            break;
        case 'hours':
            $output = $hours;
            break;
        case 'mins':
            $output = $minutes;
            break;
        case 'secs':
            $output = $seconds;
            break;
        default:
            $output = $seconds;
    }
    return $output;
}


#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function statsMailIncoming($db, $action, $year) {
    $_QRY_docsTotal = $db->sql("SELECT COUNT(*) AS docsTotal FROM mailbox_incoming WHERE YEAR(inbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $docsTotal = $_QRY_docsTotal[0]['docsTotal'];

    $_QRY_docsNoispol = $db->sql("SELECT COUNT(*) AS docsNoispol FROM mailbox_incoming WHERE (inbox_docContractorID='1' OR inbox_docContractorID IS NULL OR inbox_docContractorID='')  AND YEAR(inbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $docsNoispol = $_QRY_docsNoispol[0]['docsNoispol'];

    $_QRY_docsNoattach = $db->sql("SELECT COUNT(*) AS docsNoattach FROM mailbox_incoming WHERE (inbox_docFileID='' OR inbox_docFileID IS NULL) AND (inbox_docFileIDadd='' OR inbox_docFileIDadd IS NULL) AND YEAR(inbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $docsNoattach = $_QRY_docsNoattach[0]['docsNoattach'];

    // $_QRY_ctrlOn = $db->sql("SELECT COUNT(*) AS ctrlOn FROM mailbox_incoming WHERE inbox_controlIspolActive='1' AND inbox_controlIspolStatus NOT IN (0,1) AND YEAR(inbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $_QRY_ctrlOn = $db->sql("SELECT COUNT(*) AS ctrlOn FROM mailbox_incoming WHERE inbox_controlIspolActive='1' AND YEAR(inbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $ctrlOn = $_QRY_ctrlOn[0]['ctrlOn'];

    $_QRY_ctrlNotexec = $db->sql("SELECT COUNT(*) AS ctrlNotexec FROM mailbox_incoming WHERE inbox_controlIspolActive='1' AND inbox_controlIspolStatus IN (2,3) AND inbox_controlIspolCheckout!='1' AND YEAR(inbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $ctrlNotexec = $_QRY_ctrlNotexec[0]['ctrlNotexec'];

    $_QRY_ctrlDLon = $db->sql("SELECT COUNT(*) AS ctrlDLon FROM mailbox_incoming WHERE inbox_controlIspolActive='1' AND inbox_controlIspolUseDeadline='1' AND TIMESTAMPDIFF(HOUR, NOW(), inbox_docDateDeadline) >= 72 AND inbox_controlIspolStatus IN (2,3) AND inbox_controlIspolCheckout!='1' AND YEAR(inbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $ctrlDLon = $_QRY_ctrlDLon[0]['ctrlDLon'];

    $_QRY_ctrlDL3days = $db->sql("SELECT COUNT(*) AS ctrlDL3days FROM mailbox_incoming WHERE inbox_controlIspolActive='1' AND inbox_controlIspolUseDeadline='1' AND TIMESTAMPDIFF (HOUR, NOW(), inbox_docDateDeadline) >= 24 AND TIMESTAMPDIFF (HOUR, NOW(), inbox_docDateDeadline) < 72 AND inbox_controlIspolStatus IN (2,3) AND inbox_controlIspolCheckout!='1' AND YEAR(inbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $ctrlDL3days = $_QRY_ctrlDL3days[0]['ctrlDL3days'];

    $_QRY_ctrlDL1day = $db->sql("SELECT COUNT(*) AS ctrlDL1day FROM mailbox_incoming WHERE inbox_controlIspolActive='1' AND inbox_controlIspolUseDeadline='1' AND TIMESTAMPDIFF(HOUR, NOW(), inbox_docDateDeadline) < 24 AND TIMESTAMPDIFF(HOUR, NOW(), inbox_docDateDeadline) >= 0 AND inbox_controlIspolStatus IN (2,3) AND inbox_controlIspolCheckout!='1' AND YEAR(inbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $ctrlDL1day = $_QRY_ctrlDL1day[0]['ctrlDL1day'];

    $_QRY_ctrlDLexpired = $db->sql("SELECT COUNT(*) AS ctrlDLexpired FROM mailbox_incoming WHERE inbox_controlIspolActive='1' AND inbox_controlIspolUseDeadline='1' AND TIMESTAMPDIFF (HOUR, NOW(), inbox_docDateDeadline) < 0 AND inbox_controlIspolStatus IN (2,3) AND inbox_controlIspolCheckout!='1' AND YEAR(inbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $ctrlDLexpired = $_QRY_ctrlDLexpired[0]['ctrlDLexpired'];

    $_QRY_UpdateStats = $db->sql("UPDATE mailbox_incoming_stats SET docs_total='{$docsTotal}', docs_noispol='{$docsNoispol}', docs_noattach='{$docsNoattach}', control_on='{$ctrlOn}', control_notexec='{$ctrlNotexec}', control_DLon='{$ctrlDLon}', control_DL3days='{$ctrlDL3days}', control_DL1day='{$ctrlDL1day}', control_DLexpired='{$ctrlDLexpired}' WHERE statYear='{$year}'");
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function syncRelFields($db, $action, $id, $values, $row) {
    if ('UPD' == $action || 'CRT' == $action) {
        $_koddocmail = $row[__MAIL_INCOMING_TABLENAME]['koddocmail'];
        $_incomingLinkRowIDsArray = explode(',', $row[__MAIL_INCOMING_TABLENAME]['inbox_rowIDs_links']);
        $_outgoingLinkRowIDsArray = explode(',', $row[__MAIL_INCOMING_TABLENAME]['outbox_rowIDs_links']);
        // Синхронизируем дополнительные связи с входящими документами Почты
        if (!empty($_incomingLinkRowIDsArray)) {
            foreach ($_incomingLinkRowIDsArray as $value) {
                $reqLinkedIncoming = $db->sql("SELECT inbox_rowIDs_links FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id='{$value}'")->fetchAll();
                if ($reqLinkedIncoming) {
                    $linkIncoming = $reqLinkedIncoming[0]['inbox_rowIDs_links'];
                    if (!empty($linkIncoming)) {
                        if (!strpos($linkIncoming, $id)) {
                            $linkIncoming .= "," . $id;
                            // Записываем в БД в таблицу ".__MAIL_OUTGOING_PREFIX.", кто поставил галочку "Отправка на email" в окне редактирования и когда
                            $db->update(__MAIL_INCOMING_TABLENAME, array('inbox_rowIDs_links' => $linkIncoming), array('id' => $value));
                        }
                    } else {
                        $linkIncoming .= $id;
                    }
                }
            }
        }
        // Синхронизируем дополнительные связи с исходящими документами Почты
        if (!empty($_outgoingLinkRowIDsArray)) {
            foreach ($_outgoingLinkRowIDsArray as $value) {
                $reqLinkedOutgoing = $db->sql("SELECT inbox_rowIDs_links FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id='{$value}'")->fetchAll();
                if ($reqLinkedOutgoing) {
                    $linkOutgoing = $reqLinkedOutgoing[0]['inbox_rowIDs_links'];
                    if (!empty($linkOutgoing)) {
                        if (!strpos($linkOutgoing, $id)) {
                            $linkOutgoing .= "," . $id;
                            // Записываем в БД в таблицу ".__MAIL_OUTGOING_PREFIX.", кто поставил галочку "Отправка на email" в окне редактирования и когда
                            $db->update(__MAIL_OUTGOING_TABLENAME, array('inbox_rowIDs_links' => $linkOutgoing), array('id' => $value));
                        }
                    } else {
                        $linkOutgoing .= $id;
                    }
                }
            }
        }
    }
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
######
###### UPD 25.07.23
###### Отмечаем (при выбрпанном соответствующем чекбоксе) 
###### выполненным документ в противоположной почте, на которую редактируемый документ является ответным
######
######
function updateRelCheckout($db, $action, $id, $values, $row) {
    if ($values['set_outbox_fullCheckout']) {
        if ($action == "CRT" || $action == "UPD") {
            $relRowid = $row[__MAIL_INCOMING_TABLENAME]['outbox_rowID_rel'];
            $req1 = $db->sql("SELECT koddocmail FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id='{$relRowid}'")->fetchAll();
            if (!empty($req1)) {
                $db->update(
                    __MAIL_OUTGOING_TABLENAME . "_logCheckouts",
                    array(
                        'ispolStatus' => "1",
                    ),
                    array(
                        'koddocmail' => $req1[0]['koddocmail'],
                    )
                );
                //
                $db->update(
                    __MAIL_OUTGOING_TABLENAME,
                    array(
                        'outbox_controlIspolStatus' => "1",
                        'outbox_controlIspolCheckout' => "1",
                        'outbox_controlIspolCheckoutWhen' => date("Y-m-d H:i:s"),
                    ),
                    array(
                        'koddocmail' => $req1[0]['koddocmail'],
                    )
                );
            }
        }
    }
}
######
######
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
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
                    ->label(array('id', 'koddel', 'docnumber', 'docnameshot'))
                    ->render(function ($row) {
                        $tmp = '3-4/' . $row['docnumber'] . ' / ' . $row['docnameshot'];
                        $output = (mb_strlen($tmp, 'UTF-8') > 160) ? mb_substr($tmp, 0, 120, 'UTF-8') . " ..." : $tmp;
                        return $output;
                    })
                    ->where(function ($q) {
                        $q->where('koddel', '99', '!=');
                    })
                    ->order('docnumber DESC')
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
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.sp_rowIDs_links')
            ->setFormatter(Format::ifEmpty(NULL)),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.sp_docIDs_links')
            ->options(
                Options::inst()
                    ->table('sp_contragents')
                    ->value('id')
                    ->label(array('id', 'koddel', 'namefull', 'nameshort'))
                    ->render(function ($row) {
                        $tmp = $row['namefull'];
                        $output = (mb_strlen($tmp, 'UTF-8') > 160) ? mb_substr($tmp, 0, 120, 'UTF-8') . " ..." : $tmp;
                        return $output;
                    })
                    ->where(function ($q) {
                        $q->where('koddel', '99', '!=');
                    })
                    ->order('nameshort ASC')
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
                    ->label(array('id', 'outbox_docID', 'outbox_docRecipient', 'outbox_docDate', 'outbox_docAbout', 'outbox_docSourceID'))
                    ->render(function ($row) {
                        $date    = date_create($row['outbox_docDate']);
                        $docDate = date_format($date, "d.m.Y");
                        $sourceID = !empty($row['outbox_docSourceID']) ? $row['outbox_docSourceID'] : "---";
                        return $docDate . ' / № (АТГС) ' . $row['outbox_docID'] . ' / № (Орг) ' . $sourceID . ' / ' . $row['outbox_docRecipient'] . ' / ' . $row['outbox_docAbout'];
                    })
                    ->where(function ($q) use ($__startDate, $__endDate) {
                        $q->where('outbox_docDate', '( SELECT outbox_docDate FROM ' . __MAIL_OUTGOING_TABLENAME . ' WHERE outbox_docDate >= ' . $__startDate . ' AND outbox_docDate <= ' . $__endDate . ' )', 'IN', false);
                    })
                    ->order('outbox_docDate DESC')
            )
            ->validator(Validate::dbValues())
            ->setFormatter(Format::ifEmpty(NULL)),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_docID_rel'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_koddocmail_rel'),
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
                    ->label(array('kodcontragent', 'namefull', 'nameshort', 'zakfio'))
                    ->render(function ($row) {
                        $name = "";
                        if ($row['namefull'] != "") {
                            $name = $row['namefull'] . " (полное название)";
                        } else {
                            if ($row['nameshort'] != "") {
                                $name = $row['nameshort'] . " (краткое название)";
                            } else {
                                $name = "---";
                            }
                        }
                        return $name;
                    })
                    ->where(function ($q) {
                        $q->where('koddel', '99', '!=');
                        $q->where('useinmail', '1', '=');
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

                        $__url = str_replace($_SERVER['DOCUMENT_ROOT'], 'http://' . $_SERVER['HTTP_HOST'], $varFileArray['syspath'] . $__name);

                        move_uploaded_file($__nameTmp, $varFileArray['docpath'] . "{$__name}");
                        symlink($varFileArray['docpath'] . "{$__name}", $varFileArray['syspath'] . $__name);

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
                                'file_syspath'      => $varFileArray['syspath'] . $__name,
                                'file_webpath'      => $varFileArray['webpath'] . $__name,
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
                    ->label(array('status_ispol', 'kodispol', 'namezayvtel', 'namezayvfio'))
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
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolCheckoutComment'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolCheckoutID'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolCheckoutDates'),
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
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailNotifyDL'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailNotifyCheckout'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailSpecialNotifyCheckout'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailSpecialNotifyDL'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolUseDeadline'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolStatusDeadline'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.cntComments'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.docmailext'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_FILES_TABLENAME . '.id'),
        Field::inst(__MAIL_INCOMING_FILES_TABLENAME . '.file_webpath'),
        Field::inst(__MAIL_INCOMING_FILES_TABLENAME . '.file_originalname'),
    )
    #
    #	##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    #	##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    #	##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    #
    ->on('preUpload', function ($editor, $data) {
    })
    ->on('postUpload', function ($editor, $id, $files, $data) {
        updateSyslogAfterupload($editor->db(), $id);
    })
    ->on('preEdit', function ($editor, $id, $values) {
        checkChanges($editor->db(), $id, $values);
        fixLog($editor->db(), 'UPD', $id, $values);
        updateLogCheckouts($editor->db(), $id, $values);
        clearRelFields($editor->db(), $id, $values);
        updateLogComments($editor->db(), 'UPD', $id, $values);
        updateCountComments($editor->db(), $id);
    })
    ->on('preCreate', function ($editor, $values) {
    })
    ->on('preGet', function ($editor, $id) use ($__startDate, $__endDate) {
        $editor->where(function ($q) use ($__startDate, $__endDate) {
            $q->where(__MAIL_INCOMING_TABLENAME . '.inbox_docDate', '( SELECT inbox_docDate FROM ' . __MAIL_INCOMING_TABLENAME . ' WHERE inbox_docDate >= ' . $__startDate . ' AND inbox_docDate <= ' . $__endDate . ' )', 'IN', false);
        });
    })
    ->on('postCreate', function ($editor, $id, $values, $row) {
        updateFields($editor->db(), 'CRT', $id, $values, $row);
        fixCreate($editor->db(), 'CRT', $id, $values);
        fixLog($editor->db(), 'CRT', $id, $values);
        sendEmailToIspol_YouAreIspolnitel($editor->db(), 'Email', $id, $values, $row);
        newUserMessage($editor->db(), 'CRT', $id, $values);
        statsMailIncoming($editor->db(), 'CRT', date("Y"));
        syncRelFields($editor->db(), 'CRT', $id, $values, $row);
        updateLogComments($editor->db(), 'CRT', $id, $values);
        updateCountComments($editor->db(), $id);
    })
    ->on('postEdit', function ($editor, $id, $values, $row) {
        fixUpdate($editor->db(), 'UPD', $id, $values);
        updateFields($editor->db(), 'UPD', $id, $values, $row);
        updateRelCheckout($editor->db(), 'UPD', $id, $values, $row);
        updateIspolStatus($editor->db(), $id, $values);
        updateispolStatusOther($editor->db(), $id, $values);
        updateIspolCheckout($editor->db(), $id, $values, $row);
        updateFileIDadd($editor->db(), 'UPD', $id, $values, $row);
        sendEmailToIspol_YouAreIspolnitel($editor->db(), 'Email', $id, $values, $row);
        newUserMessage($editor->db(), 'UPD', $id, $values);
        statsMailIncoming($editor->db(), 'UPD', date("Y"));
        syncRelFields($editor->db(), 'UPD', $id, $values, $row);
    })
    ->on('preRemove', function ($editor, $id, $values) {
        fixLog($editor->db(), 'DEL', $id, $values);
        backupRemovedRecords($editor->db(), $id);
        delAttachment($editor->db(), $id);
        updateSyslogAfterremove($editor->db(), $id);
    })
    ->on('postRemove', function ($editor) {
        statsMailIncoming($editor->db(), 'DEL', date("Y"));
    })
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

    ->leftJoin('sp_contragents', 'sp_contragents.kodcontragent', '=', __MAIL_INCOMING_TABLENAME . '.inbox_docSender_kodzakaz')
    ->leftJoin(__MAIL_INCOMING_FILES_TABLENAME, __MAIL_INCOMING_FILES_TABLENAME . '.id', '=', __MAIL_INCOMING_TABLENAME . '.inbox_docFileID')
    ->leftJoin(__MAIL_OUTGOING_FILES_TABLENAME, __MAIL_OUTGOING_FILES_TABLENAME . '.id', '=', __MAIL_INCOMING_TABLENAME . '.outbox_fileID_rel')
    ->process($_POST)
    ->json();
